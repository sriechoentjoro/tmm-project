<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

/**
 * InstitutionRegistration Controller
 * 
 * Handles public registration completion for institutions
 * No authentication required
 */
class InstitutionRegistrationController extends AppController
{
    /**
     * Why the login account could not be made, or null.
     *
     * The screen used to say "failed to create user account, please contact
     * administrator" and stop. By then the institution is registered and the
     * link will not open again, so that message left somebody locked out with
     * nothing to quote to the administrator they were being sent to.
     *
     * @var string|null
     */
    protected $accountProblem = null;

    /**
     * Initialize method
     */
    public function initialize()
    {
        parent::initialize();
        if (isset($this->Auth)) {
            $this->Auth->allow(['complete']);
        }
    }

    /**
     * Before filter callback
     *
     * @param \Cake\Event\Event $event Event.
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['complete']);
    }

    /**
     * Complete registration wizard
     * 
     * Multi-step process:
     * 1. Verify token
     * 2. Show institution details
     * 3. Set password
     * 4. Create user account
     * 5. Auto-login
     *
     * @param string|null $token Registration token
     * @return \Cake\Http\Response|null
     */
    public function complete($token = null)
    {
        if (empty($token)) {
            $this->Flash->error(__('Invalid registration link.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        // Try to find institution by token in both tables
        $institution = $this->findInstitutionByToken($token);
        
        if (!$institution) {
            $this->Flash->error(__('Invalid or expired registration link.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        // Check if token is still valid
        if (!$institution->isTokenValid()) {
            $this->Flash->error(__('This registration link has expired.'));
            $this->set('expired', true);
            $this->set('institution', $institution);
            return;
        }

        // Check if already registered
        if ($institution->isRegistered()) {
            $this->Flash->info(__('This institution has already completed registration. Please login.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        // Handle form submission
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // The same five rules the administrator's reset and the LPK's own
            // registration screen enforce. This screen asked for eight
            // characters and nothing else, so the weakest password in the
            // application was the one an institution chose for itself.
            $problem = $this->passwordProblem(
                isset($data['password']) ? $data['password'] : '',
                isset($data['password_confirm']) ? $data['password_confirm'] : ''
            );

            if ($problem !== null) {
                $this->Flash->error($problem);
            } else {
                // The plain password is kept. Assigning it to the institution
                // hashes it there, and handing that hash to the account would
                // hash it a second time - see createUserAccount().
                $plain = $data['password'];

                $institution->password = $plain;
                $institution->completeRegistration();

                // Save institution
                $table = $this->getInstitutionTable($institution);
                if ($table->save($institution)) {
                    // Create user account in authentication system
                    $user = $this->createUserAccount($institution, $plain);
                    if ($user) {
                        $this->Flash->success(__('Registration completed successfully! You can now login.'));
                        if ($this->accountProblem !== null) {
                            $this->Flash->error($this->accountProblem);
                        }

                        // Auto-login the user
                        $this->autoLogin($user);

                        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                    }

                    // Not "please contact administrator" and nothing else: the
                    // institution is registered by now and the link will not
                    // open again, so a message with no reason in it leaves
                    // somebody locked out with nothing to quote.
                    $this->Flash->error($this->accountProblem
                        ?: __('Registration completed but failed to create user account. Please contact administrator.'));
                } else {
                    $this->log('Registration failed. Validation errors: ' . json_encode($institution->getErrors()), 'error');
                    $this->Flash->error(__('Failed to complete registration. Please check your input and try again.'));
                }
            }
        }

        $this->set(compact('institution', 'token'));
        $this->set('rules', $this->passwordRules());
    }

    /**
     * Find institution by registration token
     *
     * @param string $token Registration token
     * @return object|null Institution entity
     */
    protected function findInstitutionByToken($token)
    {
        // Try vocational training institutions first
        $VocationalTrainingInstitutions = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
        $institution = $VocationalTrainingInstitutions->find()
            ->where(['registration_token' => $token])
            ->first();
        
        if ($institution) {
            $institution->institution_type = 'vocational';
            return $institution;
        }

        // Try special skill support institutions
        $SpecialSkillSupportInstitutions = TableRegistry::getTableLocator()->get('SpecialSkillSupportInstitutions');
        $institution = $SpecialSkillSupportInstitutions->find()
            ->where(['registration_token' => $token])
            ->first();
        
        if ($institution) {
            $institution->institution_type = 'special_skill';
            return $institution;
        }

        return null;
    }

    /**
     * Get appropriate table for institution
     *
     * @param object $institution Institution entity
     * @return \Cake\ORM\Table
     */
    protected function getInstitutionTable($institution)
    {
        if ($institution->institution_type === 'vocational') {
            return TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
        } else {
            return TableRegistry::getTableLocator()->get('SpecialSkillSupportInstitutions');
        }
    }

    /**
     * Create the login account this institution will use.
     *
     * Three separate things stopped this working, and the first one hid the
     * other two:
     *
     * 1. institution_type was written as 'vocational' or 'special_skill'. The
     *    column is ENUM('vocational_training', 'special_skill_support'), which
     *    is what every other part of the application writes and compares. MySQL
     *    refuses a value an ENUM does not list, so the save threw, the catch
     *    below turned it into false, and the screen said "failed to create user
     *    account, please contact administrator" - every single time, for both
     *    kinds of institution. Nobody who followed a registration link has ever
     *    ended up with an account.
     *
     * 2. The password was passed already hashed, with a comment saying so. The
     *    User entity hashes whatever it is given, so the stored value was the
     *    hash of a hash and the password the institution had just chosen could
     *    never log in.
     *
     * 3. The role was the literal 6. It is lpk-penyangga in this database and
     *    lpk-so in config/sql/seed_roles.sql, so a reinstall from that file
     *    would have handed every institution somebody else's permissions.
     *
     * The account is matched on email, not username. The token proved the
     * email address and nothing else; a username that happens to collide
     * belongs to somebody else, and quietly overwriting their password would
     * be an account takeover rather than a registration.
     *
     * @param object $institution Institution entity.
     * @param string $plain The password as the institution typed it.
     * @return \Cake\Datasource\EntityInterface|null The account, or null.
     */
    protected function createUserAccount($institution, $plain)
    {
        $this->accountProblem = null;

        try {
            $Users = TableRegistry::getTableLocator()->get('Users');
            $type = $this->canonicalInstitutionType($institution->institution_type);
            $name = isset($institution->name) && $institution->name
                ? $institution->name
                : null;

            $user = $Users->find()->where(['email' => $institution->email])->first();

            if ($user) {
                // The same person, proved by the token. Give them the password
                // they have just chosen rather than leaving the old one in
                // place and reporting success.
                $user->password = $plain;
                $user->institution_id = $institution->id;
                $user->institution_type = $type;
                $user->is_active = 1;
                $user->status = 'active';
                $user->email_verified_at = new Time();
            } else {
                $taken = $Users->find()
                    ->where(['username' => $institution->username])
                    ->first();
                if ($taken) {
                    $this->accountProblem = __('The login name {0} already belongs to another account. '
                        . 'An administrator has to change it before this institution can sign in.',
                        $institution->username);
                    $this->log(sprintf('Registration for institution %d: username %s is taken by user %d',
                        $institution->id, $institution->username, $taken->id), 'error');

                    return null;
                }

                $user = $Users->newEntity([
                    'username' => $institution->username,
                    'email' => $institution->email,
                    'password' => $plain,
                    'full_name' => $name,
                    'is_active' => 1,
                    'institution_id' => $institution->id,
                    'institution_type' => $type,
                ]);

                // Not through newEntity(): status and email_verified_at are
                // not in User::$_accessible, and mass assignment drops what is
                // not accessible without a word. They are deliberately not
                // accessible - a form must never be able to post status=active
                // - so they are set here, where the token has just proved the
                // address.
                $user->status = 'active';
                $user->email_verified_at = new Time();
            }

            if (!$Users->save($user)) {
                $said = [];
                foreach ($user->getErrors() as $field => $rules) {
                    foreach ((array)$rules as $message) {
                        $said[] = $field . ': ' . (is_array($message) ? implode(', ', $message) : $message);
                    }
                }
                $this->accountProblem = __('The account could not be created: {0}',
                    $said ? implode('; ', $said) : __('no reason was given'));
                $this->log('Registration: saving the account failed. ' . json_encode($user->getErrors()), 'error');

                return null;
            }

            // The role by name. Which id lpk-penyangga has is this database's
            // business, not this file's.
            $Roles = TableRegistry::getTableLocator()->get('Roles');
            $role = $Roles->find()->where(['name' => 'lpk-penyangga'])->first();
            if (!$role) {
                $this->accountProblem = __('Your account was created, but the {0} role does not exist, '
                    . 'so it has no permissions yet. Please contact an administrator.', 'lpk-penyangga');

                return $user;
            }

            $UserRoles = TableRegistry::getTableLocator()->get('UserRoles');
            $already = $UserRoles->find()
                ->where(['user_id' => $user->id, 'role_id' => $role->id])
                ->first();
            if (!$already) {
                $userRole = $UserRoles->newEntity([
                    'user_id' => $user->id,
                    'role_id' => $role->id,
                ]);
                if (!$UserRoles->save($userRole)) {
                    // The account exists, so this is not a failed registration -
                    // saying it was would send somebody to register again with
                    // a link that no longer opens.
                    $this->accountProblem = __('Your account was created, but its role could not be '
                        . 'assigned. Please contact an administrator.');
                }
            }

            return $user;
        } catch (\Exception $e) {
            $this->accountProblem = __('The account could not be created: {0}', $e->getMessage());
            $this->log('Failed to create user account: ' . $e->getMessage(), 'error');

            return null;
        }
    }

    /**
     * Auto-login user after registration
     *
     * @param \Cake\Datasource\EntityInterface $user The account just created.
     * @return void
     */
    protected function autoLogin($user)
    {
        $this->Auth->setUser($user->toArray());
    }

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        // Handle language switching
        if ($lang = $this->request->getQuery('lang')) {
            if (in_array($lang, ['ind', 'eng', 'jpn'])) {
                $this->request->getSession()->write('Config.language', $lang);
                return $this->redirect(['action' => 'processFlow']);
            }
        }
        
        $this->viewBuilder()->setLayout('process_flow');
    }
}
<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Log\Log;

/**
 * LpkRegistration Controller
 *
 * Manages the 3-step LPK (Vocational Training Institution) registration process:
 * Step 1: Admin creates LPK record → sends verification email
 * Step 2: LPK clicks verification link → email confirmed
 * Step 3: LPK sets password → account activated
 *
 * @property \App\Model\Table\VocationalTrainingInstitutionsTable $VocationalTrainingInstitutions
 * @property \App\Model\Table\EmailVerificationTokensTable $EmailVerificationTokens
 * @property \App\Model\Table\UsersTable $Users
 * @property \App\Model\Table\StakeholderActivitiesTable $StakeholderActivities
 */
class LpkRegistrationController extends AppController
{
    /**
     * Before filter callback
     *
     * @param \Cake\Event\Event $event Event object
     * @return \Cake\Http\Response|null|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        
        // Allow public access to verification and password setup
        $this->Auth->allow(['verifyEmail', 'setPassword', 'resendVerification']);

        // Whoever opens those pages arrived from an email and is not logged in,
        // so the language switcher cannot use Users::changeLanguage - that action
        // requires authentication and would bounce them to the login form. The
        // login layout links to ?lang= instead, which is handled here, the same
        // way the other public pages do it.
        $lang = $this->request->getQuery('lang');
        if ($lang && in_array($lang, ['ind', 'eng', 'jpn'], true)) {
            $this->request->getSession()->write('Config.language', $lang);

            return $this->redirect($this->request->getPath());
        }
    }

    /**
     * Index method - List all LPK registrations
     *
     * @return \Cake\Http\Response|null|void
     */
    public function index()
    {
        $this->loadModel('VocationalTrainingInstitutions');
        
        $this->paginate = [
            'contain' => ['MasterPropinsis', 'MasterKabupatens'],
            'order' => ['VocationalTrainingInstitutions.created' => 'DESC'],
            'limit' => 20
        ];

        $institutions = $this->paginate($this->VocationalTrainingInstitutions);
        $this->set(compact('institutions'));
    }

    /**
     * Step 1: Create LPK Registration
     *
     * Admin creates new LPK record and system sends verification email
     *
     * @return \Cake\Http\Response|null|void
     */
    public function create()
    {
        $this->loadModel('VocationalTrainingInstitutions');
        $this->loadModel('EmailVerificationTokens');
        
        $institution = $this->VocationalTrainingInstitutions->newEntity();

        if ($this->request->is('post')) {
            // mou_file arrives as an upload array; uploadFile() moves it and
            // rewrites the request data with the stored path, so re-read after.
            $this->uploadFile('VocationalTrainingInstitutions', 'mou_file', 'vocationaltraininginstitutions');
            $data = $this->request->getData();

            // Clear the way for an address that has been registered before,
            // where the installation allows it. Must happen before newEntity(),
            // because the unique-email rule is checked against what is in the
            // table at save() time.
            if (!empty($data['email'])) {
                $this->_freeEmailForRetest($data['email']);
            }

            $institution = $this->VocationalTrainingInstitutions->newEntity($data);

            // Not a mass-assignable field, and only present on installations
            // that ran database/migrations/stakeholder_management_schema.sql.
            // Setting it directly is a no-op where the column is absent.
            $institution->status = 'pending_verification';

            if ($this->VocationalTrainingInstitutions->save($institution)) {
                try {
                    // Generate verification token
                    $token = $this->EmailVerificationTokens->generateToken(
                        $institution->email,
                        'email_verification'
                    );
                    
                    if ($token) {
                        // Send verification email
                        $this->loadComponent('EmailService');
                        $verificationUrl = \Cake\Routing\Router::url([
                            'controller' => 'LpkRegistration',
                            'action' => 'verifyEmail',
                            $token,
                            'prefix' => 'admin'
                        ], true);
                        
                        $emailSent = $this->EmailService->sendEmail(
                            $institution->email,
                            $institution->director,
                            'lpk_verification',
                            [
                                'directorName' => $institution->director,
                                'institutionName' => $institution->name,
                                'registrationNumber' => $institution->abbreviation,
                                'email' => $institution->email,
                                // The login name waiting behind the link. The
                                // mail goes to the institution's own address,
                                // so this is the one place it can be handed
                                // over before the account exists.
                                'username' => $institution->username,
                                'registeredByAdmin' => $this->Auth->user('fullname'),
                                'registrationDate' => ($institution->created ?: Time::now())->format('d F Y, H:i'),
                                'verificationUrl' => $verificationUrl
                            ]
                        );
                        
                        if ($emailSent) {
                            // Log activity
                            $this->loadModel('StakeholderActivities');
                            $this->StakeholderActivities->logActivity(
                                'registration',
                                'vocational_training',
                                $institution->id,
                                'LPK registered: ' . $institution->name . ' (Status: pending_verification)',
                                ['email' => $institution->email],
                                null, // user_id
                                $this->Auth->user('id') // admin_id
                            );
                            
                            $this->Flash->success(__('LPK registered successfully. Verification email sent to {0}.', $institution->email));
                            Log::info("LPK registration created: {$institution->name} (ID: {$institution->id})", ['scope' => 'lpk_registration']);
                            
                            return $this->redirect(['action' => 'index']);
                        } else {
                            $this->Flash->warning(__('LPK registered but failed to send verification email. Please resend manually.'));
                            Log::error("Failed to send verification email for LPK: {$institution->name}", ['scope' => 'lpk_registration']);
                        }
                    } else {
                        $this->Flash->error(__('LPK registered but failed to generate verification token.'));
                        Log::error("Failed to generate token for LPK: {$institution->name}", ['scope' => 'lpk_registration']);
                    }
                } catch (\Throwable $e) {  // \Error is not an \Exception; without this a
                            // fatal here leaves the record saved and the
                            // request dead, with no flash and no email.
                    $this->Flash->error(__('LPK registered but error occurred: {0}', $e->getMessage()));
                    Log::error("Error during LPK registration: " . $e->getMessage(), ['scope' => 'lpk_registration']);
                }
                
                return $this->redirect(['action' => 'index']);
            }
            
            // Without this the page fails silently whenever a required field has
            // no input on the form: the message says "check the form" but there
            // is nothing on the form to correct.
            $messages = [];
            foreach ($institution->getErrors() as $field => $fieldErrors) {
                foreach ((array)$fieldErrors as $message) {
                    $messages[] = $field . ': ' . (is_array($message) ? implode(', ', $message) : $message);
                }
            }
            $this->Flash->error(
                $messages
                    ? __('Unable to register LPK. {0}', implode(' | ', $messages))
                    : __('Unable to register LPK. Please check the form and try again.')
            );
            Log::error(
                'Failed to save LPK registration: ' . json_encode($institution->getErrors()),
                ['scope' => 'lpk_registration']
            );
        }

        // Load dropdown data
        $this->loadModel('MasterPropinsis');
        $this->loadModel('MasterKabupatens');
        $this->loadModel('MasterKecamatans');
        $this->loadModel('MasterKelurahans');
        
        $masterPropinsis = $this->MasterPropinsis->find('list')->order(['title' => 'ASC'])->toArray();
        
        // For edit mode, load only stored values
        $masterKabupatens = [];
        $masterKecamatans = [];
        $masterKelurahans = [];
        
        // Provide full data for JavaScript cascade
        $masterKabupatensData = $this->MasterKabupatens->find('all')
            ->select(['id', 'title', 'propinsi_id'])
            ->toArray();
        $masterKecamatansData = $this->MasterKecamatans->find('all')
            ->select(['id', 'title', 'kabupaten_id'])
            ->toArray();
        $masterKelurahansData = $this->MasterKelurahans->find('all')
            ->select(['id', 'title', 'kecamatan_id'])
            ->toArray();

        $this->set(compact(
            'institution',
            'masterPropinsis',
            'masterKabupatens',
            'masterKecamatans',
            'masterKelurahans',
            'masterKabupatensData',
            'masterKecamatansData',
            'masterKelurahansData'
        ));
    }

    /**
     * Step 2: Verify Email
     *
     * LPK clicks verification link from email
     * Public action - no authentication required
     *
     * @param string|null $token Verification token
     * @return \Cake\Http\Response|null|void
     */
    public function verifyEmail($token = null)
    {
        $this->viewBuilder()->setLayout('login');
        
        // Validate token format
        if (!$token || strlen($token) !== 64) {
            $this->Flash->error(__('Invalid verification link. Please check your email and try again.'));
            Log::warning("Invalid token format attempted", ['scope' => 'lpk_registration']);
            return $this->redirect('/');
        }
        
        $this->loadModel('EmailVerificationTokens');
        $this->loadModel('VocationalTrainingInstitutions');
        
        // Validate token
        $tokenRecord = $this->EmailVerificationTokens->validateToken($token, 'email_verification');
        
        if (!$tokenRecord) {
            // A second click on the same link is the ordinary case, not an
            // attack: validateToken() only matches is_used = 0, and the first
            // click set that flag. Before calling the link invalid, look the
            // token up without that filter and see whether it simply already did
            // its job - otherwise reloading the page, or switching language on
            // it, turns a completed verification into an error.
            $spent = $this->EmailVerificationTokens->find()
                ->where(['token' => $token, 'token_type' => 'email_verification'])
                ->first();

            if ($spent) {
                $verified = $this->VocationalTrainingInstitutions->find()
                    ->where(['email' => $spent->user_email])
                    ->first();

                if ($verified && $verified->status === 'active') {
                    $this->Flash->info(__('Your account is already active. Please login.'));

                    return $this->redirect(['controller' => 'Users', 'action' => 'login', 'prefix' => false]);
                }

                if ($verified && $verified->status === 'verified') {
                    $this->Flash->info(__('Email already verified. Please set your password to complete registration.'));

                    return $this->redirect(['action' => 'setPassword', $verified->id]);
                }
            }

            $this->Flash->error(__('This verification link is invalid, expired, or has already been used.'));
            Log::warning("Invalid/expired token: $token", ['scope' => 'lpk_registration']);

            $this->set('tokenStatus', 'invalid');
            return;
        }
        
        // Find institution by email
        $institution = $this->VocationalTrainingInstitutions->find()
            ->where(['email' => $tokenRecord->user_email])
            ->first();
        
        if (!$institution) {
            $this->Flash->error(__('Institution not found. Please contact support.'));
            Log::error("Institution not found for email: {$tokenRecord->user_email}", ['scope' => 'lpk_registration']);
            
            $this->set('tokenStatus', 'not_found');
            return;
        }
        
        // Check if already verified
        if ($institution->status === 'verified' || $institution->status === 'active') {
            $this->Flash->info(__('Email already verified. Please set your password to complete registration.'));
            Log::info("Email already verified for: {$institution->name}", ['scope' => 'lpk_registration']);
            
            return $this->redirect(['action' => 'setPassword', $institution->id]);
        }
        
        // Update institution status
        $institution->status = 'verified';
        $institution->email_verified_at = new Time();
        
        if ($this->VocationalTrainingInstitutions->save($institution)) {
            // Mark token as used
            $this->EmailVerificationTokens->markAsUsed($token);
            
            // Log activity
            $this->loadModel('StakeholderActivities');
            $this->StakeholderActivities->logActivity(
                'verification',
                'vocational_training',
                $institution->id,
                'Email verified: ' . $institution->email,
                ['token_used' => substr($token, 0, 10) . '...'],
                null, // user_id
                null  // admin_id
            );
            
            $this->Flash->success(__('Email verified successfully! Please set your password to activate your account.'));
            Log::info("Email verified for LPK: {$institution->name} (ID: {$institution->id})", ['scope' => 'lpk_registration']);
            
            // Redirect to password setup after 3 seconds
            $this->set('tokenStatus', 'success');
            $this->set('redirectUrl', \Cake\Routing\Router::url(['action' => 'setPassword', $institution->id], true));
            $this->set('institution', $institution);
        } else {
            $this->Flash->error(__('Error updating verification status. Please try again or contact support.'));
            Log::error("Failed to update verification status for LPK: {$institution->name}", ['scope' => 'lpk_registration']);
            
            $this->set('tokenStatus', 'error');
        }
    }

    /**
     * Step 3: Set Password
     *
     * LPK sets password to activate account
     * Public action - no authentication required
     *
     * @param int|null $id Institution ID
     * @return \Cake\Http\Response|null|void
     */
    public function setPassword($id = null)
    {
        $this->viewBuilder()->setLayout('login');
        
        $this->loadModel('VocationalTrainingInstitutions');
        $this->loadModel('Users');
        
        try {
            $institution = $this->VocationalTrainingInstitutions->get($id);
        } catch (\Throwable $e) {
            $this->Flash->error(__('Institution not found.'));
            Log::error("Institution not found: ID $id", ['scope' => 'lpk_registration']);
            return $this->redirect('/');
        }
        
        // Check status - must be verified to set password
        if ($institution->status === 'pending_verification') {
            $this->Flash->error(__('Please verify your email first.'));
            return $this->redirect('/');
        }
        
        if ($institution->status === 'active') {
            $this->Flash->info(__('Your account is already active. Please login.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login', 'prefix' => false]);
        }
        
        if ($this->request->is(['post', 'put'])) {
            $password = $this->request->getData('password');
            $confirmPassword = $this->request->getData('confirm_password');
            
            // Validate passwords match
            if ($password !== $confirmPassword) {
                $this->Flash->error(__('Passwords do not match. Please try again.'));
            }
            // Validate password length
            elseif (strlen($password) < 8) {
                $this->Flash->error(__('Password must be at least 8 characters long.'));
            }
            // Validate password complexity
            elseif (!preg_match('/[A-Z]/', $password)) {
                $this->Flash->error(__('Password must contain at least one uppercase letter.'));
            }
            elseif (!preg_match('/[a-z]/', $password)) {
                $this->Flash->error(__('Password must contain at least one lowercase letter.'));
            }
            elseif (!preg_match('/[0-9]/', $password)) {
                $this->Flash->error(__('Password must contain at least one number.'));
            }
            elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
                $this->Flash->error(__('Password must contain at least one special character (!@#$%^&* etc).'));
            }
            else {
                // Password is valid - create or update user account
                $user = $this->Users->find()
                    ->where(['email' => $institution->email])
                    ->first();
                
                if (!$user) {
                    // The login name the admin typed on the registration form,
                    // which the form promises is "the login name this LPK will
                    // use once it sets a password". It was collected, stored and
                    // then ignored here: the account was named after the
                    // institution instead, so the promise was not kept and the
                    // value shown on the record was not the one that worked.
                    $username = $this->_loginUsernameFor($institution);

                    $user = $this->Users->newEntity([
                        'username' => $username,
                        'email' => $institution->email,
                        'full_name' => $institution->director,
                        'password' => $password,
                        'institution_id' => $institution->id,
                        'institution_type' => 'vocational_training',
                        'is_active' => 1,
                        'status' => 'active',
                        'email_verified_at' => new Time()
                    ]);
                } else {
                    // Update existing user
                    $user->password = $password;
                    $user->status = 'active';
                    $user->is_active = 1;
                    $user->institution_id = $institution->id;
                    $user->institution_type = 'vocational_training';
                }
                
                if ($this->Users->save($user)) {
                    // Update institution status
                    $institution->status = 'active';

                    // ...and record that registration is finished, which this
                    // flow never did.
                    //
                    // There are two registration flows in this application and
                    // they were keeping score in different columns. The older
                    // one (InstitutionRegistration::complete) calls
                    // completeRegistration(), which sets is_registered and
                    // registered_at; this one only ever moved 'status'. Every
                    // counter and badge that asks "is it registered?" reads
                    // is_registered - so an LPK that finished here showed
                    // Status: Active and Registered: No on the same row, and
                    // stayed in the verify page's Pending count for good, with
                    // nothing an admin could press to change it.
                    $institution->completeRegistration();

                    $this->VocationalTrainingInstitutions->save($institution);

                    // Send welcome email
                    $this->loadComponent('EmailService');
                    $this->EmailService->sendEmail(
                        $institution->email,
                        $institution->director,
                        'lpk_welcome',
                        [
                            'directorName' => $institution->director,
                            'institutionName' => $institution->name,
                            'username' => $user->username,
                            'email' => $institution->email,
                            'loginUrl' => \Cake\Routing\Router::url([
                                'controller' => 'Users',
                                'action' => 'login',
                                'prefix' => false
                            ], true)
                        ]
                    );
                    
                    // Log activity
                    $this->loadModel('StakeholderActivities');
                    $this->StakeholderActivities->logActivity(
                        'activation',
                        'vocational_training',
                        $institution->id,
                        'Account activated: ' . $institution->name . ' (Username: ' . $user->username . ')',
                        ['username' => $user->username],
                        null, // user_id
                        null  // admin_id
                    );
                    
                    $this->Flash->success(__('Account activated successfully! You can now login with username: {0}', $user->username));
                    Log::info("Account activated for LPK: {$institution->name} (Username: {$user->username})", ['scope' => 'lpk_registration']);
                    
                    return $this->redirect(['controller' => 'Users', 'action' => 'login', 'prefix' => false]);
                }
                
                $this->Flash->error(__('Error creating user account. Please try again or contact support.'));
                Log::error("Failed to save user for LPK: {$institution->name}", ['scope' => 'lpk_registration']);
            }
        }
        
        $this->set(compact('institution'));
    }

    /**
     * Resend verification email
     *
     * @param int|null $id Institution ID
     * @return \Cake\Http\Response|null|void
     */
    public function resendVerification($id = null)
    {
        $this->loadModel('VocationalTrainingInstitutions');
        $this->loadModel('EmailVerificationTokens');
        
        try {
            $institution = $this->VocationalTrainingInstitutions->get($id);
        } catch (\Throwable $e) {
            $this->Flash->error(__('Institution not found.'));
            return $this->redirect('/');
        }
        
        if ($institution->status !== 'pending_verification') {
            $this->Flash->error(__('This institution has already been verified.'));
            return $this->redirect('/');
        }
        
        // Generate new token and invalidate old ones
        $token = $this->EmailVerificationTokens->resendVerification($institution->email);
        
        if ($token) {
            // Send verification email
            $this->loadComponent('EmailService');
            $verificationUrl = \Cake\Routing\Router::url([
                'controller' => 'LpkRegistration',
                'action' => 'verifyEmail',
                $token,
                'prefix' => 'admin'
            ], true);
            
            $emailSent = $this->EmailService->sendEmail(
                $institution->email,
                $institution->director,
                'lpk_verification',
                [
                    'directorName' => $institution->director,
                    'institutionName' => $institution->name,
                    'registrationNumber' => $institution->abbreviation,
                    'email' => $institution->email,
                    'username' => $institution->username,
                    'registeredByAdmin' => $this->Auth->user('fullname'),
                    'registrationDate' => ($institution->created ?: Time::now())->format('d F Y, H:i'),
                    'verificationUrl' => $verificationUrl
                ]
            );
            
            if ($emailSent) {
                $this->Flash->success(__('Verification email resent successfully to {0}.', $institution->email));
                Log::info("Verification email resent for LPK: {$institution->name}", ['scope' => 'lpk_registration']);
            } else {
                $this->Flash->error(__('Failed to send verification email. Please try again later.'));
            }
        } else {
            $this->Flash->error(__('Failed to generate verification token. Please contact support.'));
        }
        
        return $this->redirect($this->referer());
    }

    /**
     * Make an email address registrable again, where the installation allows it.
     *
     * vocational_training_institutions.email is unique, so an address that has
     * been through the flow once is spent: register -> verify -> set password
     * can be rehearsed exactly once per mailbox, and the second attempt fails
     * on the unique rule with no way forward but the database. That is no way
     * to test a three-step flow.
     *
     * With Lpk.reuseEmailForTesting on, the earlier institution and its
     * verification tokens are removed first and registration proceeds as if
     * the address were new.
     *
     * The users row is deliberately left alone. setPassword() looks the account
     * up by email and updates it rather than creating a second one, so keeping
     * it is what lets the same login work again after a retest - and deleting
     * it could take out an account someone is actually using.
     *
     * Off means off: with LPK_REUSE_EMAIL=0 this does nothing at all and a
     * duplicate address fails the unique rule exactly as before.
     *
     * @param string $email The address being registered.
     * @return void
     */
    protected function _freeEmailForRetest($email)
    {
        if (!Configure::read('Lpk.reuseEmailForTesting')) {
            return;
        }

        $this->loadModel('VocationalTrainingInstitutions');
        $this->loadModel('EmailVerificationTokens');

        $existing = $this->VocationalTrainingInstitutions->find()
            ->where(['email' => $email])
            ->first();

        if (!$existing) {
            return;
        }

        // A foreign key pointing at this row would make the delete throw, and an
        // uncaught throw here would lose the whole registration to a blank 500.
        // Report it and fall through instead: the save below then fails on the
        // unique-email rule, which is the honest outcome.
        try {
            $deleted = $this->VocationalTrainingInstitutions->delete($existing);
        } catch (\Throwable $e) {
            $this->Flash->error(__(
                'Could not clear the earlier registration for {0}: {1}',
                $email,
                $e->getMessage()
            ));
            Log::error(
                "Lpk.reuseEmailForTesting: could not delete institution {$existing->id}: " . $e->getMessage(),
                ['scope' => 'lpk_registration']
            );

            return;
        }

        if (!$deleted) {
            $this->Flash->error(__('Could not clear the earlier registration for {0}.', $email));
            Log::error(
                "Lpk.reuseEmailForTesting: delete() returned false for institution {$existing->id}",
                ['scope' => 'lpk_registration']
            );

            return;
        }

        $this->EmailVerificationTokens->deleteAll(['user_email' => $email]);

        // Said out loud in both places: the admin sees that a record vanished,
        // and the log keeps what it was, since nothing else records this.
        $this->Flash->warning(__(
            'Testing mode: the earlier registration for {0} ({1}) was deleted so the address could be reused.',
            $email,
            $existing->name
        ));
        Log::warning(
            "Lpk.reuseEmailForTesting: deleted institution {$existing->id} ({$existing->name}) "
            . "and the verification tokens for $email",
            ['scope' => 'lpk_registration']
        );
    }

    /**
     * The login name to give an institution's user account.
     *
     * The registration form asks for one and calls it "the login name this LPK
     * will use once it sets a password", and the institutions table keeps it
     * unique. So that is the name to use, and the one the record and the
     * verification email can honestly show.
     *
     * It is still checked against the users table before being taken. That
     * table holds staff accounts too, and its own unique index would reject a
     * clash at save() time with nothing but a validation error for the
     * institution to read. A clash is unlikely and worth a log line when it
     * happens; falling back to a name derived from the institution keeps
     * activation working rather than dead-ending it.
     *
     * @param \Cake\Datasource\EntityInterface $institution The institution.
     * @return string Username
     */
    protected function _loginUsernameFor($institution)
    {
        $wanted = trim((string)$institution->username);
        if ($wanted === '') {
            return $this->_generateUsername($institution->name);
        }

        $this->loadModel('Users');
        $taken = $this->Users->find()->where(['username' => $wanted])->first();
        if (!$taken) {
            return $wanted;
        }

        $fallback = $this->_generateUsername($institution->name);
        Log::warning(
            "Username '$wanted' requested for institution {$institution->id} is already held by "
            . "user {$taken->id}; using '$fallback' instead",
            ['scope' => 'lpk_registration']
        );

        return $fallback;
    }

    /**
     * Generate unique username from institution name
     *
     * @param string $institutionName Institution name
     * @return string Username
     */
    protected function _generateUsername($institutionName)
    {
        // Convert to lowercase and replace spaces with underscores
        $username = strtolower(str_replace(' ', '_', $institutionName));
        
        // Remove special characters
        $username = preg_replace('/[^a-z0-9_]/', '', $username);
        
        // Limit to 50 characters
        $username = substr($username, 0, 50);
        
        // Check if username exists
        $this->loadModel('Users');
        $existingUser = $this->Users->find()->where(['username' => $username])->first();
        
        if ($existingUser) {
            // Append number if exists
            $counter = 1;
            $baseUsername = $username;
            
            while ($existingUser) {
                $username = $baseUsername . '_' . $counter;
                $existingUser = $this->Users->find()->where(['username' => $username])->first();
                $counter++;
            }
        }
        
        return $username;
    }
    
    /**
     * Process Flow Documentation
     *
     * Displays interactive process flow diagram with database relationships
     * Helps users understand the 3-step registration workflow
     *
     * @return \Cake\Http\Response|null|void
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
        
        // This action renders the process flow documentation
        // Template: src/Template/Admin/LpkRegistration/process_flow.ctp
        // Layout: src/Template/Layout/process_flow.ctp
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Flash');
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        // Allow login, logout and changeLanguage to be accessed without being logged in
        $this->Auth->allow(['login', 'logout', 'changeLanguage', 'help', 'guide']);
    }

    /**
     * Who may reset somebody else's password.
     *
     * Only an administrator, and said here rather than left to the menu
     * permissions. getMenuRolePermissions() expands granted_actions = '*' to
     * [menu action, index, view], so resetPassword would be refused by default
     * - but a menu row whose granted_actions names it would grant it to any
     * role holding that menu, and handing one account's password to the holder
     * of another is not something a menu row should be able to decide.
     *
     * Everything else on this controller is governed as before.
     *
     * @param array|\ArrayAccess|null $user The logged-in user.
     * @return bool
     */
    public function isAuthorized($user = null)
    {
        if ($this->request->getParam('action') === 'resetPassword') {
            $roles = isset($user['role_names']) ? (array)$user['role_names'] : [];
            if (in_array('administrator', $roles, true)) {
                return true;
            }
            $this->Flash->error(__('Only an administrator can reset a password.'));

            return false;
        }

        return parent::isAuthorized($user);
    }

    /**
     * Help method - User guidance page
     */
    public function help()
    {
        // This is just a view, no data processing needed
    }

    /**
     * Guide method - full end-to-end TMM process-flow guide.
     * Accessible from the login page (no authentication required).
     */
    public function guide()
    {
        $this->viewBuilder()->setLayout('process_flow');
    }

    /**
     * Login method
     */
    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                // Manually fetch roles and add to user session
                $userEntity = $this->Users->get($user['id'], [
                    'contain' => ['Roles']
                ]);
                
                // Add role_names to session user data
                $user['role_names'] = collection($userEntity->roles)->extract('name')->toArray();
                $user['roles'] = $userEntity->roles; // Keep full role objects if needed
                
                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }
    }

    /**
     * Logout method
     */
    public function logout()
    {
        \Cake\Log\Log::debug('Logout method called');
        
        $this->Flash->success(__('You have been logged out.'));
        $logoutUrl = $this->Auth->logout();
        
        \Cake\Log\Log::debug('Logout URL: ' . print_r($logoutUrl, true));
        
        // Destroy session completely
        $this->request->getSession()->destroy();
        
        \Cake\Log\Log::debug('Redirecting to: ' . print_r($logoutUrl, true));
        
        return $this->redirect($logoutUrl);
    }

    /**
     * Change Language method
     * 
     * @param string $lang Language code (ind, eng, jpn)
     */
    public function changeLanguage($lang = 'ind')
    {
        // Validate language
        $allowedLanguages = ['ind', 'eng', 'jpn'];
        if (!in_array($lang, $allowedLanguages)) {
            $lang = 'ind'; // Default to Indonesian
        }
        
        // Store language preference in session
        $this->request->getSession()->write('Config.language', $lang);
        
        // Set flash message based on language
        $messages = [
            'ind' => 'Bahasa telah diubah ke Indonesia',
            'eng' => 'Language changed to English',
            'jpn' => '言語が日本語に変更されました'
        ];
        $this->Flash->success($messages[$lang]);
        
        // Redirect back to referring page or dashboard
        return $this->redirect($this->referer(['controller' => 'Dashboard', 'action' => 'index']));
    }

    /**
     * Index method
     */
    public function index()
    {
        // Optional filters
        $filterRole = (int)$this->request->getQuery('role_id');
        $filterStatus = (string)$this->request->getQuery('status');
        $search = trim((string)$this->request->getQuery('q'));

        $query = $this->Users->find()->contain(['Roles']);
        if ($filterRole) {
            $query->matching('Roles', function ($q) use ($filterRole) {
                return $q->where(['Roles.id' => $filterRole]);
            });
        }
        if ($filterStatus !== '') {
            $query->where(['Users.status' => $filterStatus]);
        }
        if ($search !== '') {
            $query->where(['OR' => [
                'Users.username LIKE' => '%' . $search . '%',
                'Users.email LIKE' => '%' . $search . '%',
                'Users.full_name LIKE' => '%' . $search . '%',
            ]]);
        }

        $this->paginate = ['order' => ['Users.id' => 'ASC']];
        $users = $this->paginate($query);

        // Summary stats + role/status filter data
        $roleList = $this->Users->Roles->find('list')->toArray();
        $summary = ['total' => 0, 'active' => 0, 'pending' => 0, 'inactive' => 0];
        $byRole = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_authentication_authorization');
            $summary['total'] = (int)$conn->execute('SELECT COUNT(*) FROM users')->fetch()[0];
            $summary['active'] = (int)$conn->execute("SELECT COUNT(*) FROM users WHERE status = 'active'")->fetch()[0];
            $summary['pending'] = (int)$conn->execute("SELECT COUNT(*) FROM users WHERE status = 'pending_verification'")->fetch()[0];
            $summary['inactive'] = (int)$conn->execute('SELECT COUNT(*) FROM users WHERE is_active = 0')->fetch()[0];
            foreach ($conn->execute(
                'SELECT r.id, COUNT(*) AS total FROM user_roles ur
                 JOIN roles r ON r.id = ur.role_id GROUP BY r.id'
            )->fetchAll('assoc') as $row) {
                $byRole[(int)$row['id']] = (int)$row['total'];
            }
        } catch (\Exception $e) {
        }

        // Only an administrator may reset a password, so only an administrator
        // is shown the button. isAuthorized() is what actually refuses it; this
        // is so the other roles are not offered something they cannot do.
        $this->set('isAdministrator', $this->hasRole('administrator'));

        // The institutions these accounts belong to, by name. The column shows
        // "LPK #12" without this, which is an id to go and look up rather than
        // an answer. One query per kind, not one per row.
        $this->set('institutionNames', $this->institutionNamesFor($users));

        $this->set(compact('users', 'roleList', 'summary', 'byRole',
            'filterRole', 'filterStatus', 'search'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);

        // Reset Password is offered here as a named button, not only as an icon
        // in the list: an icon among four others is not something anybody finds
        // when they are looking for it.
        $this->set('isAdministrator', $this->hasRole('administrator'));
        $this->set('institution', $this->institutionFor($user));
        $this->set('user', $user);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->loadModel('SpecialSkillSupportInstitutions');
        $this->loadModel('VocationalTrainingInstitutions');
        
        $lpkPenyangga = $this->SpecialSkillSupportInstitutions->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->order(['name' => 'ASC'])->toArray();
        
        $lpkSo = $this->VocationalTrainingInstitutions->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->order(['name' => 'ASC'])->toArray();
        
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles', 'lpkPenyangga', 'lpkSo'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => ['Roles']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $user = $this->Users->patchEntity($user, $data);
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->loadModel('SpecialSkillSupportInstitutions');
        $this->loadModel('VocationalTrainingInstitutions');

        $lpkPenyangga = $this->SpecialSkillSupportInstitutions->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->order(['name' => 'ASC'])->toArray();

        $lpkSo = $this->VocationalTrainingInstitutions->find('list', [
            'keyField' => 'id',
            'valueField' => 'name',
        ])->order(['name' => 'ASC'])->toArray();

        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles', 'lpkPenyangga', 'lpkSo'));
    }


    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Reset one account's password, as an administrator.
     *
     * Before this, an administrator could change somebody's password only by
     * opening the whole edit form: no confirmation field, none of the rules the
     * account holder had to meet when they set it themselves, and no record
     * that it happened. An account can be locked out at any hour, and the
     * person who fixes it should not have to walk past every other field on the
     * record to do it.
     *
     * The rules are the ones in PasswordPolicyTrait, which is also what the
     * LPK's own set-password screen enforces, so an administrator cannot set a
     * password the holder could not have set.
     *
     * The new password is never mailed and never put in a Flash message. Mail
     * is not a private channel and a Flash message is read by whoever is
     * standing at the screen; an administrator who typed it already knows it,
     * and a generated one is on the form in front of them. What is recorded is
     * that it happened, to whom, and by whom - not the password.
     *
     * @param string|null $id The account to reset.
     * @return \Cake\Http\Response|null Redirects on success, renders otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When no such account.
     */
    public function resetPassword($id = null)
    {
        $user = $this->Users->get($id, ['contain' => ['Roles']]);

        // Which institution this account belongs to, so the screen can say
        // whose password is about to change rather than only an id. An LPK
        // account and a head-office account look alike on a bare form.
        //
        // This had its own copy of the lookup. It compared institution_type
        // with 'special_skill_support', which nothing in this application
        // writes, and then read ->name off whichever table it got - and the
        // special-skill table names its institutions company_name. Resolved in
        // one place now; see InstitutionNameTrait.
        $institution = $this->institutionFor($user);

        if ($this->request->is(['post', 'put'])) {
            $password = (string)$this->request->getData('password');
            $problem = $this->passwordProblem($password,
                $this->request->getData('confirm_password'));

            if ($problem !== null) {
                $this->Flash->error($problem);
            } else {
                // Assigned, not patched: 'password' is accessible, but going
                // through patchEntity would also take anything else that
                // arrived in the post. This screen changes one thing.
                $user->password = $password;
                $user->setDirty('password', true);

                if ($this->Users->save($user)) {
                    $this->recordDecision('user.resetPassword', [
                        'type' => 'User',
                        'id' => $user->id,
                        'label' => $user->username,
                    ], [
                        'email' => $user->email,
                        'institution_id' => $user->institution_id,
                        'institution_type' => $user->institution_type,
                    ]);

                    $this->Flash->success(__(
                        'The password for {0} has been reset. Give it to them yourself - it is not sent by email.',
                        $user->username
                    ));

                    return $this->redirect(['action' => 'index']);
                }

                $this->Flash->error(__('The password could not be saved. Please, try again.'));
            }
        }

        $this->set(compact('user', 'institution'));
        $this->set('rules', $this->passwordRules());
    }

    /**
     * Change your own password.
     *
     * Until now the profile page said "to change your password, please contact
     * the system administrator", and it was telling the truth: there was no
     * screen for it. Every forgotten or shared password was an administrator's
     * errand, and a password somebody else chose for you is one you do not
     * change afterwards.
     *
     * It takes no id. The account is always the one in the session, so there is
     * no parameter to tamper with and no case where this screen touches
     * somebody else's account.
     *
     * The current password has to be given. A session left open on a shared
     * machine is the common case, and without this anybody passing that machine
     * could lock its owner out of their own account.
     *
     * The rules are the ones in PasswordPolicyTrait, the same ones the LPK's own
     * registration screen and the administrator's reset enforce.
     *
     * @return \Cake\Http\Response|null Redirects on success, renders otherwise.
     */
    public function changePassword()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            $this->Flash->error(__('You must be logged in to change your password.'));

            return $this->redirect(['action' => 'login']);
        }

        $user = $this->Users->get($userId);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $current = (string)$this->request->getData('current_password');
            $password = (string)$this->request->getData('password');
            $hasher = new \Cake\Auth\DefaultPasswordHasher();

            if (!$hasher->check($current, $user->password)) {
                $this->Flash->error(__('Your current password is not correct.'));
            } elseif ($password === $current) {
                $this->Flash->error(__('The new password is the same as the current one.'));
            } else {
                $problem = $this->passwordProblem($password,
                    $this->request->getData('confirm_password'));

                if ($problem !== null) {
                    $this->Flash->error($problem);
                } else {
                    // Assigned rather than patched: a post can carry anything,
                    // and this screen changes one thing.
                    $user->password = $password;
                    $user->setDirty('password', true);

                    if ($this->Users->save($user)) {
                        $this->recordDecision('user.changePassword', [
                            'type' => 'User',
                            'id' => $user->id,
                            'label' => $user->username,
                        ]);

                        $this->Flash->success(__('Your password has been changed.'));

                        return $this->redirect(['action' => 'profile']);
                    }

                    $this->Flash->error(__('The password could not be saved. Please, try again.'));
                }
            }
        }

        $this->set(compact('user'));
        $this->set('rules', $this->passwordRules());
    }

    /**
     * Profile method
     * 
     * Display current logged-in user's profile information
     *
     * @return \Cake\Http\Response|null
     */
    public function profile()
    {
        $userId = $this->Auth->user('id');
        
        if (!$userId) {
            $this->Flash->error(__('You must be logged in to view your profile.'));
            return $this->redirect(['action' => 'login']);
        }
        
        $user = $this->Users->get($userId, [
            'contain' => ['Roles']
        ]);

        // The profile had a row for this guarded on
        // $user->has('vocational_training_institution'), which UsersTable does
        // not declare and cannot - institution_id points at one of two tables -
        // so the row has never rendered on anybody's profile.
        $this->set('institution', $this->institutionFor($user));
        $this->set('user', $user);
    }

    /**
     * Settings method
     * 
     * Edit current logged-in user's profile settings
     *
     * @return \Cake\Http\Response|null Redirects on successful edit.
     */
    public function settings()
    {
        $userId = $this->Auth->user('id');
        
        if (!$userId) {
            $this->Flash->error(__('You must be logged in to edit settings.'));
            return $this->redirect(['action' => 'login']);
        }
        
        $user = $this->Users->get($userId, [
            'contain' => ['Roles']
        ]);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Named fields, not everything that arrives.
            //
            // This read patchEntity($user, $this->request->getData()) and then
            // tried to protect itself with unset($user->roles) and
            // unset($user->vocational_training_institution_id). The second one
            // names a column that does not exist - the column is
            // institution_id - so it protected nothing, and institution_id,
            // is_active and password are all accessible on the entity. Since
            // LpkDataFilterTrait scopes what an LPK user may read by their
            // institution_id, anybody logged in could post one to this screen
            // and read another institution's candidates. It is their own
            // account, so nothing stopped them.
            //
            // This screen edits three things. A password is changed on its own
            // screen, where the current one has to be given.
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => ['full_name', 'username', 'email'],
            ]);

            if ($this->Users->save($user)) {
                // Update session data. The column is full_name; this wrote
                // 'fullname', which is not a column and was always null, so the
                // name in the session was emptied on every save.
                $sessionUser = $this->Auth->user();
                $sessionUser['full_name'] = $user->full_name;
                $sessionUser['email'] = $user->email;
                $this->Auth->setUser($sessionUser);
                
                $this->Flash->success(__('Your settings have been updated.'));
                return $this->redirect(['action' => 'profile']);
            }
            $this->Flash->error(__('Your settings could not be updated. Please, try again.'));
        }
        
        $this->set(compact('user'));
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
    }
}
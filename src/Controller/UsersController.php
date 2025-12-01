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
        $this->Auth->allow(['login', 'logout', 'changeLanguage', 'help']);
    }

    /**
     * Help method - User guidance page
     */
    public function help()
    {
        // This is just a view, no data processing needed
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
        $this->paginate = [
            'contain' => ['Roles']
        ];
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
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
            'valueField' => 'company_name'
        ])->order(['company_name' => 'ASC'])->toArray();
        
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
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $this->set(compact('user', 'roles'));
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
            $user = $this->Users->patchEntity($user, $this->request->getData());
            
            // Don't allow changing roles or vocational_training_institution_id
            unset($user->roles);
            unset($user->vocational_training_institution_id);
            
            if ($this->Users->save($user)) {
                // Update session data
                $sessionUser = $this->Auth->user();
                $sessionUser['fullname'] = $user->fullname;
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
        $this->viewBuilder()->setLayout('process_flow');
    }
}
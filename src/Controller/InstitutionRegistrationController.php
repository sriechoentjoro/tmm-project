<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
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
            
            // Validate password
            if (empty($data['password']) || strlen($data['password']) < 8) {
                $this->Flash->error(__('Password must be at least 8 characters long.'));
            } elseif ($data['password'] !== $data['password_confirm']) {
                $this->Flash->error(__('Passwords do not match.'));
            } else {
                // Set password and complete registration
                $institution->password = $data['password'];
                $institution->completeRegistration();
                
                // Save institution
                $table = $this->getInstitutionTable($institution);
                if ($table->save($institution)) {
                    // Create user account in authentication system
                    if ($this->createUserAccount($institution)) {
                        $this->Flash->success(__('Registration completed successfully! You can now login.'));
                        
                        // Auto-login the user
                        $this->autoLogin($institution);
                        
                        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                    } else {
                        $this->Flash->error(__('Registration completed but failed to create user account. Please contact administrator.'));
                    }
                } else {
                    $this->log('Registration failed. Validation errors: ' . json_encode($institution->getErrors()), 'error');
                    $this->Flash->error(__('Failed to complete registration. Please check your input and try again.'));
                }
            }
        }

        $this->set(compact('institution', 'token'));
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
     * Create user account in authentication system
     *
     * @param object $institution Institution entity
     * @return bool Success status
     */
    protected function createUserAccount($institution)
    {
        try {
            $Users = TableRegistry::getTableLocator()->get('Users');
            
            // Check if user already exists
            $existingUser = $Users->find()
                ->where(['username' => $institution->username])
                ->first();
            
            if ($existingUser) {
                return true; // User already exists, consider it success
            }

            // Create new user
            $user = $Users->newEntity([
                'username' => $institution->username,
                'email' => $institution->email,
                'password' => $institution->password, // Already hashed by entity
                'full_name' => $institution->name ?? $institution->company_name,
                'is_active' => true,
                'institution_id' => $institution->id,
                'institution_type' => $institution->institution_type,
            ]);

            if ($Users->save($user)) {
                // Assign role (role_id = 6 for institutions)
                $UserRoles = TableRegistry::getTableLocator()->get('UserRoles');
                $userRole = $UserRoles->newEntity([
                    'user_id' => $user->id,
                    'role_id' => 6, // Institution role
                ]);
                
                return $UserRoles->save($userRole) !== false;
            }

            return false;
        } catch (\Exception $e) {
            $this->log('Failed to create user account: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Auto-login user after registration
     *
     * @param object $institution Institution entity
     * @return void
     */
    protected function autoLogin($institution)
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $user = $Users->find()
            ->where(['username' => $institution->username])
            ->first();
        
        if ($user) {
            $this->Auth->setUser($user->toArray());
        }
    }
}

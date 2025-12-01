<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
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
            $data = $this->request->getData();
            
            // Set initial status
            $data['status'] = 'pending_verification';
            
            $institution = $this->VocationalTrainingInstitutions->newEntity($data);

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
                            'prefix' => false
                        ], true);
                        
                        $emailSent = $this->EmailService->sendEmail(
                            $institution->email,
                            $institution->director_name,
                            'lpk_verification',
                            [
                                'directorName' => $institution->director_name,
                                'institutionName' => $institution->name,
                                'registrationNumber' => $institution->registration_number,
                                'email' => $institution->email,
                                'registeredByAdmin' => $this->Auth->user('fullname'),
                                'registrationDate' => $institution->created->format('d F Y, H:i'),
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
                } catch (\Exception $e) {
                    $this->Flash->error(__('LPK registered but error occurred: {0}', $e->getMessage()));
                    Log::error("Error during LPK registration: " . $e->getMessage(), ['scope' => 'lpk_registration']);
                }
                
                return $this->redirect(['action' => 'index']);
            }
            
            $this->Flash->error(__('Unable to register LPK. Please check the form and try again.'));
            Log::error("Failed to save LPK registration", ['scope' => 'lpk_registration']);
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
        } catch (\Exception $e) {
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
                    // Create new user
                    $username = $this->_generateUsername($institution->name);
                    
                    $user = $this->Users->newEntity([
                        'username' => $username,
                        'email' => $institution->email,
                        'full_name' => $institution->director_name,
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
                    $this->VocationalTrainingInstitutions->save($institution);
                    
                    // Send welcome email
                    $this->loadComponent('EmailService');
                    $this->EmailService->sendEmail(
                        $institution->email,
                        $institution->director_name,
                        'lpk_welcome',
                        [
                            'directorName' => $institution->director_name,
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
        } catch (\Exception $e) {
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
                'prefix' => false
            ], true);
            
            $emailSent = $this->EmailService->sendEmail(
                $institution->email,
                $institution->director_name,
                'lpk_verification',
                [
                    'directorName' => $institution->director_name,
                    'institutionName' => $institution->name,
                    'registrationNumber' => $institution->registration_number,
                    'email' => $institution->email,
                    'registeredByAdmin' => $this->Auth->user('fullname'),
                    'registrationDate' => $institution->created->format('d F Y, H:i'),
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
}

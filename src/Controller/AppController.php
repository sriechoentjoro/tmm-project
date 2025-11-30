<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;

// Manual require for ImageResize class
require_once ROOT . DS . 'vendor' . DS . 'ImageResize' . DS . 'ImageResize.php';
use ImageResize\ImageResize;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    use ExportTrait;

    public $layout = 'elegant';
    
    /**
     * Current logged in user data
     * @var array
     */
    public $currentUser = array();

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');
        
        // Load Built-in Auth Component
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'userModel' => 'Users',
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password'
                    ],
                    'passwordHasher' => 'Default'
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'controller' => 'Dashboard',
                'action' => 'index'
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authorize' => ['Controller'], // Use isAuthorized() method
            'authError' => 'You are not authorized to access this location.',
            'unauthorizedRedirect' => true, // Redirect to referrer with flash message
            'storage' => 'Session'
        ]);

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
    }

    /**
     * Before filter callback.
     *
     * @param \Cake\Event\Event $event The beforeFilter event.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        
        // Allow public access to login
        $this->Auth->allow(['login']);

        // Allow public access to Institution Registration
        $controller = $this->request->getParam('controller');
        
        if ($controller === 'InstitutionRegistration' || $controller === 'institution-registration') {
            $this->Auth->allow();
        }
    }

    /**
     * Authorization check
     * 
     * @param array $user The user to check the authorization of.
     * @return bool True if authorized, false otherwise
     */
    public function isAuthorized($user)
    {
        // Store user in property for easier access
        $this->currentUser = $user;
        
        // Admin can access every action
        if ($this->hasRole('administrator')) {
            return true;
        }
        
        // Get current controller and action
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        
        // Allow profile, settings, changeLanguage, help, logout for ALL authenticated users
        if ($controller === 'Users' && in_array($action, ['profile', 'settings', 'changeLanguage', 'help', 'logout'])) {
            return true; // All authenticated users can access their own profile and logout
        }
        
        // Check if user has permission to access this controller/action
        if (!$this->hasPermission($controller, $action)) {
            // Use enhanced unauthorized handler
            $this->handleUnauthorizedAccess($action, 
                __('Your role does not have access to {0} in {1}.', $action, $controller)
            );
            return false;
        }
        
        // Management/Director role - read-only access to all non-master tables
        if ($this->hasRole('management') || $this->hasRole('director')) {
            return $this->authorizeManagement();
        }
        
        // TMM Recruitment role
        if ($this->hasRole('tmm-recruitment')) {
            return $this->authorizeTmmRecruitment();
        }
        
        // TMM Training role
        if ($this->hasRole('tmm-training')) {
            return $this->authorizeTmmTraining();
        }
        
        // TMM Documentation role
        if ($this->hasRole('tmm-documentation')) {
            return $this->authorizeTmmDocumentation();
        }
        
        // LPK Penyangga role - institution-specific access
        if ($this->hasRole('lpk-penyangga')) {
            return $this->authorizeLpkPenyangga();
        }

        // LPK SO role - institution-specific access
        if ($this->hasRole('lpk-so')) {
            return $this->authorizeLpkSo();
        }
        
        // Default deny - should not reach here due to hasPermission check above
        $this->handleUnauthorizedAccess($action, 
            __('No authorization rules defined for your role.')
        );
        return false;
    }
    
    /**
     * Check if current user has a specific role
     * 
     * @param string $roleName Role name to check
     * @return bool
     */
    protected function hasRole($roleName)
    {
        return isset($this->currentUser['role_names']) && 
               in_array($roleName, $this->currentUser['role_names']);
    }
    
    /**
     * Authorize Management/Director role
     * Read-only access to all non-master tables
     */
    protected function authorizeManagement()
    {
        $controller = $this->request->getParam('controller');
        $action = $this->request->getParam('action');
        
        // Allow dashboard and view actions
        if ($controller === 'Dashboard' || in_array($action, ['index', 'view'])) {
            return true;
        }
        
        // Deny create, update, delete actions
        return false;
    }
    
    /**
     * Authorize TMM Recruitment role
     * Full access to candidate and apprentice recruitment data
     */
    protected function authorizeTmmRecruitment()
    {
        $controller = $this->request->getParam('controller');
        
        // Allowed controllers
        $allowedControllers = [
            'Dashboard',
            'Candidates',
            'ApprenticeOrders',
            'MasterPropinsis',  // Read-only
            'MasterKabupatens', // Read-only
            'MasterKecamatans', // Read-only
            'MasterKelurahans', // Read-only
            'VocationalTrainingInstitutions', // Read-only
        ];
        
        if (in_array($controller, $allowedControllers)) {
            // Read-only for master tables
            if (strpos($controller, 'Master') === 0) {
                return $this->isReadOnlyAction();
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Authorize TMM Training role
     * Full access to trainee and training data
     */
    protected function authorizeTmmTraining()
    {
        $controller = $this->request->getParam('controller');
        
        // Allowed controllers
        $allowedControllers = [
            'Dashboard',
            'Trainees',
            'TraineeTrainings',
            'TrainingScores',
            'AcceptanceOrganizations', // Read-only
            'MasterPropinsis',  // Read-only
            'MasterKabupatens', // Read-only
        ];
        
        if (in_array($controller, $allowedControllers)) {
            // Read-only for master tables and acceptance organizations
            if (strpos($controller, 'Master') === 0 || $controller === 'AcceptanceOrganizations') {
                return $this->isReadOnlyAction();
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Authorize TMM Documentation role
     * Full access to apprentice documentation
     */
    protected function authorizeTmmDocumentation()
    {
        $controller = $this->request->getParam('controller');
        
        // Allowed controllers
        $allowedControllers = [
            'Dashboard',
            'ApprenticeDocuments',
            'ApprenticeDocumentTicketings',
            'Trainees', // Read-only
            'Candidates', // Read-only
        ];
        
        if (in_array($controller, $allowedControllers)) {
            // Read-only for trainees and candidates
            if (in_array($controller, ['Trainees', 'Candidates'])) {
                return $this->isReadOnlyAction();
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Authorize LPK Penyangga role
     * Institution-specific access to candidate data
     */
    protected function authorizeLpkPenyangga()
    {
        $controller = $this->request->getParam('controller');
        
        // Allowed controllers
        $allowedControllers = [
            'Dashboard',
            'Candidates',
            'CandidateDocuments',
            'MasterPropinsis',  // Read-only
            'MasterKabupatens', // Read-only
            'MasterKecamatans', // Read-only
            'MasterKelurahans', // Read-only
        ];
        
        if (in_array($controller, $allowedControllers)) {
            // Read-only for master tables
            if (strpos($controller, 'Master') === 0) {
                return $this->isReadOnlyAction();
            }
            return true;
        }
        
        return false;
    }

    /**
     * Authorize LPK SO role
     * Institution-specific access to candidate data
     */
    protected function authorizeLpkSo()
    {
        $controller = $this->request->getParam('controller');
        
        // Allowed controllers
        $allowedControllers = [
            'Dashboard',
            'Candidates',
            'CandidateDocuments',
            'MasterPropinsis',  // Read-only
            'MasterKabupatens', // Read-only
            'MasterKecamatans', // Read-only
            'MasterKelurahans', // Read-only
        ];
        
        if (in_array($controller, $allowedControllers)) {
            // Read-only for master tables
            if (strpos($controller, 'Master') === 0) {
                return $this->isReadOnlyAction();
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if current action is read-only
     */
    protected function isReadOnlyAction()
    {
        $action = $this->request->getParam('action');
        return in_array($action, ['index', 'view']);
    }
    
    /**
     * Get current user's institution ID (for LPK users)
     */
    protected function getUserInstitutionId()
    {
        return isset($this->currentUser['institution_id']) ? $this->currentUser['institution_id'] : null;
    }
    
    /**
     * Get current user's institution type (for LPK users)
     */
    protected function getUserInstitutionType()
    {
        return isset($this->currentUser['institution_type']) ? $this->currentUser['institution_type'] : null;
    }
    
    /**
     * Check if record can be accessed by current user based on institution scope
     * 
     * @param object $entity Entity to check
     * @param string $institutionField Field name containing institution_id
     * @return bool True if accessible, false otherwise
     */
    protected function canAccessRecord($entity, $institutionField = 'vocational_training_institution_id')
    {
        $userInstitutionId = $this->getUserInstitutionId();
        
        if (!$userInstitutionId) {
            return false; // No institution assigned
        }
        
        // Check if entity has the institution field
        if (!isset($entity->$institutionField)) {
            return false;
        }
        
        return $entity->$institutionField == $userInstitutionId;
    }
    
    /**
     * Handle unauthorized access with informative message
     * 
     * @param string $action The action that was attempted (view, edit, delete, etc.)
     * @param string $reason Optional reason for denial
     * @return \Cake\Http\Response Redirect response
     */
    protected function handleUnauthorizedAccess($action = 'access', $reason = null)
    {
        $username = isset($this->currentUser['username']) ? $this->currentUser['username'] : 'unknown';
        $userRole = isset($this->currentUser['role_names']) ? implode(', ', $this->currentUser['role_names']) : 'unknown';
        
        if (!$reason) {
            $reason = __('You do not have permission to {0} this resource.', $action);
        }
        
        // Log unauthorized attempt
        $this->log(sprintf(
            'Unauthorized access attempt: User=%s, Role=%s, Action=%s, Controller=%s, Reason=%s',
            $username,
            $userRole,
            $action,
            $this->request->getParam('controller'),
            $reason
        ), 'warning');
        
        // Set flash message with detailed information
        $this->Flash->error(
            __('Access Denied: {0}', $reason) . '<br><br>' .
            __('Your account: <strong>{0}</strong>', $username) . '<br>' .
            __('Your role: <strong>{0}</strong>', $userRole) . '<br><br>' .
            __('Please contact your administrator if you believe this is an error, or ') .
            '<a href="/users/login">' . __('login with different account') . '</a>',
            ['escape' => false]
        );
        
        return $this->redirect($this->referer(['controller' => 'Dashboard', 'action' => 'index'], true));
    }
    
    /**
     * Check if user has permission to access controller/action
     * Based on role and database connection scope
     * 
     * @param string|null $controller Controller name (null = current)
     * @param string|null $action Action name (null = current)
     * @return bool True if has permission
     */
    protected function hasPermission($controller = null, $action = null)
    {
        $controller = $controller ? $controller : $this->request->getParam('controller');
        $action = $action ? $action : $this->request->getParam('action');
        
        // Administrator has all permissions
        if ($this->hasRole('administrator')) {
            return true;
        }
        
        // Check database scope permissions
        $connectionName = $this->getControllerConnection($controller);
        if ($connectionName && !$this->hasConnectionAccess($connectionName)) {
            return false;
        }
        
        // Check controller-specific permissions
        $rolePermissions = $this->getRolePermissions();
        if (!isset($rolePermissions[$controller])) {
            return false;
        }
        
        $allowedActions = $rolePermissions[$controller];
        
        // '*' means all actions allowed
        if (in_array('*', $allowedActions)) {
            return true;
        }
        
        return in_array($action, $allowedActions);
    }
    
    /**
     * Get database connection name for a controller
     * 
     * @param string $controller Controller name
     * @return string|null Connection name
     */
    protected function getControllerConnection($controller)
    {
        // Map controllers to their database connections
        $connectionMap = [
            // CMS Masters
            'MasterPropinsis' => 'cms_masters',
            'MasterKabupatens' => 'cms_masters',
            'MasterKecamatans' => 'cms_masters',
            'MasterKelurahans' => 'cms_masters',
            'MasterJobCategories' => 'cms_masters',
            
            // CMS LPK Candidates
            'Candidates' => 'cms_lpk_candidates',
            'CandidateDocuments' => 'cms_lpk_candidates',
            'CandidateEducations' => 'cms_lpk_candidates',
            'CandidateExperiences' => 'cms_lpk_candidates',
            
            // CMS TMM Trainees
            'Trainees' => 'cms_tmm_trainees',
            'TraineeAccountings' => 'cms_tmm_trainee_accountings',
            'TraineeTrainings' => 'cms_tmm_trainee_trainings',
            
            // CMS TMM Apprentices
            'Apprentices' => 'cms_tmm_apprentices',
            'ApprenticeDocuments' => 'cms_tmm_apprentice_documents',
            
            // CMS TMM Stakeholders
            'CooperativeAssociations' => 'cms_tmm_stakeholders',
            'AcceptanceOrganizations' => 'cms_tmm_stakeholders',
            'VocationalTrainingInstitutions' => 'cms_tmm_stakeholders',
        ];
        
        return isset($connectionMap[$controller]) ? $connectionMap[$controller] : null;
    }
    
    /**
     * Check if user has access to a database connection
     * 
     * @param string $connectionName Connection name
     * @return bool True if has access
     */
    protected function hasConnectionAccess($connectionName)
    {
        // Administrator has access to all databases
        if ($this->hasRole('administrator')) {
            return true;
        }
        
        // Define role-based database access
        $roleConnections = [
            'management' => '*', // All databases (read-only)
            'tmm-recruitment' => ['cms_lpk_candidates', 'cms_tmm_apprentices', 'cms_masters'],
            'tmm-training' => ['cms_tmm_trainees', 'cms_tmm_trainee_trainings', 'cms_masters'],
            'tmm-documentation' => ['cms_tmm_apprentice_documents', 'cms_lpk_candidate_documents', 'cms_masters'],
            'lpk-penyangga' => ['cms_lpk_candidates', 'cms_lpk_candidate_documents', 'cms_masters'],
            'lpk-so' => ['cms_lpk_candidates', 'cms_lpk_candidate_documents', 'cms_masters'],
        ];
        
        foreach ($roleConnections as $role => $connections) {
            if ($this->hasRole($role)) {
                if ($connections === '*') {
                    return true;
                }
                return in_array($connectionName, $connections);
            }
        }
        
        return false;
    }
    
    /**
     * Get role-based controller/action permissions
     * Filtered by database connection access scope
     *
     * @return array [controller => [actions]]
     */
    protected function getRolePermissions()
    {
        $permissions = [];

        // Management: Read-only access
        if ($this->hasRole('management')) {
            $permissions = $this->getManagementPermissions();
        }
        // TMM Recruitment
        elseif ($this->hasRole('tmm-recruitment')) {
            $permissions = $this->getRecruitmentPermissions();
        }
        // TMM Training
        elseif ($this->hasRole('tmm-training')) {
            $permissions = $this->getTrainingPermissions();
        }
        // TMM Documentation
        elseif ($this->hasRole('tmm-documentation')) {
            $permissions = $this->getDocumentationPermissions();
        }
        // LPK Penyangga
        elseif ($this->hasRole('lpk-penyangga')) {
            $permissions = $this->getLpkPermissions();
        }

        // Filter permissions based on database connection access
        return $this->filterPermissionsByDatabaseAccess($permissions);
    }

    /**
     * Filter permissions based on user's database connection access
     *
     * @param array $permissions Base permissions array [controller => [actions]]
     * @return array Filtered permissions array
     */
    protected function filterPermissionsByDatabaseAccess($permissions)
    {
        // Administrator has access to all permissions
        if ($this->hasRole('administrator')) {
            return $permissions;
        }

        $filteredPermissions = [];

        foreach ($permissions as $controller => $actions) {
            // Check if controller has a database connection requirement
            $connectionName = $this->getControllerConnection($controller);

            // If no connection mapping exists, allow access (for controllers like Dashboard, Users)
            if (!$connectionName || $this->hasConnectionAccess($connectionName)) {
                $filteredPermissions[$controller] = $actions;
            }
        }

        return $filteredPermissions;
    }

    /**
     * Get management role permissions (read-only)
     *
     * @return array [controller => [actions]]
     */
    protected function getManagementPermissions()
    {
        // Management can view all but cannot edit
        return [
            'Users' => ['profile', 'settings', 'changeLanguage', 'help'], // Own profile access
            'Dashboard' => ['index'],
            'Candidates' => ['index', 'view', 'export'],
            'CandidateDocuments' => ['index', 'view'],
            'Trainees' => ['index', 'view', 'export'],
            'Apprentices' => ['index', 'view', 'export'],
            'CooperativeAssociations' => ['index', 'view'],
            'AcceptanceOrganizations' => ['index', 'view'],
            'VocationalTrainingInstitutions' => ['index', 'view'],
        ];
    }
    
    /**
     * Get recruitment role permissions
     * 
     * @return array [controller => [actions]]
     */
    protected function getRecruitmentPermissions()
    {
        return [
            'Users' => ['profile', 'settings', 'changeLanguage', 'help'], // Own profile access
            'Dashboard' => ['index'],
            'Candidates' => ['*'], // Full access
            'CandidateDocuments' => ['*'],
            'CandidateEducations' => ['*'],
            'CandidateExperiences' => ['*'],
            'ApprenticeOrders' => ['*'],
            'Apprentices' => ['*'],
            'ApprenticeDocuments' => ['*'],
            'CooperativeAssociations' => ['index', 'view'],
            'AcceptanceOrganizations' => ['index', 'view'],
            'VocationalTrainingInstitutions' => ['index', 'view'],
        ];
    }
    
    /**
     * Get training role permissions
     * 
     * @return array [controller => [actions]]
     */
    protected function getTrainingPermissions()
    {
        return [
            'Users' => ['profile', 'settings', 'changeLanguage', 'help'], // Own profile access
            'Dashboard' => ['index'],
            'Trainees' => ['*'],
            'TraineeAccountings' => ['*'],
            'TraineeTrainings' => ['*'],
            'TraineeDocuments' => ['*'],
            'Candidates' => ['index', 'view'],
            'VocationalTrainingInstitutions' => ['index', 'view'],
        ];
    }
    
    /**
     * Get documentation role permissions
     * 
     * @return array [controller => [actions]]
     */
    protected function getDocumentationPermissions()
    {
        return [
            'Users' => ['profile', 'settings', 'changeLanguage', 'help'], // Own profile access
            'Dashboard' => ['index'],
            'CandidateDocuments' => ['*'],
            'ApprenticeDocuments' => ['*'],
            'TraineeDocuments' => ['*'],
            'Candidates' => ['index', 'view'],
            'Apprentices' => ['index', 'view'],
            'Trainees' => ['index', 'view'],
        ];
    }
    
    /**
     * Get LPK role permissions (institution-scoped)
     * 
     * @return array [controller => [actions]]
     */
    protected function getLpkPermissions()
    {
        return [
            // Note: profile, settings, changeLanguage, help are handled in isAuthorized() for all authenticated users
            // No Users controller access - LPK cannot manage users
            'Dashboard' => ['index'],
            'Candidates' => ['*'], // With institution scope
            'CandidateDocuments' => ['*'],
            'CandidateEducations' => ['*'],
            'CandidateExperiences' => ['*'],
        ];
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // Skip menu loading for print layout
        if ($this->viewBuilder()->getLayout() === 'print') {
            return;
        }

        // Load navigation menu dari database
        $tableLocator = \Cake\ORM\TableRegistry::getTableLocator();
        $menusTable = $tableLocator->get('Menus');

        // Load all active menus (filtering by URL pattern will be done in template)
        $menus = $menusTable->find('threaded')
            ->where(['Menus.parent_id IS' => null, 'Menus.is_active' => 1])
            ->order(['Menus.sort_order' => 'ASC'])
            ->contain(['ChildMenus' => function ($q) {
                return $q
                    ->where(['ChildMenus.is_active' => 1])
                    ->order(['ChildMenus.sort_order' => 'ASC']);
            }])
            ->all();

        $this->set('navigationMenus', $menus);
        
        // Get current user from Auth component for menu filtering
        $user = $this->Auth->user();
        
        // Pass allowed controllers and detailed permissions to template for menu filtering
        if (!empty($user)) {
            // Update currentUser for role checking
            $this->currentUser = $user;
            
            $rolePermissions = $this->getRolePermissions();
            $isAdmin = $this->hasRole('administrator');
            $allowedControllers = $isAdmin ? array() : array_keys($rolePermissions);
            
            $this->set('allowedControllers', $allowedControllers);
            $this->set('rolePermissions', $rolePermissions); // Pass full permissions for action-level checking
            $this->set('isAdministrator', $isAdmin);
        } else {
            $this->set('allowedControllers', array());
            $this->set('rolePermissions', array());
            $this->set('isAdministrator', false);
        }
    }
    
    /**
     * Get list of controllers user has permission to access
     * 
     * @return array List of controller names
     */
    protected function getAllowedControllers()
    {
        $permissions = $this->getRolePermissions();
        if (empty($permissions)) {
            return [];
        }
        
        // Extract controller names
        return array_keys($permissions);
    }

    /**
     * Handle file/image uploads for multiple fields
     * 
     * Auto-detects field type based on name:
     * - Fields with 'image', 'photo', 'gambar', 'foto' ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ webroot/img/uploads/
     * - Other file fields ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¾ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ webroot/files/uploads/
     * 
     * @param array $data Request data from $this->request->getData()
     * @param array $fileFields Array of field names that contain file/image
     * @param object|null $existingEntity For edit: existing entity to delete old files
     * @return array Modified data with file paths
     * 
     * Usage:
     *   // In add():
     *   $data = $this->handleFileUploads($data, ['image_url', 'file_path']);
     * 
     *   // In edit():
     *   $data = $this->handleFileUploads($data, ['image_url', 'file_path'], $entity);
     */
    protected function handleFileUploads(array $data, array $fileFields, $existingEntity = null)
    {
        foreach ($fileFields as $fieldName) {
            // Handle both array format (PHP $_FILES) and object format (CakePHP UploadedFile)
            if (!empty($data[$fieldName])) {
                $file = $data[$fieldName];
                $uploadError = null;
                $fileName = null;
                
                // Check if it's array format (traditional PHP $_FILES)
                if (is_array($file) && isset($file['tmp_name'])) {
                    $uploadError = $file['error'];
                    $fileName = $file['name'];
                    $tmpName = $file['tmp_name'];
                    
                    $this->log("Array format file detected: {$fileName}", 'debug');
                } 
                // Check if it's object format (CakePHP UploadedFile)
                elseif (is_object($file) && method_exists($file, 'getClientFilename')) {
                    $uploadError = $file->getError();
                    $fileName = $file->getClientFilename();
                    $tmpName = null; // Will use moveTo() method
                    
                    $this->log("Object format file detected: {$fileName}", 'debug');
                } else {
                    // Not a valid upload
                    if ($existingEntity) {
                        unset($data[$fieldName]); // Keep existing value
                    }
                    continue;
                }
                
                // Check if file was uploaded successfully
                if ($uploadError === UPLOAD_ERR_OK) {
                    // Delete old file if editing
                    if ($existingEntity && !empty($existingEntity->get($fieldName))) {
                        $oldFile = $existingEntity->get($fieldName);
                        if (file_exists(WWW_ROOT . $oldFile)) {
                            @unlink(WWW_ROOT . $oldFile);
                            $this->log("Deleted old file: {$oldFile}", 'debug');
                        }
                    }
                    
                    // Get file information
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $baseName = pathinfo($fileName, PATHINFO_FILENAME);
                    $timestamp = date('YmdHis');
                    
                    // Generate unique filename
                    $uniqueFileName = $baseName . '_' . $timestamp . '.' . $extension;
                    
                    // Determine upload directory based on field type
                    if (preg_match('/(image|photo|gambar|foto)/i', $fieldName)) {
                        $uploadPath = WWW_ROOT . 'img' . DS . 'uploads' . DS;
                        $webPath = 'img/uploads/' . $uniqueFileName;
                    } else {
                        $uploadPath = WWW_ROOT . 'files' . DS . 'uploads' . DS;
                        $webPath = 'files/uploads/' . $uniqueFileName;
                    }
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    // Move uploaded file
                    try {
                        if (is_array($file)) {
                            // Array format - use move_uploaded_file()
                            move_uploaded_file($tmpName, $uploadPath . $uniqueFileName);
                        } else {
                            // Object format - use moveTo()
                            $file->moveTo($uploadPath . $uniqueFileName);
                        }
                        
                        $data[$fieldName] = $webPath; // Store relative path in database
                        
                        $this->log("File uploaded successfully: {$webPath}", 'info');
                        $this->Flash->success(__('File {0} uploaded successfully', $fileName));
                    } catch (\Exception $e) {
                        $this->Flash->error(__('Failed to upload {0}: {1}', $fieldName, $e->getMessage()));
                        $data[$fieldName] = null;
                    }
                } else {
                    $this->log("Upload error code: {$uploadError}", 'error');
                    $data[$fieldName] = null;
                }
            } else {
                // If editing and no new file uploaded, keep old value
                if ($existingEntity) {
                    unset($data[$fieldName]); // Don't overwrite existing value
                } else {
                    $data[$fieldName] = null;
                }
            }
        }
        
        return $data;
    }

    /**
     * Convert date fields from DD-MM-YYYY or DD/MM/YYYY to YYYY-MM-DD
     * Call this method in controllers after getData() to auto-fix date formats
     * 
     * @param array $data Request data
     * @return array Modified data with corrected date formats
     */
    public function convertDateFormats($data)
    {
        foreach ($data as $fieldName => $value) {
            // Check if field name suggests it's a date field
            if (is_string($value) && !empty($value) && (preg_match('/_date$/i', $fieldName) || preg_match('/^date_/i', $fieldName))) {
                // Try DD-MM-YYYY format
                if (preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $value, $matches)) {
                    $data[$fieldName] = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // Convert to YYYY-MM-DD
                }
                // Try DD/MM/YYYY format
                elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $value, $matches)) {
                    $data[$fieldName] = $matches[3] . '-' . $matches[2] . '-' . $matches[1]; // Convert to YYYY-MM-DD
                }
            }
        }
        return $data;
    }

    /**
     * Upload file to webroot/files/uploads/{folder}
     * 
     * @param string $model Model name
     * @param string $field Field name
     * @param string $folder Subfolder name
     * @param string $reqFilename Optional custom filename
     * @return bool
     */
    public function uploadFile($model, $field, $folder, $reqFilename = '')
    {
        $data = $this->request->getData();
        
        if (!isset($data[$field])) {
            return false;
        }
        
        $file = $data[$field];
        
        // Check if it's an array with file upload data (CakePHP 3.x standard)
        if (is_array($file) && isset($file['tmp_name']) && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
            $fileName = $file['name'];
            $tmpName = $file['tmp_name'];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Generate filename
            if (empty($reqFilename)) {
                $reqFilename = strtoupper(str_replace(' ', '_', $baseName)) . '_' . $this->getRandomCode();
            }
            
            // Create directory
            $dir = WWW_ROOT . 'files' . DS . 'uploads' . DS . $folder;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            
            $finalFilename = $reqFilename . '.' . $extension;
            $relativePath = 'files' . DS . 'uploads' . DS . $folder . DS . $finalFilename;
            $absolutePath = WWW_ROOT . $relativePath;
            
            // Delete old file if editing
            if ($this->request->getData('id')) {
                $modelTable = $this->loadModel($model);
                $entity = $modelTable->get($this->request->getData('id'));
                if ($entity && !empty($entity->$field)) {
                    $oldPath = WWW_ROOT . $entity->$field;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }
            
            // Move file
            if (move_uploaded_file($tmpName, $absolutePath)) {
                // Update request data with relative path
                $this->request = $this->request->withData($field, str_replace(DS, '/', $relativePath));
                $this->log("File uploaded successfully: {$field} -> {$relativePath}", 'debug');
                return true;
            } else {
                $this->Flash->error(__('File could not be moved to destination.'));
                $this->log("File upload failed: {$field} - Could not move from {$tmpName} to {$absolutePath}", 'error');
                return false;
            }
        }
        // Check if it's UploadedFile object (alternative format)
        elseif (is_object($file) && method_exists($file, 'getClientFilename')) {
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $this->Flash->error(__('Upload failed with error code: {0}', $file->getError()));
                return false;
            }
            
            $fileName = $file->getClientFilename();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Generate filename
            if (empty($reqFilename)) {
                $reqFilename = strtoupper(str_replace(' ', '_', $baseName)) . '_' . $this->getRandomCode();
            }
            
            // Create directory
            $dir = WWW_ROOT . 'files' . DS . 'uploads' . DS . $folder;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            
            $finalFilename = $reqFilename . '.' . $extension;
            $relativePath = 'files' . DS . 'uploads' . DS . $folder . DS . $finalFilename;
            $absolutePath = WWW_ROOT . $relativePath;
            
            // Delete old file if editing
            if ($this->request->getData('id')) {
                $modelTable = $this->loadModel($model);
                $entity = $modelTable->get($this->request->getData('id'));
                if ($entity && !empty($entity->$field)) {
                    $oldPath = WWW_ROOT . $entity->$field;
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }
            }
            
            // Move file
            try {
                $file->moveTo($absolutePath);
                // Update request data with relative path
                $this->request = $this->request->withData($field, str_replace(DS, '/', $relativePath));
                return true;
            } catch (\Exception $e) {
                $this->Flash->error(__('File could not be uploaded: {0}', $e->getMessage()));
                return false;
            }
        }
        
        return false;
    }

    /**
     * Upload image to webroot/img/uploads/{folder}
     * 
     * @param string $model Model name
     * @param string $field Field name
     * @param string $folder Subfolder name (controller name)
     * @param string $reqFilename Optional custom filename
     * @return string|bool Web path on success, false on failure
     */
    public function uploadImage($model, $field, $folder, $reqFilename = '')
    {
        error_log("=== uploadImage called ===");
        error_log("Model: {$model}, Field: {$field}, Folder: {$folder}");
        
        $data = $this->request->getData();
        
        if (!isset($data[$field])) {
            error_log("Field {$field} not found in request data");
            return false;
        }
        
        $file = $data[$field];
        error_log("File type: " . gettype($file));
        
        // Handle traditional PHP array upload (CakePHP 3 default)
        if (is_array($file) && isset($file['tmp_name'])) {
            error_log("File is array (traditional upload)");
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                error_log("Upload error code: " . $file['error']);
                $this->Flash->error(__('Upload failed with error code: {0}', $file['error']));
                return false;
            }
            
            $fileName = $file['name'];
            $tmpPath = $file['tmp_name'];
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Generate filename
            if (empty($reqFilename)) {
                $baseName = strtoupper(str_replace(' ', '_', $baseName)) . '_' . $this->getRandomCode();
            } else {
                $baseName = strtoupper(str_replace(' ', '_', $reqFilename)) . '_' . $this->getRandomCode();
            }
            
            // Create directory
            $dir = WWW_ROOT . 'img' . DS . 'uploads' . DS . $folder;
            error_log("Upload directory: " . $dir);
            
            if (!is_dir($dir)) {
                error_log("Creating directory: " . $dir);
                if (!mkdir($dir, 0777, true)) {
                    error_log("MKDIR FAILED!");
                    $this->Flash->error(__('Failed to create upload directory'));
                    return false;
                }
            }
            
            $finalFilename = $baseName . '.' . $extension;
            $absolutePath = $dir . DS . $finalFilename;
            
            error_log("Absolute path: " . $absolutePath);
            error_log("Temp path: " . $tmpPath);
            
            // Delete old image if editing
            if ($this->request->getData('id')) {
                try {
                    $modelTable = $this->loadModel($model);
                    $entity = $modelTable->get($this->request->getData('id'));
                    if ($entity && !empty($entity->$field)) {
                        $oldPath = WWW_ROOT . str_replace('/', DS, $entity->$field);
                        if (file_exists($oldPath)) {
                            error_log("Deleting old file: " . $oldPath);
                            @unlink($oldPath);
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Error deleting old file: " . $e->getMessage());
                }
            }
            
            // Move uploaded file
            if (move_uploaded_file($tmpPath, $absolutePath)) {
                error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ File moved successfully");
                
                // Create thumbnail and add watermark
                try {
                    error_log("Creating thumbnail...");
                    $imageResize = new ImageResize($absolutePath);
                    $imageResize->resizeToBestFit(800, 800);
                    $imageResize->save($absolutePath);
                    error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ Thumbnail created");
                    
                    // Add watermark logo
                    $imageLogo = WWW_ROOT . 'img' . DS . 'logo.png';
                    if (file_exists($imageLogo)) {
                        error_log("Adding watermark from: " . $imageLogo);
                        $this->addWatermark($absolutePath, $imageLogo);
                        error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ Watermark added");
                    } else {
                        error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¸ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â Logo file not found: " . $imageLogo);
                    }
                } catch (\Exception $e) {
                    error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¡ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¯ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¸ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â Image processing failed: " . $e->getMessage());
                    // Continue anyway - original image uploaded
                }
                
                // Update request data with relative path (for display) - use forward slash for web
                $webPath = 'img/uploads/' . $folder . '/' . $finalFilename;
                $this->request = $this->request->withData($field, $webPath);
                
                error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ Image uploaded successfully: " . $webPath);
                return $webPath;
            } else {
                error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ move_uploaded_file FAILED!");
                $this->Flash->error(__('Failed to move uploaded file'));
                return false;
            }
        }
        
        // Handle UploadedFile object (if using PSR-7 middleware)
        if (is_object($file) && method_exists($file, 'getClientFilename')) {
            error_log("File is UploadedFile object");
            
            if ($file->getError() !== UPLOAD_ERR_OK) {
                error_log("Upload error code: " . $file->getError());
                $this->Flash->error(__('Upload failed with error code: {0}', $file->getError()));
                return false;
            }
            
            $fileName = $file->getClientFilename();
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            
            // Generate filename
            if (empty($reqFilename)) {
                $baseName = strtoupper(str_replace(' ', '_', $baseName)) . '_' . $this->getRandomCode();
            } else {
                $baseName = strtoupper(str_replace(' ', '_', $reqFilename)) . '_' . $this->getRandomCode();
            }
            
            // Create directory
            $dir = WWW_ROOT . 'img' . DS . 'uploads' . DS . $folder;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            
            $finalFilename = $baseName . '.' . $extension;
            $absolutePath = $dir . DS . $finalFilename;
            
            // Delete old image if editing
            if ($this->request->getData('id')) {
                try {
                    $modelTable = $this->loadModel($model);
                    $entity = $modelTable->get($this->request->getData('id'));
                    if ($entity && !empty($entity->$field)) {
                        $oldPath = WWW_ROOT . str_replace('/', DS, $entity->$field);
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore if entity not found
                }
            }
            
            // Move file
            try {
                $file->moveTo($absolutePath);
                // Update request data with relative path (for display) - use forward slash for web
                $webPath = 'img/uploads/' . $folder . '/' . $finalFilename;
                $this->request = $this->request->withData($field, $webPath);
                
                error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã¢â‚¬Â¦ÃƒÂ¢Ã¢â€šÂ¬Ã…â€œÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¦ Image uploaded successfully: " . $webPath);
                return $webPath;
            } catch (\Exception $e) {
                $this->Flash->error(__('Image could not be uploaded: {0}', $e->getMessage()));
                error_log("Upload error: " . $e->getMessage());
                return false;
            }
        }
        
        error_log("ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã¢â‚¬Â ÃƒÂ¢Ã¢â€šÂ¬Ã¢â€žÂ¢ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€¦Ã‚Â¡ÃƒÆ’Ã†â€™ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡ÃƒÆ’Ã¢â‚¬Å¡Ãƒâ€šÃ‚ÂÃƒÆ’Ã†â€™Ãƒâ€ Ã¢â‚¬â„¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬Ãƒâ€šÃ‚Â¦ÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¢ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¡Ãƒâ€šÃ‚Â¬ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â€šÂ¬Ã…Â¾Ãƒâ€šÃ‚Â¢ File format not recognized");
        return false;
    }

    /**
     * Save cropped image from base64 data
     * 
     * @param string $base64Data Base64 encoded image data
     * @param string $folder Subfolder name (controller name)
     * @return string|bool Web path on success, false on failure
     */
    public function saveCroppedImage($base64Data, $folder)
    {
        try {
            // Extract base64 data (remove data:image/jpeg;base64, prefix)
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $matches)) {
                $extension = $matches[1];
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            } else {
                $this->Flash->error(__('Invalid image data format'));
                return false;
            }
            
            // Decode base64
            $imageData = base64_decode($base64Data);
            if ($imageData === false) {
                $this->Flash->error(__('Failed to decode image data'));
                return false;
            }
            
            // Generate filename
            $baseName = 'photo_' . time() . '_' . $this->getRandomCode();
            $finalFilename = $baseName . '.' . $extension;
            
            // Create directory
            $dir = WWW_ROOT . 'img' . DS . 'uploads' . DS . $folder;
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            
            $absolutePath = $dir . DS . $finalFilename;
            
            // Save file
            if (file_put_contents($absolutePath, $imageData) === false) {
                $this->Flash->error(__('Failed to save cropped image'));
                return false;
            }
            
            // Return web path
            $webPath = 'img/uploads/' . $folder . '/' . $finalFilename;
            error_log("Cropped image saved: " . $webPath);
            return $webPath;
            
        } catch (\Exception $e) {
            $this->Flash->error(__('Error saving cropped image: {0}', $e->getMessage()));
            error_log("Crop save error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add watermark logo to image (bottom-right corner with 10% opacity)
     * 
     * @param string $imagePath Path to image file
     * @param string $logoPath Path to logo file
     * @return bool Success status
     */
    protected function addWatermark($imagePath, $logoPath)
    {
        try {
            // Get image info
            $imageInfo = getimagesize($imagePath);
            $logoInfo = getimagesize($logoPath);
            
            if (!$imageInfo || !$logoInfo) {
                error_log("Failed to get image info");
                return false;
            }
            
            // Create image resources based on type
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    $image = imagecreatefromjpeg($imagePath);
                    break;
                case IMAGETYPE_PNG:
                    $image = imagecreatefrompng($imagePath);
                    break;
                case IMAGETYPE_GIF:
                    $image = imagecreatefromgif($imagePath);
                    break;
                default:
                    error_log("Unsupported image type for watermark");
                    return false;
            }
            
            // Create logo resource (assuming PNG logo with transparency)
            $logo = imagecreatefrompng($logoPath);
            
            // Get dimensions
            $imageWidth = imagesx($image);
            $imageHeight = imagesy($image);
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);
            
            // Calculate logo size (max 15% of image width)
            $maxLogoWidth = $imageWidth * 0.15;
            if ($logoWidth > $maxLogoWidth) {
                $ratio = $maxLogoWidth / $logoWidth;
                $newLogoWidth = $maxLogoWidth;
                $newLogoHeight = $logoHeight * $ratio;
                
                // Resize logo
                $logoResized = imagecreatetruecolor($newLogoWidth, $newLogoHeight);
                imagealphablending($logoResized, false);
                imagesavealpha($logoResized, true);
                imagecopyresampled($logoResized, $logo, 0, 0, 0, 0, $newLogoWidth, $newLogoHeight, $logoWidth, $logoHeight);
                imagedestroy($logo);
                $logo = $logoResized;
                $logoWidth = $newLogoWidth;
                $logoHeight = $newLogoHeight;
            }
            
            // Calculate position (bottom-right with 10px margin)
            $destX = $imageWidth - $logoWidth - 10;
            $destY = $imageHeight - $logoHeight - 10;
            
            // Set opacity to 30% (0-127, where 127 is transparent)
            $opacity = 89; // ~30% opacity (127 * 0.7)
            
            // Apply watermark with opacity
            imagecopymerge($image, $logo, $destX, $destY, 0, 0, $logoWidth, $logoHeight, 30);
            
            // Save image based on type
            switch ($imageInfo[2]) {
                case IMAGETYPE_JPEG:
                    imagejpeg($image, $imagePath, 85);
                    break;
                case IMAGETYPE_PNG:
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                    imagepng($image, $imagePath, 6);
                    break;
                case IMAGETYPE_GIF:
                    imagegif($image, $imagePath);
                    break;
            }
            
            // Clean up
            imagedestroy($image);
            imagedestroy($logo);
            
            return true;
        } catch (\Exception $e) {
            error_log("Watermark error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate random code for unique filenames
     * 
     * @param int $length Code length
     * @return string
     */
    protected function getRandomCode($length = 8)
    {
        return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }

    /**
     * Handle cropped image from Cropper.js (base64 format)
     * This method processes the cropped image and saves it to the uploads directory
     * 
     * @param array &$data Reference to request data array
     * @param string $fieldName The field name for the cropped image (default: 'image_photo_cropped')
     * @param string $uploadSubdir Subdirectory for uploads (e.g., 'candidates', 'trainees')
     * @param string $targetField The target field to store the file path (default: 'image_photo')
     * @return bool Success status
     */
    protected function handleCroppedImage(&$data, $fieldName = 'image_photo_cropped', $uploadSubdir = '', $targetField = 'image_photo')
    {
        if (empty($data[$fieldName])) {
            return false;
        }

        $base64Image = $data[$fieldName];
        
        // Extract base64 data (remove "data:image/jpeg;base64," prefix)
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $imageType = $matches[1]; // jpeg, png, etc
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageData = base64_decode($base64Image);
            
            if ($imageData !== false) {
                // Generate unique filename
                $filename = 'photo_' . time() . '_' . uniqid() . '.' . $imageType;
                
                // Build upload path
                $uploadPath = WWW_ROOT . 'img' . DS . 'uploads' . DS;
                if (!empty($uploadSubdir)) {
                    $uploadPath .= $uploadSubdir . DS;
                }
                
                // Create directory if not exists
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }
                
                // Save cropped image
                if (file_put_contents($uploadPath . $filename, $imageData)) {
                    // Set the file path in data array (relative to uploads directory)
                    if (!empty($uploadSubdir)) {
                        $data[$targetField] = $uploadSubdir . '/' . $filename;
                    } else {
                        $data[$targetField] = $filename;
                    }
                    
                    // Log success
                    $this->log('Cropped image saved: ' . $filename, 'debug');
                    
                    // Remove temporary field
                    unset($data[$fieldName]);
                    
                    return true;
                }
            }
        }
        
        // Remove temporary field even if processing failed
        unset($data[$fieldName]);
        return false;
    }

    /**
     * Generic method to get regions by parent ID for address cascading
     * 
     * @param string $modelName The model name (e.g., 'MasterKabupatens')
     * @param string $queryParam The query parameter name (e.g., 'master_propinsi_id')
     * @param string $foreignKey The foreign key column name (e.g., 'propinsi_id')
     * @return void Returns JSON response
     */
    protected function getRegionsByParent($modelName, $queryParam, $foreignKey)
    {
        $this->autoRender = false;
        $parentId = $this->request->getQuery($queryParam);
        
        $result = [];
        if ($parentId) {
            $model = $this->loadModel($modelName);
            $result = $model->find('list')
                ->where([$foreignKey => $parentId])
                ->order(['title' => 'ASC'])
                ->toArray();
        }
        
        $this->response = $this->response
            ->withType('application/json')
            ->withStringBody(json_encode(['data' => $result]));
        
        return $this->response;
    }
}



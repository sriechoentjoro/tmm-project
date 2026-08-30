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

    // NOTE: do not declare a public $layout property here. CakePHP's
    // createView() copies it into the ViewBuilder after the action runs,
    // silently overriding every $this->viewBuilder()->setLayout() call
    // (process_flow, print, ...). The 'elegant' default is applied in
    // beforeRender() only when no action picked a layout.

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
                'prefix' => false,
                'controller' => 'Users',
                'action' => 'login'
            ],
            'loginRedirect' => [
                'prefix' => false,
                // Land on the system documentation (process pipeline) page;
                // it highlights the stages for the logged-in role and links
                // to the role dashboard and every module screen.
                'controller' => 'Dashboard',
                'action' => 'processFlow'
            ],
            'logoutRedirect' => [
                'prefix' => false,
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

        // Apply the interface language chosen via the switcher (Config.language).
        // Maps ind/eng/jpn to a locale so the .po translation files in
        // src/Locale/{ind,jpn}/default.po take effect across every page.
        $lang = $this->request->getSession()->read('Config.language') ?: 'ind';
        $localeMap = ['ind' => 'ind', 'eng' => 'en_US', 'jpn' => 'jpn'];
        \Cake\I18n\I18n::setLocale(isset($localeMap[$lang]) ? $localeMap[$lang] : 'ind');

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
        
        // Single gate: DB-driven permission check via role_menus → menus.granted_actions
        if ($this->hasPermission($controller, $action)) {
            return true;
        }

        $this->handleUnauthorizedAccess($action,
            __('Your role does not have access to {0} in {1}.', $action, $controller)
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

        $allowedControllers = [
            'Dashboard',
            // Candidate management
            'Candidates',
            'LpkPhysicalTests',
            'CandidateRecordInterviews',
            'CandidateRecordMedicalCheckUps',
            'CandidateDocuments',
            'ApprenticeOrders',
            // Stakeholder management (full ownership)
            'VocationalTrainingInstitutions',
            'VocationalTrainingInstitutionStories',
            'AcceptanceOrganizations',
            'AcceptanceOrganizationStories',
            'CooperativeAssociations',
            'CooperativeAssociationStories',
            'SpecialSkillSupportInstitutions',
            // Read-only master tables
            'MasterPropinsis',
            'MasterKabupatens',
            'MasterKecamatans',
            'MasterKelurahans',
        ];

        if (in_array($controller, $allowedControllers)) {
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

        $allowedControllers = [
            'Dashboard',
            'Trainees',          // includes promoteToApprentice, doPromoteToApprentice
            'TraineeTrainings',
            'TraineeDocuments',
            'TrainingScores',
            'Apprentices',       // read-only once promoted
            //'AcceptanceOrganizations',  // read-only
            'VocationalTrainingInstitutions', // read-only
            'MasterPropinsis',   // read-only
            'MasterKabupatens',  // read-only
            'MasterKecamatans',  // read-only
            'MasterKelurahans',  // read-only
        ];

        $readOnlyControllers = ['AcceptanceOrganizations', 'VocationalTrainingInstitutions', 'Apprentices'];

        if (in_array($controller, $allowedControllers)) {
            if (strpos($controller, 'Master') === 0 || in_array($controller, $readOnlyControllers)) {
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

        $allowedControllers = [
            'Dashboard',
            'Candidates',
            'CandidateDocuments',
            'LpkPhysicalTests',
            'CandidateRecordInterviews',
            'CandidateRecordMedicalCheckUps',
            'MasterPropinsis',           // read-only
            'MasterKabupatens',          // read-only
            'MasterKecamatans',          // read-only
            'MasterKelurahans',          // read-only
        ];

        if (in_array($controller, $allowedControllers)) {
            if (strpos($controller, 'Master') === 0) {
                return $this->isReadOnlyAction();
            }
            // Promote actions are tmm-recruitment only
            if ($controller === 'Candidates') {
                $action = $this->request->getParam('action');
                $promotionActions = ['promoteToTrainee', 'doPromote', 'promotionHistory'];
                if (in_array($action, $promotionActions)) {
                    return false;
                }
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
        
        // DB-driven: check role_menus → menus.granted_actions
        $permissions = $this->getMenuRolePermissions();
        if (!isset($permissions[$controller])) {
            return false;
        }

        $allowed = $permissions[$controller];
        return in_array('*', $allowed) || in_array($action, $allowed);
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
            'LpkPhysicalTests' => 'cms_lpk_candidates',
            'CandidateRecordInterviews' => 'cms_lpk_candidates',
            'CandidateRecordMedicalCheckUps' => 'cms_lpk_candidates',
            
            // CMS TMM Trainees
            'Trainees' => 'cms_tmm_trainees',
            'TraineeAccountings' => 'cms_tmm_trainee_accountings',
            'TraineeTrainings' => 'cms_tmm_trainee_trainings',
            
            // CMS TMM Apprentices
            'Apprentices' => 'cms_tmm_apprentices',
            'ApprenticeDocuments' => 'cms_tmm_apprentice_documents',
            
            // CMS TMM Stakeholders
            'AcceptanceOrganizations' => 'cms_tmm_stakeholders',
            'AcceptanceOrganizationStories' => 'cms_tmm_stakeholders',
            'CooperativeAssociations' => 'cms_tmm_stakeholders',
            'CooperativeAssociationStories' => 'cms_tmm_stakeholders',
            'VocationalTrainingInstitutions' => 'cms_tmm_stakeholders',
            'VocationalTrainingInstitutionStories' => 'cms_tmm_stakeholders',
            'SpecialSkillSupportInstitutions' => 'cms_tmm_stakeholders',
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
            'tmm-recruitment' => ['cms_lpk_candidates', 'cms_tmm_apprentices', 'cms_tmm_stakeholders', 'cms_masters'],
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
     * Returns role permissions sourced entirely from the database (role_menus →
     * menus.controller / menus.action / role_menus.granted_actions).
     * No hardcoded role arrays — manage access through /menus matrix UI.
     *
     * @return array [controller => [actions]]
     */
    protected function getRolePermissions()
    {
        return $this->getMenuRolePermissions();
    }

    /**
     * Permissions derived from menus assigned to the current user's roles.
     * Parses each assigned menu URL into [Controller => [actions]].
     *
     * @return array [controller => [actions]]
     */
    protected function getMenuRolePermissions()
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }
        $cached = [];

        $roleNames = isset($this->currentUser['role_names']) ? (array)$this->currentUser['role_names'] : [];
        if (empty($roleNames)) {
            return $cached;
        }

        try {
            $locator = \Cake\ORM\TableRegistry::getTableLocator();

            $roleIds = $locator->get('Roles')->find()
                ->where(['name IN' => $roleNames])
                ->extract('id')
                ->toList();
            if (empty($roleIds)) {
                return $cached;
            }

            // Join role_menus → menus in one query, pulling granted_actions alongside.
            // Parent-container menus (those that have at least one active child menu also
            // assigned to this role) are excluded from PAGE-LEVEL permissions — they exist
            // only as navigation containers.  Their child menus supply the actual access.
            $roleIdList = implode(',', array_map('intval', $roleIds));
            $conn   = \Cake\Datasource\ConnectionManager::get('cms_authentication_authorization');
            $rows   = $conn->execute(
                'SELECT m.controller, m.action, m.url, rm.granted_actions
                 FROM role_menus rm
                 JOIN cms_masters.menus m ON m.id = rm.menu_id
                 WHERE rm.role_id IN (' . $roleIdList . ')
                   AND rm.is_active = 1
                   AND m.is_active  = 1
                   -- Skip menus that act as navigation containers:
                   -- if this menu has at least one active child assigned to the same role,
                   -- it is a parent tab whose URL is just the default landing — child menus
                   -- supply the real page permissions.
                   AND NOT EXISTS (
                       SELECT 1
                       FROM role_menus rm2
                       JOIN cms_masters.menus m2 ON m2.id = rm2.menu_id
                       WHERE m2.parent_id = m.id
                         AND rm2.role_id IN (' . $roleIdList . ')
                         AND rm2.is_active = 1
                         AND m2.is_active  = 1
                   )'
            )->fetchAll('assoc');

            foreach ($rows as $row) {
                // Resolve controller name
                if (!empty($row['controller'])) {
                    $controller = $row['controller'];
                    $menuAction = $row['action'] ?: 'index';
                } else {
                    $parsed = $this->parseMenuUrl($row['url']);
                    if (!$parsed) {
                        continue;
                    }
                    list($controller, $menuAction) = $parsed;
                }

                $ga = trim((string)$row['granted_actions']);

                if (!empty($ga) && $ga !== '*') {
                    // Explicit comma-separated action list — grant exactly those actions.
                    $actions  = array_map('trim', explode(',', $ga));
                    $existing = isset($cached[$controller]) ? $cached[$controller] : [];
                    $cached[$controller] = array_values(array_unique(array_merge($existing, $actions)));
                } else {
                    // granted_actions is '*' or empty.
                    // We deliberately do NOT use a controller-wide ['*'] wildcard here
                    // because that leaks sibling actions the role was never assigned
                    // (e.g. Reports::incomeStatement becoming accessible just because
                    //  Reports::trainingProgress was granted to the same role).
                    //
                    // Instead, build an explicit action list from the menu's own action:
                    //   • index  → index, view         (browse + read)
                    //   • other  → that action, index, view  (specific action + read helpers)
                    //
                    // Controllers that genuinely need full CRUD should use an explicit
                    // granted_actions list like "index,view,add,edit,delete".
                    $existing = isset($cached[$controller]) ? $cached[$controller] : [];
                    $toAdd = array_unique([$menuAction, 'index', 'view']);
                    $cached[$controller] = array_values(array_unique(array_merge($existing, $toAdd)));
                }
            }
        } catch (\Exception $e) {
            $this->log('getMenuRolePermissions failed: ' . $e->getMessage(), 'error');
            $cached = [];
        }

        return $cached;
    }

    /**
     * Parse an internal menu URL into [ControllerName, actionName].
     *
     * @param string|null $url Menu URL, e.g. /candidates/promote-to-trainee
     * @return array|null [controller, action] or null when not parseable
     */
    protected function parseMenuUrl($url)
    {
        if (empty($url) || $url === '#' || strpos($url, 'http') === 0 || strpos($url, 'javascript:') === 0) {
            return null;
        }
        $parts = array_values(array_filter(explode('/', trim($url, '/'))));
        // Skip routing prefixes such as /admin/...
        if (!empty($parts) && in_array($parts[0], ['admin', 'tmm'])) {
            array_shift($parts);
        }
        if (empty($parts)) {
            return null;
        }
        $controller = \Cake\Utility\Inflector::camelize(str_replace('-', '_', $parts[0]));
        $action = isset($parts[1])
            ? \Cake\Utility\Inflector::variable(str_replace('-', '_', $parts[1]))
            : 'index';

        return [$controller, $action];
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
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        // Default layout when the action did not pick one (replaces the
        // former public $layout property, which overrode action choices).
        // AJAX requests are excluded: RequestHandler switches them to
        // AjaxView, whose own 'ajax' layout must not be overridden here.
        if ($this->viewBuilder()->getLayout() === null && !$this->request->is('ajax')) {
            $this->viewBuilder()->setLayout('elegant');
        }

        // Provide related-data tabs to view pages that opt in via the
        // 'view_tabs' element (templates without their own tab system)
        if ($this->request->getParam('action') === 'view' && !isset($this->viewVars['relatedTabs'])) {
            $this->set('relatedTabs', $this->getRelatedDataTabs());
        }

        // Skip menu loading for layouts/requests that do not render the
        // navigation (standalone pages and bare ajax/modal responses)
        if (
            $this->request->is('ajax')
            || in_array($this->viewBuilder()->getLayout(), ['print', 'process_flow', 'ajax'])
        ) {
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
            ->all()
            ->toList();

        // sort_order restarts at 1 within each category, so top-level tabs
        // must be ordered by category first; unknown categories go last
        // Tab order follows the journey timeline:
        //   Candidate (Recruitment) -> Trainee (Training, Documentation, Accounting)
        //   -> Apprentice (Apprentice in Japan)
        // with Audit pinned left-most and support menus (Reports, System) at the end.
        $categoryOrder = [
            'Audit',               // always left-most for all roles
            'Dashboards',
            'Stakeholder',
            'Recruitment',         // timeline: Candidate
            'Training',            // timeline: Trainee - training phase
            'Documentation',       // timeline: Trainee - pre-departure documents
            'Accounting',          // timeline: Trainee - installments & costs
            'Apprentice in Japan', // timeline: Apprentice
            'Reports',
            'System',
        ];
        $categoryRank = array_flip($categoryOrder);
        usort($menus, function ($a, $b) use ($categoryRank) {
            $rankA = isset($categoryRank[$a->category]) ? $categoryRank[$a->category] : PHP_INT_MAX;
            $rankB = isset($categoryRank[$b->category]) ? $categoryRank[$b->category] : PHP_INT_MAX;
            if ($rankA !== $rankB) {
                return $rankA <=> $rankB;
            }
            return (int)$a->sort_order <=> (int)$b->sort_order;
        });

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

            // Pass the explicit set of menu IDs the role was granted in role_menus.
            // The template uses this for exact-match filtering instead of the loose
            // controller-level match, which was leaking sibling menu items that share
            // the same controller (e.g. all Dashboard::* sub-pages showing up for a
            // role that only has Training Dashboard assigned).
            $allowedMenuIds = $isAdmin ? null : $this->getAllowedMenuIds();

            $this->set('allowedControllers', $allowedControllers);
            $this->set('rolePermissions', $rolePermissions);
            $this->set('isAdministrator', $isAdmin);
            $this->set('allowedMenuIds', $allowedMenuIds);
        } else {
            $this->set('allowedControllers', array());
            $this->set('rolePermissions', array());
            $this->set('isAdministrator', false);
            $this->set('allowedMenuIds', []);
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
        return array_keys($permissions);
    }

    /**
     * Return the explicit set of menu IDs (and their parent IDs) that the
     * current user's roles have been granted in the role_menus table.
     *
     * This is used by the navigation template to show ONLY menus that were
     * explicitly assigned to the role — not every menu whose controller
     * happens to match a controller the role can access.
     *
     * @return array  Flat list of integer menu IDs, or null for administrator.
     */
    protected function getAllowedMenuIds()
    {
        static $cachedMenuIds = null;
        if ($cachedMenuIds !== null) {
            return $cachedMenuIds;
        }
        $cachedMenuIds = [];

        $roleNames = isset($this->currentUser['role_names']) ? (array)$this->currentUser['role_names'] : [];
        if (empty($roleNames)) {
            return $cachedMenuIds;
        }

        try {
            $locator  = \Cake\ORM\TableRegistry::getTableLocator();
            $roleIds  = $locator->get('Roles')->find()
                ->where(['name IN' => $roleNames])
                ->extract('id')
                ->toList();

            if (empty($roleIds)) {
                return $cachedMenuIds;
            }

            $conn = \Cake\Datasource\ConnectionManager::get('cms_authentication_authorization');

            // Fetch the directly-assigned menu IDs
            $rows = $conn->execute(
                'SELECT rm.menu_id, m.parent_id
                 FROM role_menus rm
                 JOIN cms_masters.menus m ON m.id = rm.menu_id
                 WHERE rm.role_id IN (' . implode(',', array_map('intval', $roleIds)) . ')
                   AND rm.is_active = 1
                   AND m.is_active  = 1'
            )->fetchAll('assoc');

            $menuIds   = [];
            $parentIds = [];
            foreach ($rows as $row) {
                $menuIds[] = (int)$row['menu_id'];
                if (!empty($row['parent_id'])) {
                    $parentIds[] = (int)$row['parent_id'];
                }
            }

            // Also include parent menu IDs so the parent tab is visible when
            // at least one child is assigned, without exposing siblings.
            $cachedMenuIds = array_values(array_unique(array_merge($menuIds, $parentIds)));
        } catch (\Exception $e) {
            $this->log('getAllowedMenuIds failed: ' . $e->getMessage(), 'error');
            $cachedMenuIds = [];
        }

        return $cachedMenuIds;
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

    /**
     * Discover data related to the entity shown by a view action.
     *
     * Two relation kinds, resolved from information_schema of the model's
     * own database:
     *  - children: tables holding a {singular}_id column pointing at the record
     *  - siblings: rows in other tables that share one of the record's own
     *    foreign key values (e.g. other documents of the same trainee)
     *
     * @return array [['label' => ..., 'columns' => [...], 'rows' => [...]], ...]
     */
    protected function getRelatedDataTabs()
    {
        try {
            $entity = null;
            foreach ($this->viewVars as $var) {
                if ($var instanceof \Cake\Datasource\EntityInterface) {
                    $entity = $var;
                    break;
                }
            }
            if ($entity === null || empty($entity->id)) {
                return [];
            }

            $table = \Cake\ORM\TableRegistry::getTableLocator()->get($this->name);
            $conn = $table->getConnection();
            $config = $conn->config();
            $dbName = isset($config['database']) ? $config['database'] : null;
            if (!$dbName) {
                return [];
            }
            $tableName = $table->getTable();
            $singularFk = \Cake\Utility\Inflector::singularize($tableName) . '_id';

            $pairs = $conn->execute(
                "SELECT c.TABLE_NAME AS t, c.COLUMN_NAME AS c FROM information_schema.columns c
                 JOIN information_schema.tables t
                   ON t.table_schema = c.table_schema AND t.TABLE_NAME = c.TABLE_NAME
                 WHERE c.table_schema = ? AND c.COLUMN_NAME LIKE '%\\_id'
                   AND t.TABLE_TYPE = 'BASE TABLE'",
                [$dbName]
            )->fetchAll('assoc');
            $tablesByColumn = [];
            foreach ($pairs as $pair) {
                $tablesByColumn[$pair['c']][] = $pair['t'];
            }

            $tabs = [];
            $seen = [];

            // children rows referencing this record
            $childTables = isset($tablesByColumn[$singularFk]) ? $tablesByColumn[$singularFk] : [];
            foreach ($childTables as $child) {
                if ($child === $tableName || count($tabs) >= 6) {
                    continue;
                }
                $tab = $this->buildRelatedTab($conn, $child, $singularFk, $entity->id);
                if ($tab) {
                    $tabs[] = $tab;
                    $seen[$child] = true;
                }
            }

            // sibling rows sharing one of this record's foreign keys
            foreach ($entity->toArray() as $column => $value) {
                if (substr($column, -3) !== '_id' || empty($value) || !is_numeric($value) || $column === $singularFk) {
                    continue;
                }
                $siblingTables = isset($tablesByColumn[$column]) ? $tablesByColumn[$column] : [];
                foreach ($siblingTables as $sibling) {
                    if ($sibling === $tableName || isset($seen[$sibling]) || strpos($sibling, 'master_') === 0) {
                        continue;
                    }
                    if (count($tabs) >= 6) {
                        break 2;
                    }
                    $tab = $this->buildRelatedTab($conn, $sibling, $column, $value);
                    if ($tab) {
                        $tabs[] = $tab;
                        $seen[$sibling] = true;
                    }
                }
            }

            return $tabs;
        } catch (\Exception $e) {
            $this->log('getRelatedDataTabs failed: ' . $e->getMessage(), 'error');

            return [];
        }
    }

    /**
     * Fetch up to 50 related rows and shape them into a display tab.
     *
     * @return array|null Tab data, or null when the table holds no matches
     */
    protected function buildRelatedTab($conn, $tableName, $column, $value)
    {
        try {
            $rows = $conn->execute(
                sprintf('SELECT * FROM `%s` WHERE `%s` = ? ORDER BY id DESC LIMIT 50', $tableName, $column),
                [$value]
            )->fetchAll('assoc');
        } catch (\Exception $e) {
            try {
                // tables without an id column
                $rows = $conn->execute(
                    sprintf('SELECT * FROM `%s` WHERE `%s` = ? LIMIT 50', $tableName, $column),
                    [$value]
                )->fetchAll('assoc');
            } catch (\Exception $e) {
                return null;
            }
        }
        if (!$rows) {
            return null;
        }

        $columns = [];
        foreach (array_keys($rows[0]) as $col) {
            if (count($columns) >= 8) {
                break;
            }
            if (preg_match('/password|token/i', $col)) {
                continue;
            }
            $columns[] = $col;
        }

        return [
            'label' => \Cake\Utility\Inflector::humanize($tableName),
            'controller' => \Cake\Utility\Inflector::camelize($tableName),
            'columns' => $columns,
            'rows' => $rows,
        ];
    }
}

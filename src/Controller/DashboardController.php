<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;

/**
 * Dashboard Controller
 *
 * Main dashboard for TMM Apprentice Management System
 */
class DashboardController extends AppController
{
    /**
     * Authorization check
     */
    public function isAuthorized($user)
    {
        // Allow all authenticated users to access the dashboard
        return true;
    }

    /**
     * Index method - Route to role-specific dashboard
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $user = $this->Auth->user();
        $roles = isset($user['role_names']) ? $user['role_names'] : [];
        
        // Route to appropriate dashboard based on role
        if (in_array('administrator', $roles)) {
            return $this->administratorDashboard();
        }
        
        if (in_array('management', $roles) || in_array('director', $roles)) {
            return $this->managementDashboard();
        }
        
        if (in_array('tmm-recruitment', $roles)) {
            return $this->recruitmentDashboard();
        }
        
        if (in_array('tmm-training', $roles)) {
            return $this->trainingDashboard();
        }
        
        if (in_array('tmm-documentation', $roles)) {
            return $this->documentationDashboard();
        }
        
        if (in_array('lpk-penyangga', $roles)) {
            return $this->lpkDashboard();
        }
        
        // Default fallback
        $this->set([
            'totalCandidates' => 0,
            'totalTrainees' => 0,
            'totalOrders' => 0,
            'totalOrganizations' => 0,
            'recentCandidates' => [],
            'recentTrainees' => []
        ]);
    }
    
    /**
     * LPK action - Public method to access LPK dashboard
     * Routes to lpkDashboard()
     */
    public function lpk()
    {
        return $this->lpkDashboard();
    }
    
    /**
     * Administrator Dashboard - Full system overview
     */
    protected function administratorDashboard()
    {
        $this->viewBuilder()->setTemplate('admin_dashboard');
        
        // Get all statistics
        $stats = [
            'totalUsers' => $this->getTotalCount('Users', 'cms_authentication_authorization'),
            'totalCandidates' => $this->getTotalCount('Candidates', 'cms_lpk_candidates'),
            'totalTrainees' => $this->getTotalCount('Trainees', 'cms_tmm_trainees'),
            'totalOrganizations' => $this->getTotalCount('AcceptanceOrganizations', 'cms_tmm_stakeholders'),
            'totalLpkInstitutions' => $this->getTotalCount('VocationalTrainingInstitutions', 'cms_tmm_stakeholders'),
        ];
        
        // Recent activity
        $recentUsers = $this->getRecentRecords('Users', 'cms_authentication_authorization', 5);
        $recentCandidates = $this->getRecentRecords('Candidates', 'cms_lpk_candidates', 5);
        
        $this->set(compact('stats', 'recentUsers', 'recentCandidates'));
    }
    
    /**
     * Management/Director Dashboard - Read-only overview with charts
     */
    protected function managementDashboard()
    {
        $this->viewBuilder()->setTemplate('management_dashboard');
        
        // Overview statistics
        $stats = [
            'totalCandidates' => $this->getTotalCount('Candidates', 'cms_lpk_candidates'),
            'totalTrainees' => $this->getTotalCount('Trainees', 'cms_tmm_trainees'),
            'totalOrganizations' => $this->getTotalCount('AcceptanceOrganizations', 'cms_tmm_stakeholders'),
        ];
        
        // Chart data
        $candidatesByMonth = $this->getCandidatesByMonth();
        $traineesByStatus = $this->getTraineesByStatus();
        
        $this->set(compact('stats', 'candidatesByMonth', 'traineesByStatus'));
    }
    
    /**
     * TMM Recruitment Dashboard
     */
    protected function recruitmentDashboard()
    {
        $this->viewBuilder()->setTemplate('recruitment_dashboard');
        
        $stats = [
            'totalCandidates' => $this->getTotalCount('Candidates', 'cms_lpk_candidates'),
            'pendingCandidates' => $this->getCountByCondition('Candidates', 'cms_lpk_candidates', ['status' => 'pending']),
            'approvedCandidates' => $this->getCountByCondition('Candidates', 'cms_lpk_candidates', ['status' => 'approved']),
        ];
        
        $recentCandidates = $this->getRecentRecords('Candidates', 'cms_lpk_candidates', 10);
        
        $this->set(compact('stats', 'recentCandidates'));
    }
    
    /**
     * TMM Training Dashboard
     */
    protected function trainingDashboard()
    {
        $this->viewBuilder()->setTemplate('training_dashboard');
        
        $stats = [
            'totalTrainees' => $this->getTotalCount('Trainees', 'cms_tmm_trainees'),
            'activeTrainees' => $this->getCountByCondition('Trainees', 'cms_tmm_trainees', ['status' => 'active']),
            'completedTrainees' => $this->getCountByCondition('Trainees', 'cms_tmm_trainees', ['status' => 'completed']),
        ];
        
        $recentTrainees = $this->getRecentRecords('Trainees', 'cms_tmm_trainees', 10);
        
        $this->set(compact('stats', 'recentTrainees'));
    }
    
    /**
     * TMM Documentation Dashboard
     */
    protected function documentationDashboard()
    {
        $this->viewBuilder()->setTemplate('documentation_dashboard');
        
        $stats = [
            'totalDocuments' => $this->getTotalCount('ApprenticeDocuments', 'cms_tmm_apprentice_documents'),
            'pendingDocuments' => $this->getCountByCondition('ApprenticeDocuments', 'cms_tmm_apprentice_documents', ['status' => 'pending']),
        ];
        
        $recentDocuments = $this->getRecentRecords('ApprenticeDocuments', 'cms_tmm_apprentice_documents', 10);
        
        $this->set(compact('stats', 'recentDocuments'));
    }
    
    /**
     * LPK Penyangga Dashboard - Institution-specific
     */
    protected function lpkDashboard()
    {
        $this->viewBuilder()->setTemplate('lpk_dashboard');
        
        $user = $this->Auth->user();
        $institutionId = isset($user['institution_id']) ? $user['institution_id'] : null;
        $institutionType = isset($user['institution_type']) ? $user['institution_type'] : 'vocational_training';
        
        if (!$institutionId) {
            $this->Flash->error('Your account is not linked to any institution. Please contact administrator.');
            // Set default stats structure even when no institution
            $stats = [
                'institutionName' => 'No Institution',
                'totalCandidates' => 0,
                'pendingCandidates' => 0,
                'approvedCandidates' => 0,
            ];
            $this->set(['stats' => $stats, 'recentCandidates' => []]);
            return;
        }
        
        // Institution-specific statistics
        $stats = [
            'institutionName' => $this->getInstitutionName($institutionId, $institutionType),
            'totalCandidates' => $this->getCountByCondition('Candidates', 'cms_lpk_candidates', 
                ['vocational_training_institution_id' => $institutionId]),
            'pendingCandidates' => $this->getCountByCondition('Candidates', 'cms_lpk_candidates', 
                ['vocational_training_institution_id' => $institutionId, 'status' => 'pending']),
            'approvedCandidates' => $this->getCountByCondition('Candidates', 'cms_lpk_candidates', 
                ['vocational_training_institution_id' => $institutionId, 'status' => 'approved']),
        ];
        
        // Recent candidates from this institution
        $recentCandidates = $this->getRecentRecordsByCondition('Candidates', 'cms_lpk_candidates', 
            ['vocational_training_institution_id' => $institutionId], 10);
        
        $this->set(compact('stats', 'recentCandidates'));
    }
    
    /**
     * Helper: Get total count from a model
     */
    protected function getTotalCount($modelName, $connection = 'default')
    {
        try {
            $table = TableRegistry::getTableLocator()->get($modelName);
            return $table->find()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Helper: Get count by condition
     */
    protected function getCountByCondition($modelName, $connection, $conditions)
    {
        try {
            $conn = ConnectionManager::get($connection);
            $table = TableRegistry::getTableLocator()->get($modelName, ['connection' => $conn]);
            return $table->find()->where($conditions)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    /**
     * Helper: Get recent records
     */
    protected function getRecentRecords($modelName, $connection, $limit = 10)
    {
        try {
            $conn = ConnectionManager::get($connection);
            $table = TableRegistry::getTableLocator()->get($modelName, ['connection' => $conn]);
            return $table->find()
                ->order(['created' => 'DESC'])
                ->limit($limit)
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Helper: Get recent records by condition
     */
    protected function getRecentRecordsByCondition($modelName, $connection, $conditions, $limit = 10)
    {
        try {
            $conn = ConnectionManager::get($connection);
            $table = TableRegistry::getTableLocator()->get($modelName, ['connection' => $conn]);
            return $table->find()
                ->where($conditions)
                ->order(['created' => 'DESC'])
                ->limit($limit)
                ->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Helper: Get candidates by month for charts
     */
    protected function getCandidatesByMonth()
    {
        // Placeholder - implement based on your requirements
        return [];
    }
    
    /**
     * Helper: Get trainees by status for charts
     */
    protected function getTraineesByStatus()
    {
        // Placeholder - implement based on your requirements
        return [];
    }
    
    /**
     * Helper: Get institution name
     */
    protected function getInstitutionName($institutionId, $institutionType)
    {
        try {
            if ($institutionType === 'vocational_training') {
                $conn = ConnectionManager::get('cms_tmm_stakeholders');
                $table = TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions', ['connection' => $conn]);
                $institution = $table->get($institutionId);
                $institutionName = isset($institution->name) ? $institution->name : 'Unknown Institution';
                return $institutionName;
            }
            return 'Unknown Institution';
        } catch (\Exception $e) {
            return 'Unknown Institution';
        }
    }

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        $this->viewBuilder()->setLayout('process_flow');
    }
}
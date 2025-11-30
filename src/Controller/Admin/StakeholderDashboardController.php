<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * StakeholderDashboard Controller (Admin)
 *
 * Provides comprehensive monitoring dashboard for all stakeholder types:
 * - Vocational Training Institutions (LPK)
 * - Special Skill Support Institutions
 * - Acceptance Organizations
 * - Cooperative Associations
 *
 * Features:
 * - Statistics overview (counts, status distribution)
 * - Recent activity feed
 * - Pending approvals widget
 * - Chart data for visualizations
 * - Quick action buttons
 */
class StakeholderDashboardController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        
        // Load required models
        $this->loadModel('VocationalTrainingInstitutions', [
            'className' => 'VocationalTrainingInstitutions',
            'connectionName' => 'cms_tmm_stakeholders'
        ]);
        
        $this->loadModel('SpecialSkillSupportInstitutions', [
            'className' => 'SpecialSkillSupportInstitutions',
            'connectionName' => 'cms_tmm_stakeholders'
        ]);
        
        $this->loadModel('AcceptanceOrganizations', [
            'className' => 'AcceptanceOrganizations',
            'connectionName' => 'cms_tmm_stakeholders'
        ]);
        
        $this->loadModel('CooperativeAssociations', [
            'className' => 'CooperativeAssociations',
            'connectionName' => 'cms_tmm_stakeholders'
        ]);
        
        // Load stakeholder management models
        $this->loadModel('StakeholderActivities', [
            'className' => 'StakeholderActivities',
            'connectionName' => 'cms_authentication_authorization'
        ]);
        
        $this->loadModel('StakeholderPermissions', [
            'className' => 'StakeholderPermissions',
            'connectionName' => 'cms_authentication_authorization'
        ]);
        
        $this->loadModel('AdminApprovalQueue', [
            'className' => 'AdminApprovalQueue',
            'connectionName' => 'cms_authentication_authorization'
        ]);
        
        $this->loadModel('Users', [
            'className' => 'Users',
            'connectionName' => 'cms_authentication_authorization'
        ]);
    }

    /**
     * Main dashboard view
     * Shows statistics, charts, recent activities, and pending approvals
     *
     * @return void
     */
    public function index()
    {
        // Check admin permission
        if (!$this->_isAdmin()) {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        // Get statistics for all stakeholder types
        $statistics = $this->_getStatistics();
        
        // Get recent activities (last 20)
        $recentActivities = $this->StakeholderActivities->find('recent', ['limit' => 20]);
        
        // Get pending approvals
        $pendingApprovals = $this->AdminApprovalQueue->find('pending')->limit(10);
        
        // Get pending verifications
        $UsersTable = TableRegistry::getTableLocator()->get('Users');
        $pendingVerifications = $UsersTable->find()
            ->where([
                'status' => 'pending_verification',
                'verification_token IS NOT' => null,
                'verification_token_expires >' => date('Y-m-d H:i:s')
            ])
            ->order(['created' => 'DESC'])
            ->limit(10)
            ->all();
        
        // Get chart data
        $chartData = $this->_getChartData();
        
        $this->set(compact(
            'statistics',
            'recentActivities',
            'pendingApprovals',
            'pendingVerifications',
            'chartData'
        ));
    }

    /**
     * Get statistics for all stakeholder types
     *
     * @return array Statistics data
     */
    protected function _getStatistics()
    {
        $stats = array();
        
        // LPK Statistics
        $stats['lpk'] = array(
            'total' => $this->VocationalTrainingInstitutions->find()->count(),
            'active' => $this->VocationalTrainingInstitutions->find()
                ->where(['status' => 'active'])
                ->count(),
            'pending_verification' => $this->VocationalTrainingInstitutions->find()
                ->where(['status' => 'pending_verification'])
                ->count(),
            'suspended' => $this->VocationalTrainingInstitutions->find()
                ->where(['status' => 'suspended'])
                ->count()
        );
        
        // Special Skill Statistics
        $stats['special_skill'] = array(
            'total' => $this->SpecialSkillSupportInstitutions->find()->count(),
            'active' => $this->SpecialSkillSupportInstitutions->find()
                ->where(['status' => 'active'])
                ->count(),
            'pending_verification' => $this->SpecialSkillSupportInstitutions->find()
                ->where(['status' => 'pending_verification'])
                ->count(),
            'suspended' => $this->SpecialSkillSupportInstitutions->find()
                ->where(['status' => 'suspended'])
                ->count()
        );
        
        // Acceptance Organizations Statistics
        $stats['acceptance_org'] = array(
            'total' => $this->AcceptanceOrganizations->find()->count(),
            'active' => $this->AcceptanceOrganizations->find()
                ->where(['status' => 'active'])
                ->count(),
            'suspended' => $this->AcceptanceOrganizations->find()
                ->where(['status' => 'suspended'])
                ->count()
        );
        
        // Cooperative Associations Statistics
        $stats['cooperative_assoc'] = array(
            'total' => $this->CooperativeAssociations->find()->count(),
            'active' => $this->CooperativeAssociations->find()
                ->where(['status' => 'active'])
                ->count(),
            'suspended' => $this->CooperativeAssociations->find()
                ->where(['status' => 'suspended'])
                ->count()
        );
        
        // Overall statistics
        $stats['overall'] = array(
            'total_stakeholders' => $stats['lpk']['total'] + $stats['special_skill']['total'] + 
                                   $stats['acceptance_org']['total'] + $stats['cooperative_assoc']['total'],
            'total_pending_verifications' => $stats['lpk']['pending_verification'] + 
                                            $stats['special_skill']['pending_verification'],
            'total_active' => $stats['lpk']['active'] + $stats['special_skill']['active'] + 
                            $stats['acceptance_org']['active'] + $stats['cooperative_assoc']['active'],
            'total_suspended' => $stats['lpk']['suspended'] + $stats['special_skill']['suspended'] + 
                               $stats['acceptance_org']['suspended'] + $stats['cooperative_assoc']['suspended']
        );
        
        return $stats;
    }

    /**
     * Get chart data for dashboard visualizations
     *
     * @return array Chart data
     */
    protected function _getChartData()
    {
        $chartData = array();
        
        // Stakeholder type distribution (Pie Chart)
        $stats = $this->_getStatistics();
        $chartData['stakeholder_distribution'] = array(
            'labels' => array('LPK', 'Special Skill', 'Acceptance Org', 'Cooperative Assoc'),
            'data' => array(
                $stats['lpk']['total'],
                $stats['special_skill']['total'],
                $stats['acceptance_org']['total'],
                $stats['cooperative_assoc']['total']
            ),
            'colors' => array('#667eea', '#764ba2', '#f093fb', '#4facfe')
        );
        
        // Status distribution (Bar Chart)
        $chartData['status_distribution'] = array(
            'labels' => array('Active', 'Pending', 'Suspended'),
            'lpk' => array(
                $stats['lpk']['active'],
                $stats['lpk']['pending_verification'],
                $stats['lpk']['suspended']
            ),
            'special_skill' => array(
                $stats['special_skill']['active'],
                $stats['special_skill']['pending_verification'],
                $stats['special_skill']['suspended']
            ),
            'acceptance_org' => array(
                $stats['acceptance_org']['active'],
                0, // No pending for simple registrations
                $stats['acceptance_org']['suspended']
            ),
            'cooperative_assoc' => array(
                $stats['cooperative_assoc']['active'],
                0, // No pending for simple registrations
                $stats['cooperative_assoc']['suspended']
            )
        );
        
        // Monthly registration trend (Line Chart - last 6 months)
        $monthlyTrend = $this->_getMonthlyRegistrationTrend();
        $chartData['monthly_trend'] = $monthlyTrend;
        
        return $chartData;
    }

    /**
     * Get monthly registration trend for last 6 months
     *
     * @return array Monthly trend data
     */
    protected function _getMonthlyRegistrationTrend()
    {
        $months = array();
        $lpkCounts = array();
        $specialSkillCounts = array();
        $acceptanceOrgCounts = array();
        $cooperativeAssocCounts = array();
        
        for ($i = 5; $i >= 0; $i--) {
            $startDate = date('Y-m-01', strtotime("-$i months"));
            $endDate = date('Y-m-t', strtotime("-$i months"));
            $monthName = date('M Y', strtotime("-$i months"));
            
            $months[] = $monthName;
            
            // LPK count
            $lpkCounts[] = $this->VocationalTrainingInstitutions->find()
                ->where([
                    'created >=' => $startDate,
                    'created <=' => $endDate . ' 23:59:59'
                ])
                ->count();
            
            // Special Skill count
            $specialSkillCounts[] = $this->SpecialSkillSupportInstitutions->find()
                ->where([
                    'created >=' => $startDate,
                    'created <=' => $endDate . ' 23:59:59'
                ])
                ->count();
            
            // Acceptance Org count
            $acceptanceOrgCounts[] = $this->AcceptanceOrganizations->find()
                ->where([
                    'created >=' => $startDate,
                    'created <=' => $endDate . ' 23:59:59'
                ])
                ->count();
            
            // Cooperative Assoc count
            $cooperativeAssocCounts[] = $this->CooperativeAssociations->find()
                ->where([
                    'created >=' => $startDate,
                    'created <=' => $endDate . ' 23:59:59'
                ])
                ->count();
        }
        
        return array(
            'labels' => $months,
            'lpk' => $lpkCounts,
            'special_skill' => $specialSkillCounts,
            'acceptance_org' => $acceptanceOrgCounts,
            'cooperative_assoc' => $cooperativeAssocCounts
        );
    }

    /**
     * Check if current user is admin
     *
     * @return bool True if admin
     */
    protected function _isAdmin()
    {
        $user = $this->Auth->user();
        
        if (!$user) {
            return false;
        }
        
        // Check if user has admin role
        $UsersTable = TableRegistry::getTableLocator()->get('Users');
        $userEntity = $UsersTable->find()
            ->where(['id' => $user['id']])
            ->contain(['Roles'])
            ->first();
        
        if (!$userEntity) {
            return false;
        }
        
        // Check roles
        if (isset($userEntity->roles)) {
            foreach ($userEntity->roles as $role) {
                if (strtolower($role->name) === 'admin' || strtolower($role->name) === 'administrator') {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Export statistics to CSV
     *
     * @return \Cake\Http\Response|null
     */
    public function exportStatistics()
    {
        if (!$this->_isAdmin()) {
            $this->Flash->error(__('You do not have permission to access this page.'));
            return $this->redirect(['action' => 'index']);
        }

        $statistics = $this->_getStatistics();
        
        $this->response = $this->response->withDownload('stakeholder_statistics_' . date('Y-m-d') . '.csv');
        $this->viewBuilder()->setClassName('CsvView.Csv');
        
        $data = array(
            array(
                'Stakeholder Type',
                'Total',
                'Active',
                'Pending Verification',
                'Suspended'
            ),
            array(
                'Vocational Training Institutions (LPK)',
                $statistics['lpk']['total'],
                $statistics['lpk']['active'],
                $statistics['lpk']['pending_verification'],
                $statistics['lpk']['suspended']
            ),
            array(
                'Special Skill Support Institutions',
                $statistics['special_skill']['total'],
                $statistics['special_skill']['active'],
                $statistics['special_skill']['pending_verification'],
                $statistics['special_skill']['suspended']
            ),
            array(
                'Acceptance Organizations',
                $statistics['acceptance_org']['total'],
                $statistics['acceptance_org']['active'],
                'N/A',
                $statistics['acceptance_org']['suspended']
            ),
            array(
                'Cooperative Associations',
                $statistics['cooperative_assoc']['total'],
                $statistics['cooperative_assoc']['active'],
                'N/A',
                $statistics['cooperative_assoc']['suspended']
            ),
            array(
                'OVERALL TOTAL',
                $statistics['overall']['total_stakeholders'],
                $statistics['overall']['total_active'],
                $statistics['overall']['total_pending_verifications'],
                $statistics['overall']['total_suspended']
            )
        );
        
        $this->set('_serialize', 'data');
        $this->set(compact('data'));
        
        return $this->render();
    }
}

<?php
namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\Log\Log;
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
            return $this->redirect(['prefix' => false, 'controller' => 'Dashboard', 'action' => 'index']);
        }

        // Get statistics for all stakeholder types
        $statistics = $this->_getStatistics();
        
        // Get recent activities (last 20)
        $recentActivities = $this->StakeholderActivities->find('recent', ['limit' => 20]);
        
        // What is actually waiting, worked out from the registrations
        // themselves. See _waitingRegistrations() for why the two tables this
        // screen used to read could never answer that.
        $waiting = $this->_waitingRegistrations();

        // The approval queue, still read in case anything ever files into it.
        // Nothing in this application does - _waitingRegistrations() says so at
        // length - so this is all but certainly empty, and the screen now says
        // that rather than showing an all-clear it did not earn.
        // admin_approval_queue arrived with stakeholder_management_schema.sql
        // and is absent where that was never run, so a missing table is a
        // state to report, not a page that dies.
        $approvalProblem = null;
        try {
            $pendingApprovals = $this->AdminApprovalQueue->find('pending')
                ->limit(10)
                ->toArray();
        } catch (\Throwable $e) {
            $pendingApprovals = array();
            $approvalProblem = $e->getMessage();
            Log::error(
                'Stakeholder dashboard could not read admin_approval_queue: ' . $e->getMessage(),
                ['scope' => 'stakeholder_dashboard']
            );
        }

        // Get chart data. The statistics are handed over rather than counted a
        // second time: _getChartData() used to call _getStatistics() again, so
        // every count on this page ran twice.
        $chartData = $this->_getChartData($statistics);

        $this->set(compact(
            'statistics',
            'recentActivities',
            'pendingApprovals',
            'approvalProblem',
            'waiting',
            'chartData'
        ));
    }

    /**
     * The registrations that have started and not finished.
     *
     * This screen used to answer that question from two tables that nothing
     * writes to, so both of its "waiting for you" panels were empty whatever
     * the state of the system:
     *
     * - Pending email verifications read users.verification_token and
     *   users.verification_token_expires. Nothing in this application ever
     *   sets either column; the real tokens live in email_verification_tokens,
     *   keyed by address. And there is no user row to carry them anyway: an
     *   LPK registration creates the institution only, and the account is not
     *   made until the director sets a password at the end of the flow.
     * - Pending approvals read admin_approval_queue. Nothing writes to that
     *   table either, and no registration flow has an approval step: an
     *   institution goes live by following the emailed link and choosing a
     *   password, with no admin decision anywhere in between.
     *
     * Meanwhile the alert at the top of the page counts institutions genuinely
     * stuck at pending_verification, and linked to a panel that always said
     * "No pending verifications" under a green tick. The count was right and
     * the list was empty.
     *
     * So the list is built from the institutions instead, which is where the
     * state actually lives, and each one is matched by address against
     * email_verification_tokens to say whether the link it was sent is still
     * live, has expired, or has already been used. An expired link is the row
     * that matters most: that institution cannot finish on its own and needs
     * the link sent again.
     *
     * @return array rows, and what could not be established
     */
    protected function _waitingRegistrations()
    {
        $out = array(
            'rows' => array(),
            'statusKnown' => true,
            'tokenProblem' => null
        );

        $sources = array(
            'lpk' => array(
                'table' => $this->VocationalTrainingInstitutions,
                'name' => 'name',
                'contact' => 'director'
            ),
            'special_skill' => array(
                'table' => $this->SpecialSkillSupportInstitutions,
                'name' => 'company_name',
                'contact' => 'contact_person'
            )
        );

        $rows = array();
        foreach ($sources as $type => $source) {
            $schema = $source['table']->getSchema();

            // The column arrived with stakeholder_management_schema.sql and is
            // absent on an installation that never ran it. Asking for it there
            // is an error, not an empty list, so say which it is.
            if (!$schema->hasColumn('status')) {
                $out['statusKnown'] = false;
                continue;
            }

            $found = $source['table']->find()
                ->where(['status' => 'pending_verification'])
                ->order(['created' => 'ASC'])
                ->limit(25)
                ->all();

            foreach ($found as $institution) {
                $rows[] = array(
                    'type' => $type,
                    'id' => $institution->id,
                    'name' => $institution->get($source['name']),
                    'contact' => $institution->get($source['contact']),
                    'email' => $institution->email,
                    'since' => $institution->get('created'),
                    'link' => 'unknown',
                    'expires' => null
                );
            }
        }

        if (!$rows) {
            $out['rows'] = array();

            return $out;
        }

        $addresses = array();
        foreach ($rows as $row) {
            if ($row['email'] !== null && $row['email'] !== '') {
                $addresses[strtolower($row['email'])] = true;
            }
        }

        $latest = array();
        if ($addresses) {
            try {
                $tokens = TableRegistry::getTableLocator()->get('EmailVerificationTokens');
                $found = $tokens->find()
                    ->where([
                        'token_type' => 'email_verification',
                        'user_email IN' => array_keys($addresses)
                    ])
                    ->order(['created' => 'ASC'])
                    ->all();

                // Ordered oldest first, so the last one seen for an address is
                // the newest, which is the one the institution was sent.
                foreach ($found as $token) {
                    $latest[strtolower($token->user_email)] = $token;
                }
            } catch (\Throwable $e) {
                // email_verification_tokens is created by
                // phase_3_4_lpk_registration_migration.sql in
                // cms_authentication_authorization, while its table class
                // declares no connection and so is handed the default one.
                // Where those are not the same database the read fails, and
                // that is worth saying out loud: the same lookup failing
                // silently inside generateToken() is why a registration can
                // report that it could not make a token.
                $out['tokenProblem'] = $e->getMessage();
                Log::error(
                    'Stakeholder dashboard could not read email_verification_tokens: ' . $e->getMessage(),
                    ['scope' => 'stakeholder_dashboard']
                );
            }
        }

        if ($out['tokenProblem'] === null) {
            $now = new Time();
            foreach ($rows as $i => $row) {
                $key = strtolower((string)$row['email']);
                if (!isset($latest[$key])) {
                    $rows[$i]['link'] = 'none';
                    continue;
                }

                $token = $latest[$key];
                $rows[$i]['expires'] = $token->expires_at;

                if ($token->is_used) {
                    $rows[$i]['link'] = 'used';
                } elseif ($token->expires_at !== null && $token->expires_at < $now) {
                    $rows[$i]['link'] = 'expired';
                } else {
                    $rows[$i]['link'] = 'live';
                }
            }
        }

        $out['rows'] = $rows;

        return $out;
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
     * @param array|null $stats Statistics already counted, to save counting
     *  every stakeholder twice on one page load.
     * @return array Chart data
     */
    protected function _getChartData(array $stats = null)
    {
        $chartData = array();

        // Stakeholder type distribution (Pie Chart)
        if ($stats === null) {
            $stats = $this->_getStatistics();
        }
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
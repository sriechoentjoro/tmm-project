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
     * Recruitment dashboard - direct menu access
     */
    public function recruitment()
    {
        return $this->recruitmentDashboard();
    }

    /**
     * Training dashboard - direct menu access
     */
    public function training()
    {
        return $this->trainingDashboard();
    }

    /**
     * Documentation dashboard - direct menu access
     */
    public function documentation()
    {
        return $this->documentationDashboard();
    }

    /**
     * Accounting dashboard - direct menu access
     */
    public function accounting()
    {
        $this->viewBuilder()->setTemplate('accounting_dashboard');

        $accountingDb = 'cms_tmm_trainee_accountings';

        $stats = [
            'totalInstallments' => $this->getTotalCount('TraineeInstallments', $accountingDb),
            'totalJournals' => $this->getTotalCount('Journals', $accountingDb),
            'totalAccounts' => $this->getTotalCount('ChartOfAccounts', $accountingDb),
            'collected' => 0,
            'outstanding' => 0,
            'paidOffTrainees' => 0,
            'payingTrainees' => 0,
        ];

        // Trainee names for payment progress
        $traineeNames = [];
        try {
            foreach (TableRegistry::getTableLocator()->get('Trainees')
                ->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
                $traineeNames[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }

        // Payment progress: the latest installment per trainee carries the running figures
        $paymentProgress = [];
        foreach ($this->fetchRows($accountingDb,
            'SELECT t.trainee_id, t.payment_accummulated, t.full_payment_amount,
                    t.unpaid_amount, t.is_paid_off
             FROM trainee_installments t
             JOIN (SELECT trainee_id, MAX(id) AS max_id
                   FROM trainee_installments GROUP BY trainee_id) latest
               ON latest.max_id = t.id
             ORDER BY t.is_paid_off ASC, t.unpaid_amount DESC
             LIMIT 8') as $row) {
            $full = (int)$row['full_payment_amount'];
            $accumulated = (int)$row['payment_accummulated'];
            $paymentProgress[] = [
                'trainee_id' => $row['trainee_id'],
                'name' => isset($traineeNames[$row['trainee_id']]) ? $traineeNames[$row['trainee_id']] : ('#' . $row['trainee_id']),
                'accumulated' => $accumulated,
                'full' => $full,
                'unpaid' => (int)$row['unpaid_amount'],
                'is_paid_off' => (bool)$row['is_paid_off'],
                'percent' => $full > 0 ? min(100, (int)round($accumulated / $full * 100)) : 0,
            ];
            $stats['payingTrainees']++;
            $stats['collected'] += $accumulated;
            $stats['outstanding'] += (int)$row['unpaid_amount'];
            if ($row['is_paid_off']) {
                $stats['paidOffTrainees']++;
            }
        }

        // Journal entries grouped by status (Draft / Posted / Void)
        $journalsByStatus = [];
        foreach ($this->fetchRows($accountingDb,
            'SELECT status, COUNT(*) AS total FROM journals GROUP BY status') as $row) {
            $journalsByStatus[$row['status']] = (int)$row['total'];
        }

        // Recent journals with real figures
        $recentJournals = $this->fetchRows($accountingDb,
            'SELECT id, transaction_date, reference_no, description,
                    total_debit, total_credit, status
             FROM journals ORDER BY id DESC LIMIT 8');

        // Chart of accounts grouped by type
        $accountsByType = [];
        foreach ($this->fetchRows($accountingDb,
            'SELECT type, COUNT(*) AS total FROM chart_of_accounts
             WHERE is_active = 1 GROUP BY type') as $row) {
            $accountsByType[$row['type']] = (int)$row['total'];
        }

        $this->set(compact('stats', 'recentJournals', 'paymentProgress',
            'journalsByStatus', 'accountsByType'));
    }

    /**
     * Executive dashboard - cross-area overview
     */
    public function executive()
    {
        $this->viewBuilder()->setTemplate('executive_dashboard');

        $stats = [
            'totalCandidates' => $this->getTotalCount('Candidates', 'cms_lpk_candidates'),
            'totalTrainees' => $this->getTotalCount('Trainees', 'cms_tmm_trainees'),
            'totalApprentices' => $this->getTotalCount('Apprentices', 'cms_tmm_apprentices'),
            'totalOrders' => $this->getTotalCount('ApprenticeOrders', 'cms_tmm_apprentices'),
            'totalLpkInstitutions' => $this->getTotalCount('VocationalTrainingInstitutions', 'cms_tmm_stakeholders'),
            'totalOrganizations' => $this->getTotalCount('AcceptanceOrganizations', 'cms_tmm_stakeholders'),
        ];

        // Finance: installment running totals (latest row per trainee)
        $finance = ['collected' => 0, 'outstanding' => 0, 'paidOff' => 0, 'paying' => 0];
        foreach ($this->fetchRows('cms_tmm_trainee_accountings',
            'SELECT t.payment_accummulated, t.unpaid_amount, t.is_paid_off
             FROM trainee_installments t
             JOIN (SELECT trainee_id, MAX(id) AS max_id FROM trainee_installments GROUP BY trainee_id) latest
               ON latest.max_id = t.id') as $row) {
            $finance['paying']++;
            $finance['collected'] += (int)$row['payment_accummulated'];
            $finance['outstanding'] += (int)$row['unpaid_amount'];
            if ($row['is_paid_off']) {
                $finance['paidOff']++;
            }
        }

        // Departure readiness: trainees covered on all four pre-departure checks
        $readiness = ['ready' => 0, 'total' => $stats['totalTrainees']];
        try {
            $traineeIds = TableRegistry::getTableLocator()->get('Trainees')->find()
                ->select(['id'])->enableHydration(false)->extract('id')->toList();
            $covered = function ($sql, $connection) {
                $rows = $this->fetchRows($connection, $sql);

                return array_column($rows, 'trainee_id');
            };
            $passport = $covered('SELECT DISTINCT trainee_id FROM trainee_record_pasports', 'cms_tmm_trainee_documents');
            $coe = $covered('SELECT DISTINCT trainee_id FROM trainee_record_coe_visas', 'cms_tmm_trainee_documents');
            $medical = $covered('SELECT DISTINCT trainee_id FROM trainee_record_medical_check_ups', 'cms_tmm_trainee_documents');
            $ticket = $covered('SELECT DISTINCT trainee_id FROM tickets', 'cms_tmm_trainee_document_ticketings');
            foreach ($traineeIds as $traineeId) {
                if (in_array($traineeId, $passport) && in_array($traineeId, $coe)
                    && in_array($traineeId, $medical) && in_array($traineeId, $ticket)) {
                    $readiness['ready']++;
                }
            }
        } catch (\Exception $e) {
        }

        // Recent candidates with readable fields
        $recentCandidates = $this->fetchRows('cms_lpk_candidates',
            'SELECT id, name, candidate_code, created FROM candidates ORDER BY id DESC LIMIT 6');

        $this->set(compact('stats', 'finance', 'readiness', 'recentCandidates'));
    }

    /**
     * Administrator Dashboard - Full system overview
     */
    protected function administratorDashboard()
    {
        $this->viewBuilder()->setTemplate('admin_dashboard');

        // ── Pipeline counts (timeline: Candidate → Trainee → Apprentice) ──
        $totalCandidates = $this->getTotalCount('Candidates');
        $totalTrainees   = $this->getTotalCount('Trainees');
        $totalApprentices = $this->getTotalCount('Apprentices');

        // ── Stakeholders ──────────────────────────────────────────────
        $totalOrganizations   = $this->getTotalCount('AcceptanceOrganizations');
        $totalLpkInstitutions = $this->getTotalCount('VocationalTrainingInstitutions');
        $totalCoopAssociations = $this->getTotalCount('CooperativeAssociations');
        $totalSupportInstitutions = $this->getTotalCount('SpecialSkillSupportInstitutions');

        // ── Access control ────────────────────────────────────────────
        $totalUsers = $this->getTotalCount('Users');
        $totalRoles = $this->getTotalCount('Roles');

        // ── Documents / tickets workflow ──────────────────────────────
        $totalTickets = $this->getTotalCount('Tickets');

        // ── Finance snapshot (trainee installments) ───────────────────
        $financeContracted = $this->getSum('TraineeInstallments', 'full_payment_amount');
        $financeCollected  = $this->getSum('TraineeInstallments', 'payment_amount');
        $financeOutstanding = $this->getSum('TraineeInstallments', 'unpaid_amount');
        $installmentsPaidOff = $this->getCountByCondition('TraineeInstallments', 'cms_tmm_trainee_accountings', ['is_paid_off' => 1]);
        $totalInstallments = $this->getTotalCount('TraineeInstallments');
        $totalJournals = $this->getTotalCount('Journals');

        $stats = [
            'totalUsers' => $totalUsers,
            'totalRoles' => $totalRoles,
            'totalCandidates' => $totalCandidates,
            'totalTrainees' => $totalTrainees,
            'totalApprentices' => $totalApprentices,
            'totalOrganizations' => $totalOrganizations,
            'totalLpkInstitutions' => $totalLpkInstitutions,
            'totalCoopAssociations' => $totalCoopAssociations,
            'totalSupportInstitutions' => $totalSupportInstitutions,
            'totalTickets' => $totalTickets,
            'totalInstallments' => $totalInstallments,
            'totalJournals' => $totalJournals,
            'installmentsPaidOff' => $installmentsPaidOff,
            'financeContracted' => $financeContracted,
            'financeCollected' => $financeCollected,
            'financeOutstanding' => $financeOutstanding,
        ];

        // Pipeline conversion rates
        $pipeline = [
            ['key' => 'candidate', 'count' => $totalCandidates],
            ['key' => 'trainee', 'count' => $totalTrainees],
            ['key' => 'apprentice', 'count' => $totalApprentices],
        ];
        $pipelineMax = max(1, $totalCandidates, $totalTrainees, $totalApprentices);
        $candidateToTrainee = $totalCandidates > 0 ? round($totalTrainees / $totalCandidates * 100) : 0;
        $traineeToApprentice = $totalTrainees > 0 ? round($totalApprentices / $totalTrainees * 100) : 0;

        // Users grouped by role (best-effort)
        $usersByRole = $this->getUsersByRole();

        // Finance collection rate
        $collectionRate = $financeContracted > 0
            ? round($financeCollected / $financeContracted * 100)
            : 0;

        // Recent activity across the pipeline
        $recentUsers = $this->getRecentRecords('Users', 'cms_authentication_authorization', 6);
        $recentCandidates = $this->getRecentRecords('Candidates', 'cms_lpk_candidates', 6);
        $recentTrainees = $this->getRecentRecords('Trainees', 'cms_tmm_trainees', 6);

        $this->set(compact(
            'stats', 'pipeline', 'pipelineMax', 'candidateToTrainee', 'traineeToApprentice',
            'usersByRole', 'collectionRate',
            'recentUsers', 'recentCandidates', 'recentTrainees'
        ));
    }

    /**
     * Helper: Sum a numeric column on a model (uses the model's own connection).
     */
    protected function getSum($modelName, $column)
    {
        try {
            $table = TableRegistry::getTableLocator()->get($modelName);
            $result = $table->find()
                ->select(['total' => $table->find()->func()->sum($column)])
                ->first();
            return $result && $result->total !== null ? (float)$result->total : 0.0;
        } catch (\Exception $e) {
            return 0.0;
        }
    }

    /**
     * Helper: Count users grouped by role name for the admin overview.
     * Returns [['name' => 'administrator', 'count' => 3], ...].
     */
    protected function getUsersByRole()
    {
        try {
            $roles = TableRegistry::getTableLocator()->get('Roles');
            $rows = $roles->find()
                ->contain(['Users'])
                ->all();
            $out = [];
            foreach ($rows as $role) {
                $out[] = [
                    'name' => $role->name ?: $role->title ?? __('Role'),
                    'count' => is_array($role->users ?? null) ? count($role->users) : 0,
                ];
            }
            usort($out, function ($a, $b) { return $b['count'] - $a['count']; });
            return $out;
        } catch (\Exception $e) {
            return [];
        }
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

        $traineeConn    = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainees');
        $trainingConn   = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $apprenticeConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_apprentices');
        $candidateConn  = \Cake\Datasource\ConnectionManager::get('cms_lpk_candidates');

        // KPI: Trainee counts
        $traineeStats = $traineeConn->execute("
            SELECT COUNT(*) AS total,
                   SUM(is_trainee_pass) AS passed,
                   SUM(CASE WHEN is_trainee_pass=0 THEN 1 ELSE 0 END) AS in_training,
                   SUM(CASE WHEN master_gender_id=1 THEN 1 ELSE 0 END) AS male,
                   SUM(CASE WHEN master_gender_id=2 THEN 1 ELSE 0 END) AS female
            FROM trainees
        ")->fetch('assoc');

        // KPI: Certificates issued
        $certCount = (int)$trainingConn->execute("SELECT COUNT(*) FROM trainee_certificates")->fetchColumn(0);

        // KPI: Apprentices promoted out
        $apprenticeCount = (int)$apprenticeConn->execute("SELECT COUNT(*) FROM apprentices")->fetchColumn(0);

        // KPI: Eligible candidates not yet promoted to trainee
        $eligibleCandidates = (int)$candidateConn->execute("
            SELECT COUNT(*) FROM candidates
            WHERE is_candidate_pass=1 AND status_flag='active'
            AND id NOT IN (SELECT candidate_id FROM cms_tmm_trainees.trainees)
        ")->fetchColumn(0);

        // New training batches
        $batches = $trainingConn->execute("
            SELECT id, batch_code, batch_name, status, capacity, enrolled_count,
                   start_date, end_date, instructor_name, training_location,
                   DATEDIFF(end_date, CURDATE()) AS days_remaining,
                   DATEDIFF(CURDATE(), start_date) AS days_elapsed
            FROM training_batches
            ORDER BY start_date ASC
        ")->fetchAll('assoc');

        // Legacy trainee batches
        $traineeBatches = $trainingConn->execute("
            SELECT ttb.id, ttb.batch_name, ttb.origin_training_location,
                   ttb.origin_start_plan_date, ttb.origin_finish_plan_date,
                   ttb.departure_plan_date, ttb.training_term_of_months,
                   COUNT(t.id) AS enrolled,
                   SUM(t.is_trainee_pass) AS passed
            FROM cms_tmm_trainee_trainings.trainee_training_batches ttb
            LEFT JOIN cms_tmm_trainees.trainees t ON t.trainee_training_batch_id = ttb.id
            GROUP BY ttb.id
            ORDER BY ttb.origin_start_plan_date ASC
        ")->fetchAll('assoc');

        // Trainees list
        $traineeList = $traineeConn->execute("
            SELECT t.id, t.name, t.tmm_code, t.applicant_code,
                   t.is_trainee_pass, t.trainee_training_batch_id,
                   t.master_gender_id, t.image_photo,
                   TIMESTAMPDIFF(YEAR, t.birth_date, CURDATE()) AS age
            FROM trainees t
            ORDER BY t.trainee_training_batch_id, t.name
            LIMIT 100
        ")->fetchAll('assoc');

        // Batch names map
        $batchNameMap = [];
        foreach ($traineeBatches as $b) {
            $batchNameMap[(int)$b['id']] = $b['batch_name'];
        }

        // Upcoming departures
        $upcomingDepartures = $trainingConn->execute("
            SELECT batch_name, departure_plan_date, origin_training_location,
                   DATEDIFF(departure_plan_date, CURDATE()) AS days_left
            FROM cms_tmm_trainee_trainings.trainee_training_batches
            WHERE departure_plan_date >= CURDATE()
            ORDER BY departure_plan_date ASC
            LIMIT 5
        ")->fetchAll('assoc');

        // Eligible candidates → trainee (is_candidate_pass=1, not yet promoted)
        $eligibleCandidateList = $candidateConn->execute("
            SELECT c.id, c.name, c.candidate_code, c.master_gender_id, c.image_photo,
                   c.fitness_score, c.interview_score,
                   TIMESTAMPDIFF(YEAR, c.birth_date, CURDATE()) AS age
            FROM candidates c
            WHERE c.is_candidate_pass = 1
              AND c.status_flag = 'active'
              AND c.id NOT IN (SELECT candidate_id FROM cms_tmm_trainees.trainees WHERE candidate_id IS NOT NULL)
            ORDER BY c.name
            LIMIT 50
        ")->fetchAll('assoc');

        // Eligible trainees → apprentice (is_trainee_pass=1, not yet in apprentices)
        $eligibleTraineeList = $traineeConn->execute("
            SELECT t.id, t.name, t.tmm_code, t.master_gender_id, t.image_photo,
                   t.trainee_training_batch_id, t.grading_remarks,
                   TIMESTAMPDIFF(YEAR, t.birth_date, CURDATE()) AS age
            FROM trainees t
            WHERE t.is_trainee_pass = 1
              AND t.id NOT IN (SELECT trainee_id FROM cms_tmm_apprentices.apprentices WHERE trainee_id IS NOT NULL)
            ORDER BY t.name
            LIMIT 50
        ")->fetchAll('assoc');

        // Apprentice orders for promotion form
        $apprenticeOrders = $traineeConn->execute(
            "SELECT id, title, departure_year, departure_month,
                    male_trainee_number, female_trainee_number
             FROM apprentice_orders ORDER BY id DESC LIMIT 20"
        )->fetchAll('assoc');

        $this->set(compact(
            'traineeStats', 'certCount', 'apprenticeCount', 'eligibleCandidates',
            'batches', 'traineeBatches', 'traineeList', 'batchNameMap', 'upcomingDepartures',
            'eligibleCandidateList', 'eligibleTraineeList', 'apprenticeOrders'
        ));
    }
    
    /**
     * TMM Documentation Dashboard
     *
     * Pre-departure document management for apprentices going to Japan:
     * submission documents, passport/COE-visa/medical records and flight schedules.
     */
    protected function documentationDashboard()
    {
        $this->viewBuilder()->setTemplate('documentation_dashboard');

        $requiredDocumentTypes = (int)$this->fetchScalar('cms_tmm_trainee_documents',
            'SELECT COUNT(*) FROM master_trainee_submission_documents WHERE is_required = 1');
        $statusCounts = $this->getSubmissionStatusCounts();

        $stats = [
            'totalTrainees' => $this->getTotalCount('Trainees', 'cms_tmm_trainees'),
            'totalSubmissions' => $this->getTotalCount('TraineeSubmissionDocuments', 'cms_tmm_trainee_documents'),
            'submittedDocuments' => isset($statusCounts['Submitted']) ? $statusCounts['Submitted'] : 0,
            'pendingDocuments' => isset($statusCounts['Pending']) ? $statusCounts['Pending'] : 0,
            'requiredDocumentTypes' => $requiredDocumentTypes,
            'passportRecords' => $this->getTotalCount('TraineeRecordPasports', 'cms_tmm_trainee_documents'),
            'coeVisaRecords' => $this->getTotalCount('TraineeRecordCoeVisas', 'cms_tmm_trainee_documents'),
            'medicalCheckUps' => $this->getTotalCount('TraineeRecordMedicalCheckUps', 'cms_tmm_trainee_documents'),
            'totalFlights' => (int)$this->fetchScalar('cms_tmm_trainee_document_ticketings',
                'SELECT COUNT(*) FROM trainee_flights'),
        ];

        $documentReadiness = $this->getTraineeDocumentReadiness($requiredDocumentTypes, 8);
        $categoryProgress = $this->getDocumentCategoryProgress(max(1, $stats['totalTrainees']));
        $recentSubmissions = $this->getRecentSubmissionDocuments(8);
        list($flights, $hasUpcomingFlights) = $this->getDepartureFlights(5);

        $this->set(compact('stats', 'documentReadiness', 'categoryProgress',
            'recentSubmissions', 'flights', 'hasUpcomingFlights'));
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
     * Helper: Get count by condition using the model's own default connection
     * (avoids registry option conflicts for tables that define defaultConnectionName)
     */
    protected function getCountWhere($modelName, $conditions)
    {
        try {
            return TableRegistry::getTableLocator()->get($modelName)->find()->where($conditions)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Helper: Fetch a single scalar value with raw SQL on a named connection (fail-soft)
     */
    protected function fetchScalar($connectionName, $sql)
    {
        try {
            $row = ConnectionManager::get($connectionName)->execute($sql)->fetch();
            return $row ? $row[0] : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Helper: Fetch rows with raw SQL on a named connection (fail-soft)
     */
    protected function fetchRows($connectionName, $sql)
    {
        try {
            return ConnectionManager::get($connectionName)->execute($sql)->fetchAll('assoc');
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Helper: Count trainee submission documents grouped by submission status name
     * (e.g. ['Submitted' => 12, 'Pending' => 3])
     */
    protected function getSubmissionStatusCounts()
    {
        $rows = $this->fetchRows('cms_tmm_trainee_documents',
            'SELECT s.name AS status_name, COUNT(*) AS total
             FROM trainee_submission_documents d
             JOIN cms_masters.master_document_submission_statuses s
               ON s.id = d.master_document_submission_status_id
             GROUP BY s.name');

        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['status_name']] = (int)$row['total'];
        }
        return $counts;
    }

    /**
     * Helper: Per-trainee document completion (distinct required documents submitted)
     */
    protected function getTraineeDocumentReadiness($requiredCount, $limit = 8)
    {
        $rows = $this->fetchRows('cms_tmm_trainee_documents',
            'SELECT t.id, t.name, t.tmm_code,
                    COUNT(DISTINCT d.apprenticeship_submission_document_id) AS submitted
             FROM cms_tmm_trainees.trainees t
             LEFT JOIN trainee_submission_documents d ON d.trainee_id = t.id
             GROUP BY t.id, t.name, t.tmm_code
             ORDER BY t.id DESC
             LIMIT ' . (int)$limit);

        $readiness = [];
        foreach ($rows as $row) {
            $submitted = (int)$row['submitted'];
            $percent = $requiredCount > 0 ? min(100, (int)round($submitted / $requiredCount * 100)) : 0;
            $readiness[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'tmm_code' => $row['tmm_code'],
                'submitted' => $submitted,
                'required' => $requiredCount,
                'percent' => $percent,
            ];
        }
        return $readiness;
    }

    /**
     * Helper: Submission progress per document category
     * (expected = document types in category x number of trainees)
     */
    protected function getDocumentCategoryProgress($totalTrainees)
    {
        $rows = $this->fetchRows('cms_tmm_trainee_documents',
            'SELECT c.name,
                    (SELECT COUNT(*) FROM master_trainee_submission_documents m
                      WHERE m.master_trainee_submission_document_category_id = c.id) AS doc_types,
                    (SELECT COUNT(*) FROM trainee_submission_documents d
                       JOIN master_trainee_submission_documents m2
                         ON m2.id = d.apprenticeship_submission_document_id
                      WHERE m2.master_trainee_submission_document_category_id = c.id) AS submitted
             FROM master_trainee_submission_document_categories c
             ORDER BY c.id ASC');

        $progress = [];
        foreach ($rows as $row) {
            $docTypes = (int)$row['doc_types'];
            $expected = $docTypes * $totalTrainees;
            $submitted = (int)$row['submitted'];
            $progress[] = [
                'name' => $row['name'],
                'documentTypes' => $docTypes,
                'expected' => $expected,
                'submitted' => $submitted,
                'percent' => $expected > 0 ? min(100, (int)round($submitted / $expected * 100)) : 0,
            ];
        }
        return $progress;
    }

    /**
     * Helper: Latest trainee submission documents with trainee, document type and status
     */
    protected function getRecentSubmissionDocuments($limit = 8)
    {
        return $this->fetchRows('cms_tmm_trainee_documents',
            'SELECT d.id, d.uploaded_at, d.created,
                    t.name AS trainee_name,
                    m.title AS document_title,
                    s.name AS status_name
             FROM trainee_submission_documents d
             LEFT JOIN master_trainee_submission_documents m
               ON m.id = d.apprenticeship_submission_document_id
             LEFT JOIN cms_masters.master_document_submission_statuses s
               ON s.id = d.master_document_submission_status_id
             LEFT JOIN cms_tmm_trainees.trainees t ON t.id = d.trainee_id
             ORDER BY d.created DESC
             LIMIT ' . (int)$limit);
    }

    /**
     * Helper: Departure flights to Japan with airline and airports.
     * Returns [flights, hasUpcoming] - upcoming flights first, otherwise most recent past flights.
     */
    protected function getDepartureFlights($limit = 5)
    {
        $select = 'SELECT f.id, f.flight_number, f.departure_datetime, f.arrival_datetime,
                          a.name AS airline_name, a.code AS airline_code,
                          dep.code AS departure_airport, dep.cityName AS departure_city,
                          arr.code AS arrival_airport, arr.cityName AS arrival_city
                   FROM trainee_flights f
                   LEFT JOIN master_airlines a ON a.id = f.master_airline_id
                   LEFT JOIN master_airports dep ON dep.id = f.departure_airport_id
                   LEFT JOIN master_airports arr ON arr.id = f.arrival_airport_id';

        $upcoming = $this->fetchRows('cms_tmm_trainee_document_ticketings',
            $select . ' WHERE f.departure_datetime >= NOW()
                        ORDER BY f.departure_datetime ASC
                        LIMIT ' . (int)$limit);
        if (!empty($upcoming)) {
            return [$upcoming, true];
        }

        $recent = $this->fetchRows('cms_tmm_trainee_document_ticketings',
            $select . ' ORDER BY f.departure_datetime DESC
                        LIMIT ' . (int)$limit);
        return [$recent, false];
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
        
        $this->viewBuilder()->setLayout('process_flow');
    }
}
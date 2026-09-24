<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Reports Controller - cross-area analytics and financial reports
 */
class ReportsController extends AppController
{
    /**
     * Reports hub - landing page listing every report with live key figures.
     * (Also the target of generic "Reports" links; previously this action was
     * missing and /reports produced an error.)
     */
    public function index()
    {
        $figures = [
            'revenue' => 0, 'expense' => 0, 'assets' => 0,
            'journals' => 0, 'posted' => 0,
            'candidates' => $this->countOn('Candidates', 'cms_lpk_candidates'),
            'trainees' => $this->countOn('Trainees', 'cms_tmm_trainees'),
            'apprentices' => $this->countOn('Apprentices', 'cms_tmm_apprentices'),
        ];
        try {
            $conn = ConnectionManager::get('cms_tmm_trainee_accountings');
            $row = $conn->execute(
                "SELECT
                    COALESCE(SUM(CASE WHEN LOWER(coa.type) IN ('revenue','income') THEN jd.credit - jd.debit ELSE 0 END), 0) AS revenue,
                    COALESCE(SUM(CASE WHEN LOWER(coa.type) = 'expense' THEN jd.debit - jd.credit ELSE 0 END), 0) AS expense,
                    COALESCE(SUM(CASE WHEN LOWER(coa.type) = 'asset' THEN jd.debit - jd.credit ELSE 0 END), 0) AS assets
                 FROM journal_details jd
                 JOIN chart_of_accounts coa ON coa.id = jd.chart_of_account_id"
            )->fetch('assoc');
            $figures['revenue'] = (float)$row['revenue'];
            $figures['expense'] = (float)$row['expense'];
            $figures['assets'] = (float)$row['assets'];
            $figures['journals'] = (int)$conn->execute('SELECT COUNT(*) FROM journals')->fetch()[0];
            $figures['posted'] = (int)$conn->execute("SELECT COUNT(*) FROM journals WHERE status = 'Posted'")->fetch()[0];
        } catch (\Exception $e) {
        }

        $this->set(compact('figures'));
    }

    /**
     * Candidate pipeline funnel: candidate -> trainee -> apprentice
     */
    public function candidatePipeline()
    {
        $cConn = ConnectionManager::get('cms_lpk_candidates');
        $tConn = ConnectionManager::get('cms_tmm_trainees');
        $trConn = ConnectionManager::get('cms_tmm_trainee_trainings');
        $aConn = ConnectionManager::get('cms_tmm_apprentices');

        // ── Candidates ──────────────────────────────────────────────────
        $cStats = $cConn->execute("
            SELECT
              COUNT(*) AS total,
              SUM(master_gender_id=1) AS male,
              SUM(master_gender_id=2) AS female,
              SUM(is_candidate_pass=1) AS promoted,
              SUM(is_candidate_pass=0 AND status_flag='active') AS active,
              SUM(interview_score > 0) AS interviewed,
              SUM(interview_score >= 60) AS interview_passed,
              ROUND(AVG(NULLIF(interview_score,0)),1) AS avg_score,
              SUM(is_holding_passport=1) AS has_passport,
              SUM(katakana_review_status='approved') AS katakana_approved
            FROM candidates")->fetch('assoc');

        $cByYear = [];

        $cInterviewResults = $cConn->execute("
            SELECT mcir.title AS result, COUNT(*) AS cnt
            FROM candidates c
            LEFT JOIN master_candidate_interview_results mcir ON mcir.id = c.master_candidate_interview_result_id
            WHERE mcir.title IS NOT NULL
            GROUP BY mcir.title
            ORDER BY cnt DESC")->fetchAll('assoc');

        // ── Trainees ─────────────────────────────────────────────────────
        $tStats = $tConn->execute("
            SELECT
              COUNT(*) AS total,
              SUM(master_gender_id=1) AS male,
              SUM(master_gender_id=2) AS female,
              SUM(is_training_pass=1) AS training_passed,
              SUM(is_training_pass=0) AS in_training,
              SUM(is_candidate_pass=1) AS cand_passed,
              COUNT(DISTINCT vocational_training_institution_id) AS lpk_count
            FROM trainees")->fetch('assoc');

        // Training batches
        $batches = $trConn->execute("
            SELECT tb.id, tb.batch_name, tb.batch_code, tb.status, tb.capacity,
              COUNT(e.id) AS enrolled,
              SUM(e.is_passed=1) AS passed_count
            FROM training_batches tb
            LEFT JOIN training_batch_enrollments e ON e.training_batch_id = tb.id
            GROUP BY tb.id
            ORDER BY tb.id DESC
            LIMIT 10")->fetchAll('assoc');

        // ── Apprentices ──────────────────────────────────────────────────
        $aStats = $aConn->execute("
            SELECT
              COUNT(*) AS total,
              SUM(master_gender_id=1) AS male,
              SUM(master_gender_id=2) AS female,
              SUM(is_apprentice_pass=1) AS completed,
              SUM(is_apprenticeship_pass=1) AS in_japan
            FROM apprentices")->fetch('assoc');

        $postTotal = $aConn->execute("SELECT COUNT(*) AS cnt FROM post_apprentices")->fetch('assoc')['cnt'] ?? 0;

        // ── Funnel stages ────────────────────────────────────────────────
        $pipeline = [
            ['stage'=>'Candidates',     'count'=>(int)$cStats['total'],     'icon'=>'fa-users',          'color'=>'#1565c0', 'bg'=>'#e3f2fd'],
            ['stage'=>'Interviewed',    'count'=>(int)$cStats['interviewed'],'icon'=>'fa-comments',       'color'=>'#6a1b9a', 'bg'=>'#f3e5f5'],
            ['stage'=>'Trainees',       'count'=>(int)$tStats['total'],      'icon'=>'fa-chalkboard-teacher','color'=>'#2e7d32','bg'=>'#e8f5e9'],
            ['stage'=>'Apprentices',    'count'=>(int)$aStats['total'],      'icon'=>'fa-plane-departure','color'=>'#e65100', 'bg'=>'#fff3e0'],
            ['stage'=>'Post-Apprentice','count'=>(int)$postTotal,            'icon'=>'fa-user-graduate',  'color'=>'#37474f', 'bg'=>'#eceff1'],
        ];

        $this->set(compact('pipeline','cStats','tStats','aStats','cByYear','cInterviewResults','batches','postTotal'));
    }

    /**
     * Training progress: trainee pass counts and batch overview
     */
    public function trainingProgress()
    {
        $tConn  = ConnectionManager::get('cms_tmm_trainees');
        $trConn = ConnectionManager::get('cms_tmm_trainee_trainings');
        $scConn = ConnectionManager::get('cms_tmm_trainee_training_scorings');

        // Trainee summary
        $progress = $tConn->execute("
            SELECT
              COUNT(*) AS total,
              SUM(is_training_pass=1) AS passed,
              SUM(is_training_pass=0) AS in_training,
              SUM(master_gender_id=1) AS male,
              SUM(master_gender_id=2) AS female,
              COUNT(DISTINCT vocational_training_institution_id) AS lpk_count
            FROM trainees")->fetch('assoc');

        // All trainees with basic info for per-trainee card
        $traineeRows = $tConn->execute("
            SELECT id, name, tmm_code, master_gender_id, is_training_pass, trainee_training_batch_id
            FROM trainees ORDER BY name ASC")->fetchAll('assoc');

        // Score summaries per trainee
        $scoreRows = $scConn->execute("
            SELECT trainee_id, COUNT(*) AS tests,
              ROUND(AVG(score),1) AS avg_score,
              SUM(score>=60) AS passed_tests,
              MAX(score) AS best_score
            FROM trainee_training_test_scores
            GROUP BY trainee_id")->fetchAll('assoc');
        $scoreMap = array_column($scoreRows, null, 'trainee_id');

        // Overall score stats
        $scoreStats = $scConn->execute("
            SELECT COUNT(*) AS total_scores,
              ROUND(AVG(score),1) AS avg_score,
              SUM(score>=60) AS pass_count,
              SUM(score<60) AS fail_count,
              COUNT(DISTINCT trainee_id) AS scored_trainees
            FROM trainee_training_test_scores")->fetch('assoc');

        // Batches with enrollment stats
        $batches = $trConn->execute("
            SELECT tb.id, tb.batch_name, tb.batch_code, tb.training_location, tb.status,
              tb.capacity, tb.enrolled_count, tb.start_date, tb.end_date,
              tb.instructor_name, tb.coordinator_name, tb.duration_months,
              COUNT(e.id) AS actual_enrolled,
              SUM(e.is_passed=1) AS passed_count,
              ROUND(AVG(NULLIF(e.final_score,0)),1) AS avg_score
            FROM training_batches tb
            LEFT JOIN training_batch_enrollments e ON e.training_batch_id = tb.id
            GROUP BY tb.id ORDER BY tb.id DESC LIMIT 20")->fetchAll('assoc');

        $this->set(compact('progress','batches','traineeRows','scoreMap','scoreStats'));
    }

    /**
     * Active apprentices listing
     */
    public function activeApprentices()
    {
        $conn = ConnectionManager::get('cms_tmm_apprentices');

        // Summary stats
        $stats = $conn->execute("
            SELECT
              COUNT(*) AS total,
              SUM(is_apprenticeship_pass=1) AS in_japan,
              SUM(is_apprentice_pass=1)     AS completed,
              SUM(master_gender_id=1)       AS male,
              SUM(master_gender_id=2)       AS female,
              COUNT(DISTINCT acceptance_organization_id)       AS companies,
              COUNT(DISTINCT vocational_training_institution_id) AS lpks
            FROM apprentices")->fetch('assoc');

        $postTotal = (int)($conn->execute("SELECT COUNT(*) AS c FROM post_apprentices")->fetch('assoc')['c'] ?? 0);

        // Enriched apprentice list (vti is in cms_tmm_stakeholders, cross-join with full DB name)
        //
        // The orders join used to read cms_tmm_apprentices.apprentice_orders.
        // Two tables carry that name, in two databases, with different columns
        // - and apprentices.apprentice_order_id holds ids from the OTHER one:
        // every path that writes the column takes its values from
        // ApprenticeOrdersTable, which reads cms_tmm_trainees, and the
        // existsIn rule on ApprenticesTable enforces it. So this report was
        // joining ids against a table whose ids mean something else. Both had
        // four rows numbered 1 to 4, so the join matched and quietly showed
        // the wrong order against every apprentice.
        //
        // The trainees copy records a departure year and month rather than a
        // date, so the date below is the first of that month and the screen
        // shows it to month precision. It carries no status column at all,
        // which is why one is no longer selected: an order's status was never
        // recorded on the table this id points at, and showing the other
        // table's status was the bug, not a feature worth keeping.
        $apprentices = $conn->execute("
            SELECT a.id, a.name, a.tmm_code, a.email, a.telephone_mobile,
              a.master_gender_id, a.is_apprentice_pass, a.is_apprenticeship_pass,
              a.name_katakana, a.birth_date, a.image_photo,
              a.link_whatsapp, a.strengths,
              ao.title AS order_title,
              CONCAT(ao.departure_year, '-', LPAD(ao.departure_month, 2, '0'), '-01') AS departure_date,
              NULL AS order_status,
              vti.name AS vti_name
            FROM cms_tmm_apprentices.apprentices a
            LEFT JOIN cms_tmm_trainees.apprentice_orders ao ON ao.id = a.apprentice_order_id
            LEFT JOIN cms_tmm_stakeholders.vocational_training_institutions vti ON vti.id = a.vocational_training_institution_id
            ORDER BY a.is_apprenticeship_pass DESC, a.name ASC
            LIMIT 500")->fetchAll('assoc');

        // Orders summary
        $orders = $conn->execute("
            SELECT ao.title,
                   CONCAT(ao.departure_year, '-', LPAD(ao.departure_month, 2, '0'), '-01') AS departure_date,
                   NULL AS status,
                   COUNT(a.id) AS count
            FROM cms_tmm_trainees.apprentice_orders ao
            LEFT JOIN cms_tmm_apprentices.apprentices a ON a.apprentice_order_id = ao.id
            GROUP BY ao.id, ao.title, ao.departure_year, ao.departure_month
            ORDER BY ao.departure_year DESC, ao.departure_month DESC LIMIT 10")->fetchAll('assoc');

        $this->set(compact('apprentices','stats','postTotal','orders'));
    }

    /**
     * Income statement: revenue vs expense account balances
     */
    public function incomeStatement()
    {
        $rows = $this->accountBalances(['revenue', 'income', 'expense']);
        $this->set(compact('rows'));
        $this->set('excluded', $this->excludedEntries());
        $this->set('countedStatus', self::COUNTED_STATUS);
        $this->set('reportTitle', 'Income Statement');
        $this->render('financial_report');
    }

    /**
     * Balance sheet: asset / liability / equity balances
     */
    public function balanceSheet()
    {
        $rows = $this->accountBalances(['asset', 'liability', 'equity']);
        $this->set(compact('rows'));
        $this->set('excluded', $this->excludedEntries());
        $this->set('countedStatus', self::COUNTED_STATUS);
        $this->set('reportTitle', 'Balance Sheet');
        $this->render('financial_report');
    }

    /**
     * Cash flow: journal totals by month
     */
    public function cashFlow()
    {
        $conn = ConnectionManager::get('cms_tmm_trainee_accountings');
        $months = $conn->execute(
            "SELECT DATE_FORMAT(transaction_date, '%Y-%m') AS month,
                    COUNT(*) AS entries,
                    COALESCE(SUM(total_debit), 0) AS total_debit,
                    COALESCE(SUM(total_credit), 0) AS total_credit
             FROM journals
             WHERE status = ?
             GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
             ORDER BY month DESC",
            [self::COUNTED_STATUS]
        )->fetchAll('assoc');
        $this->set(compact('months'));
        $this->set('excluded', $this->excludedEntries());
        $this->set('countedStatus', self::COUNTED_STATUS);
    }

    /**
     * Helper: count rows on a model via its connection
     */
    protected function countOn($model, $connection)
    {
        try {
            $conn = ConnectionManager::get($connection);
            return TableRegistry::getTableLocator()->get($model, ['connection' => $conn])->find()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Helper: debit/credit balance per account, filtered by account type
     */
    /**
     * The one status the financial reports count. Everything else is left out.
     */
    const COUNTED_STATUS = 'Posted';

    /**
     * Per-account totals for the financial reports, from posted entries only.
     *
     * This used to sum every journal_details row there was, whatever the
     * entry's status. A journal marked Void still counted, and so did one
     * still in Draft - marking an entry Void removed it from nothing, which
     * made the income statement and the balance sheet quietly wrong in the one
     * way an accountant would never expect.
     *
     * The status is applied inside the sums rather than as a WHERE, so an
     * account whose only lines are voided still appears - at zero - instead of
     * vanishing from the report altogether. An account that has dropped off a
     * statement is much harder to notice than one showing nothing.
     *
     * @param array $types Account types to include.
     * @return array
     */
    protected function accountBalances(array $types)
    {
        $conn = ConnectionManager::get('cms_tmm_trainee_accountings');
        $placeholders = implode(',', array_fill(0, count($types), '?'));

        return $conn->execute(
            "SELECT coa.code, coa.name, coa.type,
                    COALESCE(SUM(CASE WHEN j.status = ? THEN jd.debit  ELSE 0 END), 0) AS total_debit,
                    COALESCE(SUM(CASE WHEN j.status = ? THEN jd.credit ELSE 0 END), 0) AS total_credit,
                    COALESCE(SUM(CASE WHEN j.status = ? THEN jd.debit  ELSE 0 END), 0)
                  - COALESCE(SUM(CASE WHEN j.status = ? THEN jd.credit ELSE 0 END), 0) AS balance
             FROM chart_of_accounts coa
             LEFT JOIN journal_details jd ON jd.chart_of_account_id = coa.id
             LEFT JOIN journals j ON j.id = jd.journal_id
             WHERE LOWER(coa.type) IN ($placeholders)
             GROUP BY coa.id, coa.code, coa.name, coa.type
             ORDER BY coa.code",
            array_merge(array_fill(0, 4, self::COUNTED_STATUS), array_map('strtolower', $types))
        )->fetchAll('assoc');
    }

    /**
     * How many entries the reports are leaving out, and under what status.
     *
     * A number that changed because the rule behind it changed is worse than
     * a wrong number: nobody can tell the difference without being told. Every
     * financial report therefore says what it counted and what it did not.
     *
     * @return array [status => count] for everything that is not counted.
     */
    protected function excludedEntries()
    {
        try {
            $rows = ConnectionManager::get('cms_tmm_trainee_accountings')->execute(
                'SELECT status, COUNT(*) AS total
                 FROM journals
                 WHERE status IS NULL OR status <> ?
                 GROUP BY status',
                [self::COUNTED_STATUS]
            )->fetchAll('assoc');
        } catch (\Exception $e) {
            return [];
        }

        $excluded = [];
        foreach ($rows as $row) {
            $label = ($row['status'] === null || $row['status'] === '') ? __('no status') : $row['status'];
            $excluded[$label] = (int)$row['total'];
        }

        return $excluded;
    }

    /**
     * The guide behind the "?" button.
     *
     * The page itself is config/page_guides/Reports.php, rendered by
     * Element/page_guide.ctp. The language switch writes the choice to the
     * session so the guide can be read in Indonesian, English or Japanese
     * without changing the language of the whole application first.
     *
     * @return \Cake\Http\Response|null
     */
    public function processFlow()
    {
        if ($lang = $this->request->getQuery('lang')) {
            if (in_array($lang, ['ind', 'eng', 'jpn'], true)) {
                $this->request->getSession()->write('Config.language', $lang);

                return $this->redirect(['action' => 'processFlow']);
            }
        }
    }
}

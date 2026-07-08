<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

/**
 * LpkCandidateScoring Controller - integrated per-candidate scoring dashboard
 * combining placement tests, interviews and medical check-ups
 */
class LpkCandidateScoringController extends AppController
{
    public function index()
    {
        $conn = ConnectionManager::get('cms_lpk_candidates');
        $rows = $conn->execute(
            "SELECT c.id, c.candidate_code, c.name,
                    pt.tests, pt.avg_test_score, pt.passed_tests,
                    iv.interviews, iv.avg_overall_score,
                    mcu.mcu_count, mcu.avg_mcu_score
             FROM candidates c
             LEFT JOIN (
                 SELECT candidate_id, COUNT(*) AS tests, ROUND(AVG(score), 1) AS avg_test_score,
                        SUM(is_passed = 1) AS passed_tests
                 FROM candidate_record_placement_tests GROUP BY candidate_id
             ) pt ON pt.candidate_id = c.id
             LEFT JOIN (
                 SELECT applicant_id, COUNT(*) AS interviews, ROUND(AVG(overall_score), 1) AS avg_overall_score
                 FROM candidate_record_interviews GROUP BY applicant_id
             ) iv ON iv.applicant_id = c.id
             LEFT JOIN (
                 SELECT applicant_id, COUNT(*) AS mcu_count, ROUND(AVG(final_score), 1) AS avg_mcu_score
                 FROM candidate_record_medical_check_ups GROUP BY applicant_id
             ) mcu ON mcu.applicant_id = c.id
             ORDER BY c.id DESC
             LIMIT 500"
        )->fetchAll('assoc');
        $this->set(compact('rows'));
    }
}

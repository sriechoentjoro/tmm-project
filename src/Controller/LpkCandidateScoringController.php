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
            "SELECT c.id, c.candidate_code, c.name, c.fitness_score,
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

        // Ringkasan kesiapan pipeline: lengkap = punya placement test + interview + MCU
        $stats = [
            'total' => count($rows), 'complete' => 0, 'partial' => 0, 'none' => 0,
            'avg_test' => null, 'avg_interview' => null, 'avg_mcu' => null,
        ];
        $sumT = $sumI = $sumM = $nT = $nI = $nM = 0;
        foreach ($rows as $r) {
            $have = (int)!empty($r['tests']) + (int)!empty($r['interviews']) + (int)!empty($r['mcu_count']);
            if ($have === 3) $stats['complete']++;
            elseif ($have === 0) $stats['none']++;
            else $stats['partial']++;
            if ($r['avg_test_score'] !== null)    { $sumT += $r['avg_test_score'];    $nT++; }
            if ($r['avg_overall_score'] !== null) { $sumI += $r['avg_overall_score']; $nI++; }
            if ($r['avg_mcu_score'] !== null)     { $sumM += $r['avg_mcu_score'];     $nM++; }
        }
        if ($nT) $stats['avg_test']      = round($sumT / $nT, 1);
        if ($nI) $stats['avg_interview'] = round($sumI / $nI, 1);
        if ($nM) $stats['avg_mcu']       = round($sumM / $nM, 1);

        $this->set(compact('rows', 'stats'));
    }
}

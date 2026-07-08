<?php
/**
 * @var \App\View\AppView $this
 * @var array $progress
 * @var array $batches
 * @var array $traineeRows
 * @var array $scoreMap     [trainee_id => [tests, avg_score, passed_tests, best_score]]
 * @var array $scoreStats
 */

$passRate = $progress['total'] > 0 ? round($progress['passed'] / $progress['total'] * 100) : 0;
$today    = date('d M Y');

function tpGrade($avg) {
    if ($avg === null || $avg == 0) return ['—', '#90a4ae', '#f5f7fa'];
    if ($avg >= 85) return ['A', '#1b5e20', '#e8f5e9'];
    if ($avg >= 70) return ['B', '#1565c0', '#e3f2fd'];
    if ($avg >= 60) return ['C', '#e65100', '#fff3e0'];
    return                ['D', '#b71c1c', '#fce4ec'];
}

$batchStatusMeta = [
    'recruiting' => ['bg'=>'#e3f2fd','color'=>'#1565c0','dot'=>'#1e88e5','label'=>'Recruiting'],
    'planning'   => ['bg'=>'#fff8e1','color'=>'#e65100','dot'=>'#fb8c00','label'=>'Planning'],
    'active'     => ['bg'=>'#e8f5e9','color'=>'#2e7d32','dot'=>'#43a047','label'=>'Active'],
    'completed'  => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','dot'=>'#8e24aa','label'=>'Completed'],
];
?>
<style>
.tp-hero{background:linear-gradient(135deg,#1b5e20 0%,#2e7d32 50%,#388e3c 100%);color:#fff;border-radius:16px;padding:26px 30px;margin-bottom:20px;box-shadow:0 4px 24px rgba(27,94,32,.3)}
.tp-hero-title{font-size:22px;font-weight:800;margin-bottom:3px;display:flex;align-items:center;gap:10px}
.tp-hero-sub{font-size:13px;opacity:.8}
.tp-kpi-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.tp-kpi{background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:10px 18px;text-align:center;min-width:88px}
.tp-kpi-val{font-size:22px;font-weight:800;line-height:1}
.tp-kpi-lbl{font-size:10px;font-weight:600;opacity:.7;text-transform:uppercase;letter-spacing:.5px;margin-top:2px}

.tp-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px}
.tp-grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
@media(max-width:768px){.tp-grid,.tp-grid-3{grid-template-columns:1fr}}

.tp-card{background:#fff;border:1px solid #e1e8f0;border-radius:12px;overflow:hidden}
.tp-card-hdr{padding:12px 18px;border-bottom:1px solid #f0f4f8;background:#fafbfc;display:flex;align-items:center;justify-content:space-between}
.tp-card-title{font-size:14px;font-weight:800;color:#1a2332;display:flex;align-items:center;gap:7px;margin:0}
.tp-card-body{padding:18px}
.tp-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}

/* Pass rate arc */
.tp-donut{position:relative;width:100px;height:100px;flex-shrink:0}
.tp-donut svg{transform:rotate(-90deg)}
.tp-donut-inner{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1}
.tp-donut-pct{font-size:20px;font-weight:800}
.tp-donut-sub{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#90a4ae;margin-top:2px}

/* Stat tiles */
.tp-stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.tp-stat{background:#f8fafb;border-radius:9px;padding:12px 14px}
.tp-stat-val{font-size:20px;font-weight:800;line-height:1}
.tp-stat-lbl{font-size:10px;font-weight:600;color:#90a4ae;text-transform:uppercase;letter-spacing:.5px;margin-top:3px}

/* Gender bar */
.tp-gender-bar{height:7px;border-radius:4px;overflow:hidden;display:flex;margin-top:6px}
.tp-gm{background:#1e88e5}.tp-gf{background:#e91e63}

/* Score histogram */
.tp-score-row{display:flex;align-items:center;gap:8px;margin-bottom:6px}
.tp-score-lbl{font-size:11px;font-weight:700;color:#546e7a;width:28px;text-align:right}
.tp-score-bar-wrap{flex:1;background:#f0f4f8;border-radius:3px;height:8px;overflow:hidden}
.tp-score-bar{height:100%;border-radius:3px}
.tp-score-cnt{font-size:10px;font-weight:700;color:#78909c;width:20px}

/* Trainee cards */
.tp-trainee-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:12px}
.tp-trainee-card{border:1px solid #e1e8f0;border-radius:10px;overflow:hidden;background:#fff;transition:box-shadow .15s}
.tp-trainee-card:hover{box-shadow:0 4px 14px rgba(0,0,0,.1)}
.tp-tc-top{padding:14px 14px 10px;display:flex;align-items:center;gap:10px}
.tp-avatar{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;flex-shrink:0}
.tp-tc-name{font-size:13px;font-weight:800;color:#1a2332;line-height:1.2}
.tp-tc-code{font-size:10px;font-weight:700;color:#90a4ae;font-family:monospace}
.tp-tc-body{padding:0 14px 12px;display:grid;grid-template-columns:1fr 1fr;gap:6px}
.tp-tc-stat{background:#f8fafb;border-radius:7px;padding:7px 10px}
.tp-tc-stat-val{font-size:16px;font-weight:800;line-height:1}
.tp-tc-stat-lbl{font-size:9px;font-weight:700;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px;margin-top:1px}
.tp-tc-foot{padding:8px 14px;border-top:1px solid #f0f4f8;background:#fafbfc;display:flex;align-items:center;justify-content:space-between}

/* Batch table */
.tp-tbl{width:100%;border-collapse:collapse;font-size:12px}
.tp-tbl th{padding:9px 12px;background:#f5f7fa;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#78909c;border-bottom:1px solid #e1e8f0;text-align:left;white-space:nowrap}
.tp-tbl td{padding:9px 12px;border-bottom:1px solid #f5f7fa;vertical-align:middle}
.tp-tbl tr:last-child td{border-bottom:none}
.tp-tbl tr:hover td{background:#f8fafb}
.tp-pill{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:10px;font-size:10px;font-weight:800}
.tp-dot{width:6px;height:6px;border-radius:50%;display:inline-block}

.tp-section-lbl{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#90a4ae;margin:20px 0 10px;display:flex;align-items:center;gap:8px}
.tp-section-lbl::after{content:'';flex:1;height:1px;background:#e1e8f0}
</style>

<!-- Hero -->
<div class="tp-hero">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div>
      <div class="tp-hero-title"><i class="fas fa-chart-line"></i> Training Progress</div>
      <div class="tp-hero-sub">Trainee performance overview and batch status · <?= $today ?></div>
    </div>
    <div style="font-size:11px;opacity:.65;text-align:right;line-height:1.7">
      <div><i class="fas fa-user-graduate"></i> <?= $progress['total'] ?> trainees total</div>
      <div><i class="fas fa-star"></i> <?= $scoreStats['avg_score'] ?? '—' ?> average test score</div>
    </div>
  </div>
  <div class="tp-kpi-row">
    <div class="tp-kpi"><div class="tp-kpi-val"><?= $progress['total'] ?></div><div class="tp-kpi-lbl">Trainees</div></div>
    <div class="tp-kpi"><div class="tp-kpi-val"><?= $progress['passed'] ?></div><div class="tp-kpi-lbl">Passed</div></div>
    <div class="tp-kpi"><div class="tp-kpi-val"><?= $progress['in_training'] ?></div><div class="tp-kpi-lbl">In Training</div></div>
    <div class="tp-kpi"><div class="tp-kpi-val"><?= $passRate ?>%</div><div class="tp-kpi-lbl">Pass Rate</div></div>
    <div class="tp-kpi"><div class="tp-kpi-val"><?= $scoreStats['total_scores'] ?? 0 ?></div><div class="tp-kpi-lbl">Tests Taken</div></div>
    <div class="tp-kpi"><div class="tp-kpi-val"><?= $scoreStats['avg_score'] ?? '—' ?></div><div class="tp-kpi-lbl">Avg Score</div></div>
    <div class="tp-kpi"><div class="tp-kpi-val"><?= count($batches) ?></div><div class="tp-kpi-lbl">Batches</div></div>
  </div>
</div>

<!-- Pass rate + Score stats -->
<div class="tp-grid">

  <!-- Pass Rate card -->
  <div class="tp-card">
    <div class="tp-card-hdr">
      <h3 class="tp-card-title"><i class="fas fa-user-graduate" style="color:#2e7d32"></i> Trainee Overview</h3>
      <span class="tp-badge" style="background:#e8f5e9;color:#2e7d32"><?= $progress['total'] ?> total</span>
    </div>
    <div class="tp-card-body">
      <div style="display:flex;align-items:center;gap:20px;margin-bottom:18px">
        <?php
          $r = 44; $circ = 2*M_PI*$r;
          $dash = round($circ * $passRate / 100, 1);
        ?>
        <div class="tp-donut">
          <svg width="100" height="100" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="<?= $r ?>" fill="none" stroke="#f0f4f8" stroke-width="11"/>
            <circle cx="50" cy="50" r="<?= $r ?>" fill="none" stroke="#43a047" stroke-width="11"
                    stroke-dasharray="<?= $dash ?> <?= round($circ-$dash,1) ?>" stroke-linecap="round"/>
          </svg>
          <div class="tp-donut-inner">
            <div class="tp-donut-pct" style="color:#2e7d32"><?= $passRate ?>%</div>
            <div class="tp-donut-sub">pass rate</div>
          </div>
        </div>
        <div style="flex:1">
          <div style="margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:700;margin-bottom:4px">
              <span style="color:#2e7d32"><i class="fas fa-check-circle"></i> Passed</span>
              <span><?= $progress['passed'] ?> / <?= $progress['total'] ?></span>
            </div>
            <div style="height:8px;background:#f0f4f8;border-radius:4px;overflow:hidden">
              <div style="height:100%;width:<?= $passRate ?>%;background:linear-gradient(90deg,#66bb6a,#43a047);border-radius:4px"></div>
            </div>
          </div>
          <div>
            <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:700;margin-bottom:4px">
              <span style="color:#e65100"><i class="fas fa-clock"></i> In Training</span>
              <span><?= $progress['in_training'] ?> / <?= $progress['total'] ?></span>
            </div>
            <div style="height:8px;background:#f0f4f8;border-radius:4px;overflow:hidden">
              <div style="height:100%;width:<?= $progress['total']>0?round($progress['in_training']/$progress['total']*100):0 ?>%;background:linear-gradient(90deg,#ffa726,#fb8c00);border-radius:4px"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gender split -->
      <div style="margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;font-size:11px;font-weight:700;margin-bottom:4px">
          <span style="color:#78909c"><i class="fas fa-venus-mars"></i> Gender</span>
          <span><span style="color:#1e88e5"><?= $progress['male'] ?>M</span> · <span style="color:#e91e63"><?= $progress['female'] ?>F</span></span>
        </div>
        <?php $mPct = $progress['total']>0 ? round($progress['male']/$progress['total']*100) : 50; ?>
        <div class="tp-gender-bar">
          <div class="tp-gm" style="width:<?= $mPct ?>%"></div>
          <div class="tp-gf" style="width:<?= 100-$mPct ?>%"></div>
        </div>
      </div>

      <div class="tp-stat-grid">
        <div class="tp-stat">
          <div class="tp-stat-val" style="color:#1565c0"><?= $progress['lpk_count'] ?></div>
          <div class="tp-stat-lbl">LPK Partners</div>
        </div>
        <div class="tp-stat">
          <div class="tp-stat-val" style="color:#6a1b9a"><?= $scoreStats['scored_trainees'] ?? 0 ?></div>
          <div class="tp-stat-lbl">Scored</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Test score stats -->
  <div class="tp-card">
    <div class="tp-card-hdr">
      <h3 class="tp-card-title"><i class="fas fa-clipboard-check" style="color:#1565c0"></i> Test Score Analysis</h3>
      <span class="tp-badge" style="background:#e3f2fd;color:#1565c0"><?= $scoreStats['total_scores'] ?? 0 ?> tests</span>
    </div>
    <div class="tp-card-body">
      <?php
        $totalScores = (int)($scoreStats['total_scores'] ?? 0);
        $passCount   = (int)($scoreStats['pass_count']   ?? 0);
        $failCount   = (int)($scoreStats['fail_count']   ?? 0);
        $avgScore    = (float)($scoreStats['avg_score']  ?? 0);
        $passScoreRate = $totalScores > 0 ? round($passCount/$totalScores*100) : 0;
        [$gradeLabel,$gradeColor,$gradeBg] = tpGrade($avgScore);

        // Donut for score pass rate
        $r2 = 44; $circ2 = 2*M_PI*$r2;
        $dash2 = round($circ2*$passScoreRate/100,1);
      ?>
      <div style="display:flex;align-items:center;gap:20px;margin-bottom:18px">
        <div class="tp-donut">
          <svg width="100" height="100" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="<?= $r2 ?>" fill="none" stroke="#f0f4f8" stroke-width="11"/>
            <circle cx="50" cy="50" r="<?= $r2 ?>" fill="none" stroke="#1e88e5" stroke-width="11"
                    stroke-dasharray="<?= $dash2 ?> <?= round($circ2-$dash2,1) ?>" stroke-linecap="round"/>
          </svg>
          <div class="tp-donut-inner">
            <div class="tp-donut-pct" style="color:#1565c0"><?= $passScoreRate ?>%</div>
            <div class="tp-donut-sub">tests ≥60</div>
          </div>
        </div>
        <div style="flex:1">
          <div style="display:flex;gap:12px;margin-bottom:12px;flex-wrap:wrap">
            <div style="text-align:center">
              <div style="font-size:28px;font-weight:800;line-height:1;color:<?= $gradeColor ?>"><?= $gradeLabel ?></div>
              <div style="font-size:10px;font-weight:700;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px">Grade</div>
            </div>
            <div style="text-align:center">
              <div style="font-size:28px;font-weight:800;line-height:1;color:#37474f"><?= $avgScore ?: '—' ?></div>
              <div style="font-size:10px;font-weight:700;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px">Avg Score</div>
            </div>
          </div>
          <div style="font-size:12px;color:#546e7a;line-height:2">
            <div style="display:flex;justify-content:space-between">
              <span><i class="fas fa-check" style="color:#43a047"></i> Passed (≥60)</span>
              <strong style="color:#2e7d32"><?= $passCount ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between">
              <span><i class="fas fa-times" style="color:#e53935"></i> Failed (&lt;60)</span>
              <strong style="color:#b71c1c"><?= $failCount ?></strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Score range breakdown per trainee -->
      <?php if (!empty($scoreMap)):
        $ranges = ['90–100'=>[90,100],'70–89'=>[70,89],'60–69'=>[60,69],'<60'=>[0,59]];
        $rangeCounts = [];
        foreach ($scoreMap as $sm) {
            $a = (float)$sm['avg_score'];
            if ($a>=90) $rangeCounts['90–100'] = ($rangeCounts['90–100']??0)+1;
            elseif ($a>=70) $rangeCounts['70–89'] = ($rangeCounts['70–89']??0)+1;
            elseif ($a>=60) $rangeCounts['60–69'] = ($rangeCounts['60–69']??0)+1;
            else $rangeCounts['<60'] = ($rangeCounts['<60']??0)+1;
        }
        $rangeColors = ['90–100'=>'#43a047','70–89'=>'#1e88e5','60–69'=>'#fb8c00','<60'=>'#e53935'];
        $maxRange = max(1,...array_values($rangeCounts));
      ?>
      <div style="padding-top:14px;border-top:1px solid #f0f4f8">
        <div style="font-size:11px;font-weight:700;color:#78909c;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Trainee avg score distribution</div>
        <?php foreach ($ranges as $label => $bounds):
          $cnt = $rangeCounts[$label] ?? 0;
          $w   = $maxRange>0 ? round($cnt/$maxRange*100) : 0;
          $col = $rangeColors[$label];
        ?>
        <div class="tp-score-row">
          <div class="tp-score-lbl" style="color:<?= $col ?>;font-size:10px"><?= $label ?></div>
          <div class="tp-score-bar-wrap">
            <div class="tp-score-bar" style="width:<?= $w ?>%;background:<?= $col ?>"></div>
          </div>
          <div class="tp-score-cnt"><?= $cnt ?></div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Per-Trainee Cards -->
<?php if (!empty($traineeRows)): ?>
<div class="tp-section-lbl"><i class="fas fa-id-card"></i> Trainee Performance</div>
<div class="tp-trainee-grid">
  <?php foreach ($traineeRows as $tr):
    $score = $scoreMap[$tr['id']] ?? null;
    $avgS  = $score ? (float)$score['avg_score'] : null;
    [$gl,$gc,$gbg] = tpGrade($avgS);
    $isPassed = (bool)$tr['is_training_pass'];
    $avatarColor = $tr['master_gender_id']==2 ? '#ad1457' : '#1565c0';
    $initial = mb_strtoupper(mb_substr($tr['name'],0,1));
    $testPassRate = ($score && $score['tests']>0) ? round($score['passed_tests']/$score['tests']*100) : 0;
  ?>
  <div class="tp-trainee-card">
    <div class="tp-tc-top">
      <div class="tp-avatar" style="background:<?= $avatarColor ?>"><?= $initial ?></div>
      <div style="flex:1;min-width:0">
        <div class="tp-tc-name"><?= h($tr['name']) ?></div>
        <div class="tp-tc-code"><?= h($tr['tmm_code']) ?></div>
      </div>
      <div style="text-align:center;flex-shrink:0">
        <div style="font-size:20px;font-weight:800;color:<?= $gc ?>;line-height:1"><?= $gl ?></div>
        <div style="font-size:9px;font-weight:700;color:#90a4ae;text-transform:uppercase">grade</div>
      </div>
    </div>
    <div class="tp-tc-body">
      <div class="tp-tc-stat">
        <div class="tp-tc-stat-val" style="color:#1565c0"><?= $score ? $score['tests'] : 0 ?></div>
        <div class="tp-tc-stat-lbl">Tests</div>
      </div>
      <div class="tp-tc-stat">
        <div class="tp-tc-stat-val" style="color:#37474f"><?= $avgS ? $avgS : '—' ?></div>
        <div class="tp-tc-stat-lbl">Avg Score</div>
      </div>
      <div class="tp-tc-stat">
        <div class="tp-tc-stat-val" style="color:#2e7d32"><?= $score ? $score['passed_tests'] : 0 ?></div>
        <div class="tp-tc-stat-lbl">Passed</div>
      </div>
      <div class="tp-tc-stat">
        <div class="tp-tc-stat-val" style="color:#e65100"><?= $score ? $score['best_score'] : '—' ?></div>
        <div class="tp-tc-stat-lbl">Best</div>
      </div>
    </div>
    <?php if ($score && $score['tests'] > 0): ?>
    <div style="padding:0 14px 10px">
      <div style="display:flex;justify-content:space-between;font-size:10px;font-weight:700;color:#90a4ae;margin-bottom:3px">
        <span>Test pass rate</span><span><?= $testPassRate ?>%</span>
      </div>
      <div style="height:5px;background:#f0f4f8;border-radius:3px;overflow:hidden">
        <div style="height:100%;width:<?= $testPassRate ?>%;background:<?= $gc ?>;border-radius:3px"></div>
      </div>
    </div>
    <?php endif; ?>
    <div class="tp-tc-foot">
      <span class="tp-pill" style="background:<?= $isPassed?'#e8f5e9':'#fff8e1' ?>;color:<?= $isPassed?'#2e7d32':'#e65100' ?>">
        <span class="tp-dot" style="background:<?= $isPassed?'#43a047':'#fb8c00' ?>"></span>
        <?= $isPassed ? 'Training Passed' : 'In Training' ?>
      </span>
      <?= $this->Html->link('<i class="fas fa-eye"></i>', ['controller'=>'Trainees','action'=>'view',$tr['id']], ['escape'=>false,'title'=>'View','style'=>'padding:4px 8px;border-radius:6px;background:#e3f2fd;color:#1565c0;font-size:11px;text-decoration:none']) ?>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Training Batches -->
<div class="tp-section-lbl" style="margin-top:22px"><i class="fas fa-layer-group"></i> Training Batches</div>
<div class="tp-card">
  <div class="tp-card-hdr">
    <h3 class="tp-card-title"><i class="fas fa-layer-group" style="color:#1565c0"></i> All Batches</h3>
    <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
       style="font-size:11px;font-weight:700;color:#1565c0;text-decoration:none">Manage →</a>
  </div>
  <?php if (empty($batches)): ?>
    <div style="text-align:center;padding:40px 20px;color:#90a4ae;font-size:13px">
      <i class="fas fa-layer-group" style="font-size:32px;opacity:.3;display:block;margin-bottom:10px"></i>
      No training batches yet.
    </div>
  <?php else: ?>
  <table class="tp-tbl">
    <thead>
      <tr>
        <th>Batch</th><th>Location</th><th>Duration</th><th>Status</th>
        <th>Capacity</th><th>Enrolled</th><th>Fill</th><th>Instructor</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($batches as $b):
        $sm   = $batchStatusMeta[$b['status']] ?? ['bg'=>'#f5f7fa','color'=>'#78909c','dot'=>'#90a4ae','label'=>ucfirst($b['status'])];
        $fill = (int)$b['capacity'] > 0 ? round($b['actual_enrolled']/$b['capacity']*100) : 0;
        $fillColor = $fill>=80 ? '#43a047' : ($fill>=50 ? '#1e88e5' : '#fb8c00');
        $sd = !empty($b['start_date']) ? (new \DateTime($b['start_date']))->format('d M Y') : '—';
        $ed = !empty($b['end_date'])   ? (new \DateTime($b['end_date']))->format('d M Y')   : '—';
      ?>
      <tr>
        <td>
          <div style="font-weight:700;color:#1a2332;font-size:13px"><?= h($b['batch_name']) ?></div>
          <div style="font-size:10px;font-family:monospace;color:#90a4ae"><?= h($b['batch_code']) ?></div>
          <div style="font-size:10px;color:#b0bec5;margin-top:1px"><?= $sd ?> → <?= $ed ?></div>
        </td>
        <td style="font-size:12px;color:#546e7a"><?= h($b['training_location'] ?: '—') ?></td>
        <td style="font-size:12px;font-weight:700;color:#37474f"><?= $b['duration_months'] ?> mo</td>
        <td>
          <span class="tp-pill" style="background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>">
            <span class="tp-dot" style="background:<?= $sm['dot'] ?>"></span><?= $sm['label'] ?>
          </span>
        </td>
        <td style="font-weight:700;text-align:center"><?= $b['capacity'] ?></td>
        <td style="font-weight:700;color:#1565c0;text-align:center"><?= $b['actual_enrolled'] ?></td>
        <td style="min-width:80px">
          <div style="display:flex;align-items:center;gap:5px">
            <div style="flex:1;background:#f0f4f8;border-radius:3px;height:7px;overflow:hidden">
              <div style="height:100%;width:<?= min(100,$fill) ?>%;background:<?= $fillColor ?>;border-radius:3px"></div>
            </div>
            <span style="font-size:10px;font-weight:700;color:#546e7a"><?= $fill ?>%</span>
          </div>
        </td>
        <td style="font-size:11px;color:#78909c"><?= h($b['instructor_name'] ?: '—') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

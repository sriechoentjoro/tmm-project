<?php
/**
 * @var \App\View\AppView $this
 * @var array $pipeline
 * @var array $cStats
 * @var array $tStats
 * @var array $aStats
 * @var array $cByYear
 * @var array $cInterviewResults
 * @var array $batches
 * @var int   $postTotal
 */

$convRate = fn($a,$b) => $b > 0 ? round($a / $b * 100) : 0;
$today = date('d M Y');

$batchStatusMeta = [
    'recruiting' => ['bg'=>'#e3f2fd','color'=>'#1565c0','dot'=>'#1e88e5','label'=>'Recruiting'],
    'planning'   => ['bg'=>'#fff8e1','color'=>'#e65100','dot'=>'#fb8c00','label'=>'Planning'],
    'active'     => ['bg'=>'#e8f5e9','color'=>'#2e7d32','dot'=>'#43a047','label'=>'Active'],
    'completed'  => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','dot'=>'#8e24aa','label'=>'Completed'],
];
?>
<style>
/* ── Layout ── */
.cp-hero{background:linear-gradient(135deg,#0d47a1 0%,#1565c0 45%,#1976d2 100%);color:#fff;border-radius:16px;padding:28px 32px;margin-bottom:22px;box-shadow:0 4px 24px rgba(13,71,161,.3)}
.cp-hero-title{font-size:23px;font-weight:800;margin-bottom:3px;display:flex;align-items:center;gap:10px}
.cp-hero-sub{font-size:13px;opacity:.8}
.cp-kpi-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:18px}
.cp-kpi{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:11px 18px;text-align:center;min-width:90px}
.cp-kpi-val{font-size:22px;font-weight:800;line-height:1}
.cp-kpi-lbl{font-size:10px;font-weight:600;opacity:.7;text-transform:uppercase;letter-spacing:.5px;margin-top:2px}

/* ── Grid ── */
.cp-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px}
.cp-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:18px}
@media(max-width:768px){.cp-grid,.cp-grid-3{grid-template-columns:1fr}}

/* ── Cards ── */
.cp-card{background:#fff;border:1px solid #e1e8f0;border-radius:12px;overflow:hidden}
.cp-card-hdr{padding:13px 18px;border-bottom:1px solid #f0f4f8;display:flex;align-items:center;justify-content:space-between;background:#fafbfc}
.cp-card-title{font-size:14px;font-weight:800;color:#1a2332;display:flex;align-items:center;gap:7px;margin:0}
.cp-card-body{padding:18px}
.cp-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}

/* ── Funnel ── */
.cp-funnel{display:flex;flex-direction:column;gap:10px;padding:18px}
.cp-funnel-stage{position:relative}
.cp-funnel-bar-wrap{background:#f0f4f8;border-radius:8px;overflow:hidden;height:44px;position:relative}
.cp-funnel-bar{height:100%;border-radius:8px;display:flex;align-items:center;padding:0 14px;transition:width .6s cubic-bezier(.4,0,.2,1)}
.cp-funnel-label{position:absolute;left:14px;top:0;bottom:0;display:flex;align-items:center;gap:8px;font-size:12px;font-weight:700;color:#fff;z-index:1;pointer-events:none}
.cp-funnel-count{position:absolute;right:12px;top:0;bottom:0;display:flex;align-items:center;font-size:12px;font-weight:800;z-index:1}
.cp-funnel-arrow{text-align:center;color:#c5cdd8;font-size:11px;font-weight:700;letter-spacing:.5px;padding:2px 0}
.cp-conv{font-size:10px;font-weight:700;padding:2px 8px;border-radius:8px;background:#fff3e0;color:#e65100;margin-top:4px;display:inline-block}

/* ── Stats grid ── */
.cp-stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.cp-stat{background:#f8fafb;border-radius:9px;padding:12px 14px;display:flex;flex-direction:column}
.cp-stat-val{font-size:20px;font-weight:800;line-height:1;color:#1a2332}
.cp-stat-lbl{font-size:10px;font-weight:600;color:#90a4ae;text-transform:uppercase;letter-spacing:.5px;margin-top:3px}
.cp-stat-sub{font-size:11px;color:#78909c;margin-top:2px}

/* ── Gender pill ── */
.cp-gender-bar{height:8px;border-radius:4px;overflow:hidden;display:flex;margin-top:8px}
.cp-gender-m{background:#1e88e5}
.cp-gender-f{background:#e91e63}

/* ── Year chart ── */
.cp-bar-chart{display:flex;align-items:flex-end;gap:5px;height:80px;padding:0 4px}
.cp-year-bar{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px}
.cp-year-bar-fill{width:100%;background:linear-gradient(180deg,#42a5f5,#1e88e5);border-radius:4px 4px 0 0;min-height:3px;transition:height .4s}
.cp-year-lbl{font-size:9px;font-weight:700;color:#90a4ae;white-space:nowrap}
.cp-year-val{font-size:9px;font-weight:800;color:#1565c0}

/* ── Interview results ── */
.cp-result-row{display:flex;align-items:center;gap:10px;margin-bottom:8px}
.cp-result-label{font-size:12px;font-weight:600;color:#37474f;min-width:120px}
.cp-result-bar-wrap{flex:1;background:#f0f4f8;border-radius:4px;height:10px;overflow:hidden}
.cp-result-bar{height:100%;border-radius:4px;background:linear-gradient(90deg,#42a5f5,#1e88e5)}
.cp-result-count{font-size:11px;font-weight:700;color:#546e7a;min-width:28px;text-align:right}

/* ── Batch table ── */
.cp-tbl{width:100%;border-collapse:collapse;font-size:12px}
.cp-tbl th{padding:8px 12px;background:#f5f7fa;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#78909c;border-bottom:1px solid #e1e8f0;text-align:left}
.cp-tbl td{padding:9px 12px;border-bottom:1px solid #f5f7fa;vertical-align:middle;color:#37474f}
.cp-tbl tr:last-child td{border-bottom:none}
.cp-tbl tr:hover td{background:#f8fafb}
.cp-dot{width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:4px}

/* ── Progress arc ── */
.cp-donut{position:relative;width:90px;height:90px;flex-shrink:0}
.cp-donut svg{transform:rotate(-90deg)}
.cp-donut-pct{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#1565c0;line-height:1}
.cp-donut-sub{font-size:9px;font-weight:600;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px}

/* ── Divider label ── */
.cp-section-label{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#90a4ae;margin:20px 0 10px;display:flex;align-items:center;gap:8px}
.cp-section-label::after{content:'';flex:1;height:1px;background:#e1e8f0}
</style>

<!-- Hero -->
<div class="cp-hero">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div>
      <div class="cp-hero-title"><i class="fas fa-filter"></i> Candidate Pipeline</div>
      <div class="cp-hero-sub">End-to-end view of recruitment to post-apprenticeship · As of <?= $today ?></div>
    </div>
    <div style="font-size:11px;opacity:.6;text-align:right;line-height:1.6">
      <div><i class="fas fa-users"></i> <?= number_format($cStats['total']) ?> total candidates</div>
      <div><i class="fas fa-arrow-right"></i> <?= $convRate($tStats['total'],$cStats['total']) ?>% overall conversion</div>
    </div>
  </div>
  <div class="cp-kpi-row">
    <div class="cp-kpi"><div class="cp-kpi-val"><?= number_format($cStats['total']) ?></div><div class="cp-kpi-lbl">Candidates</div></div>
    <div class="cp-kpi"><div class="cp-kpi-val"><?= number_format($cStats['interviewed']) ?></div><div class="cp-kpi-lbl">Interviewed</div></div>
    <div class="cp-kpi"><div class="cp-kpi-val"><?= number_format($tStats['total']) ?></div><div class="cp-kpi-lbl">Trainees</div></div>
    <div class="cp-kpi"><div class="cp-kpi-val"><?= number_format($aStats['total']) ?></div><div class="cp-kpi-lbl">Apprentices</div></div>
    <div class="cp-kpi"><div class="cp-kpi-val"><?= number_format($postTotal) ?></div><div class="cp-kpi-lbl">Post-App.</div></div>
    <div class="cp-kpi"><div class="cp-kpi-val"><?= $convRate($tStats['total'],$cStats['total']) ?>%</div><div class="cp-kpi-lbl">Conv. Rate</div></div>
  </div>
</div>

<!-- Funnel + Candidate Stats -->
<div class="cp-grid">

  <!-- Funnel -->
  <div class="cp-card">
    <div class="cp-card-hdr">
      <h3 class="cp-card-title"><i class="fas fa-filter" style="color:#1565c0"></i> Pipeline Funnel</h3>
      <span class="cp-badge" style="background:#e3f2fd;color:#1565c0"><?= count($pipeline) ?> stages</span>
    </div>
    <div class="cp-funnel">
      <?php
      $maxCount = max(1, ...array_column($pipeline, 'count'));
      foreach ($pipeline as $i => $stage):
        $pct = round($stage['count'] / $maxCount * 100);
        $pct = max($pct, 4);
        $prev = $i > 0 ? (int)$pipeline[$i-1]['count'] : 0;
        $conv = $i > 0 && $prev > 0 ? round($stage['count'] / $prev * 100) : null;
      ?>
      <div class="cp-funnel-stage">
        <div class="cp-funnel-bar-wrap">
          <div class="cp-funnel-bar" style="width:<?= $pct ?>%;background:<?= $stage['color'] ?>">
            <div class="cp-funnel-label">
              <i class="fas <?= $stage['icon'] ?>"></i> <?= h($stage['stage']) ?>
            </div>
          </div>
          <div class="cp-funnel-count" style="color:<?= $pct < 60 ? $stage['color'] : '#fff' ?>">
            <?= number_format($stage['count']) ?>
          </div>
        </div>
        <?php if ($conv !== null): ?>
        <div style="display:flex;align-items:center;gap:6px;margin-top:3px;padding-left:6px">
          <i class="fas fa-level-down-alt" style="font-size:9px;color:#c5cdd8"></i>
          <span class="cp-conv"><?= $conv ?>% conversion from <?= h($pipeline[$i-1]['stage']) ?></span>
        </div>
        <?php endif; ?>
      </div>
      <?php if ($i < count($pipeline)-1): ?>
      <div class="cp-funnel-arrow"><i class="fas fa-chevron-down"></i></div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Candidate Detail -->
  <div class="cp-card">
    <div class="cp-card-hdr">
      <h3 class="cp-card-title"><i class="fas fa-users" style="color:#1565c0"></i> Candidate Breakdown</h3>
      <span class="cp-badge" style="background:#e3f2fd;color:#1565c0"><?= number_format($cStats['total']) ?> total</span>
    </div>
    <div class="cp-card-body">

      <!-- Gender bar -->
      <div style="margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;margin-bottom:5px">
          <span style="font-size:12px;font-weight:700;color:#37474f"><i class="fas fa-venus-mars" style="color:#78909c"></i> Gender</span>
          <div style="display:flex;gap:12px;font-size:11px;font-weight:700">
            <span style="color:#1e88e5"><i class="fas fa-male"></i> <?= $cStats['male'] ?> M</span>
            <span style="color:#e91e63"><i class="fas fa-female"></i> <?= $cStats['female'] ?> F</span>
            <?php $unk = $cStats['total'] - $cStats['male'] - $cStats['female']; if ($unk > 0): ?>
            <span style="color:#90a4ae"><?= $unk ?> —</span>
            <?php endif; ?>
          </div>
        </div>
        <?php $mPct = $convRate($cStats['male'], $cStats['total']); ?>
        <div class="cp-gender-bar">
          <div class="cp-gender-m" style="width:<?= $mPct ?>%"></div>
          <div class="cp-gender-f" style="width:<?= 100-$mPct ?>%"></div>
        </div>
      </div>

      <div class="cp-stat-grid">
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#1565c0"><?= number_format($cStats['active']) ?></div>
          <div class="cp-stat-lbl">Active</div>
          <div class="cp-stat-sub">In recruitment process</div>
        </div>
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#2e7d32"><?= number_format($cStats['promoted']) ?></div>
          <div class="cp-stat-lbl">Promoted</div>
          <div class="cp-stat-sub">Moved to trainee</div>
        </div>
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#6a1b9a"><?= number_format($cStats['interviewed']) ?></div>
          <div class="cp-stat-lbl">Interviewed</div>
          <div class="cp-stat-sub"><?= $convRate($cStats['interviewed'],$cStats['total']) ?>% of total</div>
        </div>
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#e65100"><?= $cStats['avg_score'] ?? '—' ?></div>
          <div class="cp-stat-lbl">Avg Score</div>
          <div class="cp-stat-sub">Interview average</div>
        </div>
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#37474f"><?= number_format($cStats['has_passport']) ?></div>
          <div class="cp-stat-lbl">Has Passport</div>
          <div class="cp-stat-sub"><?= $convRate($cStats['has_passport'],$cStats['total']) ?>% ready</div>
        </div>
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#00838f"><?= number_format($cStats['katakana_approved']) ?></div>
          <div class="cp-stat-lbl">Katakana ✓</div>
          <div class="cp-stat-sub">Approved katakana</div>
        </div>
      </div>

      <!-- Registration trend by year -->
      <?php if (!empty($cByYear)): ?>
      <div style="margin-top:16px">
        <div style="font-size:11px;font-weight:700;color:#78909c;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px">
          <i class="fas fa-chart-bar"></i> Registrations by Year
        </div>
        <?php
          $maxYr = max(1, ...array_column($cByYear, 'cnt'));
        ?>
        <div class="cp-bar-chart">
          <?php foreach ($cByYear as $row):
            $h = round($row['cnt'] / $maxYr * 72);
          ?>
          <div class="cp-year-bar">
            <div class="cp-year-val"><?= $row['cnt'] ?></div>
            <div class="cp-year-bar-fill" style="height:<?= max(3,$h) ?>px"></div>
            <div class="cp-year-lbl"><?= $row['yr'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Interview Results + Trainee + Apprentice -->
<div class="cp-grid-3">

  <!-- Interview Results -->
  <div class="cp-card">
    <div class="cp-card-hdr">
      <h3 class="cp-card-title"><i class="fas fa-comments" style="color:#6a1b9a"></i> Interview Results</h3>
      <span class="cp-badge" style="background:#f3e5f5;color:#6a1b9a"><?= number_format($cStats['interviewed']) ?> done</span>
    </div>
    <div class="cp-card-body">
      <?php if (empty($cInterviewResults)): ?>
        <div style="text-align:center;padding:20px;color:#90a4ae;font-size:12px">No interview data yet</div>
      <?php else:
        $maxRes = max(1, ...array_column($cInterviewResults,'cnt'));
        foreach ($cInterviewResults as $r):
      ?>
      <div class="cp-result-row">
        <div class="cp-result-label"><?= h($r['result']) ?></div>
        <div class="cp-result-bar-wrap">
          <div class="cp-result-bar" style="width:<?= round($r['cnt']/$maxRes*100) ?>%;background:linear-gradient(90deg,#ce93d8,#8e24aa)"></div>
        </div>
        <div class="cp-result-count"><?= $r['cnt'] ?></div>
      </div>
      <?php endforeach; endif; ?>

      <div style="margin-top:16px;padding-top:14px;border-top:1px solid #f0f4f8">
        <div style="font-size:11px;font-weight:700;color:#78909c;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px">Score Distribution</div>
        <div style="display:flex;gap:10px;align-items:center">
          <?php
            $interviewPassRate = $convRate($cStats['interview_passed'], max(1,$cStats['interviewed']));
            $r = 40;
            $circ = 2 * M_PI * $r;
            $dash = round($circ * $interviewPassRate / 100, 1);
          ?>
          <div class="cp-donut">
            <svg width="90" height="90" viewBox="0 0 90 90">
              <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#f0f4f8" stroke-width="10"/>
              <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#8e24aa" stroke-width="10"
                      stroke-dasharray="<?= $dash ?> <?= round($circ-$dash,1) ?>" stroke-linecap="round"/>
            </svg>
            <div class="cp-donut-pct">
              <?= $interviewPassRate ?>%
              <div class="cp-donut-sub">pass</div>
            </div>
          </div>
          <div>
            <div style="font-size:12px;color:#37474f;line-height:1.8">
              <div><span style="font-weight:800;color:#8e24aa"><?= $cStats['interview_passed'] ?></span> scored ≥60</div>
              <div><span style="font-weight:800;color:#37474f"><?= $cStats['interviewed'] - $cStats['interview_passed'] ?></span> scored &lt;60</div>
              <div><span style="font-weight:800;color:#90a4ae"><?= $cStats['avg_score'] ?? '—' ?></span> avg score</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Trainee Stats -->
  <div class="cp-card">
    <div class="cp-card-hdr">
      <h3 class="cp-card-title"><i class="fas fa-chalkboard-teacher" style="color:#2e7d32"></i> Trainees</h3>
      <span class="cp-badge" style="background:#e8f5e9;color:#2e7d32"><?= $tStats['total'] ?> total</span>
    </div>
    <div class="cp-card-body">
      <?php
        $tPassRate = $convRate($tStats['training_passed'], max(1,$tStats['total']));
        $r = 40; $circ = 2*M_PI*$r;
        $dash = round($circ*$tPassRate/100,1);
      ?>
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
        <div class="cp-donut">
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#f0f4f8" stroke-width="10"/>
            <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#43a047" stroke-width="10"
                    stroke-dasharray="<?= $dash ?> <?= round($circ-$dash,1) ?>" stroke-linecap="round"/>
          </svg>
          <div class="cp-donut-pct" style="color:#2e7d32">
            <?= $tPassRate ?>%
            <div class="cp-donut-sub">pass rate</div>
          </div>
        </div>
        <div style="flex:1">
          <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
            <span style="font-weight:700;color:#37474f">Training passed</span>
            <span style="font-weight:800;color:#2e7d32"><?= $tStats['training_passed'] ?></span>
          </div>
          <div style="height:7px;background:#f0f4f8;border-radius:4px;overflow:hidden;margin-bottom:10px">
            <div style="height:100%;width:<?= $tPassRate ?>%;background:#43a047;border-radius:4px"></div>
          </div>
          <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:5px">
            <span style="font-weight:700;color:#37474f">In training</span>
            <span style="font-weight:800;color:#e65100"><?= $tStats['in_training'] ?></span>
          </div>
          <div style="height:7px;background:#f0f4f8;border-radius:4px;overflow:hidden">
            <div style="height:100%;width:<?= $convRate($tStats['in_training'],max(1,$tStats['total'])) ?>%;background:#fb8c00;border-radius:4px"></div>
          </div>
        </div>
      </div>

      <!-- Gender -->
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:11px;font-weight:700">
          <span style="color:#78909c">Gender split</span>
          <span><span style="color:#1e88e5"><?= $tStats['male'] ?>M</span> / <span style="color:#e91e63"><?= $tStats['female'] ?>F</span></span>
        </div>
        <?php $mPct = $convRate($tStats['male'],max(1,$tStats['total'])); ?>
        <div class="cp-gender-bar">
          <div class="cp-gender-m" style="width:<?= $mPct ?>%"></div>
          <div class="cp-gender-f" style="width:<?= 100-$mPct ?>%"></div>
        </div>
      </div>

      <div class="cp-stat-grid">
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#2e7d32"><?= $tStats['total'] ?></div>
          <div class="cp-stat-lbl">Total</div>
        </div>
        <div class="cp-stat">
          <div class="cp-stat-val" style="color:#1565c0"><?= $tStats['lpk_count'] ?></div>
          <div class="cp-stat-lbl">LPK Partners</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Apprentice Stats -->
  <div class="cp-card">
    <div class="cp-card-hdr">
      <h3 class="cp-card-title"><i class="fas fa-plane-departure" style="color:#e65100"></i> Apprentices</h3>
      <span class="cp-badge" style="background:#fff3e0;color:#e65100"><?= $aStats['total'] ?> total</span>
    </div>
    <div class="cp-card-body">
      <?php
        $aComplRate = $convRate($aStats['completed'],max(1,$aStats['total']));
        $r = 40; $circ = 2*M_PI*$r;
        $dash = round($circ*$aComplRate/100,1);
      ?>
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px">
        <div class="cp-donut">
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#f0f4f8" stroke-width="10"/>
            <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#ef6c00" stroke-width="10"
                    stroke-dasharray="<?= $dash ?> <?= round($circ-$dash,1) ?>" stroke-linecap="round"/>
          </svg>
          <div class="cp-donut-pct" style="color:#e65100">
            <?= $aComplRate ?>%
            <div class="cp-donut-sub">complete</div>
          </div>
        </div>
        <div style="flex:1">
          <div style="font-size:12px;color:#37474f;line-height:2">
            <div style="display:flex;justify-content:space-between">
              <span><i class="fas fa-circle" style="color:#1e88e5;font-size:8px"></i> In Japan</span>
              <strong style="color:#1565c0"><?= $aStats['in_japan'] ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between">
              <span><i class="fas fa-circle" style="color:#43a047;font-size:8px"></i> Completed</span>
              <strong style="color:#2e7d32"><?= $aStats['completed'] ?></strong>
            </div>
            <div style="display:flex;justify-content:space-between">
              <span><i class="fas fa-circle" style="color:#90a4ae;font-size:8px"></i> Active</span>
              <strong style="color:#546e7a"><?= $aStats['total'] - $aStats['completed'] ?></strong>
            </div>
          </div>
        </div>
      </div>

      <!-- Gender -->
      <div style="margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:11px;font-weight:700">
          <span style="color:#78909c">Gender split</span>
          <span><span style="color:#1e88e5"><?= $aStats['male'] ?>M</span> / <span style="color:#e91e63"><?= $aStats['female'] ?>F</span></span>
        </div>
        <?php $mPct = $convRate($aStats['male'],max(1,$aStats['total'])); ?>
        <div class="cp-gender-bar">
          <div class="cp-gender-m" style="width:<?= $mPct ?>%"></div>
          <div class="cp-gender-f" style="width:<?= 100-$mPct ?>%"></div>
        </div>
      </div>

      <div style="padding-top:10px;border-top:1px solid #f0f4f8">
        <div style="font-size:11px;font-weight:700;color:#78909c;margin-bottom:6px">Post-Apprenticeship</div>
        <div style="display:flex;align-items:center;gap:10px">
          <div style="width:36px;height:36px;border-radius:9px;background:#eceff1;display:flex;align-items:center;justify-content:center">
            <i class="fas fa-user-graduate" style="color:#546e7a"></i>
          </div>
          <div>
            <div style="font-size:18px;font-weight:800;color:#37474f;line-height:1"><?= $postTotal ?></div>
            <div style="font-size:10px;font-weight:600;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px">Alumni</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Training Batches -->
<?php if (!empty($batches)): ?>
<div class="cp-section-label"><i class="fas fa-layer-group"></i> Training Batches</div>
<div class="cp-card">
  <div class="cp-card-hdr">
    <h3 class="cp-card-title"><i class="fas fa-layer-group" style="color:#1565c0"></i> Active Training Batches</h3>
    <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
       style="font-size:11px;font-weight:700;color:#1565c0;text-decoration:none">View all →</a>
  </div>
  <table class="cp-tbl">
    <thead>
      <tr>
        <th>Batch</th><th>Status</th><th>Capacity</th><th>Enrolled</th><th>Fill Rate</th><th>Passed</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($batches as $b):
        $sm = $batchStatusMeta[$b['status']] ?? ['bg'=>'#f5f7fa','color'=>'#78909c','dot'=>'#90a4ae','label'=>ucfirst($b['status'])];
        $fill = $b['capacity'] > 0 ? round($b['enrolled']/$b['capacity']*100) : 0;
      ?>
      <tr>
        <td>
          <div style="font-weight:700;color:#1a2332"><?= h($b['batch_name']) ?></div>
          <div style="font-size:10px;font-family:monospace;color:#90a4ae"><?= h($b['batch_code']) ?></div>
        </td>
        <td>
          <span style="display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:10px;font-size:10px;font-weight:800;background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>">
            <span class="cp-dot" style="background:<?= $sm['dot'] ?>"></span><?= $sm['label'] ?>
          </span>
        </td>
        <td style="font-weight:700"><?= $b['capacity'] ?></td>
        <td style="font-weight:700;color:#1565c0"><?= $b['enrolled'] ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:6px">
            <div style="flex:1;background:#f0f4f8;border-radius:4px;height:8px;overflow:hidden;min-width:60px">
              <div style="height:100%;width:<?= min(100,$fill) ?>%;background:<?= $fill>=80?'#43a047':($fill>=50?'#1e88e5':'#fb8c00') ?>;border-radius:4px"></div>
            </div>
            <span style="font-size:11px;font-weight:700;color:#546e7a"><?= $fill ?>%</span>
          </div>
        </td>
        <td>
          <?php if ($b['enrolled'] > 0): ?>
          <span style="font-weight:800;color:#2e7d32"><?= $b['passed_count'] ?></span>
          <span style="font-size:10px;color:#90a4ae"> / <?= $b['enrolled'] ?></span>
          <?php else: ?>
          <span style="color:#c5cdd8">—</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Quick nav -->
<div class="cp-section-label" style="margin-top:20px"><i class="fas fa-link"></i> Quick Navigation</div>
<div style="display:flex;gap:10px;flex-wrap:wrap">
  <?php $links = [
    ['controller'=>'Candidates',  'action'=>'index', 'icon'=>'fa-users',            'label'=>'All Candidates',  'color'=>'#1565c0','bg'=>'#e3f2fd'],
    ['controller'=>'Trainees',    'action'=>'index', 'icon'=>'fa-chalkboard-teacher','label'=>'All Trainees',    'color'=>'#2e7d32','bg'=>'#e8f5e9'],
    ['controller'=>'Trainings',   'action'=>'index', 'icon'=>'fa-layer-group',       'label'=>'Training Batches','color'=>'#6a1b9a','bg'=>'#f3e5f5'],
    ['controller'=>'Apprentices', 'action'=>'index', 'icon'=>'fa-plane-departure',   'label'=>'Apprentices',     'color'=>'#e65100','bg'=>'#fff3e0'],
    ['controller'=>'Reports',     'action'=>'trainingProgress','icon'=>'fa-chart-line','label'=>'Training Report','color'=>'#00838f','bg'=>'#e0f7fa'],
  ];
  foreach ($links as $l): ?>
    <?= $this->Html->link(
      '<i class="fas '.$l['icon'].'" style="font-size:13px"></i> '.$l['label'],
      ['controller'=>$l['controller'],'action'=>$l['action']],
      ['escape'=>false,'style'=>'display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:700;background:'.$l['bg'].';color:'.$l['color'].';text-decoration:none;border:1px solid '.$l['color'].'33;transition:all .15s']
    ) ?>
  <?php endforeach; ?>
</div>

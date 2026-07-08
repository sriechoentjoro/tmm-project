<?php
$total      = (int)($traineeStats['total'] ?? 0);
$inTraining = (int)($traineeStats['in_training'] ?? 0);
$passed     = (int)($traineeStats['passed'] ?? 0);
$male       = (int)($traineeStats['male'] ?? 0);
$female     = (int)($traineeStats['female'] ?? 0);
$passRate   = $total > 0 ? round($passed / $total * 100) : 0;
$today      = date('Y-m-d');

$statusColor = [
    'planning'   => '#90a4ae',
    'recruiting' => '#42a5f5',
    'ongoing'    => '#26c6da',
    'completed'  => '#66bb6a',
    'cancelled'  => '#ef5350',
];
$statusLabel = [
    'planning'   => 'Planning',
    'recruiting' => 'Recruiting',
    'ongoing'    => 'Ongoing',
    'completed'  => 'Completed',
    'cancelled'  => 'Cancelled',
];
?>
<style>
/* ── Dashboard base ── */
.tdb-wrap { font-family: 'Segoe UI', sans-serif; color: #263238; padding: 0 4px 40px; }
.tdb-header { display:flex; align-items:center; gap:14px; margin-bottom:28px; padding-bottom:16px; border-bottom:2px solid #e0f7fa; }
.tdb-header .tdb-logo { width:44px; height:44px; background:linear-gradient(135deg,#00bcd4,#006064); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px; }
.tdb-header h1 { margin:0; font-size:20px; font-weight:700; color:#006064; }
.tdb-header p { margin:2px 0 0; font-size:12px; color:#78909c; }
.tdb-date-badge { margin-left:auto; background:#e0f7fa; color:#006064; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:600; }

/* ── KPI cards ── */
.tdb-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:28px; }
.kpi { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 2px 8px rgba(0,0,0,.07); border-top:4px solid var(--kc); position:relative; overflow:hidden; transition:transform .15s; }
.kpi:hover { transform:translateY(-2px); }
.kpi::after { content:''; position:absolute; right:-12px; top:-12px; width:64px; height:64px; background:var(--kc); opacity:.08; border-radius:50%; }
.kpi .kpi-icon { font-size:22px; margin-bottom:8px; }
.kpi .kpi-val { font-size:32px; font-weight:800; color:var(--kc); line-height:1; }
.kpi .kpi-label { font-size:11px; color:#78909c; margin-top:4px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
.kpi .kpi-sub { font-size:10px; color:#90a4ae; margin-top:3px; }

/* ── Section titles ── */
.tdb-section { margin-bottom:24px; }
.tdb-section-title { font-size:13px; font-weight:700; color:#455a64; text-transform:uppercase; letter-spacing:.7px; margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.tdb-section-title::after { content:''; flex:1; height:1px; background:#e0f7fa; }

/* ── Two-column layout ── */
.tdb-cols { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:24px; }
@media(max-width:900px){ .tdb-cols { grid-template-columns:1fr; } }

/* ── Cards container ── */
.tdb-card { background:#fff; border-radius:14px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,.07); height:100%; box-sizing:border-box; }
.tdb-card h3 { margin:0 0 14px; font-size:13px; font-weight:700; color:#37474f; display:flex; align-items:center; gap:8px; }

/* ── Pipeline funnel ── */
.pipeline { display:flex; gap:0; align-items:stretch; }
.pipe-step { flex:1; text-align:center; padding:16px 8px; position:relative; }
.pipe-step::after { content:'▶'; position:absolute; right:-10px; top:50%; transform:translateY(-50%); color:#b0bec5; font-size:16px; z-index:1; }
.pipe-step:last-child::after { display:none; }
.pipe-step .pipe-n { font-size:30px; font-weight:800; color:var(--pc); }
.pipe-step .pipe-l { font-size:11px; color:#78909c; font-weight:600; text-transform:uppercase; margin-top:4px; }
.pipe-step .pipe-sub { font-size:10px; color:#90a4ae; margin-top:2px; }
.pipe-step { background:linear-gradient(135deg,rgba(0,188,212,.06),rgba(0,188,212,.02)); border-radius:10px; margin:0 4px; }

/* ── Batch table ── */
.batch-table { width:100%; border-collapse:collapse; font-size:12px; }
.batch-table th { background:#f5f5f5; color:#546e7a; text-transform:uppercase; font-size:10px; padding:7px 10px; text-align:left; font-weight:700; }
.batch-table td { padding:9px 10px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.batch-table tr:hover td { background:#f9fbe7; }
.batch-status { display:inline-block; padding:3px 9px; border-radius:20px; font-size:10px; font-weight:700; color:#fff; text-transform:uppercase; letter-spacing:.3px; }

/* ── Capacity bar ── */
.cap-bar { height:6px; background:#eceff1; border-radius:3px; margin-top:4px; }
.cap-fill { height:6px; background:var(--bc); border-radius:3px; transition:width .5s; }
.cap-txt { font-size:10px; color:#78909c; }

/* ── Trainee cards ── */
.trainee-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:12px; }
.trainee-card { background:#fff; border-radius:12px; padding:14px 16px; box-shadow:0 2px 6px rgba(0,0,0,.06); border-left:4px solid var(--tc); display:flex; gap:12px; align-items:center; transition:box-shadow .15s; }
.trainee-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.1); }
.trainee-avatar { width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,var(--tc),color-mix(in srgb,var(--tc) 60%,#000)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:16px; flex-shrink:0; overflow:hidden; }
.trainee-avatar img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.trainee-info .ti-name { font-size:13px; font-weight:700; color:#263238; }
.trainee-info .ti-code { font-size:10px; color:#90a4ae; font-weight:600; }
.trainee-info .ti-batch { font-size:10px; color:#78909c; margin-top:3px; }
.trainee-info .ti-status { display:inline-block; margin-top:4px; padding:2px 8px; border-radius:20px; font-size:9px; font-weight:700; text-transform:uppercase; }
.ti-pass { background:#e8f5e9; color:#388e3c; }
.ti-progress { background:#e3f2fd; color:#1565c0; }

/* ── Departure timeline ── */
.depart-list { list-style:none; padding:0; margin:0; }
.depart-item { display:flex; gap:12px; align-items:flex-start; padding:10px 0; border-bottom:1px solid #f0f0f0; }
.depart-item:last-child { border-bottom:none; }
.depart-days { min-width:48px; text-align:center; }
.depart-days .dd-n { font-size:20px; font-weight:800; color:#00acc1; line-height:1; }
.depart-days .dd-l { font-size:9px; color:#90a4ae; text-transform:uppercase; }
.depart-info .di-batch { font-size:12px; font-weight:700; color:#263238; }
.depart-info .di-loc { font-size:11px; color:#78909c; }
.depart-info .di-date { font-size:10px; color:#90a4ae; }

/* ── Gender donut ── */
.gender-chart { display:flex; align-items:center; gap:20px; }
.donut-wrap { position:relative; width:80px; height:80px; }
.donut-wrap svg { transform:rotate(-90deg); }
.donut-center { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; }
.donut-center .dc-n { font-size:18px; font-weight:800; color:#263238; }
.donut-center .dc-l { font-size:9px; color:#90a4ae; }
.gender-legend { display:flex; flex-direction:column; gap:8px; }
.gl-item { display:flex; align-items:center; gap:8px; font-size:12px; }
.gl-dot { width:10px; height:10px; border-radius:50%; }

/* ── Quick actions ── */
.quick-actions { display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px; }
.qa-btn { display:flex; flex-direction:column; align-items:center; gap:6px; padding:14px 10px; border-radius:12px; background:linear-gradient(135deg,var(--qc),color-mix(in srgb,var(--qc) 80%,#000)); color:#fff; text-decoration:none; font-size:11px; font-weight:700; text-align:center; transition:transform .15s,box-shadow .15s; box-shadow:0 2px 6px rgba(0,0,0,.15); }
.qa-btn:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,.2); color:#fff; text-decoration:none; }
.qa-btn i { font-size:20px; }

/* ── Training batch cards ── */
.batch-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:14px; }
.batch-card { background:#fff; border-radius:14px; padding:16px 18px; box-shadow:0 2px 8px rgba(0,0,0,.07); border-top:3px solid var(--bc); }
.bc-head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:12px; }
.bc-code { font-size:11px; color:#90a4ae; font-weight:600; }
.bc-name { font-size:13px; font-weight:700; color:#263238; margin-top:2px; }
.bc-instructor { font-size:11px; color:#78909c; }
.bc-meta { display:flex; gap:12px; margin-top:10px; flex-wrap:wrap; }
.bc-meta-item { font-size:11px; color:#546e7a; display:flex; align-items:center; gap:4px; }
.bc-progress { margin-top:12px; }
.bc-days-badge { padding:4px 10px; border-radius:20px; font-size:10px; font-weight:700; }
.bc-days-soon { background:#fff9c4; color:#f57f17; }
.bc-days-ok { background:#e0f7fa; color:#006064; }
.bc-days-past { background:#fce4ec; color:#b71c1c; }
</style>

<div class="tdb-wrap">

  <!-- Header -->
  <div class="tdb-header">
    <div class="tdb-logo"><i class="fas fa-graduation-cap"></i></div>
    <div>
      <h1>Training Dashboard</h1>
      <p>TMM Training Division — Program Overview &amp; KPIs</p>
    </div>
    <div class="tdb-date-badge"><i class="fas fa-calendar-day"></i> <?= date('d M Y') ?></div>
  </div>

  <!-- ── KPI Row ── -->
  <div class="tdb-kpis">
    <div class="kpi" style="--kc:#00acc1">
      <div class="kpi-icon">👤</div>
      <div class="kpi-val"><?= $total ?></div>
      <div class="kpi-label">Total Trainees</div>
      <div class="kpi-sub"><?= $male ?>M / <?= $female ?>F</div>
    </div>
    <div class="kpi" style="--kc:#1e88e5">
      <div class="kpi-icon">🎓</div>
      <div class="kpi-val"><?= $inTraining ?></div>
      <div class="kpi-label">In Training</div>
      <div class="kpi-sub">Currently enrolled</div>
    </div>
    <div class="kpi" style="--kc:#43a047">
      <div class="kpi-icon">✅</div>
      <div class="kpi-val"><?= $passed ?></div>
      <div class="kpi-label">Passed</div>
      <div class="kpi-sub"><?= $passRate ?>% pass rate</div>
    </div>
    <div class="kpi" style="--kc:#f4511e">
      <div class="kpi-icon">📋</div>
      <div class="kpi-val"><?= count($batches) + count($traineeBatches) ?></div>
      <div class="kpi-label">Batches Total</div>
      <div class="kpi-sub"><?= count($batches) ?> new + <?= count($traineeBatches) ?> legacy</div>
    </div>
    <div class="kpi" style="--kc:#8e24aa">
      <div class="kpi-icon">🏅</div>
      <div class="kpi-val"><?= $certCount ?></div>
      <div class="kpi-label">Certificates</div>
      <div class="kpi-sub">Issued to date</div>
    </div>
    <div class="kpi" style="--kc:#fb8c00">
      <div class="kpi-icon">🚀</div>
      <div class="kpi-val"><?= $apprenticeCount ?></div>
      <div class="kpi-label">Apprentices</div>
      <div class="kpi-sub">Promoted out</div>
    </div>
    <div class="kpi" style="--kc:#00897b">
      <div class="kpi-icon">🌟</div>
      <div class="kpi-val"><?= $eligibleCandidates ?></div>
      <div class="kpi-label">Eligible Intake</div>
      <div class="kpi-sub">Candidates ready</div>
    </div>
  </div>

  <!-- ── Pipeline ── -->
  <div class="tdb-section">
    <div class="tdb-section-title"><i class="fas fa-stream"></i> Participant Pipeline</div>
    <div class="tdb-card">
      <div class="pipeline">
        <div class="pipe-step" style="--pc:#42a5f5">
          <div class="pipe-n"><?= $eligibleCandidates ?></div>
          <div class="pipe-l">Eligible Candidates</div>
          <div class="pipe-sub">Passed selection</div>
        </div>
        <div class="pipe-step" style="--pc:#26c6da">
          <div class="pipe-n"><?= $inTraining ?></div>
          <div class="pipe-l">In Training</div>
          <div class="pipe-sub">Active trainees</div>
        </div>
        <div class="pipe-step" style="--pc:#66bb6a">
          <div class="pipe-n"><?= $passed ?></div>
          <div class="pipe-l">Training Passed</div>
          <div class="pipe-sub">Ready to promote</div>
        </div>
        <div class="pipe-step" style="--pc:#ffa726">
          <div class="pipe-n"><?= $apprenticeCount ?></div>
          <div class="pipe-l">Apprentices</div>
          <div class="pipe-sub">Working in Japan</div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Two columns: Quick Actions + Departures ── -->
  <div class="tdb-cols">

    <!-- Quick Actions -->
    <div class="tdb-card">
      <h3><i class="fas fa-bolt" style="color:#fb8c00"></i> Quick Actions</h3>
      <div class="quick-actions">
        <a class="qa-btn" style="--qc:#00acc1" href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'index']) ?>">
          <i class="fas fa-users"></i> Trainee List
        </a>
        <a class="qa-btn" style="--qc:#1e88e5" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>">
          <i class="fas fa-layer-group"></i> Batches
        </a>
        <a class="qa-btn" style="--qc:#43a047" href="<?= $this->Url->build(['controller'=>'TraineeTrainingTestScores','action'=>'daily']) ?>">
          <i class="fas fa-pencil-alt"></i> Daily Scores
        </a>
        <a class="qa-btn" style="--qc:#8e24aa" href="<?= $this->Url->build(['controller'=>'TraineeTrainingTestScores','action'=>'report']) ?>">
          <i class="fas fa-chart-bar"></i> Score Report
        </a>
        <a class="qa-btn" style="--qc:#f4511e" href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'promoteToApprentice']) ?>">
          <i class="fas fa-rocket"></i> Promote
        </a>
        <a class="qa-btn" style="--qc:#00897b" href="<?= $this->Url->build(['controller'=>'TraineeCertificates','action'=>'index']) ?>">
          <i class="fas fa-certificate"></i> Certificates
        </a>
        <a class="qa-btn" style="--qc:#546e7a" href="<?= $this->Url->build(['controller'=>'TraineeNameCards','action'=>'index']) ?>">
          <i class="fas fa-id-card"></i> Name Cards
        </a>
        <a class="qa-btn" style="--qc:#fb8c00" href="<?= $this->Url->build(['controller'=>'TraineeSubmissionDocuments','action'=>'index']) ?>">
          <i class="fas fa-file-alt"></i> Documents
        </a>
      </div>
    </div>

    <!-- Upcoming Departures -->
    <div class="tdb-card">
      <h3><i class="fas fa-plane-departure" style="color:#1e88e5"></i> Upcoming Departures</h3>
      <?php if (empty($upcomingDepartures)): ?>
        <p style="color:#90a4ae;font-size:12px;text-align:center;padding:20px 0">No upcoming departures scheduled</p>
      <?php else: ?>
        <ul class="depart-list">
          <?php foreach ($upcomingDepartures as $dep): ?>
            <?php $days = (int)$dep['days_left']; ?>
            <li class="depart-item">
              <div class="depart-days">
                <div class="dd-n"><?= $days ?></div>
                <div class="dd-l">days</div>
              </div>
              <div class="depart-info">
                <div class="di-batch"><?= h($dep['batch_name']) ?></div>
                <div class="di-loc"><i class="fas fa-map-marker-alt"></i> <?= h($dep['origin_training_location']) ?></div>
                <div class="di-date">Departure: <?= date('d M Y', strtotime($dep['departure_plan_date'])) ?></div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <hr style="border:none;border-top:1px solid #f0f0f0;margin:12px 0">
      <h3 style="margin-top:0"><i class="fas fa-venus-mars" style="color:#8e24aa"></i> Gender Distribution</h3>
      <div class="gender-chart">
        <?php
          $maleAngle  = $total > 0 ? ($male / $total * 251.2) : 0;
          $circum = 251.2;
        ?>
        <div class="donut-wrap">
          <svg viewBox="0 0 100 100" width="80" height="80">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#e3f2fd" stroke-width="12"/>
            <circle cx="50" cy="50" r="40" fill="none" stroke="#1e88e5" stroke-width="12"
                    stroke-dasharray="<?= $maleAngle ?> <?= $circum ?>" />
            <circle cx="50" cy="50" r="40" fill="none" stroke="#f48fb1" stroke-width="12"
                    stroke-dasharray="<?= $circum - $maleAngle ?> <?= $circum ?>"
                    stroke-dashoffset="-<?= $maleAngle ?>" />
          </svg>
          <div class="donut-center">
            <div class="dc-n"><?= $total ?></div>
            <div class="dc-l">Total</div>
          </div>
        </div>
        <div class="gender-legend">
          <div class="gl-item"><div class="gl-dot" style="background:#1e88e5"></div> Male: <strong><?= $male ?></strong></div>
          <div class="gl-item"><div class="gl-dot" style="background:#f48fb1"></div> Female: <strong><?= $female ?></strong></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ── New Training Batches ── -->
  <?php if (!empty($batches)): ?>
  <div class="tdb-section">
    <div class="tdb-section-title"><i class="fas fa-calendar-alt"></i> Training Batch Schedule</div>
    <div class="batch-cards">
      <?php foreach ($batches as $b):
        $sc = $statusColor[$b['status']] ?? '#90a4ae';
        $sl = $statusLabel[$b['status']] ?? $b['status'];
        $pct = $b['capacity'] > 0 ? min(100, round($b['enrolled_count'] / $b['capacity'] * 100)) : 0;
        $daysR = (int)$b['days_remaining'];
        $daysE = (int)$b['days_elapsed'];
        $totalDays = max(1, $daysR + $daysE);
        $progressPct = $b['status'] === 'completed' ? 100 : ($totalDays > 0 ? min(100, round($daysE / $totalDays * 100)) : 0);
      ?>
      <div class="batch-card" style="--bc:<?= $sc ?>">
        <div class="bc-head">
          <div>
            <div class="bc-code"><?= h($b['batch_code']) ?></div>
            <div class="bc-name"><?= h($b['batch_name']) ?></div>
          </div>
          <span class="batch-status" style="background:<?= $sc ?>"><?= $sl ?></span>
        </div>
        <div class="bc-instructor"><i class="fas fa-chalkboard-teacher"></i> <?= h($b['instructor_name'] ?? '—') ?></div>
        <div class="bc-meta">
          <div class="bc-meta-item"><i class="fas fa-map-marker-alt"></i> <?= h($b['training_location']) ?></div>
          <div class="bc-meta-item"><i class="fas fa-calendar-check"></i>
            <?= date('d M Y', strtotime($b['start_date'])) ?> – <?= date('d M Y', strtotime($b['end_date'])) ?>
          </div>
        </div>
        <!-- Capacity fill -->
        <div class="bc-progress" style="margin-top:10px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span class="cap-txt">Capacity: <?= $b['enrolled_count'] ?>/<?= $b['capacity'] ?></span>
            <?php if ($b['status'] === 'ongoing'):
              $badge = $daysR < 14 ? 'bc-days-soon' : 'bc-days-ok';
            ?>
              <span class="bc-days-badge <?= $badge ?>"><?= $daysR ?>d left</span>
            <?php elseif ($b['status'] === 'recruiting'): ?>
              <span class="bc-days-badge bc-days-ok">Enrolling</span>
            <?php endif; ?>
          </div>
          <div class="cap-bar"><div class="cap-fill" style="--bc:<?= $sc ?>;width:<?= $pct ?>%"></div></div>
        </div>
        <!-- Timeline progress -->
        <div class="bc-progress" style="margin-top:8px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
            <span class="cap-txt">Timeline progress</span>
            <span class="cap-txt"><?= $progressPct ?>%</span>
          </div>
          <div class="cap-bar"><div class="cap-fill" style="--bc:#26c6da;width:<?= $progressPct ?>%"></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Legacy Trainee Batches ── -->
  <?php if (!empty($traineeBatches)): ?>
  <div class="tdb-section">
    <div class="tdb-section-title"><i class="fas fa-plane"></i> Japan Training Batches</div>
    <div class="tdb-card" style="padding:0">
      <table class="batch-table">
        <thead>
          <tr>
            <th>Batch</th>
            <th>Location</th>
            <th>Start</th>
            <th>End</th>
            <th>Departure</th>
            <th>Duration</th>
            <th>Enrolled</th>
            <th>Passed</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($traineeBatches as $b):
            $startDate = $b['origin_start_plan_date'];
            $endDate   = $b['origin_finish_plan_date'];
            $depDate   = $b['departure_plan_date'];
            $daysToEnd = $endDate ? (int)((strtotime($endDate) - time()) / 86400) : null;
            $isPast    = $endDate && strtotime($endDate) < time();
          ?>
          <tr>
            <td><strong><?= h($b['batch_name']) ?></strong></td>
            <td><?= h($b['origin_training_location']) ?></td>
            <td><?= $startDate ? date('d M Y', strtotime($startDate)) : '—' ?></td>
            <td><?= $endDate ? date('d M Y', strtotime($endDate)) : '—' ?></td>
            <td>
              <?php if ($depDate): ?>
                <?php $ddays = (int)((strtotime($depDate) - time()) / 86400); ?>
                <?= date('d M Y', strtotime($depDate)) ?>
                <?php if ($ddays > 0): ?>
                  <span class="batch-status" style="background:#1e88e5;font-size:9px"><?= $ddays ?>d</span>
                <?php endif; ?>
              <?php else: ?>—<?php endif; ?>
            </td>
            <td><?= (int)$b['training_term_of_months'] ?> mo</td>
            <td><strong><?= (int)$b['enrolled'] ?></strong></td>
            <td>
              <span class="batch-status" style="background:<?= (int)$b['passed'] > 0 ? '#43a047' : '#90a4ae' ?>">
                <?= (int)$b['passed'] ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Trainee Cards ── -->
  <div class="tdb-section">
    <div class="tdb-section-title"><i class="fas fa-id-badge"></i> All Trainees</div>
    <div class="trainee-grid">
      <?php foreach ($traineeList as $t):
        $gColor = ($t['master_gender_id'] == 1) ? '#1e88e5' : '#e91e8c';
        $bName  = $batchNameMap[(int)$t['trainee_training_batch_id']] ?? ('Batch #' . $t['trainee_training_batch_id']);
        $initials = implode('', array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), array_slice(explode(' ', $t['name']), 0, 2)));
        $isPass = (int)$t['is_trainee_pass'];
      ?>
      <div class="trainee-card" style="--tc:<?= $gColor ?>">
        <div class="trainee-avatar" style="background:linear-gradient(135deg,<?= $gColor ?>,<?= $gColor ?>88)">
          <?php if (!empty($t['image_photo']) && file_exists(WWW_ROOT . 'img/trainees/' . $t['image_photo'])): ?>
            <img src="<?= $this->Url->build('/img/trainees/' . $t['image_photo'], true) ?>" alt="">
          <?php else: ?>
            <?= $initials ?>
          <?php endif; ?>
        </div>
        <div class="trainee-info">
          <div class="ti-name"><?= h($t['name']) ?></div>
          <div class="ti-code"><?= h($t['tmm_code']) ?></div>
          <div class="ti-batch"><i class="fas fa-layer-group" style="font-size:9px"></i> <?= h($bName) ?></div>
          <span class="ti-status <?= $isPass ? 'ti-pass' : 'ti-progress' ?>">
            <?= $isPass ? '✓ Passed' : '⏳ In Training' ?>
          </span>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($traineeList)): ?>
        <div style="grid-column:1/-1;text-align:center;padding:40px;color:#90a4ae">
          <i class="fas fa-users" style="font-size:40px;margin-bottom:12px;display:block"></i>
          No trainees found. <a href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'add']) ?>">Add the first trainee</a>.
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

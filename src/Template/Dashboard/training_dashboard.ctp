<?php
$total      = (int)($traineeStats['total'] ?? 0);
$inTraining = (int)($traineeStats['in_training'] ?? 0);
$passed     = (int)($traineeStats['passed'] ?? 0);
$male       = (int)($traineeStats['male'] ?? 0);
$female     = (int)($traineeStats['female'] ?? 0);
$passRate   = $total > 0 ? round($passed / $total * 100) : 0;

$statusColor = [
    'planning'   => '#90a4ae',
    'recruiting' => '#42a5f5',
    'ongoing'    => '#26c6da',
    'completed'  => '#66bb6a',
    'cancelled'  => '#ef5350',
];
$statusLabel = [
    'planning'   => __('Planning'),
    'recruiting' => __('Recruiting'),
    'ongoing'    => __('Ongoing'),
    'completed'  => __('Completed'),
    'cancelled'  => __('Cancelled'),
];
?>
<style>
.tdb { font-family:'Segoe UI',sans-serif; color:#263238; padding:0 2px 40px; }
.tdb-header { display:flex; align-items:center; gap:14px; margin-bottom:26px; padding-bottom:16px; border-bottom:2px solid #e0f7fa; }
.tdb-header .tdb-logo { width:46px; height:46px; background:linear-gradient(135deg,#00bcd4,#006064); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px; flex-shrink:0; }
.tdb-header h1 { margin:0; font-size:22px; font-weight:800; color:#006064; }
.tdb-header p { margin:3px 0 0; font-size:12px; color:#78909c; }
.tdb-date { margin-left:auto; background:#e0f7fa; color:#006064; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:600; white-space:nowrap; }

/* KPIs */
.tdb-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(148px,1fr)); gap:12px; margin-bottom:24px; }
.kpi { background:#fff; border-radius:14px; padding:16px 18px; box-shadow:0 2px 8px rgba(0,0,0,.07); border-top:4px solid var(--kc); position:relative; overflow:hidden; transition:transform .15s; cursor:default; }
.kpi:hover { transform:translateY(-2px); box-shadow:0 5px 16px rgba(0,0,0,.1); }
.kpi::after { content:''; position:absolute; right:-14px; top:-14px; width:68px; height:68px; background:var(--kc); opacity:.07; border-radius:50%; pointer-events:none; }
.kpi .kpi-icon { font-size:20px; margin-bottom:6px; }
.kpi .kpi-val { font-size:34px; font-weight:800; color:var(--kc); line-height:1; }
.kpi .kpi-label { font-size:11px; color:#78909c; margin-top:4px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; }
.kpi .kpi-sub { font-size:10px; color:#b0bec5; margin-top:3px; }

/* Section headers */
.tdb-sec { margin-bottom:22px; }
.tdb-sec-title { font-size:12px; font-weight:700; color:#455a64; text-transform:uppercase; letter-spacing:.8px; margin-bottom:10px; display:flex; align-items:center; gap:8px; flex-wrap:nowrap; }
.tdb-sec-title .tdb-sec-line { flex:1; height:1px; background:#e0f7fa; }

/* Cards */
.tdb-card { background:#fff; border-radius:14px; padding:18px; box-shadow:0 2px 8px rgba(0,0,0,.07); }
.tdb-cols { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:22px; }
@media(max-width:860px){ .tdb-cols { grid-template-columns:1fr; } }

/* Pipeline */
.pipeline { display:flex; align-items:stretch; gap:0; }
.pipe-step { flex:1; text-align:center; padding:18px 6px; position:relative; background:linear-gradient(135deg,rgba(0,188,212,.07),rgba(0,188,212,.02)); border-radius:10px; margin:0 3px; }
.pipe-step::after { content:'▶'; position:absolute; right:-10px; top:50%; transform:translateY(-50%); color:#b0bec5; font-size:14px; z-index:1; }
.pipe-step:last-child::after { display:none; }
.pipe-step .pn { font-size:32px; font-weight:800; color:var(--pc); line-height:1; }
.pipe-step .pl { font-size:10px; color:#78909c; font-weight:700; text-transform:uppercase; margin-top:5px; letter-spacing:.4px; }
.pipe-step .ps { font-size:10px; color:#b0bec5; margin-top:2px; }

/* Quick actions */
.qa-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(130px,1fr)); gap:9px; }
.qa-btn { display:flex; flex-direction:column; align-items:center; gap:6px; padding:13px 8px; border-radius:12px; background:linear-gradient(135deg,var(--qc),color-mix(in srgb,var(--qc) 75%,#000)); color:#fff; text-decoration:none; font-size:11px; font-weight:700; text-align:center; transition:transform .15s,box-shadow .15s; box-shadow:0 2px 6px rgba(0,0,0,.14); }
.qa-btn:hover { transform:translateY(-2px); box-shadow:0 5px 14px rgba(0,0,0,.2); color:#fff; text-decoration:none; }
.qa-btn i { font-size:19px; }

/* Departures */
.depart-list { list-style:none; padding:0; margin:0; }
.depart-item { display:flex; gap:12px; align-items:flex-start; padding:9px 0; border-bottom:1px solid #f5f5f5; }
.depart-item:last-child { border-bottom:none; }
.depart-days { min-width:44px; text-align:center; flex-shrink:0; }
.depart-days .dd-n { font-size:22px; font-weight:800; color:#00acc1; line-height:1; }
.depart-days .dd-l { font-size:9px; color:#b0bec5; text-transform:uppercase; }
.depart-info .di-b { font-size:12px; font-weight:700; color:#263238; }
.depart-info .di-loc { font-size:11px; color:#78909c; }
.depart-info .di-date { font-size:10px; color:#b0bec5; }

/* Gender donut */
.gender-wrap { display:flex; align-items:center; gap:18px; margin-top:14px; padding-top:14px; border-top:1px solid #f0f0f0; }
.donut-rel { position:relative; width:78px; height:78px; flex-shrink:0; }
.donut-rel svg { transform:rotate(-90deg); }
.donut-center { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; }
.donut-center .dc-n { font-size:17px; font-weight:800; color:#263238; line-height:1; }
.donut-center .dc-l { font-size:9px; color:#90a4ae; }
.gl { display:flex; flex-direction:column; gap:7px; }
.gl-row { display:flex; align-items:center; gap:7px; font-size:12px; color:#546e7a; }
.gl-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; }

/* Batch cards */
.batch-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:13px; }
.bc { background:#fff; border-radius:14px; padding:15px 17px; box-shadow:0 2px 8px rgba(0,0,0,.07); border-top:3px solid var(--bc); }
.bc-head { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.bc-code { font-size:10px; color:#90a4ae; font-weight:600; }
.bc-name { font-size:13px; font-weight:700; color:#263238; margin-top:2px; line-height:1.3; }
.bc-inst { font-size:11px; color:#78909c; margin-top:4px; }
.bc-meta { display:flex; gap:10px; flex-wrap:wrap; margin-top:9px; }
.bc-meta span { font-size:10px; color:#546e7a; display:flex; align-items:center; gap:3px; }
.batch-badge { display:inline-block; padding:3px 9px; border-radius:20px; font-size:9px; font-weight:700; color:#fff; text-transform:uppercase; }
.bar-wrap { margin-top:10px; }
.bar-label { display:flex; justify-content:space-between; font-size:10px; color:#78909c; margin-bottom:3px; }
.bar { height:5px; background:#eceff1; border-radius:3px; }
.bar-fill { height:5px; border-radius:3px; transition:width .5s; }

/* Legacy batch table */
.btable { width:100%; border-collapse:collapse; font-size:12px; }
.btable th { background:#f5f5f5; color:#546e7a; font-size:10px; text-transform:uppercase; padding:7px 10px; text-align:left; font-weight:700; }
.btable td { padding:8px 10px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.btable tr:hover td { background:#f9fbe7; }

/* Trainee grid */
.tr-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:11px; }
.tr-card { background:#fff; border-radius:12px; padding:13px 15px; box-shadow:0 2px 6px rgba(0,0,0,.06); border-left:4px solid var(--tc); display:flex; gap:11px; align-items:center; transition:box-shadow .15s; }
.tr-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.1); }
.tr-av { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:15px; flex-shrink:0; overflow:hidden; background:var(--tc); }
.tr-av img { width:100%; height:100%; object-fit:cover; border-radius:50%; }
.tr-name { font-size:13px; font-weight:700; color:#263238; line-height:1.2; }
.tr-code { font-size:10px; color:#90a4ae; font-weight:600; margin-top:1px; }
.tr-batch { font-size:10px; color:#78909c; margin-top:2px; }
.tr-tag { display:inline-block; margin-top:4px; padding:2px 7px; border-radius:20px; font-size:9px; font-weight:700; text-transform:uppercase; }
.tag-pass { background:#e8f5e9; color:#2e7d32; }
.tag-prog { background:#e3f2fd; color:#1565c0; }
</style>

<div class="tdb">

  <!-- Header -->
  <div class="tdb-header">
    <div class="tdb-logo"><i class="fas fa-graduation-cap"></i></div>
    <div>
      <h1><?= __('Training Dashboard') ?></h1>
      <p><?= __('TMM Training Division — Program Overview &amp; Key Performance Indicators') ?></p>
    </div>
    <div class="tdb-date"><i class="fas fa-calendar-day"></i> <?= date('d M Y') ?></div>
  </div>

  <!-- KPI Row -->
  <div class="tdb-kpis">
    <div class="kpi" style="--kc:#00acc1">
      <div class="kpi-icon">👤</div>
      <div class="kpi-val"><?= $total ?></div>
      <div class="kpi-label"><?= __('Total Trainees') ?></div>
      <div class="kpi-sub"><?= $male ?>♂ <?= $female ?>♀</div>
    </div>
    <div class="kpi" style="--kc:#1e88e5">
      <div class="kpi-icon">🎓</div>
      <div class="kpi-val"><?= $inTraining ?></div>
      <div class="kpi-label"><?= __('In Training') ?></div>
      <div class="kpi-sub"><?= __('Currently active') ?></div>
    </div>
    <div class="kpi" style="--kc:#43a047">
      <div class="kpi-icon">✅</div>
      <div class="kpi-val"><?= $passed ?></div>
      <div class="kpi-label"><?= __('Passed') ?></div>
      <div class="kpi-sub"><?= $passRate ?>% <?= __('rate') ?></div>
    </div>
    <div class="kpi" style="--kc:#8e24aa">
      <div class="kpi-icon">🏅</div>
      <div class="kpi-val"><?= $certCount ?></div>
      <div class="kpi-label"><?= __('Certificates') ?></div>
      <div class="kpi-sub"><?= __('Issued to date') ?></div>
    </div>
    <div class="kpi" style="--kc:#fb8c00">
      <div class="kpi-icon">🚀</div>
      <div class="kpi-val"><?= $apprenticeCount ?></div>
      <div class="kpi-label"><?= __('Apprentices') ?></div>
      <div class="kpi-sub"><?= __('Promoted out') ?></div>
    </div>
    <div class="kpi" style="--kc:#f4511e">
      <div class="kpi-icon">📋</div>
      <div class="kpi-val"><?= count($batches) + count($traineeBatches) ?></div>
      <div class="kpi-label"><?= __('Batches') ?></div>
      <div class="kpi-sub"><?= count($batches) ?> <?= __('new') ?> · <?= count($traineeBatches) ?> <?= __('legacy') ?></div>
    </div>
    <div class="kpi" style="--kc:#00897b">
      <div class="kpi-icon">🌟</div>
      <div class="kpi-val"><?= $eligibleCandidates ?></div>
      <div class="kpi-label"><?= __('Eligible Intake') ?></div>
      <div class="kpi-sub"><?= __('Candidates ready') ?></div>
    </div>
  </div>

  <!-- Pipeline -->
  <div class="tdb-sec">
    <div class="tdb-sec-title"><i class="fas fa-stream"></i> <?= __('Participant Pipeline') ?> <span class="tdb-sec-line"></span></div>
    <div class="tdb-card">
      <div class="pipeline">
        <div class="pipe-step" style="--pc:#42a5f5">
          <div class="pn"><?= $eligibleCandidates ?></div>
          <div class="pl"><?= __('Eligible Candidates') ?></div>
          <div class="ps"><?= __('Passed selection') ?></div>
        </div>
        <div class="pipe-step" style="--pc:#26c6da">
          <div class="pn"><?= $inTraining ?></div>
          <div class="pl"><?= __('In Training') ?></div>
          <div class="ps"><?= __('Active trainees') ?></div>
        </div>
        <div class="pipe-step" style="--pc:#66bb6a">
          <div class="pn"><?= $passed ?></div>
          <div class="pl"><?= __('Training Passed') ?></div>
          <div class="ps"><?= __('Ready to promote') ?></div>
        </div>
        <div class="pipe-step" style="--pc:#ffa726">
          <div class="pn"><?= $apprenticeCount ?></div>
          <div class="pl"><?= __('Apprentices') ?></div>
          <div class="ps"><?= __('Working in Japan') ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions + Departures / Gender -->
  <div class="tdb-cols">
    <div class="tdb-card">
      <div style="font-size:13px;font-weight:700;color:#37474f;margin-bottom:12px;display:flex;align-items:center;gap:8px">
        <i class="fas fa-bolt" style="color:#fb8c00"></i> <?= __('Quick Actions') ?>
      </div>
      <div class="qa-grid">
        <a class="qa-btn" style="--qc:#00acc1" href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'index']) ?>">
          <i class="fas fa-users"></i> <?= __('Trainee List') ?>
        </a>
        <a class="qa-btn" style="--qc:#1e88e5" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>">
          <i class="fas fa-layer-group"></i> <?= __('Batches') ?>
        </a>
        <a class="qa-btn" style="--qc:#43a047" href="<?= $this->Url->build(['controller'=>'TraineeTrainingTestScores','action'=>'daily']) ?>">
          <i class="fas fa-pencil-alt"></i> <?= __('Daily Scores') ?>
        </a>
        <a class="qa-btn" style="--qc:#8e24aa" href="<?= $this->Url->build(['controller'=>'TraineeTrainingTestScores','action'=>'report']) ?>">
          <i class="fas fa-chart-bar"></i> <?= __('Score Report') ?>
        </a>
        <a class="qa-btn" style="--qc:#f4511e" href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'promoteToApprentice']) ?>">
          <i class="fas fa-rocket"></i> <?= __('Promote') ?>
        </a>
        <a class="qa-btn" style="--qc:#00897b" href="<?= $this->Url->build(['controller'=>'TraineeCertificates','action'=>'index']) ?>">
          <i class="fas fa-certificate"></i> <?= __('Certificates') ?>
        </a>
        <a class="qa-btn" style="--qc:#546e7a" href="<?= $this->Url->build(['controller'=>'TraineeNameCards','action'=>'index']) ?>">
          <i class="fas fa-id-card"></i> <?= __('Name Cards') ?>
        </a>
        <a class="qa-btn" style="--qc:#fb8c00" href="<?= $this->Url->build(['controller'=>'TraineeSubmissionDocuments','action'=>'index']) ?>">
          <i class="fas fa-file-alt"></i> <?= __('Documents') ?>
        </a>
      </div>
    </div>

    <div class="tdb-card">
      <div style="font-size:13px;font-weight:700;color:#37474f;margin-bottom:12px;display:flex;align-items:center;gap:8px">
        <i class="fas fa-plane-departure" style="color:#1e88e5"></i> <?= __('Upcoming Departures') ?>
      </div>
      <?php if (empty($upcomingDepartures)): ?>
        <p style="color:#90a4ae;font-size:12px;text-align:center;padding:14px 0"><?= __('No upcoming departures scheduled') ?></p>
      <?php else: ?>
        <ul class="depart-list">
          <?php foreach ($upcomingDepartures as $dep): ?>
          <li class="depart-item">
            <div class="depart-days">
              <div class="dd-n"><?= (int)$dep['days_left'] ?></div>
              <div class="dd-l"><?= __('days') ?></div>
            </div>
            <div class="depart-info">
              <div class="di-b"><?= h($dep['batch_name']) ?></div>
              <div class="di-loc"><i class="fas fa-map-marker-alt" style="font-size:9px"></i> <?= h($dep['origin_training_location']) ?></div>
              <div class="di-date"><?= date('d M Y', strtotime($dep['departure_plan_date'])) ?></div>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <div class="gender-wrap">
        <?php
          $circum = 251.2;
          $maleArc = $total > 0 ? ($male / $total * $circum) : 0;
        ?>
        <div class="donut-rel">
          <svg viewBox="0 0 100 100" width="78" height="78">
            <circle cx="50" cy="50" r="40" fill="none" stroke="#eceff1" stroke-width="14"/>
            <circle cx="50" cy="50" r="40" fill="none" stroke="#1e88e5" stroke-width="14"
                    stroke-dasharray="<?= $maleArc ?> <?= $circum ?>"/>
            <circle cx="50" cy="50" r="40" fill="none" stroke="#f48fb1" stroke-width="14"
                    stroke-dasharray="<?= $circum - $maleArc ?> <?= $circum ?>"
                    stroke-dashoffset="-<?= $maleArc ?>"/>
          </svg>
          <div class="donut-center">
            <div class="dc-n"><?= $total ?></div>
            <div class="dc-l"><?= __('Total') ?></div>
          </div>
        </div>
        <div class="gl">
          <div class="gl-row"><div class="gl-dot" style="background:#1e88e5"></div> <?= __('Male') ?> <strong><?= $male ?></strong></div>
          <div class="gl-row"><div class="gl-dot" style="background:#f48fb1"></div> <?= __('Female') ?> <strong><?= $female ?></strong></div>
          <?php if ($total > 0): ?>
          <div class="gl-row" style="color:#90a4ae;font-size:10px"><?= __('Pass rate:') ?> <?= $passRate ?>%</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Training Batch Schedule -->
  <?php if (!empty($batches)): ?>
  <div class="tdb-sec">
    <div class="tdb-sec-title">
      <i class="fas fa-calendar-alt"></i> <?= __('Training Batch Schedule') ?> <span class="tdb-sec-line"></span>
      <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'add']) ?>"
         style="margin-left:auto;font-size:11px;background:#00acc1;color:#fff;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px">
        <i class="fas fa-plus"></i> <?= __('Add Batch') ?>
      </a>
    </div>
    <div class="batch-cards">
      <?php foreach ($batches as $b):
        $sc = $statusColor[$b['status']] ?? '#90a4ae';
        $sl = $statusLabel[$b['status']] ?? ucfirst($b['status']);
        $capPct = $b['capacity'] > 0 ? min(100, round($b['enrolled_count'] / $b['capacity'] * 100)) : 0;
        $daysR  = (int)$b['days_remaining'];
        $daysE  = (int)$b['days_elapsed'];
        $total_d = max(1, $daysR + $daysE);
        $progPct = $b['status'] === 'completed' ? 100 : ($total_d > 0 ? min(100, round($daysE / $total_d * 100)) : 0);
      ?>
      <div class="bc" style="--bc:<?= $sc ?>">
        <div class="bc-head">
          <div>
            <div class="bc-code"><?= h($b['batch_code']) ?></div>
            <div class="bc-name"><?= h($b['batch_name']) ?></div>
          </div>
          <span class="batch-badge" style="background:<?= $sc ?>"><?= $sl ?></span>
        </div>
        <div class="bc-inst"><i class="fas fa-chalkboard-teacher"></i> <?= h($b['instructor_name'] ?? '—') ?></div>
        <div class="bc-meta">
          <span><i class="fas fa-map-marker-alt"></i> <?= h($b['training_location']) ?></span>
          <span><i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($b['start_date'])) ?> – <?= date('d M Y', strtotime($b['end_date'])) ?></span>
        </div>
        <div class="bar-wrap">
          <div class="bar-label">
            <span><?= __('Capacity') ?> <?= $b['enrolled_count'] ?>/<?= $b['capacity'] ?></span>
            <?php if ($b['status'] === 'ongoing'): ?>
              <span style="color:<?= $daysR < 14 ? '#f57f17' : '#00897b' ?>"><?= $daysR ?><?= __('d left') ?></span>
            <?php endif; ?>
          </div>
          <div class="bar"><div class="bar-fill" style="background:<?= $sc ?>;width:<?= $capPct ?>%"></div></div>
        </div>
        <div class="bar-wrap">
          <div class="bar-label"><span><?= __('Timeline') ?></span><span><?= $progPct ?>%</span></div>
          <div class="bar"><div class="bar-fill" style="background:#26c6da;width:<?= $progPct ?>%"></div></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:6px">
          <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
             style="flex:1;text-align:center;font-size:10px;font-weight:700;color:#fff;background:#fb8c00;padding:5px 0;border-radius:8px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:4px">
            <i class="fas fa-user-plus"></i> <?= __('Register Trainees') ?>
          </a>
          <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
             style="flex:1;text-align:center;font-size:10px;font-weight:700;color:#546e7a;background:#eceff1;padding:5px 0;border-radius:8px;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:4px">
            <i class="fas fa-cog"></i> <?= __('Manage') ?>
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Japan Training Batches (legacy) -->
  <?php if (!empty($traineeBatches)): ?>
  <div class="tdb-sec">
    <div class="tdb-sec-title">
      <i class="fas fa-plane"></i> <?= __('Japan Training Batches') ?> <span class="tdb-sec-line"></span>
      <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'add']) ?>"
         style="margin-left:auto;font-size:11px;background:#1e88e5;color:#fff;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px">
        <i class="fas fa-plus"></i> <?= __('Add Batch') ?>
      </a>
      <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
         style="font-size:11px;background:#eceff1;color:#546e7a;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px">
        <i class="fas fa-list"></i> <?= __('Manage') ?>
      </a>
    </div>
    <div class="tdb-card" style="padding:0">
      <table class="btable">
        <thead>
          <tr>
            <th><?= __('Batch') ?></th><th><?= __('Location') ?></th><th><?= __('Start') ?></th><th><?= __('End') ?></th>
            <th><?= __('Departure') ?></th><th><?= __('Duration') ?></th><th><?= __('Enrolled') ?></th><th><?= __('Passed') ?></th><th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($traineeBatches as $b): ?>
          <tr>
            <td><strong><?= h($b['batch_name']) ?></strong></td>
            <td><?= h($b['origin_training_location']) ?></td>
            <td><?= $b['origin_start_plan_date'] ? date('d M Y', strtotime($b['origin_start_plan_date'])) : '—' ?></td>
            <td><?= $b['origin_finish_plan_date'] ? date('d M Y', strtotime($b['origin_finish_plan_date'])) : '—' ?></td>
            <td>
              <?php if ($b['departure_plan_date']): ?>
                <?php $dd = (int)((strtotime($b['departure_plan_date']) - time()) / 86400); ?>
                <?= date('d M Y', strtotime($b['departure_plan_date'])) ?>
                <?php if ($dd > 0): ?><span class="batch-badge" style="background:#1e88e5;font-size:9px"><?= $dd ?>d</span><?php endif; ?>
              <?php else: ?>—<?php endif; ?>
            </td>
            <td><?= (int)$b['training_term_of_months'] ?> <?= __('mo') ?></td>
            <td><strong><?= (int)$b['enrolled'] ?></strong></td>
            <td><span class="batch-badge" style="background:<?= (int)$b['passed'] > 0 ? '#43a047' : '#90a4ae' ?>"><?= (int)$b['passed'] ?></span></td>
            <td style="white-space:nowrap">
              <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'edit',$b['id']]) ?>"
                 style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#fff;background:#fb8c00;padding:3px 9px;border-radius:20px;text-decoration:none">
                <i class="fas fa-pen"></i> <?= __('Edit') ?>
              </a>
              <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'view',$b['id']]) ?>"
                 style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#fff;background:#546e7a;padding:3px 9px;border-radius:20px;text-decoration:none;margin-left:4px">
                <i class="fas fa-eye"></i> <?= __('View') ?>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <!-- ══ Eligible: Candidate → Trainee ══ -->
  <div class="tdb-sec">
    <div class="tdb-sec-title" style="color:#00796b">
      <i class="fas fa-user-check" style="color:#00acc1"></i>
      <?= __('Eligible Candidates — Ready to Register as Trainee') ?>
      <span style="background:#e0f7fa;color:#006064;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700">
        <?= count($eligibleCandidateList ?? []) ?>
      </span>
      <span class="tdb-sec-line"></span>
      <a href="<?= $this->Url->build(['controller'=>'Candidates','action'=>'index']) ?>"
         style="font-size:11px;background:#eceff1;color:#546e7a;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px;white-space:nowrap">
        <i class="fas fa-list"></i> <?= __('All Candidates') ?>
      </a>
      <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
         style="font-size:11px;background:#00acc1;color:#fff;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px;white-space:nowrap">
        <i class="fas fa-plus"></i> <?= __('Register to Batch') ?>
      </a>
    </div>
    <?php if (empty($eligibleCandidateList)): ?>
      <div class="tdb-card" style="text-align:center;padding:30px;color:#90a4ae">
        <i class="fas fa-check-circle" style="font-size:32px;margin-bottom:8px;display:block;color:#66bb6a"></i>
        <?= __('All passed candidates have been registered as trainees.') ?>
      </div>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:10px">
        <?php foreach (($eligibleCandidateList ?? []) as $c):
          $gc    = ($c['master_gender_id'] == 1) ? '#1e88e5' : '#e91e8c';
          $words = array_slice(explode(' ', $c['name']), 0, 2);
          $init  = implode('', array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), $words));
          $fit   = $c['fitness_score']   ? round((float)$c['fitness_score'])   : null;
          $intv  = $c['interview_score'] ? round((float)$c['interview_score']) : null;
        ?>
        <div style="background:#fff;border-radius:12px;padding:12px 14px;border:1.5px solid #b2ebf2;
                    box-shadow:0 1px 4px rgba(0,0,0,.05);display:flex;gap:10px;align-items:flex-start;transition:all .15s"
             onmouseover="this.style.borderColor='#00acc1';this.style.boxShadow='0 3px 10px rgba(0,172,193,.15)'"
             onmouseout="this.style.borderColor='#b2ebf2';this.style.boxShadow='0 1px 4px rgba(0,0,0,.05)'">
          <div style="width:40px;height:40px;border-radius:50%;background:<?= $gc ?>;flex-shrink:0;
                      display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:14px;overflow:hidden">
            <?php if (!empty($c['image_photo']) && file_exists(WWW_ROOT . 'img/candidates/' . $c['image_photo'])): ?>
              <img src="<?= $this->Url->build('/img/candidates/' . $c['image_photo'], true) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
            <?php else: ?>
              <?= $init ?>
            <?php endif; ?>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:700;color:#263238;line-height:1.2"><?= h($c['name']) ?></div>
            <div style="font-size:10px;color:#90a4ae;font-weight:600;margin-top:1px"><?= h($c['candidate_code'] ?? '—') ?></div>
            <div style="display:flex;gap:5px;margin-top:5px;flex-wrap:wrap">
              <?php if ($fit !== null): ?>
                <span style="font-size:9px;font-weight:700;padding:2px 6px;border-radius:20px;background:#e3f2fd;color:#1565c0"><i class="fas fa-dumbbell"></i> <?= $fit ?></span>
              <?php endif; ?>
              <?php if ($intv !== null): ?>
                <span style="font-size:9px;font-weight:700;padding:2px 6px;border-radius:20px;background:#f3e5f5;color:#7b1fa2"><i class="fas fa-comments"></i> <?= $intv ?></span>
              <?php endif; ?>
              <?php if ($c['age']): ?>
                <span style="font-size:9px;font-weight:700;padding:2px 6px;border-radius:20px;background:#fff3e0;color:#e65100"><?= $c['age'] ?>y</span>
              <?php endif; ?>
            </div>
          </div>
          <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>"
             title="<?= __('Register to batch') ?>" style="display:inline-flex;align-items:center;justify-content:center;
             width:28px;height:28px;border-radius:7px;background:#e0f7fa;color:#00796b;
             text-decoration:none;font-size:12px;flex-shrink:0;margin-top:2px">
            <i class="fas fa-plus"></i>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ══ Eligible: Trainee → Apprentice ══ -->
  <div class="tdb-sec">
    <div class="tdb-sec-title" style="color:#6a1b9a">
      <i class="fas fa-rocket" style="color:#7b1fa2"></i>
      <?= __('Eligible Trainees — Ready to Promote to Apprentice') ?>
      <span style="background:#f3e5f5;color:#6a1b9a;padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700">
        <?= count($eligibleTraineeList ?? []) ?>
      </span>
      <span class="tdb-sec-line" style="background:#e9d5ff"></span>
      <a href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'promoteToApprentice']) ?>"
         style="font-size:11px;background:#7b1fa2;color:#fff;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px;white-space:nowrap">
        <i class="fas fa-rocket"></i> <?= __('Promotion Page') ?>
      </a>
    </div>
    <?php if (empty($eligibleTraineeList)): ?>
      <div class="tdb-card" style="text-align:center;padding:30px;color:#90a4ae">
        <i class="fas fa-check-circle" style="font-size:32px;margin-bottom:8px;display:block;color:#66bb6a"></i>
        <?= __('All passed trainees have been promoted to apprentice.') ?>
      </div>
    <?php else: ?>
      <?php if (!empty($apprenticeOrders)): ?>
      <div style="padding:10px 14px;background:#faf5ff;border-radius:10px;margin-bottom:10px;display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <label style="font-size:12px;font-weight:700;color:#6a1b9a;white-space:nowrap">
          <i class="fas fa-clipboard-list"></i> <?= __('Apprentice Order:') ?>
        </label>
        <select id="dashOrderSelect" onchange="dashSyncOrders()"
                style="padding:6px 12px;border:1.5px solid #ce93d8;border-radius:8px;font-size:12px;font-weight:600;color:#4a148c;background:#fff;outline:none">
          <option value=""><?= __('— No order —') ?></option>
          <?php foreach ($apprenticeOrders as $ao): ?>
            <option value="<?= $ao['id'] ?>">
              <?= h($ao['title']) ?> (<?= $ao['departure_year'] ?>/<?= str_pad($ao['departure_month'],2,'0',STR_PAD_LEFT) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <?php endif; ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:10px">
        <?php foreach (($eligibleTraineeList ?? []) as $t):
          $gc    = ($t['master_gender_id'] == 1) ? '#1e88e5' : '#e91e8c';
          $words = array_slice(explode(' ', $t['name']), 0, 2);
          $init  = implode('', array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), $words));
          $bn    = $batchNameMap[(int)$t['trainee_training_batch_id']] ?? null;
        ?>
        <div style="background:#fff;border-radius:12px;padding:12px 14px;border:1.5px solid #e9d5ff;
                    box-shadow:0 1px 4px rgba(123,31,162,.06);display:flex;gap:10px;align-items:flex-start;transition:all .15s"
             onmouseover="this.style.borderColor='#7b1fa2';this.style.boxShadow='0 3px 10px rgba(123,31,162,.15)'"
             onmouseout="this.style.borderColor='#e9d5ff';this.style.boxShadow='0 1px 4px rgba(123,31,162,.06)'">
          <div style="width:40px;height:40px;border-radius:50%;background:<?= $gc ?>;flex-shrink:0;
                      display:flex;align-items:center;justify-content:center;font-weight:700;color:#fff;font-size:14px;overflow:hidden">
            <?php if (!empty($t['image_photo']) && file_exists(WWW_ROOT . 'img/candidates/' . $t['image_photo'])): ?>
              <img src="<?= $this->Url->build('/img/candidates/' . $t['image_photo'], true) ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%">
            <?php else: ?>
              <?= $init ?>
            <?php endif; ?>
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-size:12px;font-weight:700;color:#263238;line-height:1.2"><?= h($t['name']) ?></div>
            <div style="font-size:10px;color:#90a4ae;font-weight:600;margin-top:1px"><?= h($t['tmm_code']) ?></div>
            <?php if ($bn): ?>
              <div style="font-size:9px;color:#9c27b0;margin-top:2px"><i class="fas fa-layer-group" style="font-size:8px"></i> <?= h($bn) ?></div>
            <?php endif; ?>
            <?php if ($t['grading_remarks']): ?>
              <div style="font-size:9px;color:#78909c;font-style:italic;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px">"<?= h($t['grading_remarks']) ?>"</div>
            <?php endif; ?>
            <span style="display:inline-block;margin-top:4px;padding:2px 7px;border-radius:20px;font-size:9px;font-weight:700;background:#e8f5e9;color:#2e7d32">✓ <?= __('Training Passed') ?></span>
          </div>
          <?= $this->Form->create(null, [
            'url'   => ['controller'=>'Trainees','action'=>'doPromoteToApprentice',$t['id']],
            'class' => 'dash-promote-form',
            'method'=> 'post',
            'style' => 'margin:0'
          ]) ?>
            <input type="hidden" name="apprentice_order_id" class="dash-order-inp" value="">
            <button type="submit" title="<?= __('Promote to Apprentice') ?>"
                    onclick="dashSyncOnSubmit(this.form)"
                    style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;
                           border-radius:8px;background:#7b1fa2;color:#fff;border:none;cursor:pointer;
                           font-size:13px;flex-shrink:0;margin-top:2px;transition:background .15s"
                    onmouseover="this.style.background='#4a148c'"
                    onmouseout="this.style.background='#7b1fa2'">
              <i class="fas fa-rocket"></i>
            </button>
          <?= $this->Form->end() ?>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- All Trainees -->
  <div class="tdb-sec">
    <div class="tdb-sec-title"><i class="fas fa-id-badge"></i> <?= __('All Trainees') ?> <span class="tdb-sec-line"></span>
      <a href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'add']) ?>"
         style="font-size:11px;background:#00acc1;color:#fff;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px">
        <i class="fas fa-plus"></i> <?= __('Add Trainee') ?>
      </a>
      <a href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'index']) ?>"
         style="font-size:11px;background:#eceff1;color:#546e7a;padding:4px 12px;border-radius:20px;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:5px">
        <i class="fas fa-list"></i> <?= __('View All') ?>
      </a>
    </div>
    <?php if (empty($traineeList)): ?>
      <div class="tdb-card" style="text-align:center;padding:40px;color:#90a4ae">
        <i class="fas fa-users" style="font-size:40px;margin-bottom:12px;display:block"></i>
        <?= __('No trainees found.') ?>
        <a href="<?= $this->Url->build(['controller'=>'Trainees','action'=>'add']) ?>"> <?= __('Add the first trainee') ?></a>.
      </div>
    <?php else: ?>
      <div class="tr-grid">
        <?php foreach ($traineeList as $t):
          $gc = ($t['master_gender_id'] == 1) ? '#1e88e5' : '#e91e8c';
          $bn = $batchNameMap[(int)$t['trainee_training_batch_id']] ?? ('Batch #' . $t['trainee_training_batch_id']);
          $words = array_slice(explode(' ', $t['name']), 0, 2);
          $init  = implode('', array_map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)), $words));
          $isP   = (int)$t['is_trainee_pass'];
        ?>
        <div class="tr-card" style="--tc:<?= $gc ?>">
          <div class="tr-av">
            <?php if (!empty($t['image_photo']) && file_exists(WWW_ROOT . 'img/trainees/' . $t['image_photo'])): ?>
              <img src="<?= $this->Url->build('/img/trainees/' . $t['image_photo'], true) ?>" alt="">
            <?php else: ?>
              <?= $init ?>
            <?php endif; ?>
          </div>
          <div>
            <div class="tr-name"><?= h($t['name']) ?></div>
            <div class="tr-code"><?= h($t['tmm_code']) ?></div>
            <div class="tr-batch"><i class="fas fa-layer-group" style="font-size:9px"></i> <?= h($bn) ?></div>
            <span class="tr-tag <?= $isP ? 'tag-pass' : 'tag-prog' ?>">
              <?= $isP ? '✓ '.__('Passed') : '⏳ '.__('In Training') ?>
            </span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>
<script>
function dashSyncOrders() {
  const val = document.getElementById('dashOrderSelect')?.value || '';
  document.querySelectorAll('.dash-order-inp').forEach(function(inp) { inp.value = val; });
}
function dashSyncOnSubmit(form) {
  const val = document.getElementById('dashOrderSelect')?.value || '';
  form.querySelector('.dash-order-inp').value = val;
}
dashSyncOrders();
</script>

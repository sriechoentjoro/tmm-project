<?php
$today = time();
$statusColor = [
    'planning'   => ['bg'=>'#e3f2fd','text'=>'#1565c0','dot'=>'#42a5f5'],
    'recruiting' => ['bg'=>'#f3e5f5','text'=>'#6a1b9a','dot'=>'#ab47bc'],
    'ongoing'    => ['bg'=>'#e8f5e9','text'=>'#1b5e20','dot'=>'#43a047'],
    'completed'  => ['bg'=>'#eceff1','text'=>'#37474f','dot'=>'#78909c'],
    'cancelled'  => ['bg'=>'#fce4ec','text'=>'#880e4f','dot'=>'#ef5350'],
];
$statusLabel = [
    'planning'   => 'Planning',
    'recruiting' => 'Recruiting',
    'ongoing'    => 'Ongoing',
    'completed'  => 'Completed',
    'cancelled'  => 'Cancelled',
];

// Summary counts
$counts = ['planning'=>0,'recruiting'=>0,'ongoing'=>0,'completed'=>0,'cancelled'=>0];
foreach ($batches as $b) {
    $s = $b->status ?? 'planning';
    if (isset($counts[$s])) $counts[$s]++;
}
$total = count($batches);
?>
<style>
.ls { font-family:'Segoe UI',sans-serif; color:#263238; padding-bottom:40px; }

/* ── Hero ── */
.ls-hero { background:linear-gradient(135deg,#1b5e20 0%,#2e7d32 50%,#388e3c 100%);
  border-radius:18px; padding:22px 28px; margin-bottom:20px; color:#fff; position:relative; overflow:hidden; }
.ls-hero::before { content:''; position:absolute; right:-40px; top:-40px; width:200px; height:200px;
  background:rgba(255,255,255,.05); border-radius:50%; pointer-events:none; }
.ls-hero-top { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:16px; }
.ls-hero h1 { margin:0; font-size:22px; font-weight:800; display:flex; align-items:center; gap:10px; }
.ls-hero-sub { font-size:12px; opacity:.75; margin-top:3px; }
.ls-kpis { display:flex; gap:10px; flex-wrap:wrap; }
.ls-kpi { background:rgba(255,255,255,.12); border-radius:10px; padding:10px 16px; min-width:80px; text-align:center; }
.ls-kpi .kv { font-size:22px; font-weight:800; line-height:1; }
.ls-kpi .kl { font-size:10px; opacity:.75; text-transform:uppercase; letter-spacing:.4px; margin-top:2px; }
.ls-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:10px;
  font-size:12px; font-weight:700; text-decoration:none; transition:all .15s; white-space:nowrap; }
.ls-btn-white { background:#fff; color:#2e7d32; }
.ls-btn-white:hover { background:#e8f5e9; color:#1b5e20; text-decoration:none; }
.ls-btn-ghost { background:rgba(255,255,255,.15); color:#fff; border:1.5px solid rgba(255,255,255,.3); }
.ls-btn-ghost:hover { background:rgba(255,255,255,.25); color:#fff; text-decoration:none; }

/* ── Toolbar ── */
.ls-toolbar { background:#fff; border-radius:14px; padding:12px 16px; margin-bottom:16px;
  box-shadow:0 2px 8px rgba(0,0,0,.06); display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.ls-search { position:relative; flex:1; min-width:180px; max-width:320px; }
.ls-search i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#90a4ae; font-size:13px; }
.ls-search input { width:100%; padding:8px 12px 8px 30px; border:1.5px solid #e0e0e0; border-radius:9px;
  font-size:13px; outline:none; box-sizing:border-box; }
.ls-search input:focus { border-color:#43a047; box-shadow:0 0 0 3px rgba(67,160,71,.1); }
.ls-chips { display:flex; gap:6px; flex-wrap:wrap; }
.ls-chip { padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700; cursor:pointer;
  border:1.5px solid #e0e0e0; background:#f5f5f5; color:#546e7a; transition:all .15s; display:flex; align-items:center; gap:5px; }
.ls-chip.active { border-color:var(--cc); background:var(--cc); color:#fff; }
.ls-chip .dot { width:7px; height:7px; border-radius:50%; background:currentColor; flex-shrink:0; }

/* ── Card grid ── */
.ls-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:14px; }

/* ── Batch card ── */
.ls-card { background:#fff; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,.06);
  border:1.5px solid #e8f5e9; overflow:hidden; transition:all .18s; }
.ls-card:hover { border-color:#43a047; box-shadow:0 6px 20px rgba(67,160,71,.14); transform:translateY(-2px); }
.ls-card[data-status="cancelled"] { border-color:#fce4ec; opacity:.8; }
.ls-card[data-status="cancelled"]:hover { border-color:#e57373; }

/* Card header strip */
.ls-card-hd { height:5px; background:var(--sc); }

/* Card body */
.ls-card-body { padding:14px 16px 10px; }
.ls-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; margin-bottom:10px; }
.ls-card-code { font-size:10px; font-weight:700; color:#90a4ae; text-transform:uppercase; letter-spacing:.5px; }
.ls-card-name { font-size:14px; font-weight:800; color:#1b5e20; line-height:1.2; margin-top:2px; }
.ls-status-badge { padding:3px 10px; border-radius:20px; font-size:10px; font-weight:700;
  text-transform:uppercase; white-space:nowrap; display:flex; align-items:center; gap:4px; flex-shrink:0; }
.ls-status-badge .dot { width:6px; height:6px; border-radius:50%; background:currentColor; }

/* Location row */
.ls-location { display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px;
  background:#f1f8e9; margin-bottom:10px; }
.ls-location-icon { width:32px; height:32px; background:#43a047; border-radius:8px; flex-shrink:0;
  display:flex; align-items:center; justify-content:center; color:#fff; font-size:13px; }
.ls-location-text { flex:1; min-width:0; }
.ls-location-text .lt-label { font-size:9px; font-weight:700; color:#90a4ae; text-transform:uppercase; letter-spacing:.4px; }
.ls-location-text .lt-val { font-size:13px; font-weight:700; color:#263238; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

/* Progress bar */
.ls-prog-wrap { margin-bottom:10px; }
.ls-prog-labels { display:flex; justify-content:space-between; font-size:10px; color:#90a4ae; margin-bottom:4px; }
.ls-prog-bar { height:6px; background:#e8f5e9; border-radius:3px; overflow:hidden; }
.ls-prog-fill { height:6px; border-radius:3px; background:linear-gradient(90deg,#66bb6a,#43a047); transition:width .4s; }

/* Capacity indicator */
.ls-capacity { display:flex; align-items:center; gap:8px; font-size:11px; color:#546e7a; margin-bottom:10px; }
.ls-cap-bar { flex:1; height:5px; background:#f0f4c3; border-radius:3px; overflow:hidden; }
.ls-cap-fill { height:5px; border-radius:3px; background:#cddc39; }
.ls-cap-fill.full  { background:#ef5350; }
.ls-cap-fill.high  { background:#ff9800; }

/* Staff row */
.ls-staff { display:flex; gap:14px; flex-wrap:wrap; padding:8px 0; border-top:1px solid #f0f0f0; margin-bottom:8px; }
.ls-staff-item { font-size:10px; color:#78909c; display:flex; align-items:center; gap:4px; }
.ls-staff-item b { color:#37474f; font-size:11px; }

/* Footer */
.ls-card-footer { display:flex; gap:6px; align-items:center; padding:10px 16px; border-top:1px solid #f0f0f0; }
.ls-act { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:8px;
  font-size:11px; font-weight:700; text-decoration:none; transition:all .15s; }
.la-shift { background:#e8f5e9; color:#2e7d32; }
.la-shift:hover { background:#2e7d32; color:#fff; text-decoration:none; }
.la-view { background:#e3f2fd; color:#1565c0; }
.la-view:hover { background:#1565c0; color:#fff; text-decoration:none; }
.la-edit { background:#fff3e0; color:#e65100; }
.la-edit:hover { background:#e65100; color:#fff; text-decoration:none; }

/* Empty */
.ls-empty { text-align:center; padding:60px 20px; color:#b0bec5; }
.ls-empty i { font-size:48px; margin-bottom:14px; display:block; opacity:.5; }
</style>

<div class="ls">

  <!-- Hero -->
  <div class="ls-hero">
    <div class="ls-hero-top">
      <div>
        <h1><i class="fas fa-exchange-alt"></i> Location Shifting</h1>
        <div class="ls-hero-sub">Manage training batch locations — track and update location assignments per batch</div>
      </div>
      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <a class="ls-btn ls-btn-white" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'add']) ?>">
          <i class="fas fa-plus"></i> New Batch
        </a>
        <a class="ls-btn ls-btn-ghost" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'index']) ?>">
          <i class="fas fa-layer-group"></i> All Batches
        </a>
      </div>
    </div>
    <div class="ls-kpis">
      <div class="ls-kpi"><div class="kv"><?= $total ?></div><div class="kl">Total</div></div>
      <?php foreach ($counts as $st => $n): if (!$n) continue; ?>
      <div class="ls-kpi">
        <div class="kv"><?= $n ?></div>
        <div class="kl"><?= ucfirst($st) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="ls-toolbar">
    <div class="ls-search">
      <i class="fas fa-search"></i>
      <input type="text" id="lsSearch" placeholder="Search batch name, code, location…">
    </div>
    <div class="ls-chips">
      <span class="ls-chip active" style="--cc:#43a047" data-filter="all">All <span style="background:rgba(0,0,0,.12);border-radius:20px;padding:1px 6px"><?= $total ?></span></span>
      <?php foreach ($counts as $st => $n): if (!$n) continue;
        $sc = $statusColor[$st] ?? ['dot'=>'#90a4ae','bg'=>'#f5f5f5','text'=>'#546e7a'];
      ?>
      <span class="ls-chip" style="--cc:<?= $sc['dot'] ?>" data-filter="<?= $st ?>">
        <span class="dot" style="background:<?= $sc['dot'] ?>"></span>
        <?= $statusLabel[$st] ?> <span style="background:rgba(0,0,0,.1);border-radius:20px;padding:1px 6px"><?= $n ?></span>
      </span>
      <?php endforeach; ?>
    </div>
    <div style="margin-left:auto;display:flex;gap:6px">
      <select id="lsSort" style="padding:7px 10px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:12px;color:#546e7a;background:#fff;outline:none">
        <option value="">Sort by…</option>
        <option value="name">Name A–Z</option>
        <option value="date">Start Date</option>
        <option value="cap">Capacity %</option>
      </select>
    </div>
  </div>

  <!-- Cards -->
  <?php if (empty($batches)): ?>
    <div class="ls-empty">
      <i class="fas fa-map-marker-alt"></i>
      <div style="font-size:15px;font-weight:700;color:#546e7a;margin-bottom:8px">No training batches found</div>
      <p><a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'add']) ?>">Create the first batch</a> to get started.</p>
    </div>
  <?php else: ?>
  <div class="ls-grid" id="lsGrid">
    <?php foreach ($batches as $b):
      $st     = $b->status ?? 'planning';
      $sc     = $statusColor[$st]  ?? ['bg'=>'#eceff1','text'=>'#546e7a','dot'=>'#90a4ae'];
      $slabel = $statusLabel[$st]  ?? ucfirst($st);
      $startTs  = $b->start_date ? strtotime($b->start_date->format('Y-m-d')) : null;
      $endTs    = $b->end_date   ? strtotime($b->end_date->format('Y-m-d'))   : null;
      $progPct  = 0;
      if ($startTs && $endTs && $endTs > $startTs) {
        $progPct = min(100, max(0, round(($today - $startTs) / ($endTs - $startTs) * 100)));
      }
      if ($st === 'completed') $progPct = 100;
      $capPct  = ($b->capacity > 0) ? min(100, round($b->enrolled_count / $b->capacity * 100)) : 0;
      $capClass = $capPct >= 100 ? 'full' : ($capPct >= 80 ? 'high' : '');
      $searchStr = strtolower(($b->batch_code ?? '') . ' ' . ($b->batch_name ?? '') . ' ' . ($b->training_location ?? ''));
    ?>
    <div class="ls-card" data-status="<?= $st ?>"
         data-search="<?= htmlspecialchars($searchStr) ?>"
         data-name="<?= htmlspecialchars(strtolower($b->batch_name ?? '')) ?>"
         data-date="<?= $b->start_date ? $b->start_date->format('Y-m-d') : '9999' ?>"
         data-cap="<?= $capPct ?>">

      <div class="ls-card-hd" style="--sc:<?= $sc['dot'] ?>"></div>

      <div class="ls-card-body">
        <!-- Top: code + name + status -->
        <div class="ls-card-top">
          <div>
            <div class="ls-card-code"><?= h($b->batch_code) ?></div>
            <div class="ls-card-name"><?= h($b->batch_name) ?></div>
          </div>
          <span class="ls-status-badge" style="background:<?= $sc['bg'] ?>;color:<?= $sc['text'] ?>">
            <span class="dot"></span><?= $slabel ?>
          </span>
        </div>

        <!-- Location -->
        <div class="ls-location">
          <div class="ls-location-icon" style="background:<?= $sc['dot'] ?>">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <div class="ls-location-text">
            <div class="lt-label">Current Location</div>
            <div class="lt-val"><?= h($b->training_location ?: '—') ?></div>
          </div>
          <a href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'edit',$b->id]) ?>"
             title="Shift location" style="display:inline-flex;align-items:center;justify-content:center;
             width:28px;height:28px;border-radius:7px;background:<?= $sc['dot'] ?>;color:#fff;
             text-decoration:none;font-size:11px;flex-shrink:0">
            <i class="fas fa-pen"></i>
          </a>
        </div>

        <!-- Timeline progress -->
        <?php if ($startTs && $endTs): ?>
        <div class="ls-prog-wrap">
          <div class="ls-prog-labels">
            <span><?= $b->start_date->format('d M Y') ?></span>
            <span style="font-weight:700;color:#43a047"><?= $progPct ?>%</span>
            <span><?= $b->end_date->format('d M Y') ?></span>
          </div>
          <div class="ls-prog-bar"><div class="ls-prog-fill" style="width:<?= $progPct ?>%"></div></div>
        </div>
        <?php endif; ?>

        <!-- Capacity -->
        <div class="ls-capacity">
          <i class="fas fa-users" style="font-size:11px;color:#8d6e63"></i>
          <span style="font-weight:700;color:#37474f"><?= $b->enrolled_count ?>/<?= $b->capacity ?></span>
          <div class="ls-cap-bar">
            <div class="ls-cap-fill <?= $capClass ?>" style="width:<?= $capPct ?>%"></div>
          </div>
          <span><?= $capPct ?>%</span>
        </div>

        <!-- Instructor / coordinator -->
        <?php if ($b->instructor_name || $b->coordinator_name): ?>
        <div class="ls-staff">
          <?php if ($b->instructor_name): ?>
            <div class="ls-staff-item"><i class="fas fa-chalkboard-teacher"></i> <b><?= h($b->instructor_name) ?></b></div>
          <?php endif; ?>
          <?php if ($b->coordinator_name): ?>
            <div class="ls-staff-item"><i class="fas fa-user-tie"></i> <b><?= h($b->coordinator_name) ?></b></div>
          <?php endif; ?>
          <div class="ls-staff-item"><i class="fas fa-clock"></i> <?= $b->duration_months ?> mo</div>
        </div>
        <?php endif; ?>
      </div>

      <!-- Footer actions -->
      <div class="ls-card-footer">
        <a class="ls-act la-shift" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'edit',$b->id]) ?>">
          <i class="fas fa-exchange-alt"></i> Shift Location
        </a>
        <a class="ls-act la-view" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'view',$b->id]) ?>">
          <i class="fas fa-eye"></i> View
        </a>
        <a class="ls-act la-edit" href="<?= $this->Url->build(['controller'=>'Trainings','action'=>'edit',$b->id]) ?>">
          <i class="fas fa-pen"></i> Edit
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>

<script>
(function(){
  var grid  = document.getElementById('lsGrid');
  var input = document.getElementById('lsSearch');
  var chips = document.querySelectorAll('.ls-chip');
  var sort  = document.getElementById('lsSort');
  var activeFilter = 'all';

  function cards() { return grid ? Array.from(grid.querySelectorAll('.ls-card')) : []; }

  function applyFilters() {
    if (!grid) return;
    cards().forEach(function(c) {
      var fOk = activeFilter === 'all' || c.dataset.status === activeFilter;
      var q   = (input ? input.value.trim().toLowerCase() : '');
      var sOk = !q || (c.dataset.search||'').includes(q);
      c.style.display = (fOk && sOk) ? '' : 'none';
    });
  }

  function applySort(val) {
    if (!grid || !val) return;
    var cs = cards();
    cs.sort(function(a,b){
      if (val==='name') return (a.dataset.name||'').localeCompare(b.dataset.name||'');
      if (val==='date') return (a.dataset.date||'').localeCompare(b.dataset.date||'');
      if (val==='cap')  return parseInt(b.dataset.cap||0) - parseInt(a.dataset.cap||0);
      return 0;
    });
    cs.forEach(function(c){ grid.appendChild(c); });
  }

  chips.forEach(function(chip){
    chip.addEventListener('click', function(){
      chips.forEach(function(c){ c.classList.remove('active'); });
      this.classList.add('active');
      activeFilter = this.dataset.filter;
      applyFilters();
    });
  });

  if (input)  input.addEventListener('input', applyFilters);
  if (sort)   sort.addEventListener('change', function(){ applySort(this.value); });
})();
</script>

<?= $this->element('process_flow_help') ?>

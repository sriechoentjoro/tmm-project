<?php
/**
 * @var \App\View\AppView    $this
 * @var array  $additionalTrainings
 * @var array  $enrollmentCounts
 * @var array  $enrollmentsByProgram
 * @var array  $allTrainees
 * @var array  $batchRows
 * @var array  $batchMap
 * @var int|null $trainingId
 * @var array|null $currentBatch
 * @var array  $modulesByCategory
 * @var array  $categoryMeta
 * @var int    $totalHours
 * @var int    $totalModules
 * @var array  $statusCounts
 */
$_roles      = (array)$this->request->session()->read('Auth.User.role_names');
$_isAdmin    = in_array('administrator', $_roles);
$_isTraining = in_array('tmm-training',  $_roles);
$_canManage  = $_isAdmin || $_isTraining;

$statusMeta = [
    'planned'   => ['bg'=>'#e3f2fd','color'=>'#1565c0','dot'=>'#1e88e5','label'=>'Planned'],
    'active'    => ['bg'=>'#e8f5e9','color'=>'#2e7d32','dot'=>'#43a047','label'=>'Active'],
    'ongoing'   => ['bg'=>'#fff8e1','color'=>'#e65100','dot'=>'#fb8c00','label'=>'Ongoing'],
    'completed' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','dot'=>'#8e24aa','label'=>'Completed'],
    'cancelled' => ['bg'=>'#fbe9e7','color'=>'#b71c1c','dot'=>'#e53935','label'=>'Cancelled'],
];
$enrollStatusMeta = [
    'enrolled'  => ['bg'=>'#e3f2fd','color'=>'#1565c0','label'=>'Enrolled'],
    'active'    => ['bg'=>'#e8f5e9','color'=>'#2e7d32','label'=>'Active'],
    'completed' => ['bg'=>'#f3e5f5','color'=>'#6a1b9a','label'=>'Completed'],
    'dropped'   => ['bg'=>'#fbe9e7','color'=>'#b71c1c','label'=>'Dropped'],
];
$today = new \DateTime();

$totalEnrolled = 0;
foreach ($enrollmentsByProgram ?? [] as $rows) $totalEnrolled += count($rows);
$statusCounts = $statusCounts ?? [];
?>
<style>
.at-hero{background:linear-gradient(135deg,#1a237e 0%,#283593 55%,#3949ab 100%);color:#fff;border-radius:16px;padding:28px 32px;margin-bottom:20px;box-shadow:0 4px 24px rgba(26,35,126,.25)}
.at-hero-title{font-size:22px;font-weight:800;margin-bottom:3px}
.at-hero-sub{font-size:13px;opacity:.8}
.at-kpi-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.at-kpi{background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:10px 18px;text-align:center;min-width:90px}
.at-kpi-val{font-size:24px;font-weight:800;line-height:1}
.at-kpi-lbl{font-size:10px;font-weight:600;opacity:.7;text-transform:uppercase;letter-spacing:.5px;margin-top:2px}
.at-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;transition:all .15s;text-decoration:none}
.at-btn-white{background:#fff;color:#1a237e}.at-btn-white:hover{background:#e8eaf6;color:#1a237e}

/* Batch selector bar */
.at-batch-bar{background:#fff;border:1px solid #e1e8f0;border-radius:12px;padding:14px 20px;margin-bottom:18px;display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.at-batch-pill{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;text-decoration:none;border:2px solid transparent;transition:all .15s}
.at-batch-pill.active{background:#1a237e;color:#fff;border-color:#1a237e}
.at-batch-pill:not(.active){background:#f5f7fa;color:#546e7a;border-color:#e1e8f0}
.at-batch-pill:not(.active):hover{border-color:#9fa8da;color:#1a237e}

.at-tabs{display:flex;gap:4px;border-bottom:2px solid #e1e8f0;margin-bottom:18px}
.at-tab{padding:9px 18px;font-size:13px;font-weight:600;color:#78909c;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;transition:all .15s;background:none;border-top:none;border-left:none;border-right:none}
.at-tab.active{color:#1a237e;border-bottom-color:#1a237e}
.at-tab-panel{display:none}.at-tab-panel.active{display:block}

.at-prog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px}
.at-prog-card{border:1px solid #e1e8f0;border-radius:10px;overflow:hidden;background:#fff;transition:box-shadow .15s;display:flex;flex-direction:column}
.at-prog-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.1)}
.at-prog-stripe{height:4px;flex-shrink:0}
.at-prog-body{padding:14px 16px;flex:1}
.at-prog-title{font-size:14px;font-weight:700;color:#1a2332;margin-bottom:4px;line-height:1.3}
.at-prog-meta{font-size:11px;color:#78909c;display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px}
.at-prog-desc{font-size:12px;color:#546e7a;line-height:1.5;margin-bottom:8px}
.at-prog-foot{display:flex;align-items:center;justify-content:space-between;padding:9px 16px;border-top:1px solid #f0f4f8;background:#fafbfc;flex-wrap:wrap;gap:6px;flex-shrink:0}
.at-status-badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700}
.at-status-dot{width:7px;height:7px;border-radius:50%;display:inline-block}

/* Enrollment bar on card */
.at-enroll-bar{padding:10px 16px;border-top:1px solid #eef2f8;background:#f4f6fb;flex-shrink:0}
.at-enroll-hdr{display:flex;align-items:center;gap:6px;margin-bottom:6px}
.at-trainee-chips{display:flex;flex-wrap:wrap;gap:3px}
.at-tchip{display:inline-flex;align-items:center;gap:3px;padding:2px 8px;border-radius:16px;font-size:10px;font-weight:700;background:#e8eaf6;color:#1a237e}
.at-tchip.passed{background:#e8f5e9;color:#2e7d32}
.at-tchip.dropped{background:#fbe9e7;color:#b71c1c}

/* Enrollment modal */
.enroll-tbl{width:100%;border-collapse:collapse;font-size:12px}
.enroll-tbl th{padding:7px 10px;background:#f0f4f8;font-weight:700;font-size:11px;text-align:left;color:#546e7a;border-bottom:1px solid #e1e8f0}
.enroll-tbl td{padding:7px 10px;border-bottom:1px solid #f5f7fa;vertical-align:middle}
.enroll-tbl tr:last-child td{border-bottom:none}
.enroll-tbl tr:hover td{background:#fafbfc}
.ein{width:100%;padding:4px 7px;border:1px solid #e1e8f0;border-radius:5px;font-size:11px;box-sizing:border-box}

.at-cat-hdr{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.at-cat-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:14px;color:#fff;flex-shrink:0}
.at-mod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:10px}
.at-mod-card{border:1px solid #e8edf2;border-radius:9px;padding:12px 14px;background:#fff;position:relative;overflow:hidden}
.at-mod-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.at-mod-code{font-size:10px;font-weight:700;letter-spacing:.8px;margin-bottom:3px;opacity:.5;font-family:monospace}
.at-mod-name{font-size:13px;font-weight:700;color:#37474f;line-height:1.3;margin-bottom:2px}
.at-mod-jpn{font-size:11px;color:#90a4ae;margin-bottom:5px}
.at-mod-chips{display:flex;gap:5px;flex-wrap:wrap;margin-top:5px}
.at-mod-chip{font-size:10px;font-weight:600;padding:2px 7px;border-radius:7px}
.at-chip-hrs{background:#e3f2fd;color:#1565c0}.at-chip-pass{background:#e8f5e9;color:#2e7d32}.at-chip-wt{background:#f3e5f5;color:#6a1b9a}
.at-empty{text-align:center;padding:40px 20px;color:#90a4ae}
.at-empty i{font-size:40px;margin-bottom:12px;display:block;opacity:.4}
@media(max-width:600px){.at-hero{padding:18px 14px}.at-hero-title{font-size:17px}.at-kpi-val{font-size:20px}}
</style>

<!-- Hero -->
<div class="at-hero">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div>
      <?php if ($currentBatch): ?>
        <div style="font-size:11px;font-weight:600;opacity:.7;margin-bottom:4px;text-transform:uppercase;letter-spacing:.5px">
          <i class="fas fa-layer-group"></i> <?= h($currentBatch['batch_code']) ?>
        </div>
      <?php endif; ?>
      <div class="at-hero-title">
        <i class="fas fa-graduation-cap"></i>
        <?= $currentBatch ? h($currentBatch['batch_name']) : 'Additional Training' ?>
      </div>
      <div class="at-hero-sub">
        <?= $currentBatch
          ? 'Supplementary programs for this training batch'
          : 'All supplementary training programs across batches' ?>
      </div>
    </div>
    <?php if ($_canManage): ?>
    <button type="button" class="at-btn at-btn-white" onclick="openAddModal()">
      <i class="fas fa-plus"></i> New Program
    </button>
    <?php endif; ?>
  </div>
  <div class="at-kpi-row">
    <div class="at-kpi"><div class="at-kpi-val"><?= count($additionalTrainings) ?></div><div class="at-kpi-lbl">Programs</div></div>
    <div class="at-kpi"><div class="at-kpi-val"><?= $totalEnrolled ?></div><div class="at-kpi-lbl">Enrollments</div></div>
    <div class="at-kpi"><div class="at-kpi-val"><?= $totalModules ?></div><div class="at-kpi-lbl">Modules</div></div>
    <div class="at-kpi"><div class="at-kpi-val"><?= $totalHours ?></div><div class="at-kpi-lbl">Total Hours</div></div>
  </div>
</div>

<!-- Batch selector -->
<div class="at-batch-bar">
  <span style="font-size:11px;font-weight:700;color:#90a4ae;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap"><i class="fas fa-filter"></i> Batch</span>
  <a href="<?= $this->Url->build(['action'=>'additionalTraining']) ?>"
     class="at-batch-pill <?= !$trainingId ? 'active' : '' ?>">
    <i class="fas fa-th-list"></i> All Batches
  </a>
  <?php foreach ($batchRows as $b): ?>
    <a href="<?= $this->Url->build(['action'=>'additionalTraining', $b['id']]) ?>"
       class="at-batch-pill <?= $trainingId == $b['id'] ? 'active' : '' ?>">
      <?= h($b['batch_code']) ?>
    </a>
  <?php endforeach; ?>
</div>

<!-- Tabs -->
<div class="at-tabs">
  <button class="at-tab active" onclick="switchTab('programs',this)">
    <i class="fas fa-list-alt"></i> Programs (<?= count($additionalTrainings) ?>)
  </button>
  <button class="at-tab" onclick="switchTab('modules',this)">
    <i class="fas fa-book-open"></i> Module Catalog (<?= $totalModules ?>)
  </button>
</div>

<!-- Programs tab -->
<div class="at-tab-panel active" id="tab-programs">
  <?php if (empty($additionalTrainings)): ?>
    <div style="background:#fff;border:1px solid #e1e8f0;border-radius:12px">
      <div class="at-empty">
        <i class="fas fa-chalkboard-teacher"></i>
        <div style="font-size:15px;font-weight:700;color:#546e7a;margin-bottom:5px">No Programs Yet</div>
        <div style="font-size:12px;margin-bottom:14px">
          <?= $currentBatch
            ? 'Add supplementary programs to '.h($currentBatch['batch_name']).'.'
            : 'Select a batch above or create the first program.' ?>
        </div>
        <?php if ($_canManage): ?>
          <button type="button" onclick="openAddModal()"
                  style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;background:#1a237e;color:#fff;border:none;cursor:pointer;font-size:13px;font-weight:700">
            <i class="fas fa-plus"></i> Create Program
          </button>
        <?php endif; ?>
      </div>
    </div>
  <?php else: ?>
    <!-- Search + status filter -->
    <div style="display:flex;gap:8px;margin-bottom:14px;align-items:center;flex-wrap:wrap">
      <input type="text" id="progSearch" placeholder="Search programs..."
             oninput="filterProgs(this.value)"
             style="flex:1;min-width:160px;padding:8px 12px;border:1px solid #e1e8f0;border-radius:8px;font-size:12px">
      <?php foreach ($statusMeta as $skey => $sm): if (!isset($statusCounts[$skey])) continue; ?>
        <button onclick="filterByStatus('<?= $skey ?>',this)"
                style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:12px;font-size:11px;font-weight:700;background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>;border:1px solid <?= $sm['dot'] ?>;cursor:pointer">
          <span class="at-status-dot" style="background:<?= $sm['dot'] ?>"></span>
          <?= $sm['label'] ?> (<?= $statusCounts[$skey] ?>)
        </button>
      <?php endforeach; ?>
      <button onclick="clearFilter()" style="padding:5px 11px;border-radius:10px;background:#f5f7fa;color:#78909c;border:1px solid #e1e8f0;cursor:pointer;font-size:11px;font-weight:600">All</button>
    </div>

    <div class="at-prog-grid" id="progGrid">
      <?php foreach ($additionalTrainings as $at):
        $pid  = (int)$at['id'];
        $st   = $at['status'] ?? 'planned';
        $sm   = $statusMeta[$st] ?? $statusMeta['planned'];
        $sd   = !empty($at['start_date']) ? new \DateTime($at['start_date']) : null;
        $ed   = !empty($at['end_date'])   ? new \DateTime($at['end_date'])   : null;
        $prog = 0;
        if ($sd && $ed && $today >= $sd) {
            $total = max(1, $sd->diff($ed)->days);
            $done  = $sd->diff($today < $ed ? $today : $ed)->days;
            $prog  = min(100, round($done / $total * 100));
        }
        $progEnrollments = $enrollmentsByProgram[$pid] ?? [];
        $enrollCount     = count($progEnrollments);
        $passedCount     = count(array_filter($progEnrollments, fn($e) => $e['is_passed'] == 1));
        // Parent batch label
        $parentBatch = $at['training_id'] ? ($batchMap[$at['training_id']] ?? null) : null;
      ?>
      <div class="at-prog-card" data-status="<?= $st ?>" data-title="<?= strtolower(h($at['title'])) ?>">
        <div class="at-prog-stripe" style="background:<?= $sm['dot'] ?>"></div>

        <div class="at-prog-body">
          <?php if ($parentBatch && !$trainingId): ?>
            <div style="font-size:10px;font-weight:700;color:#5c6bc0;margin-bottom:4px;letter-spacing:.3px">
              <i class="fas fa-layer-group"></i> <?= h($parentBatch['batch_code']) ?>
            </div>
          <?php endif; ?>
          <div class="at-prog-title"><?= h($at['title']) ?></div>
          <div class="at-prog-meta">
            <?php if ($at['location']): ?><span><i class="fas fa-map-marker-alt"></i> <?= h($at['location']) ?></span><?php endif; ?>
            <?php if ($sd): ?><span><i class="fas fa-calendar"></i> <?= $sd->format('d M Y') ?><?= $ed ? ' → '.$ed->format('d M Y') : '' ?></span><?php endif; ?>
          </div>
          <?php if ($at['description']): ?>
            <div class="at-prog-desc"><?= h(mb_substr($at['description'],0,90)).(mb_strlen($at['description'])>90?'…':'') ?></div>
          <?php endif; ?>
          <?php if ($prog > 0): ?>
            <div>
              <div style="height:5px;background:#f0f4f8;border-radius:3px;overflow:hidden">
                <div style="height:100%;width:<?= $prog ?>%;background:<?= $sm['dot'] ?>;border-radius:3px"></div>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:10px;color:#90a4ae;margin-top:2px"><span>Progress</span><span><?= $prog ?>%</span></div>
            </div>
          <?php endif; ?>
        </div>

        <!-- Enrollment bar -->
        <div class="at-enroll-bar">
          <div class="at-enroll-hdr">
            <i class="fas fa-users" style="color:#5c6bc0;font-size:11px"></i>
            <span style="font-size:11px;font-weight:700;color:#37474f"><?= $enrollCount ?> enrolled</span>
            <?php if ($passedCount > 0): ?>
              <span style="font-size:10px;font-weight:700;background:#e8f5e9;color:#2e7d32;padding:1px 7px;border-radius:9px"><i class="fas fa-check"></i> <?= $passedCount ?> passed</span>
            <?php endif; ?>
            <?php if ($_canManage): ?>
            <button onclick="openEnrollModal(<?= $pid ?>,<?= htmlspecialchars(json_encode($at),ENT_QUOTES) ?>)"
                    style="margin-left:auto;display:inline-flex;align-items:center;gap:3px;padding:3px 9px;border-radius:7px;font-size:10px;font-weight:700;background:#5c6bc0;color:#fff;border:none;cursor:pointer">
              <i class="fas fa-user-plus"></i> Manage
            </button>
            <?php endif; ?>
          </div>
          <?php if (!empty($progEnrollments)): ?>
          <div class="at-trainee-chips">
            <?php foreach ($progEnrollments as $enr):
              $cls = $enr['is_passed'] == 1 ? 'passed' : ($enr['status'] === 'dropped' ? 'dropped' : '');
            ?>
            <span class="at-tchip <?= $cls ?>" title="<?= h($enr['trainee']['name'] ?? '') ?>">
              <?php if ($enr['is_passed'] == 1): ?><i class="fas fa-check-circle"></i><?php elseif ($enr['status']==='dropped'): ?><i class="fas fa-times-circle"></i><?php else: ?><i class="fas fa-user"></i><?php endif; ?>
              <?= h($enr['trainee']['tmm_code'] ?? '') ?>
            </span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>

        <div class="at-prog-foot">
          <span class="at-status-badge" style="background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>">
            <span class="at-status-dot" style="background:<?= $sm['dot'] ?>"></span> <?= $sm['label'] ?>
          </span>
          <?php if ($_canManage): ?>
          <button onclick="openEditModal(<?= $pid ?>,<?= htmlspecialchars(json_encode($at),ENT_QUOTES) ?>)"
                  style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:600;background:#e3f2fd;color:#1565c0;border:none;cursor:pointer">
            <i class="fas fa-edit"></i> Edit
          </button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Module Catalog tab -->
<div class="at-tab-panel" id="tab-modules">
  <?php if (empty($modulesByCategory)): ?>
    <div style="background:#fff;border:1px solid #e1e8f0;border-radius:12px">
      <div class="at-empty"><i class="fas fa-book-open"></i><div>No modules found</div></div>
    </div>
  <?php else: ?>
  <!-- Breakdown -->
  <div style="background:#fff;border:1px solid #e1e8f0;border-radius:12px;margin-bottom:18px;overflow:hidden">
    <div style="padding:12px 18px;border-bottom:1px solid #f0f4f8;background:#fafbfc;display:flex;align-items:center;justify-content:space-between">
      <span style="font-size:14px;font-weight:700;color:#1a237e"><i class="fas fa-chart-bar"></i> Curriculum Breakdown</span>
      <span style="font-size:11px;font-weight:700;background:#e8eaf6;color:#1a237e;padding:3px 10px;border-radius:12px"><?= $totalHours ?>h across <?= $totalModules ?> modules</span>
    </div>
    <div style="padding:16px 20px">
      <?php foreach ($categoryMeta as $catKey => $cat):
        if (empty($modulesByCategory[$catKey])) continue;
        $catH = array_sum(array_column($modulesByCategory[$catKey],'duration_hours'));
        $catP = $totalHours > 0 ? round($catH / $totalHours * 100) : 0;
      ?>
      <div style="margin-bottom:11px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px">
          <span style="font-size:12px;font-weight:700;color:<?= $cat['color'] ?>;display:flex;align-items:center;gap:5px">
            <i class="fas <?= $cat['icon'] ?>"></i> <?= $cat['label'] ?>
            <span style="font-size:10px;font-weight:400;color:#90a4ae">(<?= count($modulesByCategory[$catKey]) ?> modules)</span>
          </span>
          <span style="font-size:11px;font-weight:700;color:<?= $cat['color'] ?>"><?= $catH ?>h · <?= $catP ?>%</span>
        </div>
        <div style="height:9px;background:#f0f4f8;border-radius:5px;overflow:hidden">
          <div style="height:100%;width:<?= $catP ?>%;background:<?= $cat['color'] ?>;border-radius:5px"></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php foreach ($categoryMeta as $catKey => $cat):
    if (empty($modulesByCategory[$catKey])) continue;
    $catMods = $modulesByCategory[$catKey];
  ?>
  <div style="margin-bottom:20px">
    <div class="at-cat-hdr">
      <div class="at-cat-icon" style="background:<?= $cat['color'] ?>"><i class="fas <?= $cat['icon'] ?>"></i></div>
      <div>
        <div style="font-size:13px;font-weight:700;color:<?= $cat['color'] ?>"><?= $cat['label'] ?></div>
        <div style="font-size:10px;color:#90a4ae"><?= count($catMods) ?> module<?= count($catMods)>1?'s':'' ?> · <?= array_sum(array_column($catMods,'duration_hours')) ?> hours</div>
      </div>
      <span style="margin-left:auto;font-size:11px;font-weight:700;padding:2px 9px;border-radius:10px;background:<?= $cat['bg'] ?>;color:<?= $cat['color'] ?>"><?= count($catMods) ?></span>
    </div>
    <div class="at-mod-grid">
      <?php foreach ($catMods as $mod): ?>
      <div class="at-mod-card">
        <style>.at-mod-card:nth-child(n){}</style>
        <div style="position:absolute;top:0;left:0;right:0;height:3px;background:<?= $cat['color'] ?>"></div>
        <div class="at-mod-code"><?= h($mod['module_code']) ?></div>
        <div class="at-mod-name"><?= h($mod['module_name']) ?></div>
        <?php if ($mod['module_name_japanese']): ?><div class="at-mod-jpn"><?= h($mod['module_name_japanese']) ?></div><?php endif; ?>
        <div class="at-mod-chips">
          <span class="at-mod-chip at-chip-hrs"><i class="fas fa-clock"></i> <?= $mod['duration_hours'] ?>h</span>
          <span class="at-mod-chip at-chip-pass">Pass ≥<?= (int)$mod['passing_score'] ?>%</span>
          <span class="at-mod-chip at-chip-wt"><?= (int)$mod['weight_percentage'] ?>% wt</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Program Add/Edit Modal -->
<?php if ($_canManage): ?>
<div id="atModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1050;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:14px;width:90%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3)">
    <div style="padding:16px 22px;border-bottom:1px solid #e1e8f0;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;z-index:1">
      <h4 id="atModalTitle" style="margin:0;font-size:15px;font-weight:800;color:#1a237e"></h4>
      <button onclick="closeModal()" style="background:none;border:none;font-size:22px;color:#90a4ae;cursor:pointer">&times;</button>
    </div>
    <div style="padding:18px 22px">
      <form id="atForm" method="post" action="<?= $this->Url->build(['action'=>'saveAdditionalTraining']) ?>">
        <input type="hidden" id="atId" name="id">
        <div style="display:grid;gap:13px">
          <!-- Parent batch selector -->
          <div>
            <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">
              <i class="fas fa-layer-group" style="color:#5c6bc0"></i> Parent Training Batch <span style="color:#e53935">*</span>
            </label>
            <select id="atBatch" name="training_id" required
                    style="width:100%;padding:9px 10px;border:1.5px solid #c5cae9;border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box">
              <option value="">— Select training batch —</option>
              <?php foreach ($batchRows as $b): ?>
                <option value="<?= $b['id'] ?>" <?= $trainingId == $b['id'] ? 'selected' : '' ?>>
                  <?= h($b['batch_code']) ?> · <?= h($b['batch_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">Program Title <span style="color:#e53935">*</span></label>
            <input id="atTitle" name="title" required type="text" maxlength="256"
                   placeholder="e.g. Advanced Japanese Language Intensive"
                   style="width:100%;padding:9px 12px;border:1.5px solid #e1e8f0;border-radius:8px;font-size:13px;box-sizing:border-box">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div>
              <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">Start Date</label>
              <input id="atStart" name="start_date" type="text" class="datepicker" autocomplete="off" placeholder="YYYY-MM-DD"
                     style="width:100%;padding:9px 12px;border:1.5px solid #e1e8f0;border-radius:8px;font-size:13px;box-sizing:border-box">
            </div>
            <div>
              <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">End Date</label>
              <input id="atEnd" name="end_date" type="text" class="datepicker" autocomplete="off" placeholder="YYYY-MM-DD"
                     style="width:100%;padding:9px 12px;border:1.5px solid #e1e8f0;border-radius:8px;font-size:13px;box-sizing:border-box">
            </div>
          </div>
          <div>
            <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">Location</label>
            <input id="atLoc" name="location" type="text" maxlength="256" placeholder="e.g. TMM Training Center"
                   style="width:100%;padding:9px 12px;border:1.5px solid #e1e8f0;border-radius:8px;font-size:13px;box-sizing:border-box">
          </div>
          <div>
            <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">Status</label>
            <div style="display:flex;gap:7px;flex-wrap:wrap" id="atStatusPills">
              <?php foreach ($statusMeta as $skey => $sm): ?>
                <div class="at-spill" data-val="<?= $skey ?>" onclick="selectAtStatus('<?= $skey ?>')"
                     style="padding:5px 11px;border-radius:14px;font-size:11px;font-weight:700;cursor:pointer;border:2px solid <?= $sm['dot'] ?>;background:<?= $sm['bg'] ?>;color:<?= $sm['color'] ?>;transition:all .15s">
                  <?= $sm['label'] ?>
                </div>
              <?php endforeach; ?>
            </div>
            <input type="hidden" id="atStatus" name="status" value="planned">
          </div>
          <div>
            <label style="font-size:12px;font-weight:700;color:#546e7a;display:block;margin-bottom:5px">Description</label>
            <textarea id="atDesc" name="description" rows="3" placeholder="Objectives and content..."
                      style="width:100%;padding:9px 12px;border:1.5px solid #e1e8f0;border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box"></textarea>
          </div>
        </div>
        <div style="display:flex;gap:9px;margin-top:16px">
          <button type="button" onclick="closeModal()"
                  style="flex:1;padding:9px;border:1px solid #e1e8f0;border-radius:8px;background:#fff;font-size:13px;font-weight:600;cursor:pointer;color:#546e7a">Cancel</button>
          <button type="submit"
                  style="flex:2;padding:9px;border:none;border-radius:8px;background:#1a237e;color:#fff;font-size:13px;font-weight:700;cursor:pointer">
            <i class="fas fa-save"></i> <span id="atSubmitLabel">Save Program</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enrollment Management Modal -->
<div id="enrollModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:1060;align-items:flex-start;justify-content:center;padding:20px 10px;overflow-y:auto">
  <div style="background:#fff;border-radius:14px;width:100%;max-width:680px;box-shadow:0 20px 60px rgba(0,0,0,.35)">
    <div style="padding:16px 22px;border-bottom:1px solid #e1e8f0;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;background:#fff;border-radius:14px 14px 0 0;z-index:1">
      <div>
        <h4 id="enrollModalTitle" style="margin:0;font-size:15px;font-weight:800;color:#1a237e"></h4>
        <div id="enrollModalSub" style="font-size:11px;color:#90a4ae;margin-top:1px"></div>
      </div>
      <button onclick="closeEnrollModal()" style="background:none;border:none;font-size:22px;color:#90a4ae;cursor:pointer">&times;</button>
    </div>

    <!-- Enroll new -->
    <div style="padding:14px 22px;border-bottom:1px solid #f0f4f8;background:#f8fbff">
      <div style="font-size:12px;font-weight:700;color:#37474f;margin-bottom:7px"><i class="fas fa-user-plus" style="color:#5c6bc0"></i> Enroll a Trainee</div>
      <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
        <select id="enrollTraineeSelect"
                style="flex:1;min-width:200px;padding:8px 10px;border:1.5px solid #c5cae9;border-radius:8px;font-size:12px;background:#fff">
          <option value="">— Select trainee —</option>
          <?php foreach ($allTrainees as $tr): ?>
            <option value="<?= $tr['id'] ?>" data-code="<?= h($tr['tmm_code']) ?>" data-name="<?= h($tr['name']) ?>">
              <?= h($tr['tmm_code']) ?> · <?= h($tr['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button onclick="doEnroll()"
                style="padding:8px 14px;border:none;border-radius:8px;background:#5c6bc0;color:#fff;font-size:12px;font-weight:700;cursor:pointer">
          <i class="fas fa-plus"></i> Enroll
        </button>
      </div>
    </div>

    <!-- Enrolled table -->
    <div style="padding:14px 22px">
      <div style="font-size:12px;font-weight:700;color:#37474f;margin-bottom:8px"><i class="fas fa-users" style="color:#5c6bc0"></i> Enrolled Trainees</div>
      <table class="enroll-tbl">
        <thead>
          <tr>
            <th>Trainee</th><th>Status</th><th style="width:75px">Attend %</th>
            <th style="width:65px">Score</th><th style="width:55px">Passed</th><th style="width:40px"></th>
          </tr>
        </thead>
        <tbody id="enrollTableBody"></tbody>
      </table>
      <div id="enrollEmpty" style="display:none;text-align:center;padding:22px;color:#90a4ae;font-size:12px">
        <i class="fas fa-user-slash" style="font-size:22px;margin-bottom:5px;display:block;opacity:.4"></i>No trainees enrolled yet
      </div>
    </div>

    <div style="padding:10px 22px;border-top:1px solid #f0f4f8;display:flex;justify-content:flex-end">
      <button onclick="closeEnrollModal()" style="padding:8px 18px;border:1px solid #e1e8f0;border-radius:8px;background:#fff;font-size:13px;font-weight:600;cursor:pointer;color:#546e7a">Done</button>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- JS data -->
<script>
var _enrollData    = <?= json_encode($enrollmentsByProgram ?? []) ?>;
var _allTrainees   = <?= json_encode(array_values($allTrainees ?? [])) ?>;
var _enrollStMeta  = <?= json_encode($enrollStatusMeta) ?>;
var _currentProgId = null;
var _csrfToken     = '<?= $this->request->getAttribute('csrfToken') ?? '' ?>';
var _enrollUrl     = '<?= $this->Url->build(['action'=>'enrollToAdditionalTraining']) ?>';
var _updateUrl     = '<?= $this->Url->build(['action'=>'updateAdditionalTrainingEnrollment']) ?>';
var _unenrollUrl   = '<?= $this->Url->build(['action'=>'unenrollFromAdditionalTraining']) ?>';
</script>

<?php $this->append('script'); ?>
<?= $this->Html->css('datepicker-fix.css') ?>
<script>
function switchTab(id,btn){
  document.querySelectorAll('.at-tab-panel').forEach(function(p){p.classList.remove('active');});
  document.querySelectorAll('.at-tab').forEach(function(b){b.classList.remove('active');});
  document.getElementById('tab-'+id).classList.add('active'); btn.classList.add('active');
}
var _activeSt=null;
function filterProgs(q){
  q=q.toLowerCase();
  document.querySelectorAll('#progGrid .at-prog-card').forEach(function(c){
    c.style.display=(c.dataset.title.includes(q)&&(!_activeSt||c.dataset.status===_activeSt))?'':'none';
  });
}
function filterByStatus(st,btn){
  _activeSt=_activeSt===st?null:st;
  document.querySelectorAll('[onclick^="filterByStatus"]').forEach(function(b){b.style.opacity='.5';});
  if(_activeSt)btn.style.opacity='1';else document.querySelectorAll('[onclick^="filterByStatus"]').forEach(function(b){b.style.opacity='1';});
  filterProgs(document.getElementById('progSearch')?.value||'');
}
function clearFilter(){
  _activeSt=null;
  if(document.getElementById('progSearch'))document.getElementById('progSearch').value='';
  document.querySelectorAll('#progGrid .at-prog-card').forEach(function(c){c.style.display='';});
  document.querySelectorAll('[onclick^="filterByStatus"]').forEach(function(b){b.style.opacity='1';});
}

<?php if ($_canManage): ?>
/* ─ Program modal ─ */
function openAddModal(){
  document.getElementById('atModalTitle').textContent='New Training Program';
  document.getElementById('atSubmitLabel').textContent='Create Program';
  ['atId','atTitle','atStart','atEnd','atLoc','atDesc'].forEach(function(id){var el=document.getElementById(id);if(el)el.value='';});
  document.getElementById('atBatch').value='<?= $trainingId ?? '' ?>';
  selectAtStatus('planned');
  document.getElementById('atModal').style.display='flex';
}
function openEditModal(id,data){
  document.getElementById('atModalTitle').textContent='Edit Training Program';
  document.getElementById('atSubmitLabel').textContent='Save Changes';
  document.getElementById('atId').value=id;
  document.getElementById('atBatch').value=data.training_id||'';
  document.getElementById('atTitle').value=data.title||'';
  document.getElementById('atStart').value=data.start_date||'';
  document.getElementById('atEnd').value=data.end_date||'';
  document.getElementById('atLoc').value=data.location||'';
  document.getElementById('atDesc').value=data.description||'';
  selectAtStatus(data.status||'planned');
  document.getElementById('atModal').style.display='flex';
}
function closeModal(){document.getElementById('atModal').style.display='none';}
document.getElementById('atModal').addEventListener('click',function(e){if(e.target===this)closeModal();});
function selectAtStatus(val){
  document.getElementById('atStatus').value=val;
  document.querySelectorAll('#atStatusPills .at-spill').forEach(function(p){
    p.style.transform=p.dataset.val===val?'scale(1.06)':'scale(1)';
    p.style.boxShadow=p.dataset.val===val?'0 0 0 3px rgba(26,35,126,.2)':'none';
  });
}

/* ─ Enrollment modal ─ */
function openEnrollModal(pid,progData){
  _currentProgId=pid;
  document.getElementById('enrollModalTitle').textContent=progData.title||'Enrollment';
  document.getElementById('enrollModalSub').textContent=(progData.location?progData.location+' · ':'')+(progData.start_date||'');
  document.getElementById('enrollTraineeSelect').value='';
  renderEnrollTable();
  document.getElementById('enrollModal').style.display='flex';
}
function closeEnrollModal(){document.getElementById('enrollModal').style.display='none';window.location.reload();}
document.getElementById('enrollModal').addEventListener('click',function(e){if(e.target===this)closeEnrollModal();});

function renderEnrollTable(){
  var rows=_enrollData[_currentProgId]||[];
  var tbody=document.getElementById('enrollTableBody');
  var empty=document.getElementById('enrollEmpty');
  tbody.innerHTML='';
  if(!rows.length){empty.style.display='block';return;}
  empty.style.display='none';
  rows.forEach(function(enr){
    var tr=document.createElement('tr');
    tr.dataset.traineeId=enr.trainee_id;
    tr.innerHTML=
      '<td><div style="font-size:12px;font-weight:700;color:#37474f">'+(enr.trainee&&enr.trainee.name?enr.trainee.name:'—')+'</div>'+
        '<div style="font-size:10px;color:#90a4ae">'+(enr.trainee&&enr.trainee.tmm_code?enr.trainee.tmm_code:'')+'</div></td>'+
      '<td><select data-field="status" onchange="saveEnroll('+enr.trainee_id+')" class="ein">'+
        Object.keys(_enrollStMeta).map(function(k){return '<option value="'+k+'"'+(enr.status===k?' selected':'')+'>'+_enrollStMeta[k].label+'</option>';}).join('')+
      '</select></td>'+
      '<td><input type="number" min="0" max="100" step="0.1" value="'+(enr.attendance_percentage||'')+'" placeholder="—" data-field="attendance_percentage" class="ein" onchange="saveEnroll('+enr.trainee_id+')"></td>'+
      '<td><input type="number" min="0" max="100" step="0.1" value="'+(enr.score||'')+'" placeholder="—" data-field="score" class="ein" onchange="saveEnroll('+enr.trainee_id+')"></td>'+
      '<td style="text-align:center"><input type="checkbox" '+(enr.is_passed==1?'checked':'')+' data-field="is_passed" onchange="saveEnroll('+enr.trainee_id+')" style="width:15px;height:15px;cursor:pointer"></td>'+
      '<td><button onclick="doUnenroll('+enr.trainee_id+')" style="background:none;border:none;cursor:pointer;color:#e53935;font-size:13px" title="Remove"><i class="fas fa-user-minus"></i></button></td>';
    tbody.appendChild(tr);
  });
}

function getRowVals(tid){
  var tr=document.querySelector('#enrollTableBody tr[data-trainee-id="'+tid+'"]');
  if(!tr)return{};
  var v={};
  tr.querySelectorAll('[data-field]').forEach(function(el){
    v[el.dataset.field]=el.type==='checkbox'?(el.checked?1:0):el.value;
  });
  return v;
}
function saveEnroll(tid){
  var v=getRowVals(tid);
  var b=new URLSearchParams({additional_training_id:_currentProgId,trainee_id:tid,
    status:v.status||'enrolled',attendance_percentage:v.attendance_percentage||'',
    score:v.score||'',is_passed:v.is_passed!==undefined?v.is_passed:'',_csrfToken:_csrfToken});
  fetch(_updateUrl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':_csrfToken},body:b})
    .then(function(r){return r.json();}).then(function(d){if(!d.ok)alert('Save failed: '+(d.msg||'error'));});
}
function doEnroll(){
  var sel=document.getElementById('enrollTraineeSelect');
  var tid=parseInt(sel.value);
  if(!tid){alert('Please select a trainee.');return;}
  var rows=_enrollData[_currentProgId]||[];
  if(rows.find(function(r){return r.trainee_id==tid;})){alert('Already enrolled.');return;}
  var opt=sel.options[sel.selectedIndex];
  var b=new URLSearchParams({additional_training_id:_currentProgId,trainee_id:tid,_csrfToken:_csrfToken});
  fetch(_enrollUrl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':_csrfToken},body:b})
    .then(function(r){return r.json();}).then(function(d){
      if(!d.ok){alert('Failed: '+(d.msg||'error'));return;}
      if(!_enrollData[_currentProgId])_enrollData[_currentProgId]=[];
      _enrollData[_currentProgId].push({additional_training_id:_currentProgId,trainee_id:tid,
        status:'enrolled',attendance_percentage:null,score:null,is_passed:null,
        trainee:{id:tid,name:opt.dataset.name,tmm_code:opt.dataset.code}});
      renderEnrollTable();sel.value='';
    }).catch(function(){alert('Network error');});
}
function doUnenroll(tid){
  if(!confirm('Remove this trainee?'))return;
  var b=new URLSearchParams({additional_training_id:_currentProgId,trainee_id:tid,_csrfToken:_csrfToken});
  fetch(_unenrollUrl,{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-Token':_csrfToken},body:b})
    .then(function(r){return r.json();}).then(function(d){
      if(!d.ok){alert('Failed: '+(d.msg||'error'));return;}
      _enrollData[_currentProgId]=(_enrollData[_currentProgId]||[]).filter(function(r){return r.trainee_id!=tid;});
      renderEnrollTable();
    }).catch(function(){alert('Network error');});
}
<?php endif; ?>

$(document).ready(function(){
  $('.datepicker').datepicker({format:'yyyy-mm-dd',autoclose:true,todayHighlight:true,orientation:'bottom auto',container:'body',zIndexOffset:1050});
});
</script>
<?php $this->end(); ?>

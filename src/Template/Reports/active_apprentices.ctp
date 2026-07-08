<?php
/**
 * @var \App\View\AppView $this
 * @var array $apprentices
 * @var array $stats
 * @var int   $postTotal
 * @var array $orders
 */

$today = new \DateTime();

function aaStatus($a) {
    if ($a['is_apprentice_pass'])    return ['Completed',  '#2e7d32','#e8f5e9','#43a047'];
    if ($a['is_apprenticeship_pass']) return ['In Japan',   '#1565c0','#e3f2fd','#1e88e5'];
    return                                   ['Preparing',  '#e65100','#fff3e0','#fb8c00'];
}
function aaAvatar($name, $gender) {
    return [
        'color' => $gender == 2 ? '#ad1457' : '#1a237e',
        'icon'  => $gender == 2 ? 'fa-female' : 'fa-male',
        'init'  => mb_strtoupper(mb_substr($name, 0, 1)),
    ];
}

$inJapan   = (int)($stats['in_japan']   ?? 0);
$completed = (int)($stats['completed']  ?? 0);
$total     = (int)($stats['total']      ?? 0);
$preparing = $total - $inJapan - $completed;
?>
<style>
.aa-hero{background:linear-gradient(135deg,#e65100 0%,#bf360c 50%,#b71c1c 100%);color:#fff;border-radius:16px;padding:26px 30px;margin-bottom:20px;box-shadow:0 4px 24px rgba(230,81,0,.3)}
.aa-hero-title{font-size:22px;font-weight:800;margin-bottom:3px;display:flex;align-items:center;gap:10px}
.aa-hero-sub{font-size:13px;opacity:.8}
.aa-kpi-row{display:flex;gap:10px;flex-wrap:wrap;margin-top:16px}
.aa-kpi{background:rgba(255,255,255,.13);border:1px solid rgba(255,255,255,.2);border-radius:12px;padding:10px 18px;text-align:center;min-width:85px}
.aa-kpi-val{font-size:22px;font-weight:800;line-height:1}
.aa-kpi-lbl{font-size:10px;font-weight:600;opacity:.7;text-transform:uppercase;letter-spacing:.5px;margin-top:2px}

.aa-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px}
@media(max-width:700px){.aa-grid{grid-template-columns:1fr}}

.aa-card{background:#fff;border:1px solid #e1e8f0;border-radius:12px;overflow:hidden}
.aa-card-hdr{padding:12px 18px;border-bottom:1px solid #f0f4f8;background:#fafbfc;display:flex;align-items:center;justify-content:space-between}
.aa-card-title{font-size:14px;font-weight:800;color:#1a2332;display:flex;align-items:center;gap:7px;margin:0}
.aa-card-body{padding:18px}
.aa-badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}

/* Toolbar */
.aa-toolbar{display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:14px}
.aa-search{flex:1;min-width:200px;padding:9px 14px 9px 36px;border:1.5px solid #e1e8f0;border-radius:9px;font-size:13px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2390a4ae'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zm-5.442 1.656a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 11px center/15px;outline:none}
.aa-search:focus{border-color:#e65100}
.aa-sel{padding:8px 10px;border:1.5px solid #e1e8f0;border-radius:8px;font-size:12px;background:#fff;outline:none;cursor:pointer}
.aa-sel:focus{border-color:#e65100}
.aa-view-btn{padding:7px 10px;border:1.5px solid #e1e8f0;border-radius:8px;background:#fff;cursor:pointer;font-size:13px;color:#78909c;transition:all .15s}
.aa-view-btn.active{background:#e65100;color:#fff;border-color:#e65100}

/* Card grid */
.aa-app-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:14px}
.aa-app-card{background:#fff;border:1px solid #e1e8f0;border-radius:12px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .15s}
.aa-app-card:hover{box-shadow:0 6px 18px rgba(0,0,0,.1)}
.aa-app-stripe{height:4px;flex-shrink:0}
.aa-app-top{padding:14px 14px 10px;display:flex;align-items:flex-start;gap:10px}
.aa-avatar{width:44px;height:44px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:800;color:#fff;flex-shrink:0;overflow:hidden}
.aa-app-name{font-size:13px;font-weight:800;color:#1a2332;line-height:1.2;margin-bottom:1px}
.aa-app-code{font-size:10px;font-weight:700;color:#90a4ae;font-family:monospace}
.aa-app-kana{font-size:10px;color:#b0bec5;margin-top:1px}
.aa-status-pill{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:10px;font-size:10px;font-weight:800;flex-shrink:0}
.aa-sdot{width:6px;height:6px;border-radius:50%}
.aa-app-body{padding:0 14px 10px;display:flex;flex-direction:column;gap:5px}
.aa-info{display:flex;align-items:center;gap:6px;font-size:11px;color:#546e7a}
.aa-info i{width:13px;text-align:center;color:#b0bec5;flex-shrink:0}
.aa-app-foot{padding:9px 14px;border-top:1px solid #f0f4f8;background:#fafbfc;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;flex-wrap:wrap;gap:4px}
.aa-action-btn{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:7px;font-size:11px;font-weight:700;text-decoration:none;transition:all .15s}

/* Table */
.aa-tbl-wrap{background:#fff;border:1px solid #e1e8f0;border-radius:12px;overflow:hidden}
.aa-tbl{width:100%;border-collapse:collapse;font-size:12px}
.aa-tbl thead{background:linear-gradient(135deg,#e65100,#b71c1c)}
.aa-tbl thead th{padding:10px 13px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#fff;white-space:nowrap;text-align:left}
.aa-tbl tbody tr{border-bottom:1px solid #f5f7fa;transition:background .1s}
.aa-tbl tbody tr:hover{background:#fff8f5}
.aa-tbl tbody td{padding:10px 13px;vertical-align:middle}
.aa-tbl tbody tr:last-child{border-bottom:none}

/* Order table */
.aa-ord-tbl{width:100%;border-collapse:collapse;font-size:12px}
.aa-ord-tbl th{padding:8px 12px;background:#fafbfc;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#90a4ae;border-bottom:1px solid #e1e8f0;text-align:left}
.aa-ord-tbl td{padding:9px 12px;border-bottom:1px solid #f5f7fa;vertical-align:middle}
.aa-ord-tbl tr:last-child td{border-bottom:none}

/* Stat bar */
.aa-stat{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid #f5f7fa}
.aa-stat:last-child{border-bottom:none}
.aa-stat-icon{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0}
.aa-stat-val{font-size:18px;font-weight:800;line-height:1;color:#1a2332}
.aa-stat-lbl{font-size:10px;font-weight:600;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px}

/* Gender bar */
.aa-gender-bar{height:8px;border-radius:4px;overflow:hidden;display:flex;margin-top:6px}
.aa-gm{background:#1e88e5}.aa-gf{background:#e91e63}

/* Donut */
.aa-donut{position:relative;width:90px;height:90px;flex-shrink:0}
.aa-donut svg{transform:rotate(-90deg)}
.aa-donut-in{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1}

.aa-section-lbl{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#90a4ae;margin:20px 0 10px;display:flex;align-items:center;gap:8px}
.aa-section-lbl::after{content:'';flex:1;height:1px;background:#e1e8f0}

.aa-empty{text-align:center;padding:60px 20px;color:#90a4ae}
.aa-empty i{font-size:44px;margin-bottom:14px;display:block;opacity:.35}
</style>

<!-- Hero -->
<div class="aa-hero">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <div>
      <div class="aa-hero-title"><i class="fas fa-plane-departure"></i> Active Apprentices</div>
      <div class="aa-hero-sub">Current apprenticeship roster — Japan deployment status · <?= $today->format('d M Y') ?></div>
    </div>
    <a href="<?= $this->Url->build(['controller'=>'Apprentices','action'=>'index']) ?>"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;background:rgba(255,255,255,.18);color:#fff;text-decoration:none;border:1px solid rgba(255,255,255,.3)">
      <i class="fas fa-list"></i> Full List
    </a>
  </div>
  <div class="aa-kpi-row">
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $total ?></div><div class="aa-kpi-lbl">Total</div></div>
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $inJapan ?></div><div class="aa-kpi-lbl">In Japan</div></div>
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $completed ?></div><div class="aa-kpi-lbl">Completed</div></div>
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $preparing ?></div><div class="aa-kpi-lbl">Preparing</div></div>
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $postTotal ?></div><div class="aa-kpi-lbl">Alumni</div></div>
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $stats['companies'] ?></div><div class="aa-kpi-lbl">Companies</div></div>
    <div class="aa-kpi"><div class="aa-kpi-val"><?= $stats['male'] ?><span style="font-size:12px;opacity:.7">M</span> <?= $stats['female'] ?><span style="font-size:12px;opacity:.7">F</span></div><div class="aa-kpi-lbl">Gender</div></div>
  </div>
</div>

<!-- Stats row -->
<div class="aa-grid" style="margin-bottom:18px">

  <!-- Status breakdown -->
  <div class="aa-card">
    <div class="aa-card-hdr">
      <h3 class="aa-card-title"><i class="fas fa-chart-pie" style="color:#e65100"></i> Status Breakdown</h3>
      <span class="aa-badge" style="background:#fff3e0;color:#e65100"><?= $total ?> total</span>
    </div>
    <div class="aa-card-body">
      <?php
        $compRate = $total > 0 ? round($completed/$total*100) : 0;
        $r = 38; $circ = 2*M_PI*$r; $dash = round($circ*$compRate/100,1);
      ?>
      <div style="display:flex;align-items:center;gap:18px;margin-bottom:16px">
        <div class="aa-donut">
          <svg width="90" height="90" viewBox="0 0 90 90">
            <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#f0f4f8" stroke-width="10"/>
            <circle cx="45" cy="45" r="<?= $r ?>" fill="none" stroke="#43a047" stroke-width="10"
                    stroke-dasharray="<?= $dash ?> <?= round($circ-$dash,1) ?>" stroke-linecap="round"/>
          </svg>
          <div class="aa-donut-in">
            <div style="font-size:18px;font-weight:800;color:#2e7d32;line-height:1"><?= $compRate ?>%</div>
            <div style="font-size:9px;font-weight:700;color:#90a4ae;text-transform:uppercase;letter-spacing:.4px">done</div>
          </div>
        </div>
        <div style="flex:1">
          <div class="aa-stat">
            <div class="aa-stat-icon" style="background:#e3f2fd"><i class="fas fa-plane" style="color:#1565c0"></i></div>
            <div><div class="aa-stat-val" style="color:#1565c0"><?= $inJapan ?></div><div class="aa-stat-lbl">In Japan</div></div>
          </div>
          <div class="aa-stat">
            <div class="aa-stat-icon" style="background:#e8f5e9"><i class="fas fa-check-circle" style="color:#2e7d32"></i></div>
            <div><div class="aa-stat-val" style="color:#2e7d32"><?= $completed ?></div><div class="aa-stat-lbl">Completed</div></div>
          </div>
          <div class="aa-stat">
            <div class="aa-stat-icon" style="background:#fff3e0"><i class="fas fa-clock" style="color:#e65100"></i></div>
            <div><div class="aa-stat-val" style="color:#e65100"><?= $preparing ?></div><div class="aa-stat-lbl">Preparing (not yet departed)</div></div>
          </div>
          <div class="aa-stat">
            <div class="aa-stat-icon" style="background:#eceff1"><i class="fas fa-user-graduate" style="color:#546e7a"></i></div>
            <div><div class="aa-stat-val" style="color:#546e7a"><?= $postTotal ?></div><div class="aa-stat-lbl">Post-Apprentice</div></div>
          </div>
        </div>
      </div>

      <!-- Gender -->
      <div>
        <div style="display:flex;justify-content:space-between;font-size:11px;font-weight:700;margin-bottom:4px">
          <span style="color:#78909c"><i class="fas fa-venus-mars"></i> Gender</span>
          <span><span style="color:#1e88e5"><?= $stats['male'] ?>M</span> · <span style="color:#e91e63"><?= $stats['female'] ?>F</span></span>
        </div>
        <?php $mPct = $total > 0 ? round($stats['male']/$total*100) : 50; ?>
        <div class="aa-gender-bar">
          <div class="aa-gm" style="width:<?= $mPct ?>%"></div>
          <div class="aa-gf" style="width:<?= 100-$mPct ?>%"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Apprentice Orders -->
  <div class="aa-card">
    <div class="aa-card-hdr">
      <h3 class="aa-card-title"><i class="fas fa-file-contract" style="color:#1565c0"></i> Apprentice Orders</h3>
      <span class="aa-badge" style="background:#e3f2fd;color:#1565c0"><?= count($orders) ?> orders</span>
    </div>
    <?php if (empty($orders)): ?>
      <div class="aa-card-body" style="text-align:center;color:#90a4ae;font-size:12px;padding:30px">No orders yet</div>
    <?php else: ?>
    <table class="aa-ord-tbl">
      <thead><tr><th>Order</th><th>Departure</th><th>Status</th><th style="text-align:center">Count</th></tr></thead>
      <tbody>
      <?php
        $ordStatusMeta = [
          'active' => ['bg'=>'#e8f5e9','color'=>'#2e7d32','dot'=>'#43a047'],
          'draft'  => ['bg'=>'#fff8e1','color'=>'#e65100','dot'=>'#fb8c00'],
          'closed' => ['bg'=>'#f5f7fa','color'=>'#78909c','dot'=>'#90a4ae'],
        ];
        foreach ($orders as $o):
          $osm = $ordStatusMeta[$o['status']] ?? ['bg'=>'#f5f7fa','color'=>'#78909c','dot'=>'#90a4ae'];
          $depDate = !empty($o['departure_date']) ? (new \DateTime($o['departure_date']))->format('d M Y') : '—';
      ?>
      <tr>
        <td style="font-size:12px">
          <div style="font-weight:700;color:#1a2332;line-height:1.3"><?= h(mb_substr($o['title'],0,45)).(mb_strlen($o['title'])>45?'…':'') ?></div>
        </td>
        <td style="font-size:11px;white-space:nowrap;color:#546e7a"><i class="fas fa-calendar" style="color:#b0bec5"></i> <?= $depDate ?></td>
        <td>
          <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:8px;font-size:10px;font-weight:800;background:<?= $osm['bg'] ?>;color:<?= $osm['color'] ?>">
            <span style="width:5px;height:5px;border-radius:50%;background:<?= $osm['dot'] ?>"></span>
            <?= ucfirst($o['status']) ?>
          </span>
        </td>
        <td style="text-align:center;font-weight:800;color:#1565c0"><?= $o['count'] ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<!-- Apprentice Cards / Table -->
<div class="aa-section-lbl"><i class="fas fa-id-card"></i> Apprentice Roster (<?= count($apprentices) ?>)</div>

<!-- Toolbar -->
<div class="aa-toolbar">
  <input type="text" class="aa-search" id="aaSearch" placeholder="Search name, code, company, LPK…" oninput="aaFilter()">
  <select class="aa-sel" id="aaGender" onchange="aaFilter()">
    <option value="">All Genders</option>
    <option value="1">Male</option>
    <option value="2">Female</option>
  </select>
  <select class="aa-sel" id="aaStatus" onchange="aaFilter()">
    <option value="">All Status</option>
    <option value="japan">In Japan</option>
    <option value="completed">Completed</option>
    <option value="preparing">Preparing</option>
  </select>
  <button class="aa-view-btn active" id="btnCard" onclick="setView('card',this)" title="Card view"><i class="fas fa-th-large"></i></button>
  <button class="aa-view-btn" id="btnTable" onclick="setView('table',this)" title="Table view"><i class="fas fa-list"></i></button>
</div>
<div style="font-size:12px;color:#90a4ae;margin-bottom:10px" id="aaCount"><?= count($apprentices) ?> apprentice<?= count($apprentices)!=1?'s':'' ?></div>

<!-- Card view -->
<div class="aa-app-grid" id="aaCardGrid">
<?php foreach ($apprentices as $a):
  [$stLabel,$stColor,$stBg,$stDot] = aaStatus($a);
  $av  = aaAvatar($a['name'], $a['master_gender_id']);
  $dep = !empty($a['departure_date']) ? (new \DateTime($a['departure_date']))->format('M Y') : null;
  $age = !empty($a['birth_date']) ? (new \DateTime($a['birth_date']))->diff($today)->y : null;
  $statusKey = $a['is_apprentice_pass'] ? 'completed' : ($a['is_apprenticeship_pass'] ? 'japan' : 'preparing');
?>
<div class="aa-app-card"
     data-name="<?= strtolower(h($a['name'])) ?>"
     data-code="<?= strtolower(h($a['tmm_code'])) ?>"
     data-order="<?= strtolower(h($a['order_title'] ?? '')) ?>"
     data-vti="<?= strtolower(h($a['vti_name'] ?? '')) ?>"
     data-gender="<?= $a['master_gender_id'] ?>"
     data-status="<?= $statusKey ?>">
  <div class="aa-app-stripe" style="background:<?= $stDot ?>"></div>
  <div class="aa-app-top">
    <div class="aa-avatar" style="background:<?= $av['color'] ?>">
      <?php if ($a['image_photo']): ?>
        <img src="/img/apprentices/<?= h($a['image_photo']) ?>" style="width:100%;height:100%;object-fit:cover"
             onerror="this.style.display='none';this.nextSibling.style.display='flex'">
        <span style="display:none;width:100%;height:100%;align-items:center;justify-content:center"><?= $av['init'] ?></span>
      <?php else: ?>
        <?= $av['init'] ?>
      <?php endif; ?>
    </div>
    <div style="flex:1;min-width:0">
      <div class="aa-app-name"><?= h($a['name']) ?></div>
      <div class="aa-app-code"><?= h($a['tmm_code']) ?></div>
      <?php if ($a['name_katakana']): ?>
        <div class="aa-app-kana"><?= h($a['name_katakana']) ?></div>
      <?php endif; ?>
    </div>
    <span class="aa-status-pill" style="background:<?= $stBg ?>;color:<?= $stColor ?>">
      <span class="aa-sdot" style="background:<?= $stDot ?>"></span><?= $stLabel ?>
    </span>
  </div>
  <div class="aa-app-body">
    <?php if ($a['order_title']): ?>
    <div class="aa-info"><i class="fas fa-file-contract"></i><span><?= h(mb_substr($a['order_title'],0,45)).(mb_strlen($a['order_title'])>45?'…':'') ?></span></div>
    <?php endif; ?>
    <?php if ($dep): ?>
    <div class="aa-info"><i class="fas fa-plane-departure"></i><span>Departed <?= $dep ?></span></div>
    <?php endif; ?>
    <?php if ($a['vti_name']): ?>
    <div class="aa-info"><i class="fas fa-school"></i><span><?= h($a['vti_name']) ?></span></div>
    <?php endif; ?>
    <?php if ($a['telephone_mobile']): ?>
    <div class="aa-info"><i class="fas fa-phone"></i><span><?= h($a['telephone_mobile']) ?></span></div>
    <?php endif; ?>
    <?php if ($a['email']): ?>
    <div class="aa-info"><i class="fas fa-envelope"></i><span style="word-break:break-all"><?= h($a['email']) ?></span></div>
    <?php endif; ?>
    <?php if ($age): ?>
    <div class="aa-info"><i class="fas fa-birthday-cake"></i><span><?= $age ?> years old</span></div>
    <?php endif; ?>
  </div>
  <div class="aa-app-foot">
    <span style="font-size:10px;font-weight:700;color:#90a4ae">
      <i class="fas fa-<?= $a['master_gender_id']==2?'female':'male' ?>" style="color:<?= $av['color'] ?>"></i>
      <?= $a['master_gender_id']==2?'Female':'Male' ?>
    </span>
    <div style="display:flex;gap:4px">
      <?= $this->Html->link('<i class="fas fa-eye"></i>',['controller'=>'Apprentices','action'=>'view',$a['id']],['escape'=>false,'class'=>'aa-action-btn','style'=>'background:#e3f2fd;color:#1565c0']) ?>
      <?php if ($a['link_whatsapp']): ?>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$a['link_whatsapp']) ?>" target="_blank"
           class="aa-action-btn" style="background:#e8f5e9;color:#2e7d32">
          <i class="fab fa-whatsapp"></i>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>

<!-- Table view (hidden) -->
<div class="aa-tbl-wrap" id="aaTableView" style="display:none">
  <table class="aa-tbl">
    <thead>
      <tr><th>Apprentice</th><th>Gender</th><th>Order</th><th>LPK</th><th>Phone</th><th>Email</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody id="aaTableBody">
      <?php foreach ($apprentices as $a):
        [$stLabel,$stColor,$stBg,$stDot] = aaStatus($a);
        $av  = aaAvatar($a['name'], $a['master_gender_id']);
        $statusKey = $a['is_apprentice_pass'] ? 'completed' : ($a['is_apprenticeship_pass'] ? 'japan' : 'preparing');
      ?>
      <tr data-name="<?= strtolower(h($a['name'])) ?>"
          data-code="<?= strtolower(h($a['tmm_code'])) ?>"
          data-order="<?= strtolower(h($a['order_title']??'')) ?>"
          data-vti="<?= strtolower(h($a['vti_name']??'')) ?>"
          data-gender="<?= $a['master_gender_id'] ?>"
          data-status="<?= $statusKey ?>">
        <td>
          <div style="display:flex;align-items:center;gap:8px">
            <div style="width:32px;height:32px;border-radius:8px;background:<?= $av['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;flex-shrink:0"><?= $av['init'] ?></div>
            <div>
              <div style="font-weight:700;color:#1a2332;font-size:12px"><?= h($a['name']) ?></div>
              <div style="font-size:10px;font-family:monospace;color:#90a4ae"><?= h($a['tmm_code']) ?></div>
            </div>
          </div>
        </td>
        <td><i class="fas fa-<?= $a['master_gender_id']==2?'female':'male' ?>" style="color:<?= $av['color'] ?>"></i> <?= $a['master_gender_id']==2?'F':'M' ?></td>
        <td style="font-size:11px;color:#546e7a;max-width:160px"><?= h(mb_substr($a['order_title']??'—',0,40)) ?><?= mb_strlen($a['order_title']??'')>40?'…':'' ?></td>
        <td style="font-size:11px;color:#78909c"><?= h(mb_substr($a['vti_name']??'—',0,25)) ?></td>
        <td style="font-size:11px"><?= h($a['telephone_mobile']??'—') ?></td>
        <td style="font-size:11px;color:#78909c;max-width:140px;word-break:break-all"><?= h($a['email']??'—') ?></td>
        <td><span style="display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:10px;font-size:10px;font-weight:800;background:<?= $stBg ?>;color:<?= $stColor ?>"><span class="aa-sdot" style="background:<?= $stDot ?>"></span><?= $stLabel ?></span></td>
        <td><?= $this->Html->link('<i class="fas fa-eye"></i>',['controller'=>'Apprentices','action'=>'view',$a['id']],['escape'=>false,'style'=>'padding:4px 7px;border-radius:6px;background:#e3f2fd;color:#1565c0;font-size:12px;text-decoration:none']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- Empty state -->
<div id="aaEmpty" style="display:none;background:#fff;border:1px solid #e1e8f0;border-radius:12px">
  <div class="aa-empty">
    <i class="fas fa-plane-departure"></i>
    <div style="font-size:15px;font-weight:700;color:#546e7a;margin-bottom:5px">No apprentices found</div>
    <div style="font-size:12px">Try adjusting the search or filters.</div>
  </div>
</div>

<?php if (empty($apprentices)): ?>
<div style="background:#fff;border:1px solid #e1e8f0;border-radius:12px">
  <div class="aa-empty">
    <i class="fas fa-plane-departure"></i>
    <div style="font-size:15px;font-weight:700;color:#546e7a;margin-bottom:5px">No Apprentices Yet</div>
    <div style="font-size:12px;margin-bottom:14px">Apprentices will appear here once trainees are promoted.</div>
    <?= $this->Html->link('<i class="fas fa-list"></i> View Trainees', ['controller'=>'Trainees','action'=>'index'], ['escape'=>false,'style'=>'display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:#e65100;color:#fff;font-size:12px;font-weight:700;text-decoration:none']) ?>
  </div>
</div>
<?php endif; ?>

<?php $this->append('script'); ?>
<script>
var _view = 'card';
function setView(v, btn) {
  _view = v;
  document.getElementById('aaCardGrid').style.display  = v==='card'  ? 'grid'  : 'none';
  document.getElementById('aaTableView').style.display = v==='table' ? 'block' : 'none';
  document.getElementById('btnCard').classList.toggle('active',  v==='card');
  document.getElementById('btnTable').classList.toggle('active', v==='table');
}
function aaFilter() {
  var q      = document.getElementById('aaSearch').value.toLowerCase();
  var gender = document.getElementById('aaGender').value;
  var status = document.getElementById('aaStatus').value;
  function matches(el) {
    var n = el.dataset;
    if (q && !n.name.includes(q) && !n.code.includes(q) && !n.order.includes(q) && !n.vti.includes(q)) return false;
    if (gender && n.gender !== gender) return false;
    if (status && n.status !== status) return false;
    return true;
  }
  var cards = document.querySelectorAll('#aaCardGrid .aa-app-card');
  var trows = document.querySelectorAll('#aaTableBody tr');
  var shown = 0;
  cards.forEach(function(c){ var v=matches(c); c.style.display=v?'':'none'; if(v)shown++; });
  trows.forEach(function(r){ r.style.display=matches(r)?'':'none'; });
  document.getElementById('aaCount').textContent = shown + ' apprentice' + (shown!==1?'s':'');
  var empty = shown===0;
  document.getElementById('aaEmpty').style.display = empty ? 'block' : 'none';
  if (empty) {
    document.getElementById('aaCardGrid').style.display='none';
    document.getElementById('aaTableView').style.display='none';
  } else { setView(_view,null); }
}
</script>
<?php $this->end(); ?>

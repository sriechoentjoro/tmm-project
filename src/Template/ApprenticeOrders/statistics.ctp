<?php
/**
 * Order Statistics (enriched)
 * Ringkasan order magang: per tahun, per kumiai, per organisasi penerima,
 * per kategori kerja, dan fulfillment (diminta vs ter-assign vs lulus).
 *
 * @var \App\View\AppView $this
 * @var array $byYear
 * @var array $totals
 * @var array $byKumiai
 * @var array $byOrg
 * @var array $byJob
 * @var array $fulfillment
 */
$fmt = function ($v) { return number_format((int)$v); };
?>
<style>
.aos-card{color:#fff;padding:18px 20px;border-radius:10px;min-width:180px;flex:1}
.aos-card h3{margin:0;font-size:2em}
.aos-card p{margin:5px 0 0;opacity:.9;font-size:13px}
.aos-panel{background:#fff;border:1px solid #e3e8ef;border-radius:10px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.05);flex:1;min-width:300px}
.aos-panel h4{margin:0 0 10px;color:#3d4a63;font-size:14px;letter-spacing:.5px;border-bottom:2px solid #667eea;padding-bottom:6px}
.aos-table{border-collapse:collapse;width:100%}
.aos-table th{padding:9px 10px;border-bottom:2px solid #667eea;background:linear-gradient(135deg,rgba(102,126,234,.12),rgba(118,75,162,.12));white-space:nowrap;font-size:13px}
.aos-table td{padding:8px 10px;border-bottom:1px solid #edf0f4;font-size:13px}
.ffbar{background:#eef1f6;border-radius:8px;height:16px;min-width:120px;overflow:hidden;position:relative}
.ffbar>span{display:block;height:100%;background:linear-gradient(90deg,#28a745,#20c997)}
.ffbar>em{position:absolute;inset:0;font-style:normal;font-size:11px;font-weight:700;text-align:center;line-height:16px;color:#2a3444}
.badge-status{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700}
</style>

<div class="index-header" style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
    <h2 style="margin:0"><i class="fas fa-chart-pie"></i> <?= __('Order Statistics') ?></h2>
    <div>
        <?= $this->Html->link('<i class="fas fa-list"></i> ' . __('All Orders'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('New Order'), ['action' => 'add'], ['class' => 'btn btn-sm btn-outline-success', 'escape' => false]) ?>
    </div>
</div>

<!-- Kartu ringkasan -->
<div style="display:flex;gap:16px;margin-bottom:22px;flex-wrap:wrap">
    <div class="aos-card" style="background:linear-gradient(135deg,#667eea,#764ba2)">
        <h3><?= $fmt($totals['orders']) ?></h3><p><i class="fas fa-file-signature"></i> <?= __('Total Orders') ?></p>
    </div>
    <div class="aos-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
        <h3><?= $fmt($totals['trainees']) ?></h3><p><i class="fas fa-users"></i> <?= __('Requested Trainees') ?></p>
    </div>
    <div class="aos-card" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">
        <h3><?= $fmt($totals['assigned'] ?? 0) ?></h3><p><i class="fas fa-user-check"></i> <?= __('Assigned Trainees') ?></p>
    </div>
    <div class="aos-card" style="background:linear-gradient(135deg,#fa709a,#fee140)">
        <h3><?= $fmt($totals['passed'] ?? 0) ?></h3><p><i class="fas fa-award"></i> <?= __('Passed Apprenticeship') ?></p>
    </div>
    <?php $pct = $totals['trainees'] ? round(100 * ($totals['assigned'] ?? 0) / $totals['trainees']) : 0; ?>
    <div class="aos-card" style="background:linear-gradient(135deg,#30cfd0,#330867)">
        <h3><?= $pct ?>%</h3><p><i class="fas fa-tasks"></i> <?= __('Overall Fulfillment') ?></p>
    </div>
</div>

<!-- Fulfillment per order -->
<div class="aos-panel" style="margin-bottom:22px">
    <h4><i class="fas fa-truck-loading"></i> <?= __('Fulfillment per Order — requested vs assigned vs passed') ?></h4>
    <div style="overflow-x:auto">
    <table class="aos-table">
        <thead><tr>
            <th><?= __('Order') ?></th><th><?= __('Kumiai') ?></th><th><?= __('Acceptance Org') ?></th>
            <th style="text-align:center"><?= __('Departure') ?></th>
            <th style="text-align:center"><?= __('Requested') ?></th>
            <th style="text-align:center"><?= __('Assigned') ?></th>
            <th style="text-align:center"><?= __('Passed') ?></th>
            <th><?= __('Progress') ?></th>
        </tr></thead>
        <tbody>
        <?php foreach ($fulfillment as $f): ?>
            <?php $p = $f['requested'] ? min(100, round(100 * $f['assigned'] / $f['requested'])) : 0; ?>
            <tr>
                <td><?= $this->Html->link(h($f['title'] ?: ('#' . $f['id'])), ['action' => 'view', $f['id']], ['style' => 'color:#4c5bd4;font-weight:600']) ?></td>
                <td style="font-size:12px"><?= h($f['kumiai']) ?></td>
                <td style="font-size:12px"><?= h($f['organization']) ?></td>
                <td style="text-align:center"><?= h(trim(($f['departure_month'] ?? '') . ' ' . ($f['departure_year'] ?? ''))) ?: '—' ?></td>
                <td style="text-align:center;font-weight:700"><?= $fmt($f['requested']) ?></td>
                <td style="text-align:center;font-weight:700;color:#28a745"><?= $fmt($f['assigned']) ?></td>
                <td style="text-align:center;color:#e67700"><?= $fmt($f['passed']) ?></td>
                <td><div class="ffbar"><span style="width:<?= $p ?>%"></span><em><?= $p ?>%</em></div></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($fulfillment)): ?>
            <tr><td colspan="8" style="padding:15px;color:#6c757d"><?= __('No orders yet.') ?></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Breakdown 3 kolom -->
<div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:22px">
    <div class="aos-panel">
        <h4><i class="fas fa-handshake"></i> <?= __('Top Kumiai (Cooperative Associations)') ?></h4>
        <table class="aos-table">
            <thead><tr><th><?= __('Kumiai') ?></th><th style="text-align:center"><?= __('Orders') ?></th><th style="text-align:center"><?= __('Requested') ?></th></tr></thead>
            <tbody>
            <?php foreach ($byKumiai as $r): ?>
                <tr><td><?= h($r['label'] ?: __('(not set)')) ?></td>
                    <td style="text-align:center"><?= $fmt($r['orders']) ?></td>
                    <td style="text-align:center;font-weight:700"><?= $fmt($r['requested']) ?></td></tr>
            <?php endforeach; if (empty($byKumiai)): ?>
                <tr><td colspan="3" style="color:#6c757d"><?= __('No data.') ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="aos-panel">
        <h4><i class="fas fa-building"></i> <?= __('Top Acceptance Organizations') ?></h4>
        <table class="aos-table">
            <thead><tr><th><?= __('Organization') ?></th><th style="text-align:center"><?= __('Orders') ?></th><th style="text-align:center"><?= __('Requested') ?></th></tr></thead>
            <tbody>
            <?php foreach ($byOrg as $r): ?>
                <tr><td><?= h($r['label'] ?: __('(not set)')) ?></td>
                    <td style="text-align:center"><?= $fmt($r['orders']) ?></td>
                    <td style="text-align:center;font-weight:700"><?= $fmt($r['requested']) ?></td></tr>
            <?php endforeach; if (empty($byOrg)): ?>
                <tr><td colspan="3" style="color:#6c757d"><?= __('No data.') ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="aos-panel">
        <h4><i class="fas fa-briefcase"></i> <?= __('Top Job Categories') ?></h4>
        <table class="aos-table">
            <thead><tr><th><?= __('Job Category') ?></th><th style="text-align:center"><?= __('Orders') ?></th><th style="text-align:center"><?= __('Requested') ?></th></tr></thead>
            <tbody>
            <?php foreach ($byJob as $r): ?>
                <tr><td><?= h($r['label'] ?: __('(not set)')) ?></td>
                    <td style="text-align:center"><?= $fmt($r['orders']) ?></td>
                    <td style="text-align:center;font-weight:700"><?= $fmt($r['requested']) ?></td></tr>
            <?php endforeach; if (empty($byJob)): ?>
                <tr><td colspan="3" style="color:#6c757d"><?= __('No data.') ?></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Per tahun keberangkatan -->
<div class="aos-panel" style="max-width:820px">
    <h4><i class="fas fa-calendar-alt"></i> <?= __('By Departure Year') ?></h4>
    <table class="aos-table">
        <thead><tr>
            <th><?= __('Departure Year') ?></th><th style="text-align:center"><?= __('Orders') ?></th>
            <th style="text-align:center"><?= __('Male') ?></th><th style="text-align:center"><?= __('Female') ?></th>
            <th style="text-align:center"><?= __('Total') ?></th>
        </tr></thead>
        <tbody>
            <?php foreach ($byYear as $row): ?>
            <tr>
                <td><?= h($row['departure_year'] ?: __('(not set)')) ?></td>
                <td style="text-align:center"><?= $fmt($row['order_count']) ?></td>
                <td style="text-align:center"><?= $fmt($row['total_male']) ?></td>
                <td style="text-align:center"><?= $fmt($row['total_female']) ?></td>
                <td style="text-align:center;font-weight:700"><?= $fmt((int)$row['total_male'] + (int)$row['total_female']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($byYear)): ?>
            <tr><td colspan="5" style="padding:15px;color:#6c757d"><?= __('No orders yet.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

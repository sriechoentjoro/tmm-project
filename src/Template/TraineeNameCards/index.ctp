<?php
/**
 * @var \App\View\AppView $this
 * @var array $trainees
 * @var array $batches
 */

// Group trainees by batch for display
$byBatch = [];
foreach ($trainees as $t) {
    $bKey = $t['batch_id'] ?? 0;
    $byBatch[$bKey]['batch_name'] = $t['batch_name'] ?? __('No Batch');
    $byBatch[$bKey]['trainees'][] = $t;
}
?>
<style>
.nc-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:22px; }
.nc-header h2 { margin:0; font-size:20px; font-weight:700; color:#1a202c; display:flex; align-items:center; gap:10px; }
.nc-header h2 i { color:#0277BD; }

/* Batch selector card */
.batch-selector {
    background:#fff; border-radius:14px;
    box-shadow:0 2px 12px rgba(0,0,0,0.08);
    padding:20px 24px; margin-bottom:24px;
}
.batch-selector h4 { margin:0 0 14px; font-size:13px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px; display:flex; align-items:center; gap:8px; }
.batch-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:12px; }
.batch-card {
    border:1.5px solid #e2e8f0; border-radius:10px; padding:14px 16px;
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    background:#f8fafc; transition:all 0.15s; text-decoration:none;
}
.batch-card:hover { border-color:#0277BD; background:#e3f2fd; text-decoration:none; box-shadow:0 2px 8px rgba(2,119,189,0.15); }
.batch-card-info .bc-name { font-size:13px; font-weight:700; color:#1a202c; }
.batch-card-info .bc-count { font-size:11px; color:#94a3b8; margin-top:2px; }
.btn-batch-print {
    display:inline-flex; align-items:center; gap:5px;
    padding:6px 14px; background:linear-gradient(135deg,#6949BC,#9C27B0);
    color:#fff; border-radius:7px; font-size:12px; font-weight:700;
    text-decoration:none; white-space:nowrap; flex-shrink:0;
}
.btn-batch-print:hover { color:#fff; text-decoration:none; opacity:0.9; }

/* Trainee table section */
.batch-section { margin-bottom:28px; }
.batch-section-header {
    display:flex; align-items:center; justify-content:space-between;
    padding:10px 16px; background:linear-gradient(135deg,#0277BD,#0097A7);
    border-radius:10px 10px 0 0; color:#fff;
}
.batch-section-header .bsh-name { font-size:14px; font-weight:700; display:flex; align-items:center; gap:8px; }
.batch-section-header .bsh-count { font-size:12px; opacity:0.8; }

.nc-table { width:100%; border-collapse:collapse; background:#fff; border-radius:0 0 10px 10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.07); }
.nc-table thead tr { background:#f0f9ff; }
.nc-table thead th { padding:9px 14px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:#0277BD; border-bottom:1.5px solid #e2e8f0; white-space:nowrap; }
.nc-table tbody tr { border-bottom:1px solid #f0f4f8; transition:background 0.12s; }
.nc-table tbody tr:hover { background:#f0f9ff; }
.nc-table tbody td { padding:10px 14px; font-size:13px; color:#374151; vertical-align:middle; }

.trainee-cell { display:flex; align-items:center; gap:10px; }
.t-avatar { width:34px; height:34px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff; }
.t-name { font-weight:600; color:#1a202c; }
.t-code { font-size:11px; color:#94a3b8; }

.btn-print-single {
    display:inline-flex; align-items:center; gap:5px;
    padding:5px 12px; background:#fff; color:#0277BD;
    border:1.5px solid #0277BD; border-radius:7px;
    font-size:12px; font-weight:700; text-decoration:none; transition:all 0.15s;
}
.btn-print-single:hover { background:#0277BD; color:#fff; text-decoration:none; }

.nc-summary { display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap; }
.nc-sum-chip { background:#fff; border-radius:8px; padding:10px 16px; box-shadow:0 2px 8px rgba(0,0,0,0.07); border-left:3px solid; font-size:12px; font-weight:600; color:#374151; }
.nc-sum-chip span { font-size:18px; font-weight:800; display:block; line-height:1; margin-bottom:2px; }
</style>

<!-- Page header -->
<div class="nc-header">
    <h2><i class="fas fa-id-card"></i> <?= __('Trainee Name Cards') ?></h2>
    <span style="font-size:12px;color:#94a3b8;"><?= __('Select a trainee to print a single card, or print an entire batch') ?></span>
</div>

<!-- Summary chips -->
<div class="nc-summary">
    <div class="nc-sum-chip" style="border-color:#0277BD;">
        <span style="color:#0277BD;"><?= count($trainees) ?></span><?= __('Total Trainees') ?>
    </div>
    <div class="nc-sum-chip" style="border-color:#6949BC;">
        <span style="color:#6949BC;"><?= count($batches) ?></span><?= __('Batches') ?>
    </div>
</div>

<!-- Batch group-print selector -->
<?php if (!empty($batches)): ?>
<div class="batch-selector">
    <h4><i class="fas fa-print"></i> <?= __('Batch Print') ?></h4>
    <div class="batch-grid">
        <?php foreach ($batches as $b): ?>
        <a href="<?= $this->Url->build(['action' => 'printBatch', $b['id']]) ?>"
           class="batch-card" target="_blank">
            <div class="batch-card-info">
                <div class="bc-name"><?= h($b['batch_name']) ?></div>
                <div class="bc-count"><?= (int)$b['trainee_count'] ?> <?= __('trainees') ?></div>
            </div>
            <span class="btn-batch-print"><i class="fas fa-print"></i> <?= __('Print All') ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Trainee list grouped by batch -->
<?php foreach ($byBatch as $batchId => $group): ?>
<div class="batch-section">
    <div class="batch-section-header">
        <div class="bsh-name">
            <i class="fas fa-layer-group"></i>
            <?= h($group['batch_name']) ?>
        </div>
        <span class="bsh-count"><?= count($group['trainees']) ?> <?= __('trainees') ?></span>
    </div>
    <table class="nc-table">
        <thead>
            <tr>
                <th><?= __('Trainee') ?></th>
                <th><?= __('Gender') ?></th>
                <th style="text-align:right;"><?= __('Print Card') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($group['trainees'] as $t):
            $gender = (int)($t['master_gender_id'] ?? 1);
            $init   = mb_strtoupper(mb_substr($t['name'] ?? '?', 0, 1));
            $bg     = $gender === 2
                ? 'background:linear-gradient(135deg,#C2185B,#E91E63);'
                : 'background:linear-gradient(135deg,#0277BD,#0097A7);';
        ?>
        <tr>
            <td>
                <div class="trainee-cell">
                    <div class="t-avatar" style="<?= $bg ?>"><?= h($init) ?></div>
                    <div>
                        <div class="t-name"><?= h($t['name'] ?? '—') ?></div>
                        <div class="t-code"><?= h($t['tmm_code'] ?? '') ?></div>
                    </div>
                </div>
            </td>
            <td style="font-size:12px;color:#64748b;"><?= $gender === 2 ? '♀ Female' : '♂ Male' ?></td>
            <td style="text-align:right;">
                <?= $this->Html->link(
                    '<i class="fas fa-print"></i> ' . __('Print Card'),
                    ['action' => 'printCard', $t['id']],
                    ['class' => 'btn-print-single', 'escape' => false, 'target' => '_blank']
                ) ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>

<?php if (empty($trainees)): ?>
<div style="text-align:center;padding:60px;color:#94a3b8;">
    <i class="fas fa-users" style="font-size:40px;display:block;margin-bottom:14px;"></i>
    <p><?= __('No trainees found.') ?></p>
</div>
<?php endif; ?>

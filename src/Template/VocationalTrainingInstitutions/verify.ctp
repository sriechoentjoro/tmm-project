<?php
/**
 * LPK Verification (enriched)
 *
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet $institutions
 * @var array $vstats            total / registered / pending / active
 * @var array $candidateCounts   lpk_id => jumlah kandidat
 */
?>
<style>
.vf-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.vf-stat .v{font-size:22px;font-weight:800;color:#212a3e}
.vf-stat .l{font-size:12px;color:#6c757d}
.vf-table th{padding:11px 12px;border-bottom:2px solid #667eea;background:linear-gradient(135deg,rgba(102,126,234,.12),rgba(118,75,162,.12));white-space:nowrap}
.vf-table td{padding:9px 12px;border-bottom:1px solid #edf0f4}
.reg-badge{display:inline-block;padding:2px 10px;border-radius:12px;font-size:12px;font-weight:700}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-clipboard-check"></i> <?= __('LPK Verification') ?></h2>
        <?= $this->Html->link('<i class="fas fa-building"></i> ' . __('All LPK'), '/vocational-training-institutions', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
    </div>
</div>

<!-- Ringkasan verifikasi -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="vf-stat"><div class="v"><?= (int)($vstats['total'] ?? 0) ?></div><div class="l"><i class="fas fa-building"></i> <?= __('Total LPK') ?></div></div>
    <div class="vf-stat" style="border-left-color:#28a745"><div class="v"><?= (int)($vstats['registered'] ?? 0) ?></div><div class="l"><i class="fas fa-check-circle"></i> <?= __('Registered') ?></div></div>
    <div class="vf-stat" style="border-left-color:#fd7e14"><div class="v"><?= (int)($vstats['pending'] ?? 0) ?></div><div class="l"><i class="fas fa-hourglass-half"></i> <?= __('Pending') ?></div></div>
    <div class="vf-stat" style="border-left-color:#20c997"><div class="v"><?= (int)($vstats['active'] ?? 0) ?></div><div class="l"><i class="fas fa-bolt"></i> <?= __('Active') ?></div></div>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="vfSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / email / status…') ?>"
           style="max-width:340px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto">
    <table class="table vf-table" style="border-collapse:collapse;width:100%" id="vfTable">
        <thead>
            <tr>
                <th><?= __('Actions') ?></th>
                <th><?= __('ID') ?></th>
                <th><?= __('Name') ?></th>
                <th style="text-align:center"><?= __('Candidates') ?></th>
                <th style="text-align:center"><?= __('Status') ?></th>
                <th style="text-align:center"><?= __('Registered') ?></th>
                <th><?= __('Registered At') ?></th>
                <th><?= __('Email') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($institutions as $row): ?>
            <tr>
                <td style="white-space:nowrap">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                </td>
                <td><?= h($row['id']) ?></td>
                <td><strong><?= $this->Html->link(h($row['name']), ['action' => 'view', $row['id']], ['style' => 'color:#4c5bd4']) ?></strong></td>
                <td style="text-align:center">
                    <?php $cc = (int)($candidateCounts[$row['id']] ?? 0); ?>
                    <?php if ($cc): ?>
                        <?= $this->Html->link($cc, '/candidates?vocational_training_institution_id=' . $row['id'], ['style' => 'font-weight:700;color:#00838f']) ?>
                    <?php else: ?><span style="color:#c3ccd6">0</span><?php endif; ?>
                </td>
                <td style="text-align:center"><span class="reg-badge" style="background:<?= $row['status'] === 'active' ? '#d4edda' : '#f1f3f5' ?>;color:<?= $row['status'] === 'active' ? '#155724' : '#6c757d' ?>"><?= h(__(ucfirst($row['status'] ?: '—'))) ?></span></td>
                <td style="text-align:center"><?= $row['is_registered'] ? '<span class="reg-badge" style="background:#d4edda;color:#155724">✔ ' . __('Yes') . '</span>' : '<span class="reg-badge" style="background:#f8d7da;color:#721c24">✘ ' . __('No') . '</span>' ?></td>
                <td style="color:#6c757d;font-size:13px"><?= h($row['registered_at']) ?></td>
                <td style="font-size:13px"><?= h($row['email']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (iterator_count($institutions) === 0): ?>
            <tr><td colspan="8" style="text-align:center;padding:30px;color:#6c757d"><?= __('No institutions found.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="paginator" style="margin-top:15px">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>

<script>
document.getElementById('vfSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#vfTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

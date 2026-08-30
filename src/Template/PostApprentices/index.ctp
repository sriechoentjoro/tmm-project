<?php
/**
 * Post Apprentices — alumni tracking (enriched)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PostApprentice[]|\Cake\Collection\CollectionInterface $postApprentices
 * @var array $apprenticeMap id => [name, tmm_code]
 * @var array $statusStats   [label, n]
 * @var int   $totalAlumni
 */
$statusColor = [
    'Employed' => '#28a745', 'Self-Employed' => '#20c997',
    'Continuing Study' => '#667eea', 'Unemployed' => '#dc3545', 'Other' => '#6c757d',
];
?>
<style>
.pa-stat{flex:1;min-width:140px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.pa-stat .v{font-size:24px;font-weight:800;color:#212a3e}
.pa-stat .l{font-size:12px;color:#6c757d}
.pa-badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;color:#fff}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-user-clock"></i> <?= __('Post Apprentices') ?> <span style="font-size:13px;color:#6c757d;font-weight:normal"><?= __('Alumni after returning home') ?></span></h2>
        <div style="display:flex;align-items:center;gap:10px">
            <?= $this->Html->link('<i class="fas fa-user-tie"></i> ' . __('All Apprentices'), '/apprentices', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
        </div>
    </div>
</div>

<!-- Ringkasan status alumni -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="pa-stat"><div class="v"><?= (int)$totalAlumni ?></div><div class="l"><i class="fas fa-users"></i> <?= __('Total Alumni Tracked') ?></div></div>
    <?php foreach ($statusStats as $s): ?>
    <div class="pa-stat" style="border-left-color:<?= $statusColor[$s['label']] ?? '#6c757d' ?>">
        <div class="v"><?= (int)$s['n'] ?></div>
        <div class="l"><?= h(__($s['label'] ?: 'Unknown')) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="paSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / employer / status…') ?>"
           style="max-width:340px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto;-webkit-overflow-scrolling:touch">
    <div class="postApprentices index content">
        <table class="table" style="border-collapse:collapse;width:100%;min-width:800px" id="paTable">
            <thead style="background:linear-gradient(135deg,rgba(102,126,234,.15),rgba(118,75,162,.15))">
                <tr>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap" class="actions"><?= __('Actions') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Apprentice') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('return_date') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('current_status') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('employer') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('position') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('created') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($postApprentices as $row): ?>
                <?php $ap = $apprenticeMap[$row['apprentice_id']] ?? null; ?>
                <tr style="border-bottom:1px solid #e9ecef">
                    <td style="padding:10px 12px;white-space:nowrap" class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </td>
                    <td style="padding:10px 12px"><?= h($row['id']) ?></td>
                    <td style="padding:10px 12px">
                        <?php if ($ap): ?>
                            <strong><?= $this->Html->link(h($ap['name']), '/apprentices/view/' . $row['apprentice_id'], ['style' => 'color:#4c5bd4']) ?></strong>
                            <small style="color:#98a2ad"><?= h($ap['tmm_code']) ?></small>
                        <?php else: ?>
                            #<?= h($row['apprentice_id']) ?>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px 12px"><?= h($row['return_date']) ?></td>
                    <td style="padding:10px 12px">
                        <span class="pa-badge" style="background:<?= $statusColor[$row['current_status']] ?? '#6c757d' ?>"><?= h(__($row['current_status'] ?: '—')) ?></span>
                    </td>
                    <td style="padding:10px 12px"><?= h($row['employer']) ?></td>
                    <td style="padding:10px 12px"><?= h($row['position']) ?></td>
                    <td style="padding:10px 12px;font-size:12px;color:#6c757d"><?= h($row['created']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (iterator_count($postApprentices) === 0): ?>
                <tr><td colspan="8" style="text-align:center;padding:30px;color:#6c757d"><?= __('No alumni records yet. Use "Add New" to start tracking returned apprentices.') ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="paginator" style="margin-top:15px">
    <ul class="pagination">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
</div>

<script>
document.getElementById('paSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#paTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

<?php
/**
 * Apprentice Stories — kaizen / problem-solving records (enriched)
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeStory[]|\Cake\Collection\CollectionInterface $apprenticeStories
 * @var array $apprenticeMap id => [name, tmm_code]
 * @var array $classStats    [label, n]
 * @var int   $totalStories
 */
?>
<style>
.as-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.as-stat .v{font-size:24px;font-weight:800;color:#212a3e}
.as-stat .l{font-size:12px;color:#6c757d}
.as-tag{display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:700;background:#eef1fb;color:#4c5bd4}
.as-clip{max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-lightbulb"></i> <?= __('Apprentice Stories') ?> <span style="font-size:13px;color:#6c757d;font-weight:normal"><?= __('Problem & solution records') ?></span></h2>
        <div style="display:flex;align-items:center;gap:10px">
            <?= $this->Html->link('<i class="fas fa-user-tie"></i> ' . __('Apprentices'), '/apprentices', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add New'), ['action' => 'add'], ['class' => 'btn-export-light', 'escape' => false]) ?>
        </div>
    </div>
</div>

<!-- Ringkasan klasifikasi masalah -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="as-stat"><div class="v"><?= (int)$totalStories ?></div><div class="l"><i class="fas fa-book-open"></i> <?= __('Total Stories') ?></div></div>
    <?php foreach (array_slice($classStats, 0, 4) as $s): ?>
    <div class="as-stat" style="border-left-color:#764ba2">
        <div class="v"><?= (int)$s['n'] ?></div>
        <div class="l"><?= h($s['label'] ?: __('Unclassified')) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="asSearch" class="form-control" placeholder="🔍 <?= __('Filter by title / apprentice / classification…') ?>"
           style="max-width:360px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto;-webkit-overflow-scrolling:touch">
    <div class="apprenticeStories index content">
        <table class="table" style="border-collapse:collapse;width:100%;min-width:800px" id="asTable">
            <thead style="background:linear-gradient(135deg,rgba(102,126,234,.15),rgba(118,75,162,.15))">
                <tr>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap" class="actions"><?= __('Actions') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('id') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('title') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Apprentice') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('date_occurrence') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= $this->Paginator->sort('problem_classification') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Solution') ?></th>
                    <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Image') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($apprenticeStories as $row): ?>
                <?php $ap = $apprenticeMap[$row['apprentice_id']] ?? null; ?>
                <tr style="border-bottom:1px solid #e9ecef">
                    <td style="padding:10px 12px;white-space:nowrap" class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $row['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
                    </td>
                    <td style="padding:10px 12px"><?= h($row['id']) ?></td>
                    <td style="padding:10px 12px"><strong><?= h($row['title']) ?></strong></td>
                    <td style="padding:10px 12px">
                        <?php if ($ap): ?>
                            <?= $this->Html->link(h($ap['name']), '/apprentices/view/' . $row['apprentice_id'], ['style' => 'color:#4c5bd4']) ?>
                            <small style="color:#98a2ad"><?= h($ap['tmm_code']) ?></small>
                        <?php else: ?>#<?= h($row['apprentice_id']) ?><?php endif; ?>
                    </td>
                    <td style="padding:10px 12px"><?= h($row['date_occurrence']) ?></td>
                    <td style="padding:10px 12px"><?php if ($row['problem_classification']): ?><span class="as-tag"><?= h($row['problem_classification']) ?></span><?php endif; ?></td>
                    <td style="padding:10px 12px;font-size:12px;color:#555" class="as-clip" title="<?= h($row['problem_solution']) ?>"><?= h($row['problem_solution']) ?></td>
                    <td style="padding:10px 12px">
                        <?php if (!empty($row['image_path'])): ?>
                            <i class="fas fa-image" style="color:#28a745" title="<?= h($row['image_path']) ?>"></i>
                        <?php else: ?><span style="color:#ccc">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (iterator_count($apprenticeStories) === 0): ?>
                <tr><td colspan="8" style="text-align:center;padding:30px;color:#6c757d"><?= __('No stories yet.') ?></td></tr>
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
document.getElementById('asSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#asTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

<?php
/**
 * Chart of Accounts - typed account list with usage
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ChartOfAccount[]|\Cake\Collection\CollectionInterface $chartOfAccounts
 * @var array $countsByType   [type => count]
 * @var array $usageByAccount [account id => journal line count]
 * @var string $filterType
 */
$this->assign('title', 'Chart of Accounts');

$typeMeta = [
    'Asset' => ['icon' => 'fa-university', 'class' => 'type-asset'],
    'Liability' => ['icon' => 'fa-credit-card', 'class' => 'type-liability'],
    'Equity' => ['icon' => 'fa-balance-scale', 'class' => 'type-equity'],
    'Revenue' => ['icon' => 'fa-arrow-circle-down', 'class' => 'type-revenue'],
    'Expense' => ['icon' => 'fa-arrow-circle-up', 'class' => 'type-expense'],
];
?>

<div class="coa-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-sitemap"></i> <?= __('Chart of Accounts') ?></h2>
            <p class="text-muted"><?= __('The account structure behind every journal entry and financial report') ?></p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Account'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-indent"></i> ' . __('Hierarchy'),
                ['action' => 'hierarchy'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Type filter chips with counts -->
    <div class="filter-chips">
        <span class="chip-label"><i class="fa fa-filter"></i> <?= __('Type:') ?></span>
        <?= $this->Html->link(__('All'), ['action' => 'index'],
            ['class' => 'filter-chip' . ($filterType === '' ? ' active' : '')]) ?>
        <?php foreach ($typeMeta as $type => $meta): ?>
            <?= $this->Html->link(
                '<i class="fa ' . $meta['icon'] . '"></i> ' . __($type) . ' (' . (isset($countsByType[$type]) ? $countsByType[$type] : 0) . ')',
                ['action' => 'index', '?' => ['type' => $type]],
                ['escape' => false, 'class' => 'filter-chip' . ($filterType === $type ? ' active' : '')]) ?>
        <?php endforeach; ?>
    </div>

    <?php $rows = iterator_to_array($chartOfAccounts); ?>
    <?php if (!empty($rows)): ?>
        <div class="card">
            <div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table rich-table">
                    <thead>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= $this->Paginator->sort('code', __('Code')) ?></th>
                            <th><?= $this->Paginator->sort('name', __('Account')) ?></th>
                            <th><?= $this->Paginator->sort('type', __('Type')) ?></th>
                            <th><?= __('Description') ?></th>
                            <th><?= __('Journal Lines') ?></th>
                            <th><?= $this->Paginator->sort('is_active', __('Status')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <?php $meta = isset($typeMeta[$row['type']]) ? $typeMeta[$row['type']] : ['icon' => 'fa-circle-o', 'class' => 'type-other']; ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                            </td>
                            <td><code class="account-code"><?= h($row['code']) ?></code></td>
                            <td><strong><?= h($row['name']) ?></strong></td>
                            <td>
                                <span class="type-badge <?= $meta['class'] ?>">
                                    <i class="fa <?= $meta['icon'] ?>"></i> <?= h($row['type']) ?>
                                </span>
                            </td>
                            <td class="desc-cell" title="<?= h($row['description']) ?>"><?= h($row['description'] ?: '-') ?></td>
                            <td>
                                <?php $usage = isset($usageByAccount[$row['id']]) ? $usageByAccount[$row['id']] : 0; ?>
                                <?php if ($usage): ?>
                                    <span class="usage-chip"><?= number_format($usage) ?> <?= __('lines') ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><?= __('unused') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['is_active']): ?>
                                    <span class="badge badge-success"><?= __('Active') ?></span>
                                <?php else: ?>
                                    <span class="badge badge-secondary"><?= __('Inactive') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="paginator" style="margin-top: 15px;">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="empty-state">
                <i class="fa fa-sitemap fa-4x"></i>
                <h4><?= $filterType ? __('No {0} accounts.', $filterType) : __('No accounts yet.') ?></h4>
                <div>
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Account'),
                        ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-success']) ?>
                    <?php if ($filterType): ?>
                        <?= $this->Html->link(__('Show All'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.coa-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.coa-page .page-header h2 { margin: 0 0 5px 0; }
.filter-chips {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 12px 18px;
    margin-bottom: 20px;
}
.chip-label { font-weight: bold; color: #6c757d; font-size: 0.9em; }
.filter-chip {
    padding: 5px 14px;
    border-radius: 15px;
    border: 1.5px solid #e0e0e0;
    background: #f5f5f5;
    color: #546e7a;
    text-decoration: none;
    font-size: 0.88em;
    font-weight: bold;
}
.filter-chip:hover { border-color: #667eea; text-decoration: none; }
.filter-chip.active { background: #667eea; border-color: #667eea; color: #fff; }
.coa-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
    overflow: hidden;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 850px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th { padding: 12px; border-bottom: 2px solid #667eea; white-space: nowrap; text-align: left; }
.rich-table td { padding: 10px 12px; border-bottom: 1px solid #e9ecef; vertical-align: middle; }
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .desc-cell { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.rich-table .actions { white-space: nowrap; }
.rich-table th.actions,
.rich-table td.actions {
    position: sticky;
    left: 0;
    z-index: 5;
    width: 1%;
    padding-left: 10px;
    padding-right: 10px;
}
.rich-table th.actions { background: #e9ecfa; z-index: 6; }
.rich-table td.actions { background: #fff; }
.rich-table tbody tr:hover td.actions { background: #f8f9ff; }
.rich-table td.actions .btn { padding: 4px 8px; }
.table-scroll-wrapper.is-scrolled .rich-table th.actions,
.table-scroll-wrapper.is-scrolled .rich-table td.actions {
    border-right: 2px solid #d3d9f5;
    box-shadow: 4px 0 8px -2px rgba(60, 70, 130, 0.18);
}
.account-code {
    background: #f1f3f9;
    padding: 3px 8px;
    border-radius: 5px;
    font-weight: bold;
    color: #3b4890;
}
.type-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: bold;
    white-space: nowrap;
}
.type-asset     { background: #e8f4fd; color: #2b7fd4; }
.type-liability { background: #fdeaea; color: #c0392b; }
.type-equity    { background: #f7f3fb; color: #764ba2; }
.type-revenue   { background: #e6f9ee; color: #1e7e42; }
.type-expense   { background: #fdf1e3; color: #e67e22; }
.type-other     { background: #f1f3f5; color: #6c757d; }
.usage-chip {
    background: #eef1ff;
    color: #3b4890;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: bold;
}
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
}
.badge-success { background: #28a745; color: white; }
.badge-secondary { background: #6c757d; color: white; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
.empty-state .btn { margin: 5px; }
</style>

<script>
(function () {
    document.querySelectorAll('.coa-page .table-scroll-wrapper').forEach(function (wrapper) {
        var update = function () {
            wrapper.classList.toggle('is-scrolled', wrapper.scrollLeft > 2);
        };
        wrapper.addEventListener('scroll', update, { passive: true });
        update();
    });
})();
</script>

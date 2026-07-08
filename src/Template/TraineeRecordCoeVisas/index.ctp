<?php
/**
 * COE & Visa Records - coverage-focused index
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeRecordCoeVisa[]|\Cake\Collection\CollectionInterface $traineeRecordCoeVisas
 * @var array $traineeNames    [id => "Name (TMM-code)"]
 * @var array $missingTrainees [id => "Name (TMM-code)"] trainees without a COE/visa record
 * @var array $summary         ['trainees','covered','missing','records']
 * @var array $coeTypeNames    [id => title]
 */
$this->assign('title', 'COE & Visa Records');

$formatDate = function ($value) {
    if ($value instanceof \DateTimeInterface || $value instanceof \Cake\I18n\Date) {
        return $value->format('d M Y');
    }
    return $value ? (string)$value : '-';
};
?>

<div class="coevisa-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-file-text-o"></i> <?= __('COE & Visa Records') ?></h2>
            <p class="text-muted"><?= __('Certificate of Eligibility and visa administration for trainees going to Japan') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Record'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-dashboard"></i> ' . __('Dashboard'),
                ['controller' => 'Dashboard', 'action' => 'documentation'],
                ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format($summary['trainees']) ?></span>
            <span class="summary-label"><i class="fa fa-users"></i> <?= __('Trainees') ?></span>
        </div>
        <div class="summary-card summary-covered">
            <span class="summary-number"><?= number_format($summary['covered']) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('With COE / Visa Record') ?></span>
        </div>
        <div class="summary-card summary-missing">
            <span class="summary-number"><?= number_format($summary['missing']) ?></span>
            <span class="summary-label"><i class="fa fa-exclamation-circle"></i> <?= __('Missing Record') ?></span>
        </div>
    </div>

    <?php if (!empty($missingTrainees)): ?>
        <div class="card missing-card">
            <div class="card-header">
                <h4><i class="fa fa-exclamation-triangle"></i> <?= __('Trainees without a COE / visa record') ?></h4>
                <small class="text-muted"><?= __('A COE and visa are required before departure - add them here') ?></small>
            </div>
            <div class="card-body">
                <div class="missing-grid">
                    <?php foreach ($missingTrainees as $traineeId => $name): ?>
                        <div class="missing-item">
                            <span><i class="fa fa-user-o"></i> <?= h($name) ?></span>
                            <?= $this->Html->link('<i class="fa fa-plus"></i> ' . __('Add record'),
                                ['action' => 'add', '?' => ['trainee_id' => $traineeId]],
                                ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php $rows = iterator_to_array($traineeRecordCoeVisas); ?>
    <?php if (!empty($rows)): ?>
        <div class="card">
            <div class="table-scroll-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table rich-table">
                    <thead>
                        <tr>
                            <th class="actions"><?= __('Actions') ?></th>
                            <th><?= $this->Paginator->sort('trainee_id', __('Trainee')) ?></th>
                            <th><?= $this->Paginator->sort('master_trainee_coe_type_id', __('COE Type')) ?></th>
                            <th><?= $this->Paginator->sort('date_coe_received', __('COE Received')) ?></th>
                            <th><?= $this->Paginator->sort('date_visa_application', __('Visa Applied')) ?></th>
                            <th><?= $this->Paginator->sort('date_visa_received', __('Visa Received')) ?></th>
                            <th><?= $this->Paginator->sort('place_visa_issued', __('Visa Issued In')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                        <tr>
                            <td class="actions">
                                <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-info', 'title' => __('View')]) ?>
                                <?= $this->Html->link('<i class="fa fa-edit"></i>', ['action' => 'edit', $row['id']],
                                    ['escape' => false, 'class' => 'btn btn-sm btn-outline-primary', 'title' => __('Edit')]) ?>
                            </td>
                            <td>
                                <strong><?= h(isset($traineeNames[$row['trainee_id']]) ? $traineeNames[$row['trainee_id']] : ('#' . $row['trainee_id'])) ?></strong>
                            </td>
                            <td>
                                <?php $typeName = isset($coeTypeNames[$row['master_trainee_coe_type_id']]) ? $coeTypeNames[$row['master_trainee_coe_type_id']] : null; ?>
                                <?php if ($typeName): ?>
                                    <span class="badge <?= strpos($typeName, 'Electronic') !== false ? 'badge-outline-blue' : 'badge-outline-purple' ?>">
                                        <i class="fa <?= strpos($typeName, 'Electronic') !== false ? 'fa-bolt' : 'fa-file-o' ?>"></i>
                                        <?= h($typeName) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><i class="fa fa-certificate text-muted"></i> <?= h($formatDate($row['date_coe_received'])) ?></td>
                            <td><i class="fa fa-paper-plane-o text-muted"></i> <?= h($formatDate($row['date_visa_application'])) ?></td>
                            <td><i class="fa fa-check text-success"></i> <?= h($formatDate($row['date_visa_received'])) ?></td>
                            <td><i class="fa fa-map-marker text-muted"></i> <?= h($row['place_visa_issued'] ?: '-') ?></td>
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
                <i class="fa fa-file-text-o fa-4x"></i>
                <h4><?= __('No COE / visa records yet.') ?></h4>
                <p class="text-muted"><?= __('Use the buttons above to record each trainee\'s COE and visa details.') ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.coevisa-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.coevisa-page .page-header h2 { margin: 0 0 5px 0; }
.summary-strip { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
.summary-card {
    flex: 1;
    min-width: 160px;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.summary-total   { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-covered { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-missing { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-label { font-size: 13px; opacity: 0.95; }
.coevisa-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.coevisa-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.coevisa-page .card-header h4 { margin: 0 0 3px 0; }
.coevisa-page .card-body { padding: 20px; }
.missing-card { border-left: 5px solid #f5576c; }
.missing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 10px;
}
.missing-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: #fff7f8;
    border: 1px solid #f8d7da;
    border-radius: 8px;
    gap: 10px;
}
.rich-table { border-collapse: collapse; width: 100%; min-width: 950px; margin: 0; }
.rich-table thead {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
}
.rich-table th {
    padding: 12px;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
    text-align: left;
}
.rich-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
    white-space: nowrap;
}
.rich-table tbody tr:hover { background: #f8f9ff; }
.rich-table .actions { white-space: nowrap; }
/* Floating (sticky) actions column */
.rich-table th.actions,
.rich-table td.actions {
    position: sticky;
    left: 0;
    z-index: 5;
    box-shadow: 2px 0 6px rgba(0,0,0,0.08);
}
.rich-table th.actions { background: #e9ecfa; z-index: 6; }
.rich-table td.actions { background: #fff; }
.rich-table tbody tr:hover td.actions { background: #f8f9ff; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
}
.badge-outline-blue { border: 1px solid #4facfe; color: #2b7fd4; background: #f0f8ff; }
.badge-outline-purple { border: 1px solid #764ba2; color: #764ba2; background: #f7f3fb; }
.text-success { color: #28a745; }
.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { margin-bottom: 15px; opacity: 0.5; }
.empty-state h4 { margin: 10px 0; color: #495057; }
</style>

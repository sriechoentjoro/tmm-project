<?php
/**
 * Accounting Dashboard - trainee installments, journals and chart of accounts
 *
 * @var \App\View\AppView $this
 * @var array $stats            counts + collected/outstanding money figures
 * @var array $recentJournals   latest journal rows
 * @var array $paymentProgress  per-trainee latest running payment figures
 * @var array $journalsByStatus [status => count]
 * @var array $accountsByType   [type => count]
 */
$this->assign('title', 'Accounting Dashboard');

$money = function ($amount) {
    return 'Rp ' . number_format((int)$amount, 0, ',', '.');
};

$formatDate = function ($value) {
    $timestamp = $value ? strtotime((string)$value) : false;
    return $timestamp ? date('d M Y', $timestamp) : '-';
};

$journalBadge = function ($status) {
    switch ($status) {
        case 'Posted':
            return 'success';
        case 'Draft':
            return 'warning';
        case 'Void':
            return 'danger';
        default:
            return 'secondary';
    }
};

$accountTypeMeta = [
    'Asset' => ['icon' => 'fa-university', 'color' => '#2b7fd4'],
    'Liability' => ['icon' => 'fa-credit-card', 'color' => '#c0392b'],
    'Equity' => ['icon' => 'fa-balance-scale', 'color' => '#764ba2'],
    'Revenue' => ['icon' => 'fa-arrow-circle-down', 'color' => '#1e7e42'],
    'Expense' => ['icon' => 'fa-arrow-circle-up', 'color' => '#e67e22'],
];
?>

<div class="dashboard accounting-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h2><i class="fa fa-calculator"></i> Accounting Dashboard</h2>
            <p class="text-muted">Trainee installments, journal entries and chart of accounts</p>
        </div>
    </div>

    <!-- Money + volume stats -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-blue">
                <h3 class="stat-money"><?= $money($stats['collected']) ?></h3>
                <p>Collected from Trainees</p>
                <i class="fa fa-arrow-circle-down"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-pink">
                <h3 class="stat-money"><?= $money($stats['outstanding']) ?></h3>
                <p>Outstanding</p>
                <i class="fa fa-exclamation-circle"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-purple">
                <h3><?= number_format($stats['totalInstallments']) ?></h3>
                <p>Installment Payments</p>
                <i class="fa fa-money"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-green">
                <h3><?= number_format($stats['totalJournals']) ?></h3>
                <p>Journal Entries</p>
                <i class="fa fa-book"></i>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Payment progress -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header header-flex">
                    <h4><i class="fa fa-tasks"></i> Payment Progress per Trainee</h4>
                    <?= $this->Html->link('Payment Tracking',
                        ['controller' => 'TraineeInstallments', 'action' => 'tracking'],
                        ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($paymentProgress)): ?>
                        <?php foreach ($paymentProgress as $row): ?>
                            <div class="progress-row">
                                <div class="progress-row-header">
                                    <span class="progress-name">
                                        <?php if ($row['is_paid_off']): ?>
                                            <i class="fa fa-check-circle text-success"></i>
                                        <?php else: ?>
                                            <i class="fa fa-clock-o text-warning"></i>
                                        <?php endif; ?>
                                        <?= h($row['name']) ?>
                                    </span>
                                    <span class="progress-figures">
                                        <?= $money($row['accumulated']) ?> / <?= $money($row['full']) ?>
                                        <?php if ($row['is_paid_off']): ?>
                                            <span class="badge badge-success">Paid Off</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Owes <?= $money($row['unpaid']) ?></span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: <?= (int)$row['percent'] ?>%;
                                        background: <?= $row['percent'] >= 100 ? '#43e97b' : ($row['percent'] >= 50 ? '#4facfe' : '#f5a623') ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x"></i><br>
                            No installment payments recorded yet.<br>
                            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Record the first payment',
                                ['controller' => 'TraineeInstallments', 'action' => 'add'],
                                ['escape' => false, 'class' => 'btn btn-success', 'style' => 'margin-top:10px;']) ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent journals -->
            <div class="card">
                <div class="card-header header-flex">
                    <h4><i class="fa fa-book"></i> Recent Journal Entries</h4>
                    <div>
                        <?php foreach (['Posted', 'Draft', 'Void'] as $status): ?>
                            <?php if (!empty($journalsByStatus[$status])): ?>
                                <span class="badge badge-<?= $journalBadge($status) ?>"><?= $journalsByStatus[$status] ?> <?= h($status) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?= $this->Html->link('View All',
                            ['controller' => 'Journals', 'action' => 'index'],
                            ['class' => 'btn btn-sm btn-primary']) ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentJournals)): ?>
                        <div class="table-responsive">
                            <table class="table journal-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Ref</th>
                                        <th>Description</th>
                                        <th class="text-right">Debit</th>
                                        <th class="text-right">Credit</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentJournals as $journal): ?>
                                    <tr>
                                        <td><?= h($formatDate($journal['transaction_date'])) ?></td>
                                        <td>
                                            <?= $this->Html->link(h($journal['reference_no'] ?: ('#' . $journal['id'])),
                                                ['controller' => 'Journals', 'action' => 'view', $journal['id']]) ?>
                                        </td>
                                        <td class="desc-cell"><?= h($journal['description'] ?: '-') ?></td>
                                        <td class="text-right"><?= $money($journal['total_debit']) ?></td>
                                        <td class="text-right"><?= $money($journal['total_credit']) ?></td>
                                        <td><span class="badge badge-<?= $journalBadge($journal['status']) ?>"><?= h($journal['status']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-book fa-3x"></i><br>
                            No journal entries yet.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <!-- Chart of accounts overview -->
            <div class="card">
                <div class="card-header header-flex">
                    <h4><i class="fa fa-sitemap"></i> Chart of Accounts</h4>
                    <?= $this->Html->link('Manage',
                        ['controller' => 'ChartOfAccounts', 'action' => 'index'],
                        ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($accountsByType)): ?>
                        <?php foreach ($accountTypeMeta as $type => $meta): ?>
                            <?php if (!empty($accountsByType[$type])): ?>
                                <div class="coa-row">
                                    <span class="coa-icon" style="color: <?= $meta['color'] ?>;"><i class="fa <?= $meta['icon'] ?>"></i></span>
                                    <span class="coa-type"><?= h($type) ?></span>
                                    <span class="coa-count"><?= $accountsByType[$type] ?> <?= $accountsByType[$type] > 1 ? 'accounts' : 'account' ?></span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <p class="text-muted" style="font-size: 0.85em; margin: 10px 0 0;">
                            <i class="fa fa-info-circle"></i>
                            <?= number_format($stats['totalAccounts']) ?> accounts in total.
                        </p>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-sitemap fa-3x"></i><br>
                            No accounts configured yet.
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-bolt"></i> Quick Actions</h4></div>
                <div class="card-body quick-actions">
                    <?= $this->Html->link('<i class="fa fa-plus-circle"></i> Add Installment Payment',
                        ['controller' => 'TraineeInstallments', 'action' => 'add'],
                        ['escape' => false, 'class' => 'btn btn-success btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-line-chart"></i> Payment Tracking',
                        ['controller' => 'TraineeInstallments', 'action' => 'tracking'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-file-text-o"></i> Receipts (Paid Off)',
                        ['controller' => 'TraineeInstallments', 'action' => 'receipts'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-book"></i> Journal Entries',
                        ['controller' => 'Journals', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-sitemap"></i> Chart of Accounts',
                        ['controller' => 'ChartOfAccounts', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-info btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-bar-chart"></i> Financial Reports',
                        ['controller' => 'Reports', 'action' => 'index'],
                        ['escape' => false, 'class' => 'btn btn-secondary btn-block']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.accounting-dashboard .stat-card {
    padding: 25px 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    color: white;
    transition: transform 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.accounting-dashboard .stat-card:hover { transform: translateY(-3px); }
.stat-card-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card-blue   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card-green  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stat-card-pink   { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.accounting-dashboard .stat-card h3 { font-size: 32px; margin: 0; font-weight: bold; }
.accounting-dashboard .stat-card h3.stat-money { font-size: 22px; }
.accounting-dashboard .stat-card p { margin: 8px 0 0 0; font-size: 14px; opacity: 0.95; }
.accounting-dashboard .stat-card i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 44px;
    opacity: 0.22;
}
.accounting-dashboard .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.accounting-dashboard .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.accounting-dashboard .card-header h4 { margin: 0; }
.accounting-dashboard .card-body { padding: 20px; }
.header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}
.progress-row { margin-bottom: 16px; }
.progress-row-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    flex-wrap: wrap;
    gap: 4px;
}
.progress-name { font-weight: bold; }
.progress-figures { font-size: 0.88em; color: #495057; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.progress-track { height: 10px; background: #e9ecef; border-radius: 5px; overflow: hidden; }
.progress-fill { height: 100%; border-radius: 5px; transition: width 0.5s ease; }
.journal-table { width: 100%; border-collapse: collapse; }
.journal-table th {
    text-align: left;
    padding: 8px 10px;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
}
.journal-table td { padding: 8px 10px; border-bottom: 1px solid #e9ecef; }
.journal-table .text-right { text-align: right; white-space: nowrap; }
.journal-table .desc-cell { max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.coa-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}
.coa-row:last-of-type { border-bottom: none; }
.coa-icon { font-size: 20px; width: 26px; text-align: center; }
.coa-type { font-weight: bold; flex: 1; }
.coa-count { color: #6c757d; font-size: 0.9em; }
.badge {
    padding: 4px 10px;
    font-size: 11px;
    text-transform: uppercase;
    border-radius: 4px;
    font-weight: bold;
    white-space: nowrap;
}
.badge-success { background: #28a745; color: white; }
.badge-warning { background: #ffc107; color: #333; }
.badge-danger { background: #dc3545; color: white; }
.badge-secondary { background: #6c757d; color: white; }
.quick-actions .btn { margin-bottom: 10px; text-align: left; }
.btn-block { display: block; width: 100%; }
.text-success { color: #28a745; }
.text-warning { color: #f5a623; }
</style>

<?php
/**
 * Reports hub - every report with live key figures
 *
 * @var \App\View\AppView $this
 * @var array $figures
 */
$this->assign('title', 'Reports');

$money = function ($amount) {
    return 'Rp ' . number_format((float)$amount, 0, ',', '.');
};

$net = $figures['revenue'] - $figures['expense'];

$reportGroups = [
    [
        'title' => __('Financial Reports'),
        'icon' => 'fa-money',
        'reports' => [
            ['title' => __('Income Statement'), 'icon' => 'fa-line-chart', 'action' => 'incomeStatement',
             'description' => __('Revenue vs expenses from posted journals'),
             'figure' => ($net >= 0 ? '+' : '') . $money($net), 'figureLabel' => __('Net result')],
            ['title' => __('Balance Sheet'), 'icon' => 'fa-balance-scale', 'action' => 'balanceSheet',
             'description' => __('Assets, liabilities and equity balances'),
             'figure' => $money($figures['assets']), 'figureLabel' => __('Total assets')],
            ['title' => __('Cash Flow'), 'icon' => 'fa-exchange', 'action' => 'cashFlow',
             'description' => __('Monthly debit / credit journal totals'),
             'figure' => number_format($figures['journals']), 'figureLabel' => __('Journal entries')],
        ],
    ],
    [
        'title' => __('Operational Reports'),
        'icon' => 'fa-users',
        'reports' => [
            ['title' => __('Candidate Pipeline'), 'icon' => 'fa-filter', 'action' => 'candidatePipeline',
             'description' => __('Funnel from candidate to trainee to apprentice'),
             'figure' => number_format($figures['candidates']), 'figureLabel' => __('Candidates')],
            ['title' => __('Training Progress'), 'icon' => 'fa-graduation-cap', 'action' => 'trainingProgress',
             'description' => __('Trainee batches, scores and completion'),
             'figure' => number_format($figures['trainees']), 'figureLabel' => __('Trainees')],
            ['title' => __('Active Apprentices'), 'icon' => 'fa-briefcase', 'action' => 'activeApprentices',
             'description' => __('Apprentices currently working in Japan'),
             'figure' => number_format($figures['apprentices']), 'figureLabel' => __('Apprentices')],
        ],
    ],
];
?>

<div class="reports-hub">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-bar-chart"></i> <?= __('Reports') ?></h2>
            <p class="text-muted"><?= __('Financial and operational reporting - {0} of {1} journals posted',
                number_format($figures['posted']), number_format($figures['journals'])) ?></p>
        </div>
        <?= $this->Html->link('<i class="fa fa-dashboard"></i> ' . __('Dashboard'),
            ['controller' => 'Dashboard', 'action' => 'index'],
            ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>

    <?php foreach ($reportGroups as $group): ?>
        <h5 class="group-title"><i class="fa <?= $group['icon'] ?>"></i> <?= $group['title'] ?></h5>
        <div class="report-grid">
            <?php foreach ($group['reports'] as $report): ?>
                <a class="report-card" href="<?= $this->Url->build(['action' => $report['action']]) ?>">
                    <div class="report-top">
                        <i class="fa <?= $report['icon'] ?>"></i>
                        <div class="report-figure">
                            <strong><?= h($report['figure']) ?></strong>
                            <small><?= h($report['figureLabel']) ?></small>
                        </div>
                    </div>
                    <h4><?= h($report['title']) ?></h4>
                    <p><?= h($report['description']) ?></p>
                    <span class="report-open"><?= __('Open report') ?> <i class="fa fa-long-arrow-right"></i></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<style>
.reports-hub .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.reports-hub .page-header h2 { margin: 0 0 5px 0; }
.group-title {
    margin: 20px 0 12px;
    color: #764ba2;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 13px;
    font-weight: bold;
}
.report-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 15px;
}
.report-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #667eea;
    padding: 18px 20px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
}
.report-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(60, 70, 130, 0.18);
    text-decoration: none;
    color: inherit;
}
.report-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}
.report-top > i { font-size: 26px; color: #667eea; }
.report-figure { text-align: right; line-height: 1.2; }
.report-figure strong { display: block; color: #1e7e42; }
.report-figure small { color: #6c757d; }
.report-card h4 { margin: 0 0 4px 0; }
.report-card p { margin: 0 0 10px 0; color: #6c757d; font-size: 13px; flex: 1; }
.report-open { color: #667eea; font-size: 13px; font-weight: bold; }
</style>

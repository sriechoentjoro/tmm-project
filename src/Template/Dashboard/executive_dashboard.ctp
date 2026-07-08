<?php
/**
 * Executive Dashboard - full pipeline, finance and readiness at a glance
 *
 * @var \App\View\AppView $this
 * @var array $stats
 * @var array $finance          ['collected','outstanding','paidOff','paying']
 * @var array $readiness        ['ready','total']
 * @var array $recentCandidates
 */
$this->assign('title', 'Executive Dashboard');

$money = function ($amount) {
    return 'Rp ' . number_format((int)$amount, 0, ',', '.');
};
$formatDate = function ($value) {
    $timestamp = $value ? strtotime((string)$value) : false;
    if (!$timestamp) {
        return '-';
    }
    return date('Y-m-d', $timestamp) === date('Y-m-d') ? __('Today') : date('d M Y', $timestamp);
};
$conversion = function ($from, $to) {
    return $from > 0 ? round($to / $from * 100) . '%' : '-';
};
?>

<div class="dashboard executive-dashboard">
    <div class="row">
        <div class="col-md-12">
            <h2><i class="fa fa-line-chart"></i> Executive Dashboard</h2>
            <p class="text-muted">The full journey at a glance: Candidate → Trainee → Apprentice in Japan 🇯🇵</p>
        </div>
    </div>

    <!-- Timeline pipeline -->
    <div class="card">
        <div class="card-header"><h4><i class="fa fa-road"></i> <?= __('Pipeline Timeline') ?></h4></div>
        <div class="card-body">
            <div class="pipeline">
                <?= $this->Html->link(
                    '<span class="pipeline-icon"><i class="fa fa-user-plus"></i></span>' .
                    '<span class="pipeline-count">' . number_format($stats['totalCandidates']) . '</span>' .
                    '<span class="pipeline-label">' . __('Candidates') . '</span>',
                    ['controller' => 'Candidates', 'action' => 'index'],
                    ['escape' => false, 'class' => 'pipeline-stage']) ?>
                <span class="pipeline-arrow">
                    <i class="fa fa-angle-right"></i>
                    <small><?= $conversion($stats['totalCandidates'], $stats['totalTrainees']) ?></small>
                </span>
                <?= $this->Html->link(
                    '<span class="pipeline-icon"><i class="fa fa-graduation-cap"></i></span>' .
                    '<span class="pipeline-count">' . number_format($stats['totalTrainees']) . '</span>' .
                    '<span class="pipeline-label">' . __('Trainees') . '</span>',
                    ['controller' => 'Trainees', 'action' => 'index'],
                    ['escape' => false, 'class' => 'pipeline-stage']) ?>
                <span class="pipeline-arrow">
                    <i class="fa fa-angle-right"></i>
                    <small><?= $conversion($stats['totalTrainees'], $stats['totalApprentices']) ?></small>
                </span>
                <?= $this->Html->link(
                    '<span class="pipeline-icon"><i class="fa fa-briefcase"></i></span>' .
                    '<span class="pipeline-count">' . number_format($stats['totalApprentices']) . '</span>' .
                    '<span class="pipeline-label">' . __('Apprentices') . '</span>',
                    ['controller' => 'Apprentices', 'action' => 'index'],
                    ['escape' => false, 'class' => 'pipeline-stage']) ?>
                <span class="pipeline-arrow"><i class="fa fa-angle-right"></i></span>
                <span class="pipeline-stage pipeline-stage-final">
                    <span class="pipeline-icon">🇯🇵</span>
                    <span class="pipeline-count">&nbsp;</span>
                    <span class="pipeline-label"><?= __('Working in Japan') ?></span>
                </span>
            </div>
        </div>
    </div>

    <!-- KPI cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="stat-card stat-card-blue">
                <h3 class="stat-money"><?= $money($finance['collected']) ?></h3>
                <p><?= __('Collected from Trainees') ?></p>
                <i class="fa fa-arrow-circle-down"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-pink">
                <h3 class="stat-money"><?= $money($finance['outstanding']) ?></h3>
                <p><?= __('Outstanding Payments') ?></p>
                <i class="fa fa-exclamation-circle"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-green">
                <h3><?= number_format($readiness['ready']) ?> / <?= number_format($readiness['total']) ?></h3>
                <p><?= __('Trainees Ready to Depart') ?></p>
                <i class="fa fa-plane"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-card-purple">
                <h3><?= number_format($stats['totalOrders']) ?></h3>
                <p><?= __('Apprentice Orders') ?></p>
                <i class="fa fa-file-text-o"></i>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header header-flex">
                    <h4><i class="fa fa-user-plus"></i> <?= __('Latest Candidates') ?></h4>
                    <?= $this->Html->link(__('View All'), ['controller' => 'Candidates', 'action' => 'index'],
                        ['class' => 'btn btn-sm btn-primary']) ?>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentCandidates)): ?>
                        <table class="mini-table">
                            <tbody>
                                <?php foreach ($recentCandidates as $candidate): ?>
                                <tr>
                                    <td>
                                        <?= $this->Html->link(h($candidate['name'] ?: ('#' . $candidate['id'])),
                                            ['controller' => 'Candidates', 'action' => 'view', $candidate['id']]) ?>
                                    </td>
                                    <td class="text-muted"><?= h($candidate['candidate_code'] ?: '-') ?></td>
                                    <td class="text-muted text-right"><?= h($formatDate($candidate['created'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center text-muted py-4"><i class="fa fa-inbox fa-2x"></i><br><?= __('No candidates yet.') ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h4><i class="fa fa-building"></i> <?= __('Network') ?></h4></div>
                <div class="card-body">
                    <div class="network-row">
                        <span><i class="fa fa-school text-purple"></i> <?= __('LPK Institutions') ?></span>
                        <?= $this->Html->link(number_format($stats['totalLpkInstitutions']),
                            ['controller' => 'VocationalTrainingInstitutions', 'action' => 'index'],
                            ['class' => 'network-count']) ?>
                    </div>
                    <div class="network-row">
                        <span><i class="fa fa-building text-blue"></i> <?= __('Acceptance Organizations') ?></span>
                        <?= $this->Html->link(number_format($stats['totalOrganizations']),
                            ['controller' => 'AcceptanceOrganizations', 'action' => 'index'],
                            ['class' => 'network-count']) ?>
                    </div>
                    <div class="network-row">
                        <span><i class="fa fa-check-circle text-green"></i> <?= __('Trainees Fully Paid') ?></span>
                        <span class="network-count"><?= number_format($finance['paidOff']) ?> / <?= number_format($finance['paying']) ?></span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4><i class="fa fa-bar-chart"></i> <?= __('Deep-Dive Reports') ?></h4></div>
                <div class="card-body quick-actions">
                    <?= $this->Html->link('<i class="fa fa-filter"></i> ' . __('Candidate Pipeline'),
                        ['controller' => 'Reports', 'action' => 'candidatePipeline'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-graduation-cap"></i> ' . __('Training Progress'),
                        ['controller' => 'Reports', 'action' => 'trainingProgress'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-briefcase"></i> ' . __('Active Apprentices'),
                        ['controller' => 'Reports', 'action' => 'activeApprentices'],
                        ['escape' => false, 'class' => 'btn btn-primary btn-block']) ?>
                    <?= $this->Html->link('<i class="fa fa-line-chart"></i> ' . __('Income Statement'),
                        ['controller' => 'Reports', 'action' => 'incomeStatement'],
                        ['escape' => false, 'class' => 'btn btn-info btn-block']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.executive-dashboard .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border-radius: 10px;
    background: #fff;
}
.executive-dashboard .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    font-weight: bold;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.executive-dashboard .card-header h4 { margin: 0; }
.executive-dashboard .card-body { padding: 20px; }
.header-flex { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; }
.pipeline {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 5px;
}
.pipeline-stage {
    flex: 1;
    min-width: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 18px 10px;
    border-radius: 8px;
    background: #f8f9fa;
    text-decoration: none;
    color: #333;
    transition: transform 0.2s, box-shadow 0.2s;
}
a.pipeline-stage:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
    color: #333;
}
.pipeline-stage-final {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.pipeline-icon { font-size: 26px; margin-bottom: 5px; }
.pipeline-count { font-size: 26px; font-weight: bold; }
.pipeline-label { font-size: 12px; text-align: center; opacity: 0.85; }
.pipeline-arrow {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #adb5bd;
    line-height: 1.1;
}
.pipeline-arrow small { font-size: 11px; font-weight: bold; color: #764ba2; }
.executive-dashboard .stat-card {
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
.executive-dashboard .stat-card:hover { transform: translateY(-3px); }
.stat-card-purple { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card-blue   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stat-card-green  { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stat-card-pink   { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.executive-dashboard .stat-card h3 { font-size: 30px; margin: 0; font-weight: bold; }
.executive-dashboard .stat-card h3.stat-money { font-size: 21px; }
.executive-dashboard .stat-card p { margin: 8px 0 0 0; font-size: 14px; opacity: 0.95; }
.executive-dashboard .stat-card i {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 44px;
    opacity: 0.22;
}
.mini-table { width: 100%; border-collapse: collapse; }
.mini-table td { padding: 8px 6px; border-bottom: 1px solid #f0f0f0; }
.mini-table .text-right { text-align: right; }
.network-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}
.network-row:last-child { border-bottom: none; }
.network-count { font-weight: bold; font-size: 1.1em; color: #3b4890; text-decoration: none; }
.text-purple { color: #764ba2; }
.text-blue { color: #2b7fd4; }
.text-green { color: #1e7e42; }
.quick-actions .btn { margin-bottom: 10px; text-align: left; }
.btn-block { display: block; width: 100%; }
</style>

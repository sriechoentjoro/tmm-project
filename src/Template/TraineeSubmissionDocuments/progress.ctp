<?php
/**
 * Submission Progress Tracking - who is ready, what is missing, what is the bottleneck
 *
 * @var \App\View\AppView $this
 * @var array $summary          overall KPIs
 * @var array $traineeProgress  per-trainee rows, least complete first
 * @var array $documentProgress per-document coverage, most missing first
 */
$this->assign('title', 'Submission Progress');

$progressColor = function ($percent) {
    if ($percent >= 100) {
        return '#43e97b';
    }
    if ($percent >= 50) {
        return '#4facfe';
    }
    if ($percent > 0) {
        return '#f5a623';
    }
    return '#dc3545';
};
?>

<div class="progress-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-line-chart"></i> <?= __('Submission Progress') ?></h2>
            <p class="text-muted"><?= __('Pre-departure document readiness for trainees going to Japan') ?> 🇯🇵</p>
        </div>
        <div class="page-header-actions">
            <?= $this->Html->link('<i class="fa fa-plus-circle"></i> ' . __('Add Submission'),
                ['action' => 'add'], ['escape' => false, 'class' => 'btn btn-sm btn-success']) ?>
            <?= $this->Html->link('<i class="fa fa-check-square-o"></i> ' . __('Checklist'),
                ['action' => 'checklist'], ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
            <?= $this->Html->link('<i class="fa fa-th-list"></i> ' . __('All Documents'),
                ['action' => 'index'], ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>

    <!-- Overall completion -->
    <div class="card overall-card">
        <div class="card-body">
            <div class="overall-top">
                <div>
                    <span class="overall-percent"><?= (int)$summary['overallPercent'] ?>%</span>
                    <span class="overall-label"><?= __('overall completion') ?></span>
                </div>
                <div class="overall-detail">
                    <?= __('{0} of {1} required documents collected', number_format($summary['collected']), number_format($summary['expected'])) ?>
                    <small class="text-muted">
                        (<?= number_format($summary['requiredDocs']) ?> <?= __('required document types') ?>
                        &times; <?= number_format($summary['traineeTotal']) ?> <?= __('trainees') ?>)
                    </small>
                </div>
            </div>
            <div class="progress-track progress-track-big">
                <div class="progress-fill" style="width: <?= (int)$summary['overallPercent'] ?>%; background: <?= $progressColor($summary['overallPercent']) ?>;"></div>
            </div>
            <div class="overall-chips">
                <span class="chip chip-success"><i class="fa fa-check-circle"></i> <?= number_format($summary['ready']) ?> <?= __('ready to depart') ?></span>
                <span class="chip chip-info"><i class="fa fa-spinner"></i> <?= number_format($summary['inProgress']) ?> <?= __('in progress') ?></span>
                <span class="chip chip-danger"><i class="fa fa-exclamation-circle"></i> <?= number_format($summary['notStarted']) ?> <?= __('not started') ?></span>
                <span class="chip chip-muted">
                    <i class="fa fa-hourglass-half"></i>
                    <?= number_format($summary['pending']) ?> <?= __('uploads pending review') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Per trainee -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-users"></i> <?= __('By Trainee') ?></h4>
                    <small class="text-muted"><?= __('Least complete first — open a row to see exactly what is missing') ?></small>
                </div>
                <div class="card-body">
                    <?php if (!empty($traineeProgress)): ?>
                        <?php foreach ($traineeProgress as $row): ?>
                            <details class="progress-details">
                                <summary>
                                    <div class="progress-row-header">
                                        <span class="progress-name">
                                            <?php if ($row['percent'] >= 100): ?>
                                                <i class="fa fa-check-circle text-success"></i>
                                            <?php elseif ($row['percent'] > 0): ?>
                                                <i class="fa fa-clock-o text-warning"></i>
                                            <?php else: ?>
                                                <i class="fa fa-exclamation-circle text-danger"></i>
                                            <?php endif; ?>
                                            <?= h($row['name']) ?>
                                            <?php if (!empty($row['tmm_code'])): ?>
                                                <small class="text-muted">(<?= h($row['tmm_code']) ?>)</small>
                                            <?php endif; ?>
                                        </span>
                                        <span class="progress-count"><?= (int)$row['submitted'] ?> / <?= (int)$row['required'] ?></span>
                                    </div>
                                    <div class="progress-track">
                                        <div class="progress-fill" style="width: <?= (int)$row['percent'] ?>%; background: <?= $progressColor($row['percent']) ?>;"></div>
                                    </div>
                                </summary>
                                <div class="missing-panel">
                                    <?php if (empty($row['missing'])): ?>
                                        <p class="all-done"><i class="fa fa-check-circle"></i> <?= __('All required documents collected!') ?> 🇯🇵</p>
                                    <?php else: ?>
                                        <p class="missing-title"><?= __('Missing {0} document(s):', count($row['missing'])) ?></p>
                                        <ul class="missing-list">
                                            <?php foreach ($row['missing'] as $title): ?>
                                                <li><i class="fa fa-square-o"></i> <?= h($title) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <?= $this->Html->link('<i class="fa fa-check-square-o"></i> ' . __('Open checklist for {0}', h($row['name'])),
                                        ['action' => 'checklist', '?' => ['trainee_id' => $row['id']]],
                                        ['escape' => false, 'class' => 'btn btn-sm btn-primary']) ?>
                                </div>
                            </details>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x"></i><br><?= __('No trainees found.') ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Per document -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fa fa-file-text-o"></i> <?= __('By Document') ?></h4>
                    <small class="text-muted"><?= __('Most missing first — these documents are the bottleneck') ?></small>
                </div>
                <div class="card-body">
                    <?php if (!empty($documentProgress)): ?>
                        <?php foreach ($documentProgress as $doc): ?>
                            <div class="doc-row">
                                <div class="progress-row-header">
                                    <span class="progress-name">
                                        <?= h($doc['title']) ?>
                                        <small class="text-muted"><?= h($doc['category']) ?></small>
                                    </span>
                                    <span class="progress-count">
                                        <?= (int)$doc['covered'] ?> / <?= (int)$doc['total'] ?> <?= __('trainees') ?>
                                    </span>
                                </div>
                                <div class="progress-track">
                                    <div class="progress-fill" style="width: <?= (int)$doc['percent'] ?>%; background: <?= $progressColor($doc['percent']) ?>;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted py-4">
                            <i class="fa fa-inbox fa-3x"></i><br><?= __('No required documents configured.') ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card legend-card">
                <div class="card-body">
                    <strong><i class="fa fa-info-circle"></i> <?= __('How to read this page') ?></strong>
                    <ul class="legend-list">
                        <li><span class="legend-dot" style="background:#43e97b;"></span> <?= __('Green = complete (ready to depart)') ?></li>
                        <li><span class="legend-dot" style="background:#4facfe;"></span> <?= __('Blue = more than half collected') ?></li>
                        <li><span class="legend-dot" style="background:#f5a623;"></span> <?= __('Orange = started, still far to go') ?></li>
                        <li><span class="legend-dot" style="background:#dc3545;"></span> <?= __('Red = nothing collected yet') ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.progress-page .page-header h2 { margin: 0 0 5px 0; }
.progress-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.progress-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.progress-page .card-header h4 { margin: 0 0 3px 0; }
.progress-page .card-body { padding: 20px; }
.overall-card { border-left: 5px solid #667eea; }
.overall-top {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 10px;
}
.overall-percent {
    font-size: 42px;
    font-weight: bold;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.overall-label { font-size: 16px; color: #6c757d; margin-left: 6px; }
.overall-detail { text-align: right; }
.overall-detail small { display: block; }
.progress-track {
    height: 10px;
    background: #e9ecef;
    border-radius: 5px;
    overflow: hidden;
}
.progress-track-big { height: 18px; border-radius: 9px; }
.progress-fill {
    height: 100%;
    border-radius: inherit;
    transition: width 0.5s ease;
}
.overall-chips { display: flex; gap: 10px; margin-top: 12px; flex-wrap: wrap; }
.chip {
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: bold;
}
.chip-success { background: #e6f9ee; color: #1e7e42; }
.chip-info    { background: #e8f4fd; color: #2b7fd4; }
.chip-danger  { background: #fdeaea; color: #c0392b; }
.chip-muted   { background: #f1f3f5; color: #6c757d; }
.progress-details { margin-bottom: 16px; }
.progress-details summary { cursor: pointer; list-style: none; }
.progress-details summary::-webkit-details-marker { display: none; }
.progress-row-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 5px;
    flex-wrap: wrap;
    gap: 4px;
}
.progress-name { font-weight: bold; }
.progress-count { font-size: 0.9em; color: #6c757d; }
.missing-panel {
    margin: 10px 0 5px 0;
    padding: 12px 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 3px solid #f5a623;
}
.missing-title { font-weight: bold; margin: 0 0 8px 0; }
.missing-list { list-style: none; padding: 0; margin: 0 0 10px 0; }
.missing-list li { padding: 3px 0; color: #495057; }
.missing-list i { color: #adb5bd; margin-right: 6px; }
.all-done { color: #1e7e42; font-weight: bold; margin: 0 0 10px 0; }
.doc-row { margin-bottom: 16px; }
.doc-row .progress-name small { display: block; font-weight: normal; }
.legend-card { background: #fafbff; }
.legend-list { list-style: none; padding: 0; margin: 10px 0 0 0; }
.legend-list li { padding: 3px 0; color: #495057; }
.legend-dot {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}
.text-success { color: #28a745; }
.text-warning { color: #f5a623; }
.text-danger { color: #dc3545; }
</style>

<?php
/**
 * Departure Documents - readiness hub
 *
 * @var \App\View\AppView $this
 * @var array $sections          module cards with live coverage
 * @var array $readiness         per-trainee readiness matrix (most ready first)
 * @var array $summary           ['trainees','ready','notReady']
 * @var array $requiredDocuments master departure document reference
 * @var int $traineeTotal
 */
$this->assign('title', 'Departure Documents');

$checkColumns = [
    'passport' => ['label' => __('Passport'), 'icon' => 'fa-id-card-o', 'controller' => 'TraineeRecordPasports', 'prefill' => true],
    'coe_visa' => ['label' => __('COE / Visa'), 'icon' => 'fa-file-text-o', 'controller' => 'TraineeRecordCoeVisas', 'prefill' => true],
    'medical' => ['label' => __('Medical'), 'icon' => 'fa-heartbeat', 'controller' => 'TraineeRecordMedicalCheckUps', 'prefill' => true],
    'ticket' => ['label' => __('Ticket'), 'icon' => 'fa-plane', 'controller' => 'Tickets', 'prefill' => false],
];
?>

<div class="departure-page">
    <div class="page-header">
        <div>
            <h2><i class="fa fa-plane"></i> <?= __('Departure Documents') ?></h2>
            <p class="text-muted"><?= __('Everything a trainee needs before boarding the flight to Japan') ?> 🇯🇵</p>
        </div>
        <?= $this->Html->link('<i class="fa fa-dashboard"></i> ' . __('Dashboard'),
            ['controller' => 'Dashboard', 'action' => 'documentation'],
            ['escape' => false, 'class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>

    <!-- Summary strip -->
    <div class="summary-strip">
        <div class="summary-card summary-total">
            <span class="summary-number"><?= number_format($summary['trainees']) ?></span>
            <span class="summary-label"><i class="fa fa-users"></i> <?= __('Trainees') ?></span>
        </div>
        <div class="summary-card summary-ready">
            <span class="summary-number"><?= number_format($summary['ready']) ?></span>
            <span class="summary-label"><i class="fa fa-check-circle"></i> <?= __('Ready to Depart') ?></span>
        </div>
        <div class="summary-card summary-notready">
            <span class="summary-number"><?= number_format($summary['notReady']) ?></span>
            <span class="summary-label"><i class="fa fa-exclamation-circle"></i> <?= __('Still Missing Items') ?></span>
        </div>
    </div>

    <!-- Readiness matrix -->
    <div class="card">
        <div class="card-header">
            <h4><i class="fa fa-th-list"></i> <?= __('Departure Readiness per Trainee') ?></h4>
            <small class="text-muted"><?= __('Green = record exists. Click a red item to add the missing record.') ?></small>
        </div>
        <div class="card-body">
            <?php if (!empty($readiness)): ?>
                <div class="table-scroll-wrapper" style="overflow-x: auto;">
                    <table class="readiness-table">
                        <thead>
                            <tr>
                                <th><?= __('Trainee') ?></th>
                                <?php foreach ($checkColumns as $column): ?>
                                    <th class="check-col"><i class="fa <?= $column['icon'] ?>"></i> <?= $column['label'] ?></th>
                                <?php endforeach; ?>
                                <th class="check-col"><?= __('Status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($readiness as $row): ?>
                            <tr>
                                <td class="trainee-cell">
                                    <strong><?= h($row['name']) ?></strong>
                                    <div class="mini-track">
                                        <div class="mini-fill" style="width: <?= (int)($row['done'] / 4 * 100) ?>%;
                                            background: <?= $row['done'] === 4 ? '#43e97b' : ($row['done'] >= 2 ? '#4facfe' : '#f5a623') ?>;"></div>
                                    </div>
                                </td>
                                <?php foreach ($checkColumns as $key => $column): ?>
                                    <td class="check-col">
                                        <?php if ($row['checks'][$key]): ?>
                                            <span class="check-chip check-ok"><i class="fa fa-check"></i></span>
                                        <?php else: ?>
                                            <?php
                                            $url = ['controller' => $column['controller'], 'action' => 'add'];
                                            if ($column['prefill']) {
                                                $url['?'] = ['trainee_id' => $row['id']];
                                            }
                                            ?>
                                            <?= $this->Html->link('<i class="fa fa-plus"></i>', $url, [
                                                'escape' => false,
                                                'class' => 'check-chip check-missing',
                                                'title' => __('Add {0} for {1}', $column['label'], $row['name']),
                                            ]) ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="check-col">
                                    <?php if ($row['done'] === 4): ?>
                                        <span class="ready-badge ready-yes">🇯🇵 <?= __('Ready') ?></span>
                                    <?php else: ?>
                                        <span class="ready-badge ready-no"><?= $row['done'] ?>/4</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-muted py-4">
                    <i class="fa fa-inbox fa-3x"></i><br><?= __('No trainees found.') ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Module cards with live coverage -->
    <div class="module-grid">
        <?php foreach ($sections as $section): ?>
            <?php $percent = $traineeTotal > 0 ? (int)round($section['covered'] / $traineeTotal * 100) : 0; ?>
            <a class="module-card" href="<?= $this->Url->build(['controller' => $section['controller'], 'action' => isset($section['action']) ? $section['action'] : 'index']) ?>">
                <div class="module-top">
                    <i class="fas <?= h($section['icon']) ?>"></i>
                    <span class="module-coverage"><?= $section['covered'] ?>/<?= $traineeTotal ?> <?= __('trainees') ?></span>
                </div>
                <h4><?= h($section['title']) ?></h4>
                <p><?= h($section['description']) ?></p>
                <div class="mini-track">
                    <div class="mini-fill" style="width: <?= $percent ?>%;
                        background: <?= $percent >= 100 ? '#43e97b' : ($percent >= 50 ? '#4facfe' : '#f5a623') ?>;"></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Required document reference -->
    <?php if (!empty($requiredDocuments)): ?>
        <div class="card">
            <div class="card-header">
                <h4><i class="fa fa-suitcase"></i> <?= __('The Departure Folder') ?></h4>
                <small class="text-muted"><?= __('Documents every trainee must carry when flying to Japan') ?></small>
            </div>
            <div class="card-body">
                <div class="folder-grid">
                    <?php foreach ($requiredDocuments as $i => $doc): ?>
                        <div class="folder-item">
                            <span class="folder-num"><?= $i + 1 ?></span>
                            <div>
                                <strong><?= h($doc['title']) ?></strong>
                                <small class="text-muted"><?= h($doc['format']) ?><?= $doc['is_required'] ? ' · ' . __('required') : '' ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.departure-page .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}
.departure-page .page-header h2 { margin: 0 0 5px 0; }
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
.summary-total    { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.summary-ready    { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.summary-notready { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.summary-number { font-size: 28px; font-weight: bold; line-height: 1.1; }
.summary-label { font-size: 13px; opacity: 0.95; }
.departure-page .card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-radius: 10px;
    background: #fff;
    margin-bottom: 20px;
}
.departure-page .card-header {
    background: #f8f9fa;
    border-bottom: 2px solid #e0e0e0;
    padding: 12px 20px;
    border-radius: 10px 10px 0 0;
}
.departure-page .card-header h4 { margin: 0 0 3px 0; }
.departure-page .card-body { padding: 20px; }
.readiness-table { width: 100%; border-collapse: collapse; min-width: 650px; }
.readiness-table th {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 2px solid #667eea;
    white-space: nowrap;
}
.readiness-table td { padding: 12px; border-bottom: 1px solid #e9ecef; vertical-align: middle; }
.readiness-table tbody tr:hover { background: #f8f9ff; }
.readiness-table .check-col { text-align: center; }
.trainee-cell { min-width: 220px; }
.mini-track {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 6px;
}
.mini-fill { height: 100%; border-radius: 3px; transition: width 0.5s ease; }
.check-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 13px;
    text-decoration: none;
}
.check-ok { background: #e6f9ee; color: #1e7e42; }
.check-missing {
    background: #fdeaea;
    color: #c0392b;
    border: 1px dashed #e57373;
    transition: transform 0.15s, background 0.15s;
}
.check-missing:hover { background: #c0392b; color: #fff; text-decoration: none; transform: scale(1.1); }
.ready-badge {
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    font-weight: bold;
    white-space: nowrap;
}
.ready-yes { background: #e6f9ee; color: #1e7e42; }
.ready-no { background: #f1f3f5; color: #6c757d; }
.module-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}
.module-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #667eea;
    padding: 18px 20px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s, box-shadow 0.2s;
}
.module-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 16px rgba(60, 70, 130, 0.18);
    text-decoration: none;
    color: inherit;
}
.module-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.module-top i { font-size: 24px; color: #667eea; }
.module-coverage { font-size: 12px; font-weight: bold; color: #6c757d; }
.module-card h4 { margin: 0 0 4px 0; }
.module-card p { margin: 0 0 10px 0; color: #6c757d; font-size: 13px; }
.folder-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 10px;
}
.folder-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #fafbff;
    border: 1px solid #e4e8fb;
    border-radius: 8px;
}
.folder-item small { display: block; }
.folder-num {
    flex: 0 0 26px;
    height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: bold;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

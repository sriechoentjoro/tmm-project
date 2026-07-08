<?php
/**
 * @var \App\View\AppView $this
 * @var array $row       certificate + joined trainee/batch/score summary
 * @var array $scoreRows per-competency test scores for this trainee
 */

function certGrade($avg) {
    if ($avg === null) return null;
    if ($avg >= 81) return ['A', 'Sangat Baik',   'background:#e8f5e9;color:#2E7D32;border-color:#43A047;'];
    if ($avg >= 61) return ['B', 'Baik',           'background:#e3f2fd;color:#1565C0;border-color:#1E88E5;'];
    if ($avg >= 41) return ['C', 'Cukup',          'background:#fffde7;color:#F57F17;border-color:#F9A825;'];
    if ($avg >= 21) return ['D', 'Kurang',         'background:#fff3e0;color:#E65100;border-color:#FB8C00;'];
    return               ['E', 'Sangat Kurang',   'background:#fce4ec;color:#B71C1C;border-color:#E53935;'];
}

$name     = $row['trainee_name'] ?? '—';
$code     = $row['trainee_code'] ?? '';
$init     = mb_strtoupper(mb_substr($name, 0, 1));
$certNo   = $row['certificate_no'] ?? '—';
$batch    = $row['batch_code']    ?? null;
$issueD   = $row['issue_date']    ? date('d M Y', strtotime($row['issue_date'])) : '—';
$createdD = $row['created']       ? date('d M Y H:i', strtotime($row['created']))   : '—';
$avg      = $row['avg_score'] !== null ? (float)$row['avg_score'] : null;
$tests    = (int)$row['test_count'];
$passed   = (int)($row['passed'] ?? 0);
$failed   = (int)($row['failed'] ?? 0);
$minS     = $row['min_score'] ?? null;
$maxS     = $row['max_score'] ?? null;
$gradeInfo = certGrade($avg);

$gradeStyle = [
    'A' => 'background:#e8f5e9;color:#2E7D32;border-color:#43A047;',
    'B' => 'background:#e3f2fd;color:#1565C0;border-color:#1E88E5;',
    'C' => 'background:#fffde7;color:#F57F17;border-color:#F9A825;',
    'D' => 'background:#fff3e0;color:#E65100;border-color:#FB8C00;',
    'E' => 'background:#fce4ec;color:#B71C1C;border-color:#E53935;',
];
?>
<style>
/* ── Layout ───────────────────────────────────────────────────── */
.cert-view-grid { display: grid; grid-template-columns: 340px 1fr; gap: 20px; align-items: start; }
@media(max-width:900px) { .cert-view-grid { grid-template-columns: 1fr; } }

/* ── Certificate card ─────────────────────────────────────────── */
.cert-card {
    background: #fff; border-radius: 14px;
    box-shadow: 0 4px 20px rgba(105,73,188,0.12);
    overflow: hidden;
}
.cert-card-header {
    background: linear-gradient(135deg, #6949BC 0%, #9C27B0 100%);
    padding: 24px 22px; text-align: center;
}
.cert-avatar {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 26px; font-weight: 800;
    margin: 0 auto 12px;
}
.cert-card-header h3 { color: #fff; margin: 0 0 4px; font-size: 16px; font-weight: 700; }
.cert-card-header p  { color: rgba(255,255,255,0.72); margin: 0; font-size: 12px; }

.cert-body { padding: 20px 22px; }
.cert-field { margin-bottom: 14px; }
.cert-field-label {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #94a3b8; margin-bottom: 3px;
}
.cert-field-value { font-size: 14px; color: #1a202c; font-weight: 500; }

.cert-no-display {
    font-family: 'Courier New', monospace;
    font-size: 15px; font-weight: 700; color: #6949BC;
    background: #f3f0ff; border: 1.5px dashed #c4b5fd;
    border-radius: 7px; padding: 8px 12px;
    letter-spacing: 1px; display: block;
}
.batch-chip {
    display: inline-flex; align-items: center; gap: 5px;
    background: #e0f2fe; color: #0277BD; border-radius: 6px;
    padding: 3px 10px; font-size: 12px; font-weight: 600;
}

/* ── Right-side panels ────────────────────────────────────────── */
.panel {
    background: #fff; border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    overflow: hidden; margin-bottom: 18px;
}
.panel-header {
    padding: 14px 20px; border-bottom: 1px solid #f0f4f8;
    display: flex; align-items: center; gap: 10px;
}
.panel-header-icon {
    width: 32px; height: 32px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.panel-header h4 { margin: 0; font-size: 14px; font-weight: 700; color: #1a202c; }
.panel-body { padding: 18px 20px; }

/* ── Stat row ─────────────────────────────────────────────────── */
.stat-mini-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
.stat-mini {
    flex: 1; min-width: 90px;
    background: #f8fafc; border-radius: 10px;
    padding: 12px 14px; border-left: 3px solid;
    text-align: center;
}
.stat-mini .sv { font-size: 22px; font-weight: 800; line-height: 1; }
.stat-mini .sl { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px; color: #94a3b8; margin-top: 3px; }

/* ── Grade display ────────────────────────────────────────────── */
.grade-display {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 10px 16px; border-radius: 10px; border: 1.5px solid;
    font-size: 14px; font-weight: 600;
}
.grade-letter { font-size: 28px; font-weight: 900; line-height: 1; }

/* ── Score progress bar ───────────────────────────────────────── */
.score-bar-wrap { margin: 12px 0 6px; }
.score-bar-label { display:flex; justify-content:space-between; font-size:11px; color:#94a3b8; margin-bottom:3px; }
.score-bar-bg { height:10px; border-radius:5px; background:#e2e8f0; overflow:hidden; }
.score-bar-fill {
    height:100%; border-radius:5px;
    background:linear-gradient(90deg,#ef4444 0%,#f59e0b 50%,#22c55e 100%);
    transition: width 0.5s ease;
}

/* ── Score history table ──────────────────────────────────────── */
.score-history-table { width:100%; border-collapse:collapse; font-size:13px; }
.score-history-table thead tr { background:#f8fafc; }
.score-history-table thead th {
    padding:8px 12px; font-size:11px; font-weight:700;
    text-transform:uppercase; letter-spacing:0.7px; color:#64748b;
    border-bottom:1.5px solid #e2e8f0; text-align:left;
}
.score-history-table tbody tr { border-bottom:1px solid #f0f4f8; transition:background 0.12s; }
.score-history-table tbody tr:hover { background:#f8fafc; }
.score-history-table tbody td { padding:9px 12px; color:#374151; vertical-align:middle; }
.score-bar-mini { display:flex; align-items:center; gap:8px; }
.score-num { font-weight:700; min-width:28px; }
.score-track { flex:1; height:5px; border-radius:3px; background:#e2e8f0; min-width:60px; overflow:hidden; }
.score-fill-s { height:100%; border-radius:3px; background:linear-gradient(90deg,#ef4444 0%,#f59e0b 50%,#22c55e 100%); }
.grade-badge-sm {
    display:inline-flex; align-items:center; justify-content:center;
    width:26px; height:26px; border-radius:6px;
    font-size:12px; font-weight:800; border:1.5px solid;
}
.empty-scores { text-align:center; padding:32px; color:#94a3b8; }
.empty-scores i { font-size:32px; margin-bottom:10px; display:block; }
</style>

<!-- Cross-navigation -->
<div style="display:flex;gap:8px;flex-wrap:wrap;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:10px 16px;margin-bottom:16px;align-items:center;">
    <span style="font-size:12px;color:#94a3b8;font-weight:600;"><i class="fas fa-link" style="margin-right:4px;"></i><?= __('Related') ?>:</span>
    <?= $this->Html->link('<i class="fas fa-list"></i> ' . __('All Certificates'), ['action' => 'index'], ['style' => 'color:#6949BC;border-color:#6949BC;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-calendar-day"></i> ' . __('Daily Entry'), ['controller' => 'TraineeTrainingTestScores', 'action' => 'daily'], ['style' => 'color:#00897B;border-color:#00897B;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-table"></i> ' . __('Score Summary'), ['controller' => 'TraineeTrainingTestScores', 'action' => 'index'], ['style' => 'color:#0288D1;border-color:#0288D1;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-check-double"></i> ' . __('Pass/Fail Report'), ['controller' => 'TraineeTrainingTestScores', 'action' => 'report'], ['style' => 'color:#0097A7;border-color:#0097A7;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
</div>

<!-- Page header -->
<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:18px;">
    <h2 style="margin:0;font-size:20px;font-weight:700;color:#1a202c;display:flex;align-items:center;gap:10px;">
        <i class="fas fa-certificate" style="color:#6949BC;"></i>
        <?= __('Certificate Detail') ?>
        <span style="font-size:13px;font-weight:500;color:#94a3b8;"># <?= h($row['id']) ?></span>
    </h2>
    <div style="display:flex;gap:8px;">
        <?= $this->Html->link('<i class="fas fa-print"></i> ' . __('Print Certificate'), ['action' => 'printCertificate', $row['id']], ['style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:linear-gradient(135deg,#6949BC,#9C27B0);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;box-shadow:0 2px 8px rgba(105,73,188,0.25);', 'escape' => false, 'target' => '_blank']) ?>
        <?= $this->Html->link('<i class="fas fa-pen"></i> ' . __('Edit'), ['action' => 'edit', $row['id']], ['style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#fff;color:#6949BC;border:1.5px solid #6949BC;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="fas fa-arrow-left"></i> ' . __('Back'), ['action' => 'index'], ['style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:#fff;color:#64748b;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;', 'escape' => false]) ?>
    </div>
</div>

<!-- Main grid -->
<div class="cert-view-grid">

    <!-- LEFT: Certificate identity card -->
    <div>
        <div class="cert-card">
            <div class="cert-card-header">
                <div class="cert-avatar"><?= h($init) ?></div>
                <h3><?= h($name) ?></h3>
                <?php if ($code): ?><p><?= h($code) ?></p><?php endif; ?>
            </div>
            <div class="cert-body">

                <div class="cert-field">
                    <div class="cert-field-label"><?= __('Certificate No.') ?></div>
                    <span class="cert-no-display"><?= h($certNo) ?></span>
                </div>

                <div class="cert-field">
                    <div class="cert-field-label"><?= __('Issue Date') ?></div>
                    <div class="cert-field-value">
                        <i class="fas fa-calendar-check" style="color:#6949BC;margin-right:6px;font-size:12px;"></i>
                        <?= h($issueD) ?>
                    </div>
                </div>

                <?php if ($batch): ?>
                <div class="cert-field">
                    <div class="cert-field-label"><?= __('Training Batch') ?></div>
                    <div class="cert-field-value">
                        <span class="batch-chip"><i class="fas fa-layer-group"></i> <?= h($batch) ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($row['notes']): ?>
                <div class="cert-field">
                    <div class="cert-field-label"><?= __('Notes') ?></div>
                    <div class="cert-field-value" style="font-size:13px;color:#4a5568;"><?= h($row['notes']) ?></div>
                </div>
                <?php endif; ?>

                <div class="cert-field" style="border-top:1px solid #f0f4f8;padding-top:12px;margin-top:6px;">
                    <div class="cert-field-label"><?= __('Recorded') ?></div>
                    <div class="cert-field-value" style="font-size:12px;color:#94a3b8;"><?= h($createdD) ?></div>
                </div>

            </div>
        </div>
    </div>

    <!-- RIGHT: Score summary + history -->
    <div>

        <!-- Score summary panel -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-header-icon" style="background:#e0f2fe;color:#0288D1;"><i class="fas fa-chart-bar"></i></div>
                <h4><?= __('Test Score Summary') ?></h4>
            </div>
            <div class="panel-body">

                <?php if ($avg !== null): ?>

                <!-- Stat mini row -->
                <div class="stat-mini-row">
                    <div class="stat-mini" style="border-color:#0288D1;">
                        <div class="sv" style="color:#0288D1;"><?= $tests ?></div>
                        <div class="sl"><?= __('Tests') ?></div>
                    </div>
                    <div class="stat-mini" style="border-color:#6949BC;">
                        <div class="sv" style="color:#6949BC;"><?= number_format($avg, 1) ?></div>
                        <div class="sl"><?= __('Avg Score') ?></div>
                    </div>
                    <div class="stat-mini" style="border-color:#22c55e;">
                        <div class="sv" style="color:#22c55e;"><?= $passed ?></div>
                        <div class="sl"><?= __('Passed') ?></div>
                    </div>
                    <div class="stat-mini" style="border-color:#ef4444;">
                        <div class="sv" style="color:#ef4444;"><?= $failed ?></div>
                        <div class="sl"><?= __('Failed') ?></div>
                    </div>
                </div>

                <!-- Average score bar -->
                <div class="score-bar-wrap">
                    <div class="score-bar-label">
                        <span><?= __('Average Score') ?>: <strong><?= number_format($avg, 1) ?></strong></span>
                        <?php if ($minS !== null && $maxS !== null): ?>
                        <span style="color:#94a3b8;">
                            <span style="color:#E53935;"><?= $minS ?></span>
                            &nbsp;—&nbsp;
                            <span style="color:#2E7D32;"><?= $maxS ?></span>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="score-bar-bg">
                        <div class="score-bar-fill" style="width:<?= min($avg, 100) ?>%;"></div>
                    </div>
                </div>

                <!-- Grade -->
                <?php if ($gradeInfo): [$gl, $gd, $gs] = $gradeInfo; ?>
                <div style="margin-top:14px;">
                    <span class="grade-display" style="<?= $gs ?>">
                        <span class="grade-letter"><?= h($gl) ?></span>
                        <div>
                            <div style="font-size:13px;"><?= h($gd) ?></div>
                            <div style="font-size:11px;font-weight:400;opacity:0.75;"><?= __('from avg score') ?></div>
                        </div>
                    </span>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-scores">
                    <i class="fas fa-chart-line"></i>
                    <p><?= __('No test scores recorded yet for this trainee.') ?></p>
                    <?= $this->Html->link('<i class="fas fa-plus"></i> ' . __('Add Score'),
                        ['controller' => 'TraineeTrainingTestScores', 'action' => 'add'],
                        ['style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:linear-gradient(135deg,#0288D1,#0097A7);color:#fff;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;', 'escape' => false]) ?>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Per-competency score history -->
        <div class="panel">
            <div class="panel-header">
                <div class="panel-header-icon" style="background:#f3f0ff;color:#6949BC;"><i class="fas fa-history"></i></div>
                <h4><?= __('Score History') ?> <span style="font-size:12px;font-weight:400;color:#94a3b8;">(<?= count($scoreRows) ?>)</span></h4>
                <?php if (!empty($scoreRows)): ?>
                <?= $this->Html->link('<i class="fas fa-calendar-day"></i> ' . __('Daily Entry'),
                    ['controller' => 'TraineeTrainingTestScores', 'action' => 'daily'],
                    ['style' => 'margin-left:auto;display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border:1.5px solid #00897B;color:#00897B;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;', 'escape' => false]) ?>
                <?php endif; ?>
            </div>
            <div class="panel-body" style="padding:0;">

                <?php if (empty($scoreRows)): ?>
                <div class="empty-scores">
                    <i class="fas fa-inbox"></i>
                    <p><?= __('No scores on record.') ?></p>
                </div>
                <?php else: ?>
                <table class="score-history-table">
                    <thead>
                        <tr>
                            <th><?= __('Date') ?></th>
                            <th><?= __('Competency') ?></th>
                            <th><?= __('Score') ?></th>
                            <th><?= __('Grade') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($scoreRows as $s):
                        $sc    = (int)$s['score'];
                        $g     = $s['grade'] ?? '?';
                        $gDesc = $s['grade_desc'] ?? '';
                        $gSt   = isset($gradeStyle[$g]) ? $gradeStyle[$g] : 'background:#f1f5f9;color:#64748b;border-color:#cbd5e0;';
                        $dt    = $s['test_date'] ? date('d M Y', strtotime($s['test_date'])) : '—';
                    ?>
                    <tr>
                        <td style="white-space:nowrap;color:#4a5568;font-size:12px;">
                            <i class="fas fa-calendar-alt" style="color:#0288D1;margin-right:4px;font-size:10px;"></i>
                            <?= h($dt) ?>
                        </td>
                        <td style="max-width:180px;font-size:12px;color:#374151;"><?= h($s['competency'] ?? '—') ?></td>
                        <td>
                            <div class="score-bar-mini">
                                <span class="score-num" style="font-size:13px;"><?= $sc ?></span>
                                <div class="score-track">
                                    <div class="score-fill-s" style="width:<?= $sc ?>%;"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="grade-badge-sm" style="<?= $gSt ?>" title="<?= h($gDesc) ?>"><?= h($g) ?></span>
                        </td>
                        <td style="text-align:right;">
                            <?= $this->Html->link('<i class="fas fa-pen" style="font-size:11px;"></i>',
                                ['controller' => 'TraineeTrainingTestScores', 'action' => 'edit', $s['id']],
                                ['style' => 'color:#00897B;padding:3px 8px;border:1.5px solid #00897B;border-radius:5px;text-decoration:none;', 'escape' => false, 'title' => __('Edit score')]) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

            </div>
        </div>

    </div><!-- /.right -->

</div><!-- /.cert-view-grid -->

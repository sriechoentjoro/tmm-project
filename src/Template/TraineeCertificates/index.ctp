<?php
/**
 * @var \App\View\AppView $this
 * @var array $rows   certificate rows with joined trainee/batch/score data
 */

$gradeStyle = [
    'A' => 'background:#e8f5e9;color:#2E7D32;border-color:#43A047;',
    'B' => 'background:#e3f2fd;color:#1565C0;border-color:#1E88E5;',
    'C' => 'background:#fffde7;color:#F57F17;border-color:#F9A825;',
    'D' => 'background:#fff3e0;color:#E65100;border-color:#FB8C00;',
    'E' => 'background:#fce4ec;color:#B71C1C;border-color:#E53935;',
];
function avgGradeCert($avg) {
    if ($avg === null) return null;
    if ($avg >= 81) return 'A';
    if ($avg >= 61) return 'B';
    if ($avg >= 41) return 'C';
    if ($avg >= 21) return 'D';
    return 'E';
}
$total = count($rows);
?>
<style>
.cert-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px; margin-bottom: 20px;
}
.cert-header h2 {
    margin: 0; font-size: 20px; font-weight: 700; color: #1a202c;
    display: flex; align-items: center; gap: 10px;
}
.cert-header h2 i { color: #6949BC; }

/* Cross-nav band */
.cross-nav {
    display: flex; gap: 10px; flex-wrap: wrap;
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 12px 18px; margin-bottom: 20px;
    align-items: center;
}
.cross-nav span { font-size: 12px; color: #94a3b8; font-weight: 600; margin-right: 4px; }
.cross-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px; border-radius: 7px;
    font-size: 12px; font-weight: 600;
    text-decoration: none; border: 1.5px solid; transition: all 0.15s;
}
.cross-link:hover { text-decoration: none; }
.cl-score  { color: #0288D1; border-color: #0288D1; background: #fff; }
.cl-score:hover  { background: #0288D1; color: #fff; }
.cl-report { color: #6949BC; border-color: #6949BC; background: #fff; }
.cl-report:hover { background: #6949BC; color: #fff; }
.cl-daily  { color: #00897B; border-color: #00897B; background: #fff; }
.cl-daily:hover  { background: #00897B; color: #fff; }

.btn-add-cert {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #6949BC 0%, #8E24AA 100%);
    color: #fff; border: none; border-radius: 8px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(105,73,188,0.22);
}
.btn-add-cert:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(105,73,188,0.32); color:#fff; text-decoration:none; }

/* Stat cards */
.cert-stats { display:flex; gap:12px; flex-wrap:wrap; margin-bottom:20px; }
.cert-stat {
    background:#fff; border-radius:10px; padding:12px 18px;
    box-shadow:0 2px 8px rgba(0,0,0,0.06); border-left:4px solid;
    display:flex; flex-direction:column; gap:2px; min-width:120px;
}
.cert-stat .sv { font-size:22px; font-weight:800; line-height:1; }
.cert-stat .sl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.7px; color:#94a3b8; }

/* Table */
.cert-table-wrap { overflow-x:auto; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.07); }
.cert-table { width:100%; border-collapse:collapse; min-width:760px; background:#fff; }
.cert-table thead tr { background:linear-gradient(135deg,#6949BC 0%,#8E24AA 100%); }
.cert-table thead th {
    padding:12px 14px; color:#fff;
    font-size:11px; font-weight:700;
    text-transform:uppercase; letter-spacing:0.8px;
    white-space:nowrap; border:none;
}
.cert-table thead th a { color:#fff !important; text-decoration:none; }
.cert-table thead th a:hover { text-decoration:underline; }
.cert-table tbody tr { border-bottom:1px solid #f0f4f8; transition:background 0.15s; }
.cert-table tbody tr:hover { background:#f5f3ff; }
.cert-table tbody td { padding:12px 14px; font-size:13px; color:#374151; vertical-align:middle; }

.trainee-cell { display:flex; align-items:center; gap:10px; }
.trainee-avatar {
    width:34px; height:34px; border-radius:50%;
    background:linear-gradient(135deg,#6949BC,#8E24AA);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:13px; font-weight:700; flex-shrink:0;
}
.trainee-name { font-weight:600; color:#1a202c; }
.trainee-code { font-size:11px; color:#94a3b8; }

/* Certificate number */
.cert-no {
    font-family: monospace; font-size:12px;
    background:#f1f5f9; color:#475569;
    padding:3px 8px; border-radius:5px;
    white-space:nowrap;
}

/* Score bar */
.score-bar-mini { display:flex; align-items:center; gap:8px; }
.score-num { font-weight:700; font-size:13px; color:#1a202c; min-width:34px; }
.score-track { flex:1; height:5px; border-radius:3px; background:#e2e8f0; min-width:60px; overflow:hidden; }
.score-fill { height:100%; border-radius:3px; background:linear-gradient(90deg,#ef4444 0%,#f59e0b 50%,#22c55e 100%); }

.grade-badge {
    display:inline-flex; align-items:center; justify-content:center;
    width:26px; height:26px; border-radius:6px;
    font-size:12px; font-weight:800; border:1.5px solid;
}
.no-score { font-size:11px; color:#cbd5e0; font-style:italic; }

/* Batch pill */
.batch-pill {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:12px;
    font-size:11px; font-weight:600;
    background:#ede9fe; color:#6949BC;
    border:1px solid #c4b5fd;
}

/* Action buttons */
.act-btn {
    display:inline-flex; align-items:center; gap:4px;
    padding:4px 10px; border-radius:6px;
    font-size:12px; font-weight:600;
    text-decoration:none; border:1.5px solid; transition:all 0.15s;
    margin-right:4px; white-space:nowrap;
}
.act-view  { color:#6949BC; border-color:#6949BC; background:#fff; }
.act-view:hover  { background:#6949BC; color:#fff; text-decoration:none; }
.act-print { color:#7C3AED; border-color:#7C3AED; background:#fff; }
.act-print:hover { background:#7C3AED; color:#fff; text-decoration:none; }
.act-edit  { color:#00897B; border-color:#00897B; background:#fff; }
.act-edit:hover  { background:#00897B; color:#fff; text-decoration:none; }
.act-scores { color:#0288D1; border-color:#0288D1; background:#fff; }
.act-scores:hover { background:#0288D1; color:#fff; text-decoration:none; }

.cert-table .filter-row td { padding:5px 8px; background:#f8fafc; }
.cert-table .filter-row input {
    width:100%; padding:5px 8px; font-size:12px;
    border:1px solid #e2e8f0; border-radius:6px; background:#fff;
}
.cert-table .filter-row input:focus { border-color:#6949BC; outline:none; }
</style>

<!-- Header -->
<div class="cert-header">
    <h2><i class="fas fa-certificate"></i> <?= __('Trainee Certificates') ?></h2>
    <?= $this->Html->link(
        '<i class="fas fa-plus"></i> ' . __('Issue Certificate'),
        ['action' => 'add'],
        ['class' => 'btn-add-cert', 'escape' => false]
    ) ?>
</div>

<!-- Cross-navigation to scoring -->
<div class="cross-nav">
    <span><i class="fas fa-link" style="margin-right:4px;"></i><?= __('Scoring') ?>:</span>
    <?= $this->Html->link(
        '<i class="fas fa-calendar-day"></i> ' . __('Daily Entry'),
        ['controller' => 'TraineeTrainingTestScores', 'action' => 'daily'],
        ['class' => 'cross-link cl-daily', 'escape' => false]
    ) ?>
    <?= $this->Html->link(
        '<i class="fas fa-table"></i> ' . __('Score Summary'),
        ['controller' => 'TraineeTrainingTestScores', 'action' => 'index'],
        ['class' => 'cross-link cl-score', 'escape' => false]
    ) ?>
    <?= $this->Html->link(
        '<i class="fas fa-check-double"></i> ' . __('Pass/Fail Report'),
        ['controller' => 'TraineeTrainingTestScores', 'action' => 'report'],
        ['class' => 'cross-link cl-report', 'escape' => false]
    ) ?>
</div>

<!-- Stat cards -->
<div class="cert-stats">
    <div class="cert-stat" style="border-color:#6949BC;">
        <div class="sv" style="color:#6949BC;"><?= $total ?></div>
        <div class="sl"><?= __('Certificates') ?></div>
    </div>
    <?php
    $withScores = array_filter($rows, fn($r) => $r['test_count'] > 0);
    $noScores   = array_filter($rows, fn($r) => !$r['test_count']);
    ?>
    <div class="cert-stat" style="border-color:#0288D1;">
        <div class="sv" style="color:#0288D1;"><?= count($withScores) ?></div>
        <div class="sl"><?= __('With Test Scores') ?></div>
    </div>
    <?php if (count($noScores) > 0): ?>
    <div class="cert-stat" style="border-color:#f59e0b;">
        <div class="sv" style="color:#f59e0b;"><?= count($noScores) ?></div>
        <div class="sl"><?= __('No Scores Yet') ?></div>
    </div>
    <?php endif; ?>
</div>

<!-- Table -->
<div class="cert-table-wrap">
    <table class="cert-table" id="certTable">
        <thead>
            <tr>
                <th style="width:110px;"><?= __('Actions') ?></th>
                <th><?= __('Trainee') ?></th>
                <th><?= __('Batch') ?></th>
                <th><?= __('Certificate No.') ?></th>
                <th><?= __('Issue Date') ?></th>
                <th><?= __('Avg Score') ?></th>
                <th><?= __('Grade') ?></th>
            </tr>
            <tr class="filter-row">
                <td></td>
                <td><input type="text" id="fTrainee" placeholder="<?= __('Search trainee…') ?>"></td>
                <td><input type="text" id="fBatch"   placeholder="<?= __('Batch…') ?>"></td>
                <td><input type="text" id="fCertNo"  placeholder="<?= __('Cert no…') ?>"></td>
                <td></td>
                <td></td>
                <td><input type="text" id="fGrade"   placeholder="<?= __('A/B/C…') ?>"></td>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="7" style="text-align:center;padding:48px 20px;color:#94a3b8;">
            <i class="fas fa-inbox" style="font-size:40px;margin-bottom:12px;display:block;"></i>
            <?= __('No certificates issued yet.') ?>
        </td></tr>
        <?php else: ?>
        <?php foreach ($rows as $row):
            $name    = $row['trainee_name'] ?? '—';
            $code    = $row['trainee_code'] ?? '';
            $init    = mb_strtoupper(mb_substr($name, 0, 1));
            $certNo  = $row['certificate_no'] ?? '—';
            $batch   = $row['batch_code']   ?? null;
            $issueD  = $row['issue_date'] ? date('d M Y', strtotime($row['issue_date'])) : '—';
            $avg     = $row['avg_score'] !== null ? (float)$row['avg_score'] : null;
            $tests   = (int)$row['test_count'];
            $gl      = $avg !== null ? avgGradeCert($avg) : null;
            $gs      = $gl && isset($gradeStyle[$gl]) ? $gradeStyle[$gl] : '';
        ?>
        <tr>
            <td style="white-space:nowrap;">
                <?= $this->Html->link('<i class="fas fa-eye"></i>',
                    ['action' => 'view', $row['id']],
                    ['class' => 'act-btn act-view', 'escape' => false, 'title' => __('View certificate')]) ?>
                <?= $this->Html->link('<i class="fas fa-print"></i>',
                    ['action' => 'printCertificate', $row['id']],
                    ['class' => 'act-btn act-print', 'escape' => false, 'title' => __('Print certificate'), 'target' => '_blank']) ?>
                <?= $this->Html->link('<i class="fas fa-pen"></i>',
                    ['action' => 'edit', $row['id']],
                    ['class' => 'act-btn act-edit', 'escape' => false, 'title' => __('Edit certificate')]) ?>
                <?= $this->Html->link('<i class="fas fa-chart-bar"></i>',
                    ['controller' => 'TraineeTrainingTestScores', 'action' => 'index',
                     '?' => ['trainee' => urlencode($name)]],
                    ['class' => 'act-btn act-scores', 'escape' => false, 'title' => __('View test scores')]) ?>
            </td>
            <td>
                <div class="trainee-cell">
                    <div class="trainee-avatar"><?= h($init) ?></div>
                    <div>
                        <div class="trainee-name"><?= h($name) ?></div>
                        <?php if ($code): ?><div class="trainee-code"><?= h($code) ?></div><?php endif; ?>
                    </div>
                </div>
            </td>
            <td>
                <?php if ($batch): ?>
                <span class="batch-pill"><i class="fas fa-layer-group" style="font-size:9px;"></i> <?= h($batch) ?></span>
                <?php else: ?>
                <span class="no-score">—</span>
                <?php endif; ?>
            </td>
            <td><span class="cert-no"><?= h($certNo) ?></span></td>
            <td style="white-space:nowrap;color:#4a5568;">
                <i class="fas fa-calendar-alt" style="color:#6949BC;margin-right:5px;font-size:11px;"></i>
                <?= h($issueD) ?>
            </td>
            <td>
                <?php if ($avg !== null): ?>
                <div class="score-bar-mini">
                    <span class="score-num"><?= number_format($avg, 1) ?></span>
                    <div class="score-track">
                        <div class="score-fill" style="width:<?= min($avg, 100) ?>%;"></div>
                    </div>
                    <span style="font-size:10px;color:#94a3b8;">(<?= $tests ?>)</span>
                </div>
                <?php else: ?>
                <span class="no-score"><?= __('No scores') ?></span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($gl): ?>
                <span class="grade-badge" style="<?= $gs ?>"><?= h($gl) ?></span>
                <?php else: ?>
                <span class="no-score">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="paginator" style="margin-top:14px;">
    <p style="font-size:12px;color:#94a3b8;"><?= __('Showing {0} certificate(s)', $total) ?></p>
</div>

<?php $this->append('script'); ?>
<script>
(function () {
    function filter() {
        var fT = $('#fTrainee').val().toLowerCase();
        var fB = $('#fBatch').val().toLowerCase();
        var fC = $('#fCertNo').val().toLowerCase();
        var fG = $('#fGrade').val().toLowerCase();
        $('#certTable tbody tr').each(function () {
            var $td = $(this).find('td');
            var show = (!fT || $td.eq(1).text().toLowerCase().indexOf(fT) >= 0) &&
                       (!fB || $td.eq(2).text().toLowerCase().indexOf(fB) >= 0) &&
                       (!fC || $td.eq(3).text().toLowerCase().indexOf(fC) >= 0) &&
                       (!fG || $td.eq(6).text().toLowerCase().indexOf(fG) >= 0);
            $(this).toggle(show);
        });
    }
    $('#fTrainee,#fBatch,#fCertNo,#fGrade').on('input', filter);
})();
</script>
<?php $this->end(); ?>

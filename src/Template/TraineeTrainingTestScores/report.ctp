<?php
/**
 * @var \App\View\AppView $this
 * @var array $rows  trainee summary rows
 */

// Grade from average score
function avgGrade($avg) {
    if ($avg >= 81) return ['A', 'Sangat Baik',  'background:#e8f5e9;color:#2E7D32;border-color:#43A047;'];
    if ($avg >= 61) return ['B', 'Baik',          'background:#e3f2fd;color:#1565C0;border-color:#1E88E5;'];
    if ($avg >= 41) return ['C', 'Cukup',         'background:#fffde7;color:#F57F17;border-color:#F9A825;'];
    if ($avg >= 21) return ['D', 'Kurang',        'background:#fff3e0;color:#E65100;border-color:#FB8C00;'];
    return               ['E', 'Sangat Kurang',  'background:#fce4ec;color:#B71C1C;border-color:#E53935;'];
}

$total     = count($rows);
$allPassed = 0; $allFailed = 0;
foreach ($rows as $r) { $allPassed += (int)$r['passed']; $allFailed += (int)$r['failed']; }
$allTests  = $allPassed + $allFailed;
?>
<style>
/* ── Page header ─────────────────────────────────────────────── */
.report-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 10px; margin-bottom: 20px;
}
.report-header h2 {
    margin: 0; font-size: 20px; font-weight: 700; color: #1a202c;
    display: flex; align-items: center; gap: 10px;
}
.report-header h2 i { color: #0288D1; }

/* ── Summary stat cards ──────────────────────────────────────── */
.stat-row {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px; margin-bottom: 24px;
}
.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px 18px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    border-left: 4px solid;
    display: flex; flex-direction: column; gap: 4px;
}
.stat-card .stat-value { font-size: 26px; font-weight: 800; line-height: 1; }
.stat-card .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; }

/* ── Table ───────────────────────────────────────────────────── */
.report-table-wrap {
    overflow-x: auto; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.report-table {
    width: 100%; border-collapse: collapse; min-width: 700px; background: #fff;
}
.report-table thead tr {
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
}
.report-table thead th {
    padding: 12px 14px; color: #fff;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    white-space: nowrap; border: none;
}
.report-table tbody tr { border-bottom: 1px solid #f0f4f8; transition: background 0.15s; }
.report-table tbody tr:hover { background: #f0f9ff; }
.report-table tbody td { padding: 12px 14px; font-size: 13px; color: #374151; vertical-align: middle; }

/* Rank */
.rank-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px; border-radius: 50%;
    font-size: 12px; font-weight: 700; color: #fff;
}

/* Trainee cell */
.trainee-cell { display: flex; align-items: center; gap: 10px; }
.trainee-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #0288D1, #0097A7);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.trainee-name { font-weight: 600; color: #1a202c; }
.trainee-code { font-size: 11px; color: #94a3b8; }

/* Score bar */
.score-bar-mini { display: flex; align-items: center; gap: 8px; }
.score-num { font-weight: 700; font-size: 14px; color: #1a202c; min-width: 36px; }
.score-track {
    flex: 1; height: 7px; border-radius: 4px; background: #e2e8f0; min-width: 80px; overflow: hidden;
}
.score-fill {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, #ef4444 0%, #f59e0b 50%, #22c55e 100%);
}

/* Grade badge */
.grade-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 6px;
    font-size: 13px; font-weight: 800; border: 1.5px solid;
}

/* Pass/fail pill */
.pf-wrap { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.pill {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 12px;
    font-size: 11px; font-weight: 700;
}
.pill-pass { background: #e8f5e9; color: #2E7D32; }
.pill-fail { background: #fce4ec; color: #B71C1C; }

/* Min/max range */
.range-cell { display: flex; align-items: center; gap: 4px; font-size: 12px; color: #4a5568; }
.range-min { color: #E53935; font-weight: 600; }
.range-max { color: #2E7D32; font-weight: 600; }

/* Filter row */
.report-table .filter-row td { padding: 6px 8px; background: #f8fafc; }
.report-table .filter-row input {
    width: 100%; padding: 5px 8px; font-size: 12px;
    border: 1px solid #e2e8f0; border-radius: 6px;
    background: #fff; color: #374151;
}
.report-table .filter-row input:focus { border-color: #0288D1; outline: none; }

/* Empty */
.report-empty { text-align: center; padding: 48px 20px; color: #94a3b8; }
.report-empty i { font-size: 40px; margin-bottom: 12px; display: block; }
</style>

<!-- Cross-navigation -->
<div style="display:flex;gap:8px;flex-wrap:wrap;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:10px 16px;margin-bottom:16px;align-items:center;">
    <span style="font-size:12px;color:#94a3b8;font-weight:600;"><i class="fas fa-link" style="margin-right:4px;"></i><?= __('Related') ?>:</span>
    <?= $this->Html->link('<i class="fas fa-certificate"></i> ' . __('Certificates'), ['controller' => 'TraineeCertificates', 'action' => 'index'], ['style' => 'color:#6949BC;border-color:#6949BC;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-calendar-day"></i> ' . __('Daily Entry'), ['action' => 'daily'], ['style' => 'color:#00897B;border-color:#00897B;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-table"></i> ' . __('Score Summary'), ['action' => 'index'], ['style' => 'color:#0288D1;border-color:#0288D1;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
</div>

<!-- Page header -->
<div class="report-header">
    <h2><i class="fas fa-check-double"></i> <?= __('Pass / Fail Report') ?></h2>
    <span style="font-size:12px;color:#94a3b8;">
        <?= __('Average score per trainee · pass threshold: 60') ?>
    </span>
</div>

<!-- Summary stat cards -->
<div class="stat-row">
    <div class="stat-card" style="border-color:#0288D1;">
        <div class="stat-value" style="color:#0288D1;"><?= $total ?></div>
        <div class="stat-label"><?= __('Trainees') ?></div>
    </div>
    <div class="stat-card" style="border-color:#64748b;">
        <div class="stat-value" style="color:#64748b;"><?= $allTests ?></div>
        <div class="stat-label"><?= __('Total Tests') ?></div>
    </div>
    <div class="stat-card" style="border-color:#22c55e;">
        <div class="stat-value" style="color:#22c55e;"><?= $allPassed ?></div>
        <div class="stat-label"><?= __('Tests Passed') ?></div>
    </div>
    <div class="stat-card" style="border-color:#ef4444;">
        <div class="stat-value" style="color:#ef4444;"><?= $allFailed ?></div>
        <div class="stat-label"><?= __('Tests Failed') ?></div>
    </div>
    <?php if ($allTests > 0): ?>
    <div class="stat-card" style="border-color:#f59e0b;">
        <div class="stat-value" style="color:#f59e0b;"><?= round($allPassed / $allTests * 100) ?>%</div>
        <div class="stat-label"><?= __('Pass Rate') ?></div>
    </div>
    <?php endif; ?>
</div>

<!-- Report table -->
<div class="report-table-wrap">
    <table class="report-table" id="reportTable">
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th><?= __('Trainee') ?></th>
                <th><?= __('Tests') ?></th>
                <th><?= __('Average Score') ?></th>
                <th><?= __('Grade') ?></th>
                <th><?= __('Pass / Fail') ?></th>
                <th><?= __('Range') ?></th>
                <th title="<?= __('Certificate') ?>"><i class="fas fa-certificate"></i></th>
            </tr>
            <tr class="filter-row">
                <td></td>
                <td><input type="text" id="fTrainee"  placeholder="<?= __('Search trainee…') ?>"></td>
                <td></td>
                <td></td>
                <td><input type="text" id="fGrade"    placeholder="<?= __('A/B/C…') ?>"></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
        <tr><td colspan="8">
            <div class="report-empty">
                <i class="fas fa-inbox"></i>
                <p><?= __('No scores recorded yet.') ?></p>
            </div>
        </td></tr>
        <?php else: ?>
        <?php foreach ($rows as $i => $row):
            $rank      = $i + 1;
            $name      = $row['trainee_name'] ?? '—';
            $code      = $row['trainee_code'] ?? '';
            $initials  = mb_strtoupper(mb_substr($name, 0, 1));
            $avg       = (float)$row['avg_score'];
            $tests     = (int)$row['tests'];
            $passed    = (int)$row['passed'];
            $failed    = (int)$row['failed'];
            $min       = (int)$row['min_score'];
            $max       = (int)$row['max_score'];
            [$gl, $gd, $gs] = avgGrade($avg);

            // Rank badge colour
            $rankBg  = $rank === 1 ? '#F59E0B' : ($rank === 2 ? '#94A3B8' : ($rank === 3 ? '#CD7C3A' : '#e2e8f0'));
            $rankFg  = $rank <= 3 ? '#fff' : '#64748b';
            $hasCert = in_array((int)($row['trainee_id'] ?? 0), array_map('intval', $certifiedIds ?? []));
        ?>
        <tr>
            <td style="text-align:center;">
                <span class="rank-badge" style="background:<?= $rankBg ?>;color:<?= $rankFg ?>;"><?= $rank ?></span>
            </td>
            <td>
                <div class="trainee-cell">
                    <div class="trainee-avatar"><?= h($initials) ?></div>
                    <div>
                        <div class="trainee-name"><?= h($name) ?></div>
                        <?php if ($code): ?><div class="trainee-code"><?= h($code) ?></div><?php endif; ?>
                    </div>
                </div>
            </td>
            <td style="font-weight:600;color:#4a5568;"><?= $tests ?></td>
            <td>
                <div class="score-bar-mini">
                    <span class="score-num"><?= number_format($avg, 1) ?></span>
                    <div class="score-track">
                        <div class="score-fill" style="width:<?= min($avg, 100) ?>%;"></div>
                    </div>
                </div>
            </td>
            <td>
                <span class="grade-badge" style="<?= $gs ?>" title="<?= h($gd) ?>"><?= h($gl) ?></span>
                <span style="font-size:11px;color:#94a3b8;margin-left:5px;"><?= h($gd) ?></span>
            </td>
            <td>
                <div class="pf-wrap">
                    <?php if ($passed > 0): ?>
                    <span class="pill pill-pass"><i class="fas fa-check" style="font-size:9px;"></i> <?= $passed ?> <?= __('pass') ?></span>
                    <?php endif; ?>
                    <?php if ($failed > 0): ?>
                    <span class="pill pill-fail"><i class="fas fa-times" style="font-size:9px;"></i> <?= $failed ?> <?= __('fail') ?></span>
                    <?php endif; ?>
                </div>
            </td>
            <td>
                <div class="range-cell">
                    <span class="range-min"><?= $min ?></span>
                    <span style="color:#cbd5e0;">—</span>
                    <span class="range-max"><?= $max ?></span>
                </div>
            </td>
            <td style="text-align:center;">
                <?php if ($hasCert): ?>
                <?= $this->Html->link('<i class="fas fa-certificate" style="color:#6949BC;font-size:15px;"></i>',
                    ['controller' => 'TraineeCertificates', 'action' => 'index'],
                    ['escape' => false, 'title' => __('Certificate issued')]) ?>
                <?php else: ?>
                <i class="fas fa-certificate" style="color:#e2e8f0;font-size:15px;" title="<?= __('No certificate yet') ?>"></i>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php $this->append('script'); ?>
<script>
(function () {
    function filter() {
        var fName  = $('#fTrainee').val().toLowerCase();
        var fGrade = $('#fGrade').val().toLowerCase();
        $('#reportTable tbody tr').each(function () {
            var $tr = $(this);
            var name  = $tr.find('td').eq(1).text().toLowerCase();
            var grade = $tr.find('td').eq(4).text().toLowerCase();
            var show  = (!fName  || name.indexOf(fName)   >= 0) &&
                        (!fGrade || grade.indexOf(fGrade)  >= 0);
            $tr.toggle(show);
        });
    }
    $('#fTrainee, #fGrade').on('input', filter);
})();
</script>
<?php $this->end(); ?>

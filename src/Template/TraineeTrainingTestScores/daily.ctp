<?php
/**
 * @var \App\View\AppView $this
 * @var array  $scores
 * @var string $latestDate
 * @var string $selectedDate
 * @var array  $allDates
 */

$gradeStyle = [
    'A' => 'background:#e8f5e9;color:#2E7D32;border-color:#43A047;',
    'B' => 'background:#e3f2fd;color:#1565C0;border-color:#1E88E5;',
    'C' => 'background:#fffde7;color:#F57F17;border-color:#F9A825;',
    'D' => 'background:#fff3e0;color:#E65100;border-color:#FB8C00;',
    'E' => 'background:#fce4ec;color:#B71C1C;border-color:#E53935;',
];

$totalScores = count($scores);
$passed = $failed = $sumScore = 0;
foreach ($scores as $s) {
    $sc = (int)$s['score'];
    $sumScore += $sc;
    if ($sc >= 60) $passed++; else $failed++;
}
$avgScore = $totalScores ? round($sumScore / $totalScores, 1) : null;
?>
<style>
.daily-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 18px;
}
.daily-header h2 {
    margin: 0; font-size: 20px; font-weight: 700; color: #1a202c;
    display: flex; align-items: center; gap: 10px;
}
.daily-header h2 i { color: #0288D1; }

/* Date toolbar */
.date-toolbar {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    background: #fff; border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.07);
    padding: 12px 18px; margin-bottom: 20px;
}
.date-toolbar label {
    font-size: 12px; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 0.8px;
    white-space: nowrap;
}
.date-toolbar select {
    border: 1.5px solid #e2e8f0; border-radius: 7px;
    padding: 7px 12px; font-size: 13px; font-weight: 600;
    color: #1a202c; background: #f8fafc; cursor: pointer;
    transition: border-color 0.2s;
}
.date-toolbar select:focus { border-color: #0288D1; outline: none; }
.date-toolbar .sep { color: #e2e8f0; font-size: 18px; }

.btn-add-score {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
    color: #fff; border: none; border-radius: 8px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(2,136,209,0.22);
    white-space: nowrap;
}
.btn-add-score:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(2,136,209,0.32); color:#fff; text-decoration:none; }

/* Stat mini cards */
.daily-stats {
    display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px;
}
.daily-stat {
    background: #fff; border-radius: 10px;
    padding: 12px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-left: 4px solid;
    display: flex; flex-direction: column; gap: 2px;
    min-width: 120px;
}
.daily-stat .sv { font-size: 22px; font-weight: 800; line-height: 1; }
.daily-stat .sl { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.7px; color: #94a3b8; }

/* Table */
.daily-table-wrap {
    overflow-x: auto; border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
}
.daily-table {
    width: 100%; border-collapse: collapse; min-width: 620px; background: #fff;
}
.daily-table thead tr {
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
}
.daily-table thead th {
    padding: 12px 14px; color: #fff;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    white-space: nowrap; border: none;
}
.daily-table tbody tr { border-bottom: 1px solid #f0f4f8; transition: background 0.15s; }
.daily-table tbody tr:hover { background: #f0f9ff; }
.daily-table tbody td { padding: 11px 14px; font-size: 13px; color: #374151; vertical-align: middle; }

.trainee-cell { display: flex; align-items: center; gap: 10px; }
.trainee-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #0288D1, #0097A7);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 12px; font-weight: 700; flex-shrink: 0;
}
.trainee-name { font-weight: 600; color: #1a202c; font-size: 13px; }
.trainee-code { font-size: 11px; color: #94a3b8; }

.score-bar-mini { display: flex; align-items: center; gap: 8px; }
.score-num { font-weight: 700; font-size: 14px; color: #1a202c; min-width: 28px; }
.score-track { flex:1; height:6px; border-radius:3px; background:#e2e8f0; min-width:70px; overflow:hidden; }
.score-fill { height:100%; border-radius:3px; background:linear-gradient(90deg,#ef4444 0%,#f59e0b 50%,#22c55e 100%); }

.grade-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 27px; height: 27px; border-radius: 6px;
    font-size: 13px; font-weight: 800; border: 1.5px solid;
}

.act-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600;
    text-decoration: none; border: 1.5px solid; transition: all 0.15s;
    margin-right: 4px;
}
.act-edit { color: #00897B; border-color: #00897B; background: #fff; }
.act-edit:hover { background: #00897B; color: #fff; text-decoration: none; }

.daily-table .filter-row td { padding: 5px 8px; background: #f8fafc; }
.daily-table .filter-row input {
    width: 100%; padding: 5px 8px; font-size: 12px;
    border: 1px solid #e2e8f0; border-radius: 6px; background: #fff; color: #374151;
}
.daily-table .filter-row input:focus { border-color: #0288D1; outline: none; }

.empty-daily { text-align:center; padding:48px 20px; color:#94a3b8; }
.empty-daily i { font-size:40px; margin-bottom:12px; display:block; }
</style>

<!-- Page header -->
<div class="daily-header">
    <h2><i class="fas fa-calendar-day"></i> <?= __('Daily Score Entry') ?></h2>
    <?= $this->Html->link(
        '<i class="fas fa-plus"></i> ' . __('Enter New Score'),
        ['action' => 'add'],
        ['class' => 'btn-add-score', 'escape' => false]
    ) ?>
</div>

<!-- Cross-navigation -->
<div style="display:flex;gap:8px;flex-wrap:wrap;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:10px 16px;margin-bottom:16px;align-items:center;">
    <span style="font-size:12px;color:#94a3b8;font-weight:600;"><i class="fas fa-link" style="margin-right:4px;"></i><?= __('Related') ?>:</span>
    <?= $this->Html->link('<i class="fas fa-certificate"></i> ' . __('Certificates'), ['controller' => 'TraineeCertificates', 'action' => 'index'], ['style' => 'color:#6949BC;border-color:#6949BC;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-table"></i> ' . __('Score Summary'), ['action' => 'index'], ['style' => 'color:#0288D1;border-color:#0288D1;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-check-double"></i> ' . __('Pass/Fail Report'), ['action' => 'report'], ['style' => 'color:#6949BC;border-color:#6949BC;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
</div>

<!-- Date selector toolbar -->
<div class="date-toolbar">
    <label><i class="fas fa-calendar-alt" style="margin-right:5px;"></i><?= __('Test Date') ?></label>
    <select id="datePicker" onchange="location.href='?date='+this.value">
        <?php foreach ($allDates as $d):
            $dVal = is_string($d['test_date']) ? $d['test_date'] : $d['test_date']->format('Y-m-d');
            $sel  = ($dVal === $selectedDate) ? ' selected' : '';
        ?>
        <option value="<?= h($dVal) ?>"<?= $sel ?>><?= h($dVal) ?></option>
        <?php endforeach; ?>
        <?php if (empty($allDates)): ?>
        <option><?= __('No dates yet') ?></option>
        <?php endif; ?>
    </select>

    <?php if ($selectedDate && $selectedDate === $latestDate): ?>
    <span style="font-size:12px;background:#e0f7fa;color:#00838F;padding:3px 10px;border-radius:12px;font-weight:600;">
        <i class="fas fa-circle" style="font-size:8px;margin-right:4px;"></i><?= __('Most Recent') ?>
    </span>
    <?php endif; ?>
</div>

<!-- Mini stat cards -->
<?php if ($totalScores > 0): ?>
<div class="daily-stats">
    <div class="daily-stat" style="border-color:#0288D1;">
        <div class="sv" style="color:#0288D1;"><?= $totalScores ?></div>
        <div class="sl"><?= __('Scores') ?></div>
    </div>
    <div class="daily-stat" style="border-color:#f59e0b;">
        <div class="sv" style="color:#f59e0b;"><?= $avgScore ?></div>
        <div class="sl"><?= __('Average') ?></div>
    </div>
    <div class="daily-stat" style="border-color:#22c55e;">
        <div class="sv" style="color:#22c55e;"><?= $passed ?></div>
        <div class="sl"><?= __('Passed (≥60)') ?></div>
    </div>
    <div class="daily-stat" style="border-color:#ef4444;">
        <div class="sv" style="color:#ef4444;"><?= $failed ?></div>
        <div class="sl"><?= __('Failed (<60)') ?></div>
    </div>
</div>
<?php endif; ?>

<!-- Table -->
<div class="daily-table-wrap">
    <table class="daily-table" id="dailyTable">
        <thead>
            <tr>
                <th><?= __('Actions') ?></th>
                <th><?= __('Trainee') ?></th>
                <th><?= __('Competency') ?></th>
                <th><?= __('Score') ?></th>
                <th><?= __('Grade') ?></th>
                <th title="<?= __('Certificate') ?>"><i class="fas fa-certificate"></i></th>
            </tr>
            <tr class="filter-row">
                <td></td>
                <td><input type="text" id="fTrainee"    placeholder="<?= __('Search trainee…') ?>"></td>
                <td><input type="text" id="fCompetency" placeholder="<?= __('Search competency…') ?>"></td>
                <td></td>
                <td><input type="text" id="fGrade"      placeholder="<?= __('A/B/C…') ?>"></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($scores)): ?>
        <tr><td colspan="6">
            <div class="empty-daily">
                <i class="fas fa-inbox"></i>
                <p><?= $selectedDate
                    ? __('No scores recorded for {0}.', h($selectedDate))
                    : __('No scores recorded yet.') ?></p>
            </div>
        </td></tr>
        <?php else: ?>
        <?php foreach ($scores as $s):
            $name   = $s['trainee_name'] ?? '—';
            $code   = $s['tmm_code']     ?? '';
            $init   = mb_strtoupper(mb_substr($name, 0, 1));
            $comp   = $s['competency']   ?? '—';
            $score  = (int)$s['score'];
            $grade  = $s['grade']        ?? '?';
            $gdesc  = $s['grade_desc']   ?? '';
            $gs      = $gradeStyle[$grade] ?? 'background:#f1f5f9;color:#64748b;border-color:#cbd5e0;';
            $hasCert = in_array((int)($s['trainee_id'] ?? 0), array_map('intval', $certifiedIds ?? []));
        ?>
        <tr>
            <td>
                <?= $this->Html->link(
                    '<i class="fas fa-pen"></i>',
                    ['action' => 'edit', $s['id']],
                    ['class' => 'act-btn act-edit', 'escape' => false, 'title' => __('Edit')]
                ) ?>
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
            <td style="max-width:200px;"><?= h($comp) ?></td>
            <td>
                <div class="score-bar-mini">
                    <span class="score-num"><?= $score ?></span>
                    <div class="score-track">
                        <div class="score-fill" style="width:<?= $score ?>%;"></div>
                    </div>
                </div>
            </td>
            <td>
                <span class="grade-badge" style="<?= $gs ?>" title="<?= h($gdesc) ?>"><?= h($grade) ?></span>
                <span style="font-size:11px;color:#94a3b8;margin-left:5px;"><?= h($gdesc) ?></span>
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
        var fT = $('#fTrainee').val().toLowerCase();
        var fC = $('#fCompetency').val().toLowerCase();
        var fG = $('#fGrade').val().toLowerCase();
        $('#dailyTable tbody tr').each(function () {
            var $td = $(this).find('td');
            var show = (!fT || $td.eq(1).text().toLowerCase().indexOf(fT) >= 0) &&
                       (!fC || $td.eq(2).text().toLowerCase().indexOf(fC) >= 0) &&
                       (!fG || $td.eq(4).text().toLowerCase().indexOf(fG) >= 0);
            $(this).toggle(show);
        });
    }
    $('#fTrainee, #fCompetency, #fGrade').on('input', filter);
})();
</script>
<?php $this->end(); ?>

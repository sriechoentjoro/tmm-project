<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeTrainingTestScore[]|\Cake\Collection\CollectionInterface $traineeTrainingTestScores
 */

// Grade colour map
$gradeStyle = [
    'A' => 'background:#e8f5e9;color:#2E7D32;border-color:#43A047;',
    'B' => 'background:#e3f2fd;color:#1565C0;border-color:#1E88E5;',
    'C' => 'background:#fffde7;color:#F57F17;border-color:#F9A825;',
    'D' => 'background:#fff3e0;color:#E65100;border-color:#FB8C00;',
    'E' => 'background:#fce4ec;color:#B71C1C;border-color:#E53935;',
];
?>
<style>
.score-index-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; flex-wrap: wrap; gap: 10px;
}
.score-index-header h2 {
    margin: 0; font-size: 20px; font-weight: 700; color: #1a202c;
    display: flex; align-items: center; gap: 10px;
}
.score-index-header h2 i { color: #0288D1; font-size: 18px; }
.btn-add-score {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 8px 18px;
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
    color: #fff; border: none; border-radius: 8px;
    font-size: 13px; font-weight: 600;
    text-decoration: none; transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(2,136,209,0.22);
}
.btn-add-score:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(2,136,209,0.32); color:#fff; text-decoration:none; }

/* Table */
.score-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
.score-table {
    width: 100%; border-collapse: collapse; min-width: 720px;
    background: #fff;
}
.score-table thead tr {
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
}
.score-table thead th {
    padding: 12px 14px; color: #fff;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    white-space: nowrap; border: none;
}
.score-table thead th a { color: #fff !important; text-decoration: none; }
.score-table thead th a:hover { text-decoration: underline; }

.score-table tbody tr { border-bottom: 1px solid #f0f4f8; transition: background 0.15s; }
.score-table tbody tr:hover { background: #f0f9ff; }
.score-table tbody td { padding: 11px 14px; font-size: 13px; color: #374151; vertical-align: middle; }

/* Trainee cell */
.trainee-cell { display: flex; align-items: center; gap: 10px; }
.trainee-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    background: linear-gradient(135deg, #0288D1, #0097A7);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 13px; font-weight: 700; flex-shrink: 0;
}
.trainee-name { font-weight: 600; color: #1a202c; font-size: 13px; line-height: 1.3; }
.trainee-code { font-size: 11px; color: #94a3b8; }

/* Score bar */
.score-bar-mini { display: flex; align-items: center; gap: 8px; }
.score-num { font-weight: 700; font-size: 14px; color: #1a202c; min-width: 28px; }
.score-track {
    flex: 1; height: 5px; border-radius: 3px; background: #e2e8f0; min-width: 60px; overflow: hidden;
}
.score-fill {
    height: 100%; border-radius: 3px;
    background: linear-gradient(90deg, #ef4444 0%, #f59e0b 50%, #22c55e 100%);
}

/* Grade badge */
.grade-badge {
    display: inline-flex; align-items: center; justify-content: center;
    width: 28px; height: 28px; border-radius: 6px;
    font-size: 13px; font-weight: 800;
    border: 1.5px solid;
}

/* Competency */
.comp-title { font-size: 13px; color: #374151; max-width: 200px; }

/* Date */
.date-cell { white-space: nowrap; color: #4a5568; font-size: 13px; }

/* Action buttons */
.act-btn {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 6px;
    font-size: 12px; font-weight: 600;
    text-decoration: none; border: 1.5px solid; transition: all 0.15s;
    margin-right: 4px;
}
.act-view { color: #0288D1; border-color: #0288D1; background: #fff; }
.act-view:hover { background: #0288D1; color: #fff; text-decoration: none; }
.act-edit { color: #00897B; border-color: #00897B; background: #fff; }
.act-edit:hover { background: #00897B; color: #fff; text-decoration: none; }

/* Filter row */
.score-table .filter-row td { padding: 6px 8px; background: #f8fafc; }
.score-table .filter-row input {
    width: 100%; padding: 5px 8px; font-size: 12px;
    border: 1px solid #e2e8f0; border-radius: 6px;
    background: #fff; color: #374151;
}
.score-table .filter-row input:focus { border-color: #0288D1; outline: none; }
</style>

<div class="score-index-header">
    <h2><i class="fas fa-chart-bar"></i> <?= __('Trainee Test Scores') ?></h2>
    <?= $this->Html->link(
        '<i class="fas fa-plus"></i> ' . __('Add New'),
        ['action' => 'add'],
        ['class' => 'btn-add-score', 'escape' => false]
    ) ?>
</div>

<!-- Cross-navigation to certificates & other scoring views -->
<div style="display:flex;gap:8px;flex-wrap:wrap;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:10px 16px;margin-bottom:18px;align-items:center;">
    <span style="font-size:12px;color:#94a3b8;font-weight:600;margin-right:2px;"><i class="fas fa-link" style="margin-right:4px;"></i><?= __('Related') ?>:</span>
    <?= $this->Html->link('<i class="fas fa-certificate"></i> ' . __('Certificates'), ['controller' => 'TraineeCertificates', 'action' => 'index'], ['class' => 'cross-link', 'style' => 'color:#6949BC;border-color:#6949BC;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-calendar-day"></i> ' . __('Daily Entry'), ['action' => 'daily'], ['class' => 'cross-link', 'style' => 'color:#00897B;border-color:#00897B;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
    <?= $this->Html->link('<i class="fas fa-check-double"></i> ' . __('Pass/Fail Report'), ['action' => 'report'], ['class' => 'cross-link', 'style' => 'color:#6949BC;border-color:#6949BC;background:#fff;display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;border:1.5px solid;', 'escape' => false]) ?>
</div>

<div class="score-table-wrap">
    <table class="score-table" id="scoreTable">
        <thead>
            <tr>
                <th><?= __('Actions') ?></th>
                <th><?= $this->Paginator->sort('trainee_id', __('Trainee')) ?></th>
                <th><?= $this->Paginator->sort('master_training_competency_id', __('Competency')) ?></th>
                <th><?= $this->Paginator->sort('test_date', __('Test Date')) ?></th>
                <th><?= $this->Paginator->sort('score', __('Score')) ?></th>
                <th><?= $this->Paginator->sort('master_training_test_score_grade_id', __('Grade')) ?></th>
                <th title="<?= __('Certificate issued') ?>"><i class="fas fa-certificate"></i></th>
            </tr>
            <tr class="filter-row">
                <td></td>
                <td><input type="text" id="fTrainee"      placeholder="<?= __('Search trainee…') ?>"></td>
                <td><input type="text" id="fCompetency"   placeholder="<?= __('Search competency…') ?>"></td>
                <td><input type="text" id="fDate"         placeholder="<?= __('YYYY-MM-DD') ?>"></td>
                <td><input type="text" id="fScore"        placeholder="<?= __('Score') ?>"></td>
                <td><input type="text" id="fGrade"        placeholder="<?= __('A/B/C…') ?>"></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($traineeTrainingTestScores as $row):
            $traineeName = isset($row->trainee->name)    ? $row->trainee->name    : '—';
            $traineeCode = isset($row->trainee->tmm_code) ? $row->trainee->tmm_code : '';
            $initials    = mb_strtoupper(mb_substr($traineeName, 0, 1));
            $competency  = isset($row->master_training_competency->title) ? $row->master_training_competency->title : h($row->master_training_competency_id);
            $grade       = isset($row->master_training_test_score_grade->title) ? $row->master_training_test_score_grade->title : '?';
            $gradeDesc   = isset($row->master_training_test_score_grade->description) ? $row->master_training_test_score_grade->description : '';
            $gStyle      = isset($gradeStyle[$grade]) ? $gradeStyle[$grade] : 'background:#f1f5f9;color:#64748b;border-color:#cbd5e0;';
            $score       = (int) $row->score;
            $testDate    = $row->test_date ? $row->test_date->format('d M Y') : '—';
            $hasCert     = in_array($row->trainee_id, $certifiedIds ?? []);
        ?>
        <tr>
            <td>
                <?= $this->Html->link('<i class="fas fa-eye"></i>', ['action' => 'view', $row->id],
                    ['class' => 'act-btn act-view', 'escape' => false, 'title' => __('View')]) ?>
                <?= $this->Html->link('<i class="fas fa-pen"></i>', ['action' => 'edit', $row->id],
                    ['class' => 'act-btn act-edit', 'escape' => false, 'title' => __('Edit')]) ?>
            </td>
            <td>
                <div class="trainee-cell">
                    <div class="trainee-avatar"><?= h($initials) ?></div>
                    <div>
                        <div class="trainee-name"><?= h($traineeName) ?></div>
                        <?php if ($traineeCode): ?>
                        <div class="trainee-code"><?= h($traineeCode) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td><div class="comp-title"><?= h($competency) ?></div></td>
            <td><div class="date-cell"><i class="fas fa-calendar-alt" style="color:#0288D1;margin-right:5px;font-size:11px;"></i><?= h($testDate) ?></div></td>
            <td>
                <div class="score-bar-mini">
                    <span class="score-num"><?= $score ?></span>
                    <div class="score-track">
                        <div class="score-fill" style="width:<?= $score ?>%;"></div>
                    </div>
                </div>
            </td>
            <td>
                <span class="grade-badge" style="<?= $gStyle ?>" title="<?= h($gradeDesc) ?>">
                    <?= h($grade) ?>
                </span>
                <?php if ($gradeDesc): ?>
                <span style="font-size:11px;color:#94a3b8;margin-left:6px;"><?= h($gradeDesc) ?></span>
                <?php endif; ?>
            </td>
            <td style="text-align:center;">
                <?php if ($hasCert): ?>
                <?= $this->Html->link(
                    '<i class="fas fa-certificate" style="color:#6949BC;font-size:16px;" title="' . __('Certificate issued') . '"></i>',
                    ['controller' => 'TraineeCertificates', 'action' => 'index'],
                    ['escape' => false, 'title' => __('View certificate')]
                ) ?>
                <?php else: ?>
                <i class="fas fa-certificate" style="color:#e2e8f0;font-size:16px;" title="<?= __('No certificate yet') ?>"></i>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="paginator" style="margin-top:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
    <ul class="pagination" style="margin:0;">
        <?= $this->Paginator->first('<< ' . __('first')) ?>
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
        <?= $this->Paginator->last(__('last') . ' >>') ?>
    </ul>
    <p style="margin:0;font-size:12px;color:#94a3b8;">
        <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, {{count}} records total')]) ?>
    </p>
</div>

<?php $this->append('script'); ?>
<script>
// Client-side column filter
(function () {
    var cols = [
        { input: '#fTrainee',    col: 1 },
        { input: '#fCompetency', col: 2 },
        { input: '#fDate',       col: 3 },
        { input: '#fScore',      col: 4 },
        { input: '#fGrade',      col: 5 },
    ];
    function filter() {
        var terms = cols.map(function(c) {
            return { col: c.col, val: $(c.input).val().toLowerCase() };
        });
        $('#scoreTable tbody tr').each(function() {
            var $tr = $(this);
            var show = terms.every(function(t) {
                if (!t.val) return true;
                return $tr.find('td').eq(t.col).text().toLowerCase().indexOf(t.val) >= 0;
            });
            $tr.toggle(show);
        });
    }
    cols.forEach(function(c) { $(c.input).on('input', filter); });
})();
</script>
<?php $this->end(); ?>

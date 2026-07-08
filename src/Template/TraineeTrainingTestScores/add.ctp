<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeTrainingTestScore $traineeTrainingTestScore
 * @var array $trainees
 * @var array $masterTrainingCompetencies
 * @var array $masterTrainingTestScoreGrades
 */
?>
<?= $this->Html->css('datepicker-fix.css') ?>
<?= $this->Html->script('form-confirm.js?v=2.0') ?>

<style>
.score-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 24px rgba(2,119,189,0.10);
    overflow: hidden;
    margin-bottom: 30px;
    max-width: 680px;
}
.score-header {
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
    padding: 22px 30px;
    display: flex; align-items: center; gap: 16px;
}
.score-header-icon {
    width: 52px; height: 52px;
    background: rgba(255,255,255,0.18);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.score-header-icon i { font-size: 24px; color: #fff; }
.score-header h3 { margin: 0 0 2px; color: #fff; font-size: 20px; font-weight: 700; }
.score-header p  { margin: 0; color: rgba(255,255,255,0.78); font-size: 13px; }

.score-body { padding: 30px; }

.score-section {
    border-left: 3px solid #0288D1;
    padding-left: 16px;
    margin-bottom: 26px;
}
.score-section-title {
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1.3px;
    color: #0277BD; margin-bottom: 14px;
    display: flex; align-items: center; gap: 7px;
}
.form-label {
    font-weight: 600; font-size: 13px;
    color: #374151; margin-bottom: 5px; display: block;
}
.req { color: #e53e3e; margin-left: 2px; }
.opt { color: #94a3b8; font-weight: 400; font-size: 11px; margin-left: 4px; }

.form-control, select.form-control {
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    padding: 9px 14px; font-size: 14px; width: 100%;
    background: #f8fafc; color: #1a202c;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.form-control:focus, select.form-control:focus {
    border-color: #0288D1;
    box-shadow: 0 0 0 3px rgba(2,136,209,0.12);
    background: #fff; outline: none;
}
.form-control.is-invalid { border-color: #e53e3e !important; }

/* Score + grade row */
.score-grade-row { display: flex; gap: 16px; align-items: flex-start; }
.score-grade-row .score-col { flex: 0 0 150px; }
.score-grade-row .grade-col { flex: 1; }

.grade-badge {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 16px; border-radius: 8px;
    font-size: 14px; font-weight: 700;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc; color: #94a3b8;
    transition: all 0.25s;
    min-height: 42px;
}
.grade-badge.g-A { background:#e8f5e9; border-color:#43A047; color:#2E7D32; }
.grade-badge.g-B { background:#e3f2fd; border-color:#1E88E5; color:#1565C0; }
.grade-badge.g-C { background:#fffde7; border-color:#F9A825; color:#F57F17; }
.grade-badge.g-D { background:#fff3e0; border-color:#FB8C00; color:#E65100; }
.grade-badge.g-E { background:#fce4ec; border-color:#E53935; color:#B71C1C; }

.grade-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }

/* Score bar */
.score-bar-wrap { margin-top: 14px; }
.score-bar-label {
    display: flex; justify-content: space-between;
    font-size: 11px; color: #94a3b8; margin-bottom: 4px;
}
.score-bar-bg {
    height: 8px; border-radius: 4px;
    background: #e2e8f0; overflow: hidden;
}
.score-bar-fill {
    height: 100%; border-radius: 4px;
    background: linear-gradient(90deg, #ef4444 0%, #f59e0b 50%, #22c55e 100%);
    width: 0%; transition: width 0.3s ease;
}

/* Grade legend chips */
.grade-legend {
    display: flex; gap: 6px; flex-wrap: wrap;
    margin-top: 10px;
}
.grade-chip {
    font-size: 11px; font-weight: 600;
    padding: 2px 10px; border-radius: 12px;
    border: 1px solid currentColor;
    opacity: 0.55; transition: opacity 0.2s;
}
.grade-chip.active { opacity: 1; }
.grade-chip.g-A { color: #2E7D32; background: #e8f5e9; }
.grade-chip.g-B { color: #1565C0; background: #e3f2fd; }
.grade-chip.g-C { color: #F57F17; background: #fffde7; }
.grade-chip.g-D { color: #E65100; background: #fff3e0; }
.grade-chip.g-E { color: #B71C1C; background: #fce4ec; }

.form-actions {
    display: flex; gap: 12px;
    padding: 20px 30px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 14px 14px;
}
.btn-save {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 26px;
    background: linear-gradient(135deg, #0288D1 0%, #0097A7 100%);
    color: #fff; border: none; border-radius: 8px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(2,136,209,0.25);
}
.btn-save:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(2,136,209,0.35); color:#fff; text-decoration:none; }
.btn-cancel {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 20px; background: #fff; color: #4a5568;
    border: 1.5px solid #e2e8f0; border-radius: 8px;
    font-size: 14px; font-weight: 600;
    cursor: pointer; text-decoration: none; transition: all 0.2s;
}
.btn-cancel:hover { background: #f1f5f9; color: #1a202c; text-decoration: none; }
</style>

<!-- Actions sidebar -->
<nav class="actions-sidebar" id="actions-sidebar">
    <button class="menu-toggle" onclick="toggleActionsMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link('<i class="fas fa-calendar-day"></i> ' . __('Daily Entry'), ['action' => 'daily'], ['escape' => false]) ?></li>
        <li><?= $this->Html->link('<i class="fas fa-table"></i> ' . __('Score Summary'), ['action' => 'index'], ['escape' => false]) ?></li>
    </ul>
</nav>

<div class="traineeTrainingTestScores form content">
    <?= $this->Form->create($traineeTrainingTestScore, [
        'id'           => 'scoreForm',
        'data-confirm' => 'true',
    ]) ?>

    <div class="score-card">

        <!-- Header -->
        <div class="score-header">
            <div class="score-header-icon"><i class="fas fa-star"></i></div>
            <div>
                <h3><?= __('Enter Test Score') ?></h3>
                <p><?= __('Record a competency test result for a trainee') ?></p>
            </div>
        </div>

        <div class="score-body">

            <!-- Trainee & Date -->
            <div class="score-section">
                <div class="score-section-title">
                    <i class="fas fa-user-graduate"></i> <?= __('Trainee & Test Date') ?>
                </div>
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label"><?= __('Trainee') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('trainee_id', [
                            'options'  => $trainees,
                            'class'    => 'form-control',
                            'label'    => false,
                            'empty'    => __('-- Select Trainee --'),
                            'required' => true,
                        ]) ?>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label"><?= __('Test Date') ?> <span class="req">*</span></label>
                        <?= $this->Form->control('test_date', [
                            'type'         => 'text',
                            'class'        => 'form-control datepicker',
                            'label'        => false,
                            'placeholder'  => 'YYYY-MM-DD',
                            'autocomplete' => 'off',
                            'required'     => true,
                            'value'        => $traineeTrainingTestScore->test_date
                                             ? $traineeTrainingTestScore->test_date->format('Y-m-d')
                                             : date('Y-m-d'),
                        ]) ?>
                    </div>
                </div>
            </div>

            <!-- Competency -->
            <div class="score-section">
                <div class="score-section-title">
                    <i class="fas fa-book-open"></i> <?= __('Test Module / Competency') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?= __('Competency') ?> <span class="req">*</span></label>
                    <?= $this->Form->control('master_training_competency_id', [
                        'options'  => $masterTrainingCompetencies,
                        'class'    => 'form-control',
                        'label'    => false,
                        'empty'    => __('-- Select Competency --'),
                        'required' => true,
                    ]) ?>
                </div>
            </div>

            <!-- Score & Grade -->
            <div class="score-section">
                <div class="score-section-title">
                    <i class="fas fa-chart-bar"></i> <?= __('Score & Grade') ?>
                </div>

                <div class="score-grade-row mb-2">
                    <div class="score-col">
                        <label class="form-label"><?= __('Score') ?> <span class="req">*</span> <span class="opt">(0–100)</span></label>
                        <?= $this->Form->control('score', [
                            'type'        => 'number',
                            'class'       => 'form-control',
                            'label'       => false,
                            'min'         => 0,
                            'max'         => 100,
                            'required'    => true,
                            'id'          => 'ScoreInput',
                            'placeholder' => '0',
                        ]) ?>
                    </div>
                    <div class="grade-col">
                        <label class="form-label"><?= __('Grade') ?> <span class="opt">(<?= __('auto-detected') ?>)</span></label>
                        <div class="grade-badge" id="gradeBadge">
                            <i class="fas fa-minus" id="gradeIcon"></i>
                            <span id="gradeLabel" style="font-size:16px;">–</span>
                            <span id="gradeDesc" style="font-weight:400;font-size:12px;"></span>
                        </div>
                        <div class="grade-hint" id="gradeHint"><?= __('Enter score to auto-detect grade') ?></div>
                    </div>
                </div>

                <!-- Progress bar -->
                <div class="score-bar-wrap">
                    <div class="score-bar-label">
                        <span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
                    </div>
                    <div class="score-bar-bg">
                        <div class="score-bar-fill" id="scoreBar"></div>
                    </div>
                </div>

                <!-- Grade legend -->
                <div class="grade-legend">
                    <span class="grade-chip g-E" id="chip-E">E: 0–20 Sangat Kurang</span>
                    <span class="grade-chip g-D" id="chip-D">D: 21–40 Kurang</span>
                    <span class="grade-chip g-C" id="chip-C">C: 41–60 Cukup</span>
                    <span class="grade-chip g-B" id="chip-B">B: 61–80 Baik</span>
                    <span class="grade-chip g-A" id="chip-A">A: 81–100 Sangat Baik</span>
                </div>

                <!-- Hidden grade ID submitted with form -->
                <?= $this->Form->control('master_training_test_score_grade_id', [
                    'type'  => 'hidden',
                    'label' => false,
                    'id'    => 'GradeId',
                ]) ?>
            </div>

        </div><!-- /.score-body -->

        <!-- Footer -->
        <div class="form-actions">
            <?= $this->Form->button('<i class="fas fa-save"></i> ' . __('Save Score'), [
                'type'   => 'submit',
                'class'  => 'btn-save',
                'id'     => 'submitBtn',
                'escape' => false,
            ]) ?>
            <?= $this->Html->link(
                '<i class="fas fa-arrow-left"></i> ' . __('Back to Daily Entry'),
                ['action' => 'daily'],
                ['class' => 'btn-cancel', 'escape' => false]
            ) ?>
        </div>

    </div><!-- /.score-card -->

    <?= $this->Form->end() ?>
</div>

<?php $this->append('script'); ?>
<script>
$(document).ready(function () {

    // Datepicker
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom auto',
            container: 'body',
            zIndexOffset: 1050
        });
    }

    // Grade table (mirrors master_training_test_score_grades)
    var GRADES = [
        { id: 1, label: 'A', min: 81,  max: 100, desc: 'Sangat Baik',   cls: 'g-A', icon: 'fa-award'            },
        { id: 2, label: 'B', min: 61,  max: 80,  desc: 'Baik',          cls: 'g-B', icon: 'fa-thumbs-up'        },
        { id: 3, label: 'C', min: 41,  max: 60,  desc: 'Cukup',         cls: 'g-C', icon: 'fa-minus-circle'     },
        { id: 4, label: 'D', min: 21,  max: 40,  desc: 'Kurang',        cls: 'g-D', icon: 'fa-exclamation-circle'},
        { id: 5, label: 'E', min: 0,   max: 20,  desc: 'Sangat Kurang', cls: 'g-E', icon: 'fa-times-circle'     }
    ];

    function findGrade(score) {
        for (var i = 0; i < GRADES.length; i++) {
            if (score >= GRADES[i].min && score <= GRADES[i].max) return GRADES[i];
        }
        return null;
    }

    function updateGrade() {
        var raw   = $('#ScoreInput').val();
        var score = parseInt(raw);
        var valid = raw !== '' && !isNaN(score) && score >= 0 && score <= 100;
        var g     = valid ? findGrade(score) : null;

        // Bar
        $('#scoreBar').css('width', (valid ? Math.min(score, 100) : 0) + '%');

        // Clear all chip highlights
        $('.grade-chip').removeClass('active');

        if (!g) {
            $('#gradeBadge').removeClass('g-A g-B g-C g-D g-E')
                .html('<i class="fas fa-minus"></i> <span style="font-size:16px;">–</span>');
            $('#gradeHint').text('<?= __('Enter score to auto-detect grade') ?>');
            $('#GradeId').val('');
            return;
        }

        $('#gradeBadge')
            .removeClass('g-A g-B g-C g-D g-E')
            .addClass(g.cls)
            .html('<i class="fas ' + g.icon + '"></i>' +
                  '<span style="font-size:17px;font-weight:800;">' + g.label + '</span>' +
                  '<span style="font-weight:400;font-size:12px;">' + g.desc + '</span>');
        $('#gradeHint').text('<?= __('Range') ?>: ' + g.min + ' – ' + g.max);
        $('#GradeId').val(g.id);
        $('#chip-' + g.label).addClass('active');
    }

    $('#ScoreInput').on('input change', updateGrade);
    updateGrade(); // init (handles validation-fail redisplay)

    // Validation before submit
    $('#scoreForm').on('submit', function (e) {
        var score = parseInt($('#ScoreInput').val());
        if (isNaN(score) || score < 0 || score > 100) {
            e.preventDefault();
            $('#ScoreInput').addClass('is-invalid').focus();
            return false;
        }
        if (!$('#GradeId').val()) {
            e.preventDefault();
            alert('<?= __('Unable to detect grade. Enter a score between 0 and 100.') ?>');
            return false;
        }
    });
    $('#ScoreInput').on('input', function () { $(this).removeClass('is-invalid'); });
});
</script>
<?php $this->end(); ?>

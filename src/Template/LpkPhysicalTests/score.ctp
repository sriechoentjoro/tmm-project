<?php
/**
 * Physical Fitness Test Scoring form for a single candidate
 */
?>
<style>
.pf-card{background:#fff;border:1px solid #e0f7fa;border-radius:12px;padding:24px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,188,212,.08)}
.pf-label{font-weight:600;color:#006064;margin-bottom:6px;display:block}
.pf-input{width:100%;padding:10px 14px;border:1px solid #b2ebf2;border-radius:8px;font-size:15px;transition:.2s}
.pf-input:focus{outline:none;border-color:#00bcd4;box-shadow:0 0 0 3px rgba(0,188,212,.2)}
.pf-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:18px;margin-bottom:18px}
.pf-score-box{background:linear-gradient(135deg,#e0f7fa,#b2ebf2);border-radius:10px;padding:16px;text-align:center}
.pf-score-val{font-size:36px;font-weight:700;color:#006064}
.pf-score-lbl{font-size:12px;color:#00838f;margin-top:2px}
.pf-guide{background:#f0f4ff;border-left:4px solid #7986cb;padding:12px 16px;border-radius:6px;font-size:13px;color:#3d3d6b;margin-bottom:18px}
.pf-guide table{border-collapse:collapse;margin-top:8px}
.pf-guide td,.pf-guide th{padding:4px 10px;border:1px solid #c5cae9;font-size:12px}
.pf-guide th{background:#e8eaf6}
</style>

<div class="index-header" style="margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between">
        <h2 style="margin:0"><i class="fas fa-dumbbell"></i> <?= __('Physical Fitness Test') ?></h2>
        <?= $this->Html->link('<i class="fas fa-arrow-left"></i> ' . __('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
    </div>
</div>

<!-- Candidate info card -->
<div class="pf-card" style="background:linear-gradient(135deg,#e0f7fa,#e8f5e9);border-color:#a5d6a7">
    <div style="display:flex;align-items:center;gap:20px">
        <?php if ($candidate->image_photo): ?>
        <img src="<?= h($candidate->image_photo) ?>" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:3px solid #00bcd4">
        <?php else: ?>
        <div style="width:64px;height:64px;border-radius:50%;background:#b2ebf2;display:flex;align-items:center;justify-content:center;font-size:24px;color:#006064"><i class="fas fa-user"></i></div>
        <?php endif; ?>
        <div>
            <div style="font-size:20px;font-weight:700;color:#004d40"><?= h($candidate->name) ?></div>
            <div style="color:#00695c;font-size:13px"><?= h($candidate->candidate_code) ?> &nbsp;|&nbsp;
                <?= h($candidate->has('vocational_training_institution') ? ($candidate->vocational_training_institution->name ?? '') : '') ?>
            </div>
        </div>
        <?php if ($candidate->fitness_score > 0): ?>
        <div class="pf-score-box" style="margin-left:auto">
            <div class="pf-score-val"><?= number_format($candidate->fitness_score, 1) ?></div>
            <div class="pf-score-lbl"><?= __('Current Fitness Score') ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Scoring guide -->
<div class="pf-guide">
    <strong><?= __('Scoring Reference') ?></strong>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-top:10px">
        <div>
            <strong><?= __('Push-ups') ?> (max 40 pts):</strong><br>
            <span style="color:#555;font-size:12px"><?= __('Score = number of reps, capped at 40') ?></span>
        </div>
        <div>
            <strong><?= __('Sit-ups') ?> (max 30 pts):</strong><br>
            <span style="color:#555;font-size:12px"><?= __('Score = number of reps, capped at 30') ?></span>
        </div>
        <div>
            <strong><?= __('2.4 km Run') ?> (max 30 pts):</strong>
            <table style="margin-top:4px">
                <tr><th><?= __('Time') ?></th><th><?= __('Points') ?></th></tr>
                <tr><td>&lt; 10:00</td><td>30</td></tr>
                <tr><td>10:00–10:59</td><td>25</td></tr>
                <tr><td>11:00–11:59</td><td>20</td></tr>
                <tr><td>12:00–12:59</td><td>15</td></tr>
                <tr><td>13:00–13:59</td><td>10</td></tr>
                <tr><td>&ge; 14:00</td><td>5</td></tr>
            </table>
        </div>
    </div>
</div>

<!-- Scoring form / view-only -->
<?php if (!empty($viewOnly)): ?>
<div class="pf-card" style="background:#f9fbe7;border-color:#c5e1a5">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;color:#558b2f">
        <i class="fas fa-eye fa-lg"></i>
        <span style="font-weight:600"><?= __('View Only — your role can view but not change scores') ?></span>
    </div>
    <div class="pf-row">
        <div>
            <span class="pf-label"><i class="fas fa-hand-rock"></i> <?= __('Push-ups (reps)') ?></span>
            <div class="pf-input" style="background:#f1f8e9;cursor:default"><?= h($candidate->fitness_pushups ?? 0) ?></div>
        </div>
        <div>
            <span class="pf-label"><i class="fas fa-child"></i> <?= __('Sit-ups (reps)') ?></span>
            <div class="pf-input" style="background:#f1f8e9;cursor:default"><?= h($candidate->fitness_situps ?? 0) ?></div>
        </div>
        <div>
            <span class="pf-label"><i class="fas fa-running"></i> <?= __('Run — minutes') ?></span>
            <div class="pf-input" style="background:#f1f8e9;cursor:default"><?= h($candidate->fitness_running_minutes ?? 0) ?></div>
        </div>
        <div>
            <span class="pf-label"><i class="fas fa-stopwatch"></i> <?= __('seconds') ?></span>
            <div class="pf-input" style="background:#f1f8e9;cursor:default"><?= h($candidate->fitness_running_seconds ?? 0) ?></div>
        </div>
    </div>
    <?php if ($candidate->fitness_notes): ?>
    <div style="margin-bottom:18px">
        <span class="pf-label"><?= __('Notes / Observations') ?></span>
        <div class="pf-input" style="background:#f1f8e9;cursor:default;white-space:pre-wrap"><?= h($candidate->fitness_notes) ?></div>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="pf-card">
    <?= $this->Form->create(null, ['url' => ['action' => 'score', $candidate->id], 'type' => 'post']) ?>
    <div class="pf-row">
        <div>
            <label class="pf-label"><i class="fas fa-hand-rock"></i> <?= __('Push-ups (reps)') ?></label>
            <?= $this->Form->control('fitness_pushups', [
                'label' => false,
                'type' => 'number', 'min' => 0, 'max' => 200,
                'value' => $candidate->fitness_pushups ?? 0,
                'class' => 'pf-input', 'id' => 'fPushups',
            ]) ?>
            <div style="font-size:11px;color:#00838f;margin-top:4px"><?= __('40+ reps = full 40 pts') ?></div>
        </div>
        <div>
            <label class="pf-label"><i class="fas fa-child"></i> <?= __('Sit-ups (reps)') ?></label>
            <?= $this->Form->control('fitness_situps', [
                'label' => false,
                'type' => 'number', 'min' => 0, 'max' => 200,
                'value' => $candidate->fitness_situps ?? 0,
                'class' => 'pf-input', 'id' => 'fSitups',
            ]) ?>
            <div style="font-size:11px;color:#00838f;margin-top:4px"><?= __('30+ reps = full 30 pts') ?></div>
        </div>
        <div>
            <label class="pf-label"><i class="fas fa-running"></i> <?= __('2.4 km Run — minutes') ?></label>
            <?= $this->Form->control('fitness_running_minutes', [
                'label' => false,
                'type' => 'number', 'min' => 0, 'max' => 60,
                'value' => $candidate->fitness_running_minutes ?? 0,
                'class' => 'pf-input', 'id' => 'fMins',
            ]) ?>
        </div>
        <div>
            <label class="pf-label"><i class="fas fa-stopwatch"></i> <?= __('seconds') ?></label>
            <?= $this->Form->control('fitness_running_seconds', [
                'label' => false,
                'type' => 'number', 'min' => 0, 'max' => 59,
                'value' => $candidate->fitness_running_seconds ?? 0,
                'class' => 'pf-input', 'id' => 'fSecs',
            ]) ?>
        </div>
    </div>

    <!-- Live score preview -->
    <div class="pf-score-box" id="scorePreview" style="margin-bottom:18px;display:inline-block;min-width:200px">
        <div class="pf-score-val" id="previewVal">—</div>
        <div class="pf-score-lbl"><?= __('Calculated Fitness Score / 100') ?></div>
    </div>

    <div style="margin-bottom:18px">
        <label class="pf-label"><?= __('Notes / Observations') ?></label>
        <?= $this->Form->control('fitness_notes', [
            'label' => false,
            'type' => 'textarea', 'rows' => 3,
            'value' => $candidate->fitness_notes ?? '',
            'class' => 'pf-input', 'placeholder' => __('Optional notes about the test session, candidate condition, etc.'),
        ]) ?>
    </div>

    <div style="display:flex;gap:12px">
        <?= $this->Form->button('<i class="fas fa-save"></i> ' . __('Save Score'), [
            'class' => 'btn btn-primary', 'type' => 'submit', 'escape' => false
        ]) ?>
        <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
</div>
<?php endif; ?>

<?php if (empty($viewOnly)): ?>
<script>
(function() {
    var pushups = document.getElementById('fPushups');
    var situps  = document.getElementById('fSitups');
    var mins    = document.getElementById('fMins');
    var secs    = document.getElementById('fSecs');
    var preview = document.getElementById('previewVal');

    function calcScore() {
        var p = Math.min(parseInt(pushups.value) || 0, 40);
        var s = Math.min(parseInt(situps.value)  || 0, 30);
        var m = parseInt(mins.value) || 0;
        var se= parseInt(secs.value) || 0;
        var totalSecs = m * 60 + se;
        var r = 0;
        if (totalSecs > 0) {
            if      (totalSecs <= 600) r = 30;
            else if (totalSecs <= 660) r = 25;
            else if (totalSecs <= 720) r = 20;
            else if (totalSecs <= 780) r = 15;
            else if (totalSecs <= 840) r = 10;
            else                       r = 5;
        }
        var total = p + s + r;
        preview.textContent = total;
        var box = document.getElementById('scorePreview');
        box.style.background = total >= 75 ? 'linear-gradient(135deg,#c8e6c9,#a5d6a7)' :
                               total >= 50 ? 'linear-gradient(135deg,#fff9c4,#fff176)' :
                               total > 0   ? 'linear-gradient(135deg,#ffcdd2,#ef9a9a)' :
                                             'linear-gradient(135deg,#e0f7fa,#b2ebf2)';
    }
    [pushups, situps, mins, secs].forEach(function(el) {
        el.addEventListener('input', calcScore);
    });
    calcScore();
})();
</script>
<?php endif; ?>

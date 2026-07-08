<?php
/**
 * AO Interview Scoring form for a single candidate
 */
?>
<style>
.ao-card{background:#fff;border:1px solid #e8eaf6;border-radius:12px;padding:24px;margin-bottom:20px;box-shadow:0 2px 8px rgba(121,134,203,.1)}
.ao-label{font-weight:600;color:#3d3d6b;margin-bottom:6px;display:block;font-size:14px}
.ao-input{width:100%;padding:10px 14px;border:1px solid #c5cae9;border-radius:8px;font-size:15px;transition:.2s}
.ao-input:focus{outline:none;border-color:#7986cb;box-shadow:0 0 0 3px rgba(121,134,203,.2)}
.dim-row{display:grid;grid-template-columns:1fr;gap:4px;margin-bottom:16px}
.dim-slider{width:100%;height:6px;border-radius:3px;appearance:none;background:#e8eaf6;cursor:pointer}
.dim-slider::-webkit-slider-thumb{appearance:none;width:18px;height:18px;border-radius:50%;background:#7986cb;cursor:pointer}
.score-total{font-size:44px;font-weight:700;color:#3d3d6b}
.rec-btn{padding:10px 22px;border:2px solid;border-radius:8px;cursor:pointer;font-weight:600;font-size:14px;transition:.2s;background:#fff}
.rec-pass{border-color:#43a047;color:#2e7d32}
.rec-pass.active,.rec-pass:hover{background:#43a047;color:#fff}
.rec-reserved{border-color:#fb8c00;color:#e65100}
.rec-reserved.active,.rec-reserved:hover{background:#fb8c00;color:#fff}
.rec-fail{border-color:#e53935;color:#b71c1c}
.rec-fail.active,.rec-fail:hover{background:#e53935;color:#fff}
.hist-row{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;background:#f8f9fa;margin-bottom:8px;font-size:13px}
</style>

<div class="index-header" style="margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-handshake"></i> <?= __('Interview with Acceptance Organization — Scoring') ?></h2>
        <?= $this->Html->link('<i class="fas fa-list"></i> ' . __('All Interview Records'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
    </div>
</div>

<!-- Candidate card -->
<div class="ao-card" style="background:linear-gradient(135deg,#ede7f6,#e8eaf6);border-color:#b39ddb">
    <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap">
        <div style="width:58px;height:58px;border-radius:50%;background:#9fa8da;display:flex;align-items:center;justify-content:center;font-size:22px;color:#fff;flex-shrink:0"><i class="fas fa-user"></i></div>
        <div>
            <div style="font-size:20px;font-weight:700;color:#1a237e"><?= h($candidate->name) ?></div>
            <div style="color:#3949ab;font-size:13px">
                <?= h($candidate->candidate_code) ?> &nbsp;|&nbsp;
                LPK: <?= h($candidate->has('vocational_training_institution') ? ($candidate->vocational_training_institution->name ?? '-') : '-') ?>
                <?php if ($candidate->has('acceptance_organization') && $candidate->acceptance_organization): ?>
                    &nbsp;|&nbsp; AO: <?= h($candidate->acceptance_organization->company_name ?? '') ?>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($candidate->interview_score > 0): ?>
        <div style="margin-left:auto;text-align:center;background:#fff;border-radius:10px;padding:12px 20px">
            <div style="font-size:32px;font-weight:700;color:#3d3d6b"><?= number_format($candidate->interview_score, 1) ?></div>
            <div style="font-size:11px;color:#7986cb"><?= __('Current Interview Score / 100') ?></div>
            <div style="font-size:12px;margin-top:2px">
                <?php $rec = $candidate->interview_recommendation ?? ''; ?>
                <?php if ($rec === 'pass'): ?><span style="color:#2e7d32;font-weight:600"><?= __('Recommended: PASS') ?></span>
                <?php elseif ($rec === 'reserved'): ?><span style="color:#e65100;font-weight:600"><?= __('Recommended: RESERVED') ?></span>
                <?php elseif ($rec === 'fail'): ?><span style="color:#b71c1c;font-weight:600"><?= __('Recommended: FAIL') ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Scoring form -->
<?php if (!empty($viewOnly)): ?>
<div class="ao-card" style="background:#f9fbe7;border-color:#c5e1a5">
    <div style="display:flex;align-items:center;gap:10px;color:#558b2f">
        <i class="fas fa-eye fa-lg"></i>
        <span style="font-weight:600"><?= __('View Only — your role can view but not record interview scores') ?></span>
    </div>
</div>
<?php else: ?>
<div class="ao-card">
    <h4 style="margin:0 0 18px;color:#3d3d6b"><i class="fas fa-plus-circle"></i> <?= __('Record New Interview Session') ?></h4>
    <?= $this->Form->create(null, ['url' => ['action' => 'aoScore', $candidate->id], 'type' => 'post', 'id' => 'aoForm']) ?>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:18px">
        <div>
            <label class="ao-label"><?= __('Interview Date') ?></label>
            <?= $this->Form->control('date_interview', ['label' => false, 'type' => 'date', 'class' => 'ao-input', 'value' => date('Y-m-d')]) ?>
        </div>
        <div>
            <label class="ao-label"><?= __('Result Notification Date') ?></label>
            <?= $this->Form->control('date_interview_result', ['label' => false, 'type' => 'date', 'class' => 'ao-input', 'value' => date('Y-m-d')]) ?>
        </div>
    </div>

    <hr style="border-color:#e8eaf6;margin:0 0 18px">

    <!-- Five scoring dimensions -->
    <?php
    $dimensions = [
        ['politeness_score',    __('Politeness / Manner (礼儀)'),       20, __('Bowing, greeting, respectful behavior')],
        ['communication_score', __('Communication (コミュニケーション)'), 20, __('Clarity, listening, expressiveness')],
        ['motivation_score',    __('Motivation (意欲)'),                 20, __('Drive, enthusiasm, goals')],
        ['adaptability_score',  __('Adaptability (適応力)'),             20, __('Flexibility, openness to new environment')],
        ['technical_score',     __('Technical Skill (技術)'),             20, __('Relevant skills, training background')],
    ];
    foreach ($dimensions as [$field, $label, $max, $hint]):
        $prev = $candidate->{'interview_' . explode('_', $field)[0]} ?? 0;
        if ($field === 'adaptability_score') $prev = $candidate->interview_adaptability ?? 0;
        if ($field === 'technical_score')    $prev = $candidate->interview_technical ?? 0;
        if ($field === 'motivation_score')   $prev = $candidate->interview_motivation ?? 0;
    ?>
    <div class="dim-row">
        <div style="display:flex;justify-content:space-between;align-items:center">
            <label class="ao-label" style="margin:0"><?= $label ?></label>
            <span style="font-weight:700;font-size:18px;color:#5c6bc0;min-width:36px;text-align:right" id="val_<?= $field ?>">0</span>
        </div>
        <div style="font-size:11px;color:#9e9e9e;margin-bottom:6px"><?= $hint ?></div>
        <input type="range" class="dim-slider" name="<?= $field ?>" id="<?= $field ?>"
               min="0" max="<?= $max ?>" value="0"
               oninput="document.getElementById('val_<?= $field ?>').textContent = this.value; calcTotal()">
        <div style="display:flex;justify-content:space-between;font-size:11px;color:#bdbdbd;margin-top:2px"><span>0</span><span><?= $max ?></span></div>
    </div>
    <?php endforeach; ?>

    <!-- Live total -->
    <div style="text-align:center;background:#f3f4ff;border-radius:10px;padding:16px;margin-bottom:18px">
        <div class="score-total" id="totalScore">0</div>
        <div style="font-size:13px;color:#7986cb"><?= __('Total Interview Score / 100') ?></div>
        <div style="margin-top:8px;font-size:12px;color:#9e9e9e">
            <?= __('≥ 70 = Recommended Pass &nbsp;|&nbsp; 50–69 = Reserved &nbsp;|&nbsp; < 50 = Fail') ?>
        </div>
    </div>

    <!-- Recommendation -->
    <div style="margin-bottom:18px">
        <label class="ao-label"><?= __('Recommendation') ?></label>
        <div style="display:flex;gap:12px;flex-wrap:wrap" id="recGroup">
            <button type="button" class="rec-btn rec-pass" data-val="pass" onclick="setRec('pass')">
                <i class="fas fa-check"></i> <?= __('Pass') ?>
            </button>
            <button type="button" class="rec-btn rec-reserved" data-val="reserved" onclick="setRec('reserved')">
                <i class="fas fa-pause"></i> <?= __('Reserved') ?>
            </button>
            <button type="button" class="rec-btn rec-fail" data-val="fail" onclick="setRec('fail')">
                <i class="fas fa-times"></i> <?= __('Fail') ?>
            </button>
        </div>
        <input type="hidden" name="recommendation" id="recInput" value="pass">
    </div>

    <!-- Notes -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px">
        <div>
            <label class="ao-label"><?= __('Interview Notes (Internal)') ?></label>
            <?= $this->Form->control('comments', ['label' => false, 'type' => 'textarea', 'rows' => 4, 'class' => 'ao-input',
                'placeholder' => __('Notes for TMM internal use')]) ?>
        </div>
        <div>
            <label class="ao-label"><?= __('Notes for Acceptance Organization (Japanese)') ?></label>
            <?= $this->Form->control('interview_notes_japanese', ['label' => false, 'type' => 'textarea', 'rows' => 4, 'class' => 'ao-input',
                'placeholder' => '受け入れ機関へのコメント（日本語）']) ?>
        </div>
    </div>

    <div style="display:flex;gap:12px">
        <?= $this->Form->button('<i class="fas fa-save"></i> ' . __('Save Interview Record'), [
            'class' => 'btn btn-primary', 'escape' => false
        ]) ?>
        <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
</div>
<?php endif; ?>

<!-- History -->
<?php if (count($interviewHistory->toArray())): ?>
<div class="ao-card">
    <h4 style="margin:0 0 14px;color:#3d3d6b"><i class="fas fa-history"></i> <?= __('Past Interview Records') ?></h4>
    <?php foreach ($interviewHistory as $h): ?>
    <div class="hist-row">
        <span style="font-weight:600;color:#3d3d6b;min-width:90px"><?= h($h->date_interview) ?></span>
        <span style="background:#e8eaf6;padding:2px 10px;border-radius:8px;font-size:12px"><?= h($h->has('master_candidate_interview_type') ? $h->master_candidate_interview_type->title : '') ?></span>
        <span style="font-weight:700;font-size:16px;color:#5c6bc0;min-width:36px"><?= number_format($h->overall_score, 1) ?></span>
        <span style="flex:1"><?= h($h->comments) ?></span>
        <?php $r = $h->recommendation ?? ''; ?>
        <?php if ($r === 'pass'): ?>
            <span style="background:#e8f5e9;color:#2e7d32;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600"><?= __('Pass') ?></span>
        <?php elseif ($r === 'reserved'): ?>
            <span style="background:#fff3e0;color:#e65100;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600"><?= __('Reserved') ?></span>
        <?php else: ?>
            <span style="background:#ffebee;color:#b71c1c;padding:2px 10px;border-radius:8px;font-size:12px;font-weight:600"><?= __('Fail') ?></span>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (empty($viewOnly)): ?>
<script>
function calcTotal() {
    var fields = ['politeness_score','communication_score','motivation_score','adaptability_score','technical_score'];
    var total = 0;
    fields.forEach(function(f) {
        total += parseInt(document.getElementById(f).value) || 0;
    });
    document.getElementById('totalScore').textContent = total;
    // Auto-suggest recommendation
    if (total >= 70) autoRec('pass');
    else if (total >= 50) autoRec('reserved');
    else autoRec('fail');
}
function autoRec(val) {
    // Only auto-suggest if user hasn't manually set
    if (!window._recManual) setRec(val);
}
function setRec(val) {
    window._recManual = true;
    document.getElementById('recInput').value = val;
    document.querySelectorAll('.rec-btn').forEach(function(b) {
        b.classList.toggle('active', b.dataset.val === val);
    });
}
// Init with pass active
setRec('pass');
window._recManual = false;
</script>
<?php endif; ?>

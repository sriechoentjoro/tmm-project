<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate[] $candidates           eligible (pass=1, not yet trainee)
 * @var \App\Model\Entity\Candidate[] $promotedCandidates   already promoted to trainee
 * @var array $docCompleteness   [applicant_id => pct]
 * @var int   $totalRequired
 * @var array $trainingBatches
 * @var array $relCounts         [candidate_id => [educations,experiences,...]]
 */

function ptScore($val) {
    if ($val === null || $val == 0) return null;
    return (float)$val;
}
function ptScoreClass($val) {
    if ($val === null || $val == 0) return ['#94a3b8','#f1f5f9'];
    if ($val >= 75) return ['#2E7D32','#e8f5e9'];
    if ($val >= 50) return ['#E65100','#fff3e0'];
    return ['#B71C1C','#fce4ec'];
}
?>
<style>
/* ── Page header ─────────────────────────────────────────────── */
.pta-page-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:22px; }
.pta-page-header h2 { margin:0; font-size:20px; font-weight:700; color:#1a202c; display:flex; align-items:center; gap:10px; }
.pta-page-header h2 i { color:#2e7d32; }

/* ── Section title ───────────────────────────────────────────── */
.section-title { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#64748b; margin-bottom:14px; display:flex; align-items:center; gap:8px; padding-bottom:8px; border-bottom:2px solid #f0f4f8; }

/* ── Candidate card grid ─────────────────────────────────────── */
.cand-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(360px,1fr)); gap:18px; margin-bottom:32px; }

.cand-card { background:#fff; border-radius:14px; box-shadow:0 3px 16px rgba(0,0,0,0.08); overflow:hidden; display:flex; flex-direction:column; border:2px solid transparent; transition:border-color 0.2s, box-shadow 0.2s; }
.cand-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.12); }
.cand-card.ready { border-color:#2e7d32; }
.cand-card.done  { border-color:#22c55e; opacity:0.82; }

/* Card header */
.cc-header { padding:14px 16px; display:flex; align-items:center; gap:12px; }
.cc-avatar { width:46px; height:46px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:800; color:#fff; }
.cc-name-block .cc-name { font-size:15px; font-weight:700; color:#1a202c; }
.cc-name-block .cc-code { font-size:11px; color:#94a3b8; margin-top:2px; }
.cc-status-pill { margin-left:auto; flex-shrink:0; display:inline-flex; align-items:center; gap:5px; padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700; }
.s-eligible { background:#f0fdf4; color:#15803d; }
.s-promoted { background:#dcfce7; color:#15803d; }

/* Card body */
.cc-body { padding:0 16px 14px; flex:1; }
.cc-divider { height:1px; background:#f0f4f8; margin:10px 0; }

/* Score stats */
.cc-stats { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px; }
.cc-stat { flex:1; min-width:80px; text-align:center; background:#f8fafc; border-radius:8px; padding:8px 6px; border-left:3px solid; }
.cc-stat .csv { font-size:16px; font-weight:800; line-height:1; }
.cc-stat .csl { font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; margin-top:2px; }

/* Score bar */
.score-bar-wrap { margin:6px 0 8px; }
.score-bar-bg { height:6px; border-radius:3px; background:#e2e8f0; overflow:hidden; }
.score-bar-fill { height:100%; border-radius:3px; }
.score-bar-label { display:flex; justify-content:space-between; font-size:10px; color:#94a3b8; margin-bottom:2px; }

/* Doc completeness */
.doc-bar-wrap { display:flex; align-items:center; gap:8px; margin-top:8px; }
.doc-bar-bg { flex:1; height:6px; border-radius:3px; background:#e2e8f0; overflow:hidden; }
.doc-bar-fill { height:100%; border-radius:3px; }
.doc-pct { font-size:11px; font-weight:700; width:32px; text-align:right; }

/* Relation chips */
.cc-relations { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
.rel-chip { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:600; padding:2px 8px; border-radius:10px; background:#f1f5f9; color:#64748b; }
.rel-chip.has { background:#e0f2fe; color:#0277BD; }

/* Org row */
.cc-org { display:flex; gap:10px; margin-top:8px; flex-wrap:wrap; }
.cc-org-item { display:flex; align-items:center; gap:5px; font-size:11px; color:#64748b; }
.cc-org-item strong { color:#374151; }

/* Card footer */
.cc-footer { padding:12px 16px; background:#f8fafc; border-top:1px solid #f0f4f8; display:flex; align-items:center; justify-content:space-between; gap:10px; }
.btn-promote { display:inline-flex; align-items:center; gap:7px; padding:8px 18px; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; transition:all 0.2s; background:linear-gradient(135deg,#2e7d32,#43a047); color:#fff; box-shadow:0 2px 8px rgba(46,125,50,0.25); text-decoration:none; }
.btn-promote:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(46,125,50,0.35); color:#fff; text-decoration:none; }
.btn-view-sm { display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border:1.5px solid #e2e8f0; border-radius:7px; font-size:12px; font-weight:600; color:#64748b; text-decoration:none; background:#fff; }
.btn-view-sm:hover { background:#f1f5f9; color:#1a202c; text-decoration:none; }

/* Empty state */
.empty-state { text-align:center; padding:50px 20px; color:#94a3b8; }
.empty-state i { font-size:40px; display:block; margin-bottom:14px; }

/* ── Batch selection modal ────────────────────────────────────── */
.modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050; align-items:center; justify-content:center; }
.modal-bg.show { display:flex; }
.modal-box { background:#fff; border-radius:16px; padding:0; max-width:540px; width:92%; box-shadow:0 20px 60px rgba(0,0,0,0.2); overflow:hidden; }
.modal-header { background:linear-gradient(135deg,#2e7d32,#43a047); padding:20px 24px; color:#fff; }
.modal-header h5 { margin:0; font-size:16px; font-weight:700; }
.modal-header p  { margin:4px 0 0; font-size:12px; opacity:0.8; }
.modal-body { padding:20px 24px; max-height:70vh; overflow-y:auto; }
.modal-footer { padding:14px 24px; background:#f8fafc; border-top:1px solid #f0f4f8; display:flex; justify-content:flex-end; gap:10px; }

.batch-option { display:flex; align-items:center; gap:10px; padding:10px 14px; border:2px solid #e2e8f0; border-radius:10px; cursor:pointer; transition:all 0.15s; margin-bottom:8px; }
.batch-option:hover { border-color:#43a047; background:#f0fdf4; }
.batch-option.selected { border-color:#2e7d32; background:#f0fdf4; }
.batch-option input[type=radio] { accent-color:#2e7d32; width:15px; height:15px; flex-shrink:0; }

.remarks-label { font-size:12px; font-weight:700; color:#374151; margin-bottom:6px; display:block; }
.remarks-input { width:100%; border:1.5px solid #e2e8f0; border-radius:8px; padding:9px 12px; font-size:13px; resize:vertical; font-family:inherit; transition:border-color 0.2s; }
.remarks-input:focus { border-color:#2e7d32; outline:none; }

.btn-modal-cancel  { padding:9px 20px; border:1.5px solid #e2e8f0; border-radius:8px; background:#fff; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; }
.btn-modal-confirm { padding:9px 22px; background:linear-gradient(135deg,#2e7d32,#43a047); color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; }
.btn-modal-confirm:hover { opacity:0.9; }
</style>

<!-- Page header -->
<div class="pta-page-header">
    <h2><i class="fas fa-user-graduate"></i> <?= __('Promote Candidate to Trainee') ?></h2>
    <?= $this->Html->link('<i class="fas fa-history"></i> ' . __('Promotion History'),
        ['action' => 'promotionHistory'],
        ['escape' => false, 'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:600;color:#64748b;text-decoration:none;background:#fff;']) ?>
</div>

<!-- ── ELIGIBLE CANDIDATES ────────────────────────────────────── -->
<?php if (!$candidates->isEmpty()): ?>
<div class="section-title">
    <i class="fas fa-user-clock" style="color:#2e7d32;"></i>
    <?= __('Eligible for Promotion') ?>
    <span style="background:#f0fdf4;color:#15803d;padding:2px 10px;border-radius:12px;font-size:11px;"><?= $candidates->count() ?></span>
</div>

<div class="cand-grid">
<?php foreach ($candidates as $c):
    $cid     = $c->id;
    $gender  = (int)($c->master_gender_id ?? 1);
    $init    = mb_strtoupper(mb_substr($c->name, 0, 1));
    $bg      = $gender === 2 ? 'linear-gradient(135deg,#C2185B,#E91E63)' : 'linear-gradient(135deg,#1565C0,#2e7d32)';
    $fs      = ptScore($c->fitness_score);
    $is      = ptScore($c->interview_score);
    $ms      = ptScore($c->mcu_score);
    $docPct  = $docCompleteness[$cid] ?? 0;
    $rels    = $relCounts[$cid] ?? [];
    [$fsc,$fsbg] = ptScoreClass($fs);
    [$isc,$isbg] = ptScoreClass($is);
    [$msc,$msbg] = ptScoreClass($ms);

    $recOk   = in_array($c->interview_recommendation ?? '', ['recommended','pass','yes','1',1,true]);
    $copyJson = json_encode([
        ['icon'=>'fa-user','label'=> __('Core profile, address, contacts'),'has'=>true],
        ['icon'=>'fa-graduation-cap','label'=> __('Education') . ' (' . ($rels['educations'] ?? 0) . ')','has'=>($rels['educations']??0)>0],
        ['icon'=>'fa-briefcase','label'=> __('Work experience') . ' (' . ($rels['experiences'] ?? 0) . ')','has'=>($rels['experiences']??0)>0],
        ['icon'=>'fa-certificate','label'=> __('Certifications') . ' (' . ($rels['certifications'] ?? 0) . ')','has'=>($rels['certifications']??0)>0],
        ['icon'=>'fa-book','label'=> __('Courses') . ' (' . ($rels['courses'] ?? 0) . ')','has'=>($rels['courses']??0)>0],
        ['icon'=>'fa-users','label'=> __('Family data') . ' (' . ($rels['families'] ?? 0) . ')','has'=>($rels['families']??0)>0],
    ]);
?>
<div class="cand-card ready">

    <div class="cc-header">
        <div class="cc-avatar" style="background:<?= $bg ?>;"><?= h($init) ?></div>
        <div class="cc-name-block">
            <div class="cc-name"><?= h($c->name) ?></div>
            <div class="cc-code"><?= h($c->candidate_code) ?></div>
        </div>
        <span class="cc-status-pill s-eligible">
            <i class="fas fa-star" style="font-size:9px;"></i> <?= __('Eligible') ?>
        </span>
    </div>

    <div class="cc-body">
        <!-- Scores -->
        <div class="cc-stats">
            <div class="cc-stat" style="border-color:<?= $fsc ?>;">
                <div class="csv" style="color:<?= $fsc ?>;"><?= $fs !== null ? number_format($fs,1) : '—' ?></div>
                <div class="csl"><?= __('Physical') ?></div>
            </div>
            <div class="cc-stat" style="border-color:<?= $isc ?>;">
                <div class="csv" style="color:<?= $isc ?>;"><?= $is !== null ? number_format($is,1) : '—' ?></div>
                <div class="csl"><?= __('Interview') ?></div>
            </div>
            <div class="cc-stat" style="border-color:<?= $msc ?>;">
                <div class="csv" style="color:<?= $msc ?>;"><?= $ms !== null ? number_format($ms,1) : '—' ?></div>
                <div class="csl"><?= __('MCU') ?></div>
            </div>
            <?php if ($recOk): ?>
            <div class="cc-stat" style="border-color:#2e7d32;background:#f0fdf4;">
                <div class="csv" style="color:#2e7d32;font-size:14px;"><i class="fas fa-thumbs-up"></i></div>
                <div class="csl"><?= __('Rec') ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Physical score bar -->
        <?php if ($fs !== null): ?>
        <div class="score-bar-wrap">
            <div class="score-bar-label"><span><?= __('Physical Score') ?></span><span><?= number_format($fs,1) ?>/100</span></div>
            <div class="score-bar-bg"><div class="score-bar-fill" style="width:<?= min($fs,100) ?>%;background:<?= $fsc ?>;"></div></div>
        </div>
        <?php endif; ?>

        <!-- Document completeness -->
        <div class="doc-bar-wrap">
            <span style="font-size:10px;font-weight:600;color:#64748b;white-space:nowrap;"><?= __('Docs') ?></span>
            <div class="doc-bar-bg">
                <div class="doc-bar-fill" style="width:<?= $docPct ?>%;background:<?= $docPct>=100?'#22c55e':($docPct>=70?'#fb8c00':'#ef4444') ?>;"></div>
            </div>
            <span class="doc-pct" style="color:<?= $docPct>=100?'#22c55e':($docPct>=70?'#fb8c00':'#ef4444') ?>;"><?= $docPct ?>%</span>
        </div>

        <div class="cc-divider"></div>

        <!-- Data to copy chips -->
        <div class="cc-relations">
            <?php foreach (['educations'=>__('Education'),'experiences'=>__('Experience'),'certifications'=>__('Certs'),'courses'=>__('Courses'),'families'=>__('Family')] as $k=>$lbl): ?>
            <span class="rel-chip <?= ($rels[$k]??0)>0?'has':'' ?>">
                <span><?= $rels[$k]??0 ?></span> <?= $lbl ?>
            </span>
            <?php endforeach; ?>
        </div>

        <!-- Org / LPK -->
        <div class="cc-org">
            <?php if ($c->vocational_training_institution): ?>
            <div class="cc-org-item"><i class="fas fa-school" style="font-size:10px;"></i> <strong><?= h($c->vocational_training_institution->name ?? '') ?></strong></div>
            <?php endif; ?>
            <?php if ($c->acceptance_organization): ?>
            <div class="cc-org-item"><i class="fas fa-building" style="font-size:10px;"></i> <strong><?= h($c->acceptance_organization->name ?? '') ?></strong></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="cc-footer">
        <?= $this->Html->link('<i class="fas fa-eye"></i> ' . __('Profile'),
            ['action' => 'view', $cid],
            ['class' => 'btn-view-sm', 'escape' => false]) ?>
        <button class="btn-promote" onclick="openModal(<?= $cid ?>)">
            <i class="fas fa-user-graduate"></i> <?= __('Promote to Trainee') ?>
        </button>
    </div>

</div>
<?php endforeach; ?>
</div>

<?php else: ?>
<div class="empty-state">
    <i class="fas fa-user-check"></i>
    <p style="font-size:15px;font-weight:600;color:#64748b;"><?= __('No candidates pending promotion.') ?></p>
    <p style="font-size:12px;margin-top:6px;"><?= __('Candidates with is_candidate_pass=1 who have not yet become trainees will appear here.') ?></p>
</div>
<?php endif; ?>


<!-- ── ALREADY PROMOTED ──────────────────────────────────────── -->
<?php if (!empty($promotedCandidates) && (is_array($promotedCandidates) ? count($promotedCandidates) : !$promotedCandidates->isEmpty())): ?>
<div class="section-title" style="margin-top:10px;">
    <i class="fas fa-check-double" style="color:#22c55e;"></i>
    <?= __('Already Promoted to Trainee') ?>
    <span style="background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:11px;"><?= is_array($promotedCandidates) ? count($promotedCandidates) : $promotedCandidates->count() ?></span>
</div>
<div class="cand-grid">
<?php foreach ($promotedCandidates as $c):
    $cid    = $c->id;
    $gender = (int)($c->master_gender_id ?? 1);
    $init   = mb_strtoupper(mb_substr($c->name, 0, 1));
    $bg     = $gender === 2 ? 'linear-gradient(135deg,#C2185B,#E91E63)' : 'linear-gradient(135deg,#0277BD,#0097A7)';
    $fs     = ptScore($c->fitness_score);
    $is2    = ptScore($c->interview_score);
    $rels   = $relCounts[$cid] ?? [];
    [$fsc,$fsbg] = ptScoreClass($fs);
    [$isc,$isbg] = ptScoreClass($is2);
?>
<div class="cand-card done">
    <div class="cc-header">
        <div class="cc-avatar" style="background:<?= $bg ?>;"><?= h($init) ?></div>
        <div class="cc-name-block">
            <div class="cc-name"><?= h($c->name) ?></div>
            <div class="cc-code"><?= h($c->candidate_code) ?></div>
        </div>
        <span class="cc-status-pill s-promoted"><i class="fas fa-check-circle" style="font-size:9px;"></i> <?= __('Promoted') ?></span>
    </div>
    <div class="cc-body">
        <div class="cc-stats">
            <div class="cc-stat" style="border-color:<?= $fsc ?>;"><div class="csv" style="color:<?= $fsc ?>;"><?= $fs !== null ? number_format($fs,1) : '—' ?></div><div class="csl"><?= __('Physical') ?></div></div>
            <div class="cc-stat" style="border-color:<?= $isc ?>;"><div class="csv" style="color:<?= $isc ?>;"><?= $is2 !== null ? number_format($is2,1) : '—' ?></div><div class="csl"><?= __('Interview') ?></div></div>
        </div>
        <div class="cc-relations" style="margin-top:8px;">
            <?php foreach (['educations'=>__('Edu'),'experiences'=>__('Exp'),'certifications'=>__('Cert'),'courses'=>__('Course'),'families'=>__('Family')] as $k=>$lbl): ?>
            <span class="rel-chip <?= ($rels[$k]??0)>0?'has':'' ?>"><?= $rels[$k]??0 ?> <?= $lbl ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="cc-footer">
        <?= $this->Html->link('<i class="fas fa-eye"></i> ' . __('Profile'), ['action' => 'view', $cid], ['class' => 'btn-view-sm', 'escape' => false]) ?>
        <span style="font-size:12px;color:#15803d;font-weight:700;"><i class="fas fa-check-circle"></i> <?= __('Trainee record created') ?></span>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>


<!-- ── Promotion Modal ────────────────────────────────────────── -->
<div class="modal-bg" id="promoteModal">
    <div class="modal-box">
        <div class="modal-header">
            <h5><i class="fas fa-user-graduate" style="margin-right:8px;"></i><?= __('Confirm Promotion to Trainee') ?></h5>
            <p id="modal-subtitle"></p>
        </div>
        <div class="modal-body">

            <!-- Score summary -->
            <div id="modal-score-summary" style="background:#f8fdf8;border:1.5px solid #c8e6c9;border-radius:10px;padding:12px 16px;font-size:13px;line-height:2;margin-bottom:16px;"></div>

            <!-- Data copy checklist -->
            <p style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;"><?= __('Data to be copied') ?></p>
            <div id="modal-copy-list" style="margin-bottom:16px;"></div>

            <!-- Training batch selection -->
            <p style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">
                <i class="fas fa-layer-group" style="color:#2e7d32;"></i>
                <?= __('Assign to Training Batch') ?> <span style="color:#e53935;">*</span>
            </p>
            <div style="max-height:210px;overflow-y:auto;padding-right:4px;">
                <?php foreach ($trainingBatches as $b):
                    $enrolled  = (int)$b['enrolled'];
                    $startDate = $b['origin_start_plan_date'] ? date('d M Y', strtotime($b['origin_start_plan_date'])) : '—';
                    $endDate   = $b['origin_finish_plan_date'] ? date('d M Y', strtotime($b['origin_finish_plan_date'])) : '—';
                ?>
                <label class="batch-option" data-id="<?= $b['id'] ?>">
                    <input type="radio" name="batch_pick" value="<?= $b['id'] ?>"
                        onchange="selectBatch(<?= $b['id'] ?>)">
                    <div style="flex:1;">
                        <div style="font-size:13px;font-weight:700;color:#1a202c;"><?= h($b['batch_name']) ?></div>
                        <div style="font-size:11px;color:#64748b;margin-top:2px;">
                            <?= h($b['origin_training_location'] ?? '') ?>
                            · <?= $startDate ?> – <?= $endDate ?>
                            · <?= $b['training_term_of_months'] ?? '?' ?> <?= __('months') ?>
                        </div>
                    </div>
                    <div style="text-align:center;flex-shrink:0;">
                        <div style="font-size:16px;font-weight:800;color:#0277BD;"><?= $enrolled ?></div>
                        <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;"><?= __('enrolled') ?></div>
                    </div>
                </label>
                <?php endforeach; ?>
                <?php if (empty($trainingBatches)): ?>
                <div style="padding:14px;text-align:center;color:#94a3b8;font-size:12px;border:1.5px dashed #e2e8f0;border-radius:8px;">
                    <?= __('No training batches available. Please create one first.') ?>
                </div>
                <?php endif; ?>
            </div>
            <div id="batch-error" style="display:none;color:#e53935;font-size:12px;margin-top:6px;">
                <i class="fas fa-exclamation-circle"></i> <?= __('Please select a training batch.') ?>
            </div>

            <!-- Remarks -->
            <div style="margin-top:14px;">
                <label class="remarks-label"><?= __('Grading Remarks') ?> <span style="font-weight:400;color:#94a3b8;">(<?= __('optional') ?>)</span></label>
                <textarea id="modal-remarks" class="remarks-input" rows="2"
                    placeholder="<?= __('Note the basis for this promotion decision…') ?>"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-cancel" onclick="closeModal()"><?= __('Cancel') ?></button>
            <button class="btn-modal-confirm" onclick="submitPromotion()">
                <i class="fas fa-user-graduate"></i> <?= __('Confirm Promotion') ?>
            </button>
        </div>
    </div>
</div>

<style>
.batch-option:hover { border-color:#43a047; background:#f0fdf4; }
.batch-option.selected { border-color:#2e7d32; background:#f0fdf4; }
</style>

<form id="promoteForm" method="post" style="display:none;">
    <?= $this->Form->hidden('force_promote', ['value' => '1']) ?>
    <input type="hidden" name="grading_remarks" id="form-remarks">
    <input type="hidden" name="trainee_training_batch_id" id="form-batch-id">
    <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken')]) ?>
</form>

<script>
// Card data keyed by candidate ID — built server-side to avoid inline onclick escaping issues
var _cardData = <?php
$map = [];
foreach ($candidates as $c) {
    $fs   = (float)($c->fitness_score ?? 0);
    $is   = (float)($c->interview_score ?? 0);
    $rels = $relCounts[$c->id] ?? [];
    $map[$c->id] = [
        'name'      => $c->name,
        'fs'        => $fs,
        'intScore'  => $is,
        'docPct'    => $docCompleteness[$c->id] ?? 0,
        'copyItems' => [
            ['icon'=>'fa-user',          'label'=>__('Core profile, address, contacts'),                            'has'=>true],
            ['icon'=>'fa-graduation-cap','label'=>__('Education')      .' ('.($rels['educations']??0).')',     'has'=>($rels['educations']??0)>0],
            ['icon'=>'fa-briefcase',     'label'=>__('Work experience') .' ('.($rels['experiences']??0).')',   'has'=>($rels['experiences']??0)>0],
            ['icon'=>'fa-certificate',   'label'=>__('Certifications') .' ('.($rels['certifications']??0).')','has'=>($rels['certifications']??0)>0],
            ['icon'=>'fa-book',          'label'=>__('Courses')        .' ('.($rels['courses']??0).')',        'has'=>($rels['courses']??0)>0],
            ['icon'=>'fa-users',         'label'=>__('Family data')    .' ('.($rels['families']??0).')',       'has'=>($rels['families']??0)>0],
        ],
    ];
}
echo json_encode($map, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>;

var _cid = null;
var _selectedBatchId = null;

function selectBatch(id) {
    _selectedBatchId = id;
    document.querySelectorAll('.batch-option').forEach(function(el) {
        el.classList.toggle('selected', parseInt(el.dataset.id) === id);
    });
    document.getElementById('batch-error').style.display = 'none';
}

function openModal(id) {
    var d = _cardData[id];
    if (!d) return;
    _cid = id;
    _selectedBatchId = null;
    document.getElementById('modal-subtitle').textContent = d.name;
    document.getElementById('modal-remarks').value = '';
    document.querySelectorAll('.batch-option').forEach(function(el) { el.classList.remove('selected'); });
    document.querySelectorAll('input[name="batch_pick"]').forEach(function(el) { el.checked = false; });
    document.getElementById('batch-error').style.display = 'none';

    // Score summary
    var warnings = [];
    if (d.fs < 1)       warnings.push('⚠ <?= __('No physical test recorded') ?>');
    if (d.intScore < 1) warnings.push('⚠ <?= __('No AO interview recorded') ?>');
    if (d.docPct < 100) warnings.push('⚠ <?= __('Documents not 100% complete') ?> (' + d.docPct + '%)');

    var summary = '<strong><?= __('Physical Score') ?>:</strong> ' + (d.fs > 0 ? d.fs.toFixed(1) + '/100' : '<?= __('Not tested') ?>') + '<br>'
                + '<strong><?= __('Interview Score') ?>:</strong> ' + (d.intScore > 0 ? d.intScore.toFixed(1) + '/100' : '<?= __('Not scored') ?>') + '<br>'
                + '<strong><?= __('Document Completeness') ?>:</strong> ' + d.docPct + '%';
    if (warnings.length) {
        summary += '<br><span style="color:#e65100;">' + warnings.join('<br>') + '</span>';
    } else {
        summary += '<br><span style="color:#2e7d32;font-weight:600;">✓ <?= __('All requirements met') ?></span>';
    }
    document.getElementById('modal-score-summary').innerHTML = summary;

    // Copy checklist
    var html = '';
    d.copyItems.forEach(function(item) {
        var checkIcon  = item.has ? 'fa-check-circle' : 'fa-minus-circle';
        var checkColor = item.has ? '#22c55e' : '#f59e0b';
        html += '<div style="display:flex;align-items:center;gap:10px;padding:6px 10px;border-radius:7px;font-size:12px;margin-bottom:4px;background:#f8fafc;">'
              + '<i class="fas ' + checkIcon + '" style="color:' + checkColor + ';width:14px;"></i>'
              + '<span>' + item.label + '</span></div>';
    });
    document.getElementById('modal-copy-list').innerHTML = html;

    document.getElementById('promoteModal').classList.add('show');
}

function closeModal() {
    document.getElementById('promoteModal').classList.remove('show');
    _cid = null;
}

function submitPromotion() {
    if (!_cid) return;
    if (!_selectedBatchId) {
        document.getElementById('batch-error').style.display = 'block';
        return;
    }
    document.getElementById('form-remarks').value  = document.getElementById('modal-remarks').value;
    document.getElementById('form-batch-id').value = _selectedBatchId;
    var form = document.getElementById('promoteForm');
    form.action = '/candidates/do-promote/' + _cid;
    form.submit();
}

document.getElementById('promoteModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

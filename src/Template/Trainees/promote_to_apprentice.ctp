<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trainee[] $trainees        eligible (not yet promoted)
 * @var \App\Model\Entity\Trainee[] $promoted         already promoted
 * @var array $scoreSummary       [trainee_id => [tests,avg_score,passed]]
 * @var int[] $certTraineeIds
 * @var array $batchNames         [batch_id => batch_name]
 * @var array $relCounts          [trainee_id => [educations,experiences,...]]
 * @var int[] $existingApprenticeIds
 */

function pgGrade($avg) {
    if ($avg === null) return null;
    if ($avg >= 81) return ['A', 'Sangat Baik',   '#2E7D32', '#e8f5e9', '#43A047'];
    if ($avg >= 61) return ['B', 'Baik',           '#1565C0', '#e3f2fd', '#1E88E5'];
    if ($avg >= 41) return ['C', 'Cukup',          '#F57F17', '#fffde7', '#F9A825'];
    if ($avg >= 21) return ['D', 'Kurang',         '#E65100', '#fff3e0', '#FB8C00'];
    return               ['E', 'Sangat Kurang',   '#B71C1C', '#fce4ec', '#E53935'];
}
?>
<style>
/* ── Page header ─────────────────────────────────────────────── */
.pta-page-header {
    display:flex; align-items:center; justify-content:space-between;
    flex-wrap:wrap; gap:12px; margin-bottom:22px;
}
.pta-page-header h2 {
    margin:0; font-size:20px; font-weight:700; color:#1a202c;
    display:flex; align-items:center; gap:10px;
}
.pta-page-header h2 i { color:#7C3AED; }

/* ── Section title ───────────────────────────────────────────── */
.section-title {
    font-size:13px; font-weight:700; text-transform:uppercase;
    letter-spacing:1.2px; color:#64748b; margin-bottom:14px;
    display:flex; align-items:center; gap:8px; padding-bottom:8px;
    border-bottom:2px solid #f0f4f8;
}

/* ── Trainee card grid ───────────────────────────────────────── */
.trainee-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(360px,1fr)); gap:18px; margin-bottom:32px; }

.trainee-card {
    background:#fff; border-radius:14px;
    box-shadow:0 3px 16px rgba(0,0,0,0.08);
    overflow:hidden; display:flex; flex-direction:column;
    border:2px solid transparent; transition:border-color 0.2s, box-shadow 0.2s;
}
.trainee-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.12); }
.trainee-card.ready  { border-color:#7C3AED; }
.trainee-card.done   { border-color:#22c55e; opacity:0.82; }

/* Card header strip */
.tc-header {
    padding:14px 16px; display:flex; align-items:center; gap:12px;
}
.tc-avatar {
    width:46px; height:46px; border-radius:50%; flex-shrink:0;
    display:flex; align-items:center; justify-content:center;
    font-size:18px; font-weight:800; color:#fff;
}
.tc-name-block .tc-name { font-size:15px; font-weight:700; color:#1a202c; }
.tc-name-block .tc-code { font-size:11px; color:#94a3b8; margin-top:2px; }
.tc-name-block .tc-batch {
    display:inline-flex; align-items:center; gap:4px; margin-top:4px;
    font-size:10px; font-weight:600; padding:2px 8px; border-radius:10px;
    background:#e0f2fe; color:#0277BD;
}
.tc-status-pill {
    margin-left:auto; flex-shrink:0;
    display:inline-flex; align-items:center; gap:5px;
    padding:4px 12px; border-radius:20px; font-size:11px; font-weight:700;
}
.s-eligible { background:#f3f0ff; color:#7C3AED; }
.s-promoted { background:#dcfce7; color:#15803d; }

/* Data rows */
.tc-body { padding:0 16px 14px; flex:1; }
.tc-divider { height:1px; background:#f0f4f8; margin:10px 0; }

/* Stat pills row */
.tc-stats { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:10px; }
.tc-stat {
    flex:1; min-width:70px; text-align:center;
    background:#f8fafc; border-radius:8px; padding:8px 6px;
    border-left:3px solid;
}
.tc-stat .tsv { font-size:16px; font-weight:800; line-height:1; }
.tc-stat .tsl { font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; color:#94a3b8; margin-top:2px; }

/* Score bar */
.score-bar-wrap { margin:6px 0 8px; }
.score-bar-bg { height:6px; border-radius:3px; background:#e2e8f0; overflow:hidden; }
.score-bar-fill { height:100%; border-radius:3px; background:linear-gradient(90deg,#ef4444 0%,#f59e0b 50%,#22c55e 100%); }
.score-bar-label { display:flex; justify-content:space-between; font-size:10px; color:#94a3b8; margin-bottom:2px; }

/* Grade badge */
.grade-badge {
    display:inline-flex; align-items:center; gap:5px;
    padding:3px 10px; border-radius:8px; font-size:12px; font-weight:800; border:1.5px solid;
}

/* Relation chips */
.tc-relations { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
.rel-chip {
    display:inline-flex; align-items:center; gap:4px;
    font-size:10px; font-weight:600; padding:2px 8px;
    border-radius:10px; background:#f1f5f9; color:#64748b;
}
.rel-chip.has { background:#e0f2fe; color:#0277BD; }

/* Org / Institution row */
.tc-org { display:flex; gap:10px; margin-top:8px; flex-wrap:wrap; }
.tc-org-item { display:flex; align-items:center; gap:5px; font-size:11px; color:#64748b; }
.tc-org-item i { color:#94a3b8; font-size:10px; }
.tc-org-item strong { color:#374151; }

/* Certificate badge */
.cert-badge {
    display:inline-flex; align-items:center; gap:4px;
    padding:2px 8px; border-radius:8px; font-size:10px; font-weight:700;
    background:#f3f0ff; color:#7C3AED; border:1px solid #c4b5fd;
}
.cert-none { background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0; }

/* Card footer with action */
.tc-footer {
    padding:12px 16px; background:#f8fafc; border-top:1px solid #f0f4f8;
    display:flex; align-items:center; justify-content:space-between; gap:10px;
}
.btn-promote {
    display:inline-flex; align-items:center; gap:7px;
    padding:8px 18px; border:none; border-radius:8px;
    font-size:13px; font-weight:700; cursor:pointer; transition:all 0.2s;
    background:linear-gradient(135deg,#7C3AED,#9C27B0);
    color:#fff; box-shadow:0 2px 8px rgba(124,58,237,0.25);
    text-decoration:none;
}
.btn-promote:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(124,58,237,0.35); color:#fff; text-decoration:none; }
.btn-promote:disabled { opacity:0.5; cursor:not-allowed; transform:none; }
.btn-view-sm {
    display:inline-flex; align-items:center; gap:5px;
    padding:6px 12px; border:1.5px solid #e2e8f0; border-radius:7px;
    font-size:12px; font-weight:600; color:#64748b; text-decoration:none; background:#fff;
}
.btn-view-sm:hover { background:#f1f5f9; color:#1a202c; text-decoration:none; }

/* Empty state */
.empty-state { text-align:center; padding:50px 20px; color:#94a3b8; }
.empty-state i { font-size:40px; display:block; margin-bottom:14px; }

/* ── Promotion modal ─────────────────────────────────────────── */
.modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1050; align-items:center; justify-content:center; }
.modal-bg.show { display:flex; }
.modal-box {
    background:#fff; border-radius:16px; padding:0; max-width:500px; width:92%;
    box-shadow:0 20px 60px rgba(0,0,0,0.2); overflow:hidden;
}
.modal-header {
    background:linear-gradient(135deg,#7C3AED,#9C27B0);
    padding:20px 24px; color:#fff;
}
.modal-header h5 { margin:0; font-size:16px; font-weight:700; }
.modal-header p  { margin:4px 0 0; font-size:12px; opacity:0.8; }
.modal-body { padding:22px 24px; }
.modal-footer { padding:14px 24px; background:#f8fafc; border-top:1px solid #f0f4f8; display:flex; justify-content:flex-end; gap:10px; }

.data-copy-list { margin:12px 0 16px; }
.dcl-item {
    display:flex; align-items:center; gap:10px;
    padding:7px 12px; border-radius:8px; font-size:12px; margin-bottom:4px;
    background:#f8fafc;
}
.dcl-item i { color:#22c55e; width:14px; }
.dcl-item.warning i { color:#f59e0b; }

.remarks-label { font-size:12px; font-weight:700; color:#374151; margin-bottom:6px; display:block; }
.remarks-input {
    width:100%; border:1.5px solid #e2e8f0; border-radius:8px;
    padding:9px 12px; font-size:13px; resize:vertical;
    font-family:inherit; transition:border-color 0.2s;
}
.remarks-input:focus { border-color:#7C3AED; outline:none; }

.btn-modal-cancel {
    padding:9px 20px; border:1.5px solid #e2e8f0; border-radius:8px;
    background:#fff; color:#64748b; font-size:13px; font-weight:600; cursor:pointer;
}
.btn-modal-confirm {
    padding:9px 22px; background:linear-gradient(135deg,#7C3AED,#9C27B0);
    color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer;
}
.btn-modal-confirm:hover { opacity:0.9; }
</style>

<!-- Page header -->
<div class="pta-page-header">
    <h2><i class="fas fa-graduation-cap"></i> <?= __('Promote Trainee to Apprentice') ?></h2>
    <?= $this->Html->link('<i class="fas fa-history"></i> ' . __('Promotion History'),
        ['action' => 'promotionHistory'],
        ['escape' => false, 'style' => 'display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:600;color:#64748b;text-decoration:none;background:#fff;']) ?>
</div>

<!-- ── ELIGIBLE TRAINEES ─────────────────────────────────────── -->
<?php if (!empty($trainees)): ?>
<div class="section-title">
    <i class="fas fa-user-clock" style="color:#7C3AED;"></i>
    <?= __('Eligible for Promotion') ?>
    <span style="background:#f3f0ff;color:#7C3AED;padding:2px 10px;border-radius:12px;font-size:11px;"><?= count($trainees) ?></span>
</div>

<div class="trainee-grid">
<?php foreach ($trainees as $t):
    $tid     = $t->id;
    $gender  = (int)($t->master_gender_id ?? 1);
    $init    = mb_strtoupper(mb_substr($t->name, 0, 1));
    $bg      = $gender === 2 ? 'linear-gradient(135deg,#C2185B,#E91E63)' : 'linear-gradient(135deg,#4F46E5,#7C3AED)';
    $sc      = $scoreSummary[$tid] ?? null;
    $hasCert = in_array($tid, $certTraineeIds);
    $batch   = isset($t->trainee_training_batch_id) ? ($batchNames[$t->trainee_training_batch_id] ?? null) : null;
    $rels    = $relCounts[$tid] ?? [];
    $isAlready = in_array($tid, $existingApprenticeIds);
    $avgScore  = $sc ? (float)$sc['avg_score'] : null;
    $gradeInfo = $avgScore !== null ? pgGrade($avgScore) : null;

    // Build relation data copy list for modal
    $copyItems = [
        ['icon'=>'fa-user','label'=> __('Core profile, address, contacts'),'has'=>true],
        ['icon'=>'fa-graduation-cap','label'=> __('Education') . ' (' . ($rels['educations'] ?? 0) . ')', 'has'=>($rels['educations'] ?? 0)>0],
        ['icon'=>'fa-briefcase','label'=> __('Work experience') . ' (' . ($rels['experiences'] ?? 0) . ')','has'=>($rels['experiences'] ?? 0)>0],
        ['icon'=>'fa-certificate','label'=> __('Certifications') . ' (' . ($rels['certifications'] ?? 0) . ')','has'=>($rels['certifications'] ?? 0)>0],
        ['icon'=>'fa-book','label'=> __('Courses') . ' (' . ($rels['courses'] ?? 0) . ')','has'=>($rels['courses'] ?? 0)>0],
        ['icon'=>'fa-users','label'=> __('Family data') . ' (' . ($rels['families'] ?? 0) . ')','has'=>($rels['families'] ?? 0)>0],
    ];
?>
<div class="trainee-card ready">

    <!-- Header -->
    <div class="tc-header">
        <div class="tc-avatar" style="background:<?= $bg ?>;"><?= h($init) ?></div>
        <div class="tc-name-block">
            <div class="tc-name"><?= h($t->name) ?></div>
            <div class="tc-code"><?= h($t->tmm_code) ?></div>
            <?php if ($batch): ?>
            <span class="tc-batch"><i class="fas fa-layer-group" style="font-size:8px;"></i> <?= h($batch) ?></span>
            <?php endif; ?>
        </div>
        <span class="tc-status-pill s-eligible"><i class="fas fa-clock" style="font-size:9px;"></i> <?= __('Eligible') ?></span>
    </div>

    <div class="tc-body">

        <!-- Score stats -->
        <?php if ($sc): ?>
        <div class="tc-stats">
            <div class="tc-stat" style="border-color:#0288D1;">
                <div class="tsv" style="color:#0288D1;"><?= (int)$sc['tests'] ?></div>
                <div class="tsl"><?= __('Tests') ?></div>
            </div>
            <div class="tc-stat" style="border-color:#7C3AED;">
                <div class="tsv" style="color:#7C3AED;"><?= number_format((float)$sc['avg_score'],1) ?></div>
                <div class="tsl"><?= __('Avg') ?></div>
            </div>
            <div class="tc-stat" style="border-color:#22c55e;">
                <div class="tsv" style="color:#22c55e;"><?= (int)$sc['passed'] ?></div>
                <div class="tsl"><?= __('Passed') ?></div>
            </div>
            <?php if ($gradeInfo): [$gl,$gd,$gc,$gbg,$gb] = $gradeInfo; ?>
            <div class="tc-stat" style="border-color:<?= $gb ?>;background:<?= $gbg ?>;">
                <div class="tsv" style="color:<?= $gc ?>;"><?= h($gl) ?></div>
                <div class="tsl"><?= h($gd) ?></div>
            </div>
            <?php endif; ?>
        </div>
        <!-- Score bar -->
        <div class="score-bar-wrap">
            <div class="score-bar-label">
                <span><?= __('Avg Score') ?></span>
                <span><?= number_format((float)$sc['avg_score'],1) ?> / 100</span>
            </div>
            <div class="score-bar-bg">
                <div class="score-bar-fill" style="width:<?= min((float)$sc['avg_score'],100) ?>%;"></div>
            </div>
        </div>
        <?php else: ?>
        <div style="font-size:11px;color:#94a3b8;padding:6px 0;">
            <i class="fas fa-info-circle"></i> <?= __('No test scores recorded yet') ?>
        </div>
        <?php endif; ?>

        <div class="tc-divider"></div>

        <!-- Data that will be copied -->
        <div class="tc-relations">
            <span class="rel-chip <?= ($rels['educations']??0)>0?'has':'' ?>">
                <i class="fas fa-graduation-cap" style="font-size:9px;"></i> <?= $rels['educations']??0 ?> <?= __('Education') ?>
            </span>
            <span class="rel-chip <?= ($rels['experiences']??0)>0?'has':'' ?>">
                <i class="fas fa-briefcase" style="font-size:9px;"></i> <?= $rels['experiences']??0 ?> <?= __('Experience') ?>
            </span>
            <span class="rel-chip <?= ($rels['certifications']??0)>0?'has':'' ?>">
                <i class="fas fa-certificate" style="font-size:9px;"></i> <?= $rels['certifications']??0 ?> <?= __('Certs') ?>
            </span>
            <span class="rel-chip <?= ($rels['courses']??0)>0?'has':'' ?>">
                <i class="fas fa-book" style="font-size:9px;"></i> <?= $rels['courses']??0 ?> <?= __('Courses') ?>
            </span>
            <span class="rel-chip <?= ($rels['families']??0)>0?'has':'' ?>">
                <i class="fas fa-users" style="font-size:9px;"></i> <?= $rels['families']??0 ?> <?= __('Family') ?>
            </span>
            <?php if ($hasCert): ?>
            <span class="rel-chip has cert-badge"><i class="fas fa-award" style="font-size:9px;"></i> <?= __('Certificate') ?></span>
            <?php endif; ?>
        </div>

        <!-- Org / Institution -->
        <div class="tc-org">
            <?php if ($t->vocational_training_institution): ?>
            <div class="tc-org-item"><i class="fas fa-school"></i> <strong><?= h($t->vocational_training_institution->name ?? '') ?></strong></div>
            <?php endif; ?>
            <?php if ($t->acceptance_organization): ?>
            <div class="tc-org-item"><i class="fas fa-building"></i> <strong><?= h($t->acceptance_organization->name ?? '') ?></strong></div>
            <?php endif; ?>
        </div>

    </div><!-- /.tc-body -->

    <!-- Footer action -->
    <div class="tc-footer">
        <?= $this->Html->link('<i class="fas fa-eye"></i> ' . __('Profile'),
            ['action' => 'view', $tid],
            ['class' => 'btn-view-sm', 'escape' => false]) ?>
        <?php if ($isAlready): ?>
        <span style="font-size:12px;color:#22c55e;font-weight:700;">
            <i class="fas fa-check-circle"></i> <?= __('Already promoted') ?>
        </span>
        <?php else: ?>
        <button class="btn-promote" onclick="openModal(<?= $tid ?>)">
            <i class="fas fa-level-up-alt"></i> <?= __('Promote to Apprentice') ?>
        </button>
        <?php endif; ?>
    </div>

</div>
<?php endforeach; ?>
</div><!-- /.trainee-grid -->

<?php else: ?>
<div class="empty-state">
    <i class="fas fa-user-check"></i>
    <p style="font-size:15px;font-weight:600;color:#64748b;"><?= __('No trainees pending promotion.') ?></p>
    <p style="font-size:12px;margin-top:6px;"><?= __('All eligible trainees have already been promoted to apprentice.') ?></p>
</div>
<?php endif; ?>

<!-- ── ALREADY PROMOTED ─────────────────────────────────────── -->
<?php if (!empty($promoted)): ?>
<div class="section-title" style="margin-top:10px;">
    <i class="fas fa-check-double" style="color:#22c55e;"></i>
    <?= __('Already Promoted') ?>
    <span style="background:#dcfce7;color:#15803d;padding:2px 10px;border-radius:12px;font-size:11px;"><?= count($promoted) ?></span>
</div>
<div class="trainee-grid">
<?php foreach ($promoted as $t):
    $tid    = $t->id;
    $gender = (int)($t->master_gender_id ?? 1);
    $init   = mb_strtoupper(mb_substr($t->name, 0, 1));
    $bg     = $gender === 2 ? 'linear-gradient(135deg,#C2185B,#E91E63)' : 'linear-gradient(135deg,#0277BD,#0097A7)';
    $sc     = $scoreSummary[$tid] ?? null;
    $batch  = isset($t->trainee_training_batch_id) ? ($batchNames[$t->trainee_training_batch_id] ?? null) : null;
    $rels   = $relCounts[$tid] ?? [];
    $avgScore  = $sc ? (float)$sc['avg_score'] : null;
    $gradeInfo = $avgScore !== null ? pgGrade($avgScore) : null;
?>
<div class="trainee-card done">
    <div class="tc-header">
        <div class="tc-avatar" style="background:<?= $bg ?>;"><?= h($init) ?></div>
        <div class="tc-name-block">
            <div class="tc-name"><?= h($t->name) ?></div>
            <div class="tc-code"><?= h($t->tmm_code) ?></div>
            <?php if ($batch): ?>
            <span class="tc-batch"><i class="fas fa-layer-group" style="font-size:8px;"></i> <?= h($batch) ?></span>
            <?php endif; ?>
        </div>
        <span class="tc-status-pill s-promoted"><i class="fas fa-check-circle" style="font-size:9px;"></i> <?= __('Promoted') ?></span>
    </div>
    <div class="tc-body">
        <?php if ($sc): ?>
        <div class="tc-stats">
            <div class="tc-stat" style="border-color:#0288D1;"><div class="tsv" style="color:#0288D1;"><?= (int)$sc['tests'] ?></div><div class="tsl"><?= __('Tests') ?></div></div>
            <div class="tc-stat" style="border-color:#7C3AED;"><div class="tsv" style="color:#7C3AED;"><?= number_format($avgScore,1) ?></div><div class="tsl"><?= __('Avg') ?></div></div>
            <div class="tc-stat" style="border-color:#22c55e;"><div class="tsv" style="color:#22c55e;"><?= (int)$sc['passed'] ?></div><div class="tsl"><?= __('Passed') ?></div></div>
            <?php if ($gradeInfo): [$gl,$gd,$gc,$gbg,$gb] = $gradeInfo; ?>
            <div class="tc-stat" style="border-color:<?= $gb ?>;background:<?= $gbg ?>;"><div class="tsv" style="color:<?= $gc ?>;"><?= h($gl) ?></div><div class="tsl"><?= h($gd) ?></div></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <div class="tc-relations" style="margin-top:8px;">
            <?php foreach (['educations'=>__('Education'),'experiences'=>__('Experience'),'certifications'=>__('Certs'),'courses'=>__('Courses'),'families'=>__('Family')] as $k=>$label): ?>
            <span class="rel-chip <?= ($rels[$k]??0)>0?'has':'' ?>"><span><?= $rels[$k]??0 ?></span> <?= $label ?></span>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="tc-footer">
        <?= $this->Html->link('<i class="fas fa-eye"></i> ' . __('Profile'), ['action' => 'view', $tid], ['class' => 'btn-view-sm', 'escape' => false]) ?>
        <span style="font-size:12px;color:#15803d;font-weight:700;"><i class="fas fa-check-circle"></i> <?= __('Apprentice record created') ?></span>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ── Confirm Promotion Modal ───────────────────────────────── -->
<div class="modal-bg" id="promoteModal">
    <div class="modal-box" style="max-width:560px;">
        <div class="modal-header">
            <h5><i class="fas fa-graduation-cap" style="margin-right:8px;"></i><?= __('Confirm Promotion to Apprentice') ?></h5>
            <p id="modal-subtitle"></p>
        </div>
        <div class="modal-body" style="max-height:70vh;overflow-y:auto;">

            <!-- Data copy checklist -->
            <p style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">
                <?= __('Data to be copied') ?>
            </p>
            <div class="data-copy-list" id="modal-copy-list"></div>

            <!-- Apprentice order / placement selection -->
            <div style="margin-top:16px;">
                <p style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:8px;">
                    <i class="fas fa-building" style="color:#7C3AED;"></i>
                    <?= __('Assign to Apprentice Order / Placement') ?> <span style="color:#e53935;">*</span>
                </p>
                <div style="display:flex;flex-direction:column;gap:7px;max-height:200px;overflow-y:auto;padding-right:4px;">
                    <?php foreach ($apprenticeOrders as $o):
                        $totalSeats = (int)($o['male_trainee_number'] ?? 0) + (int)($o['female_trainee_number'] ?? 0);
                        $enrolled   = (int)($o['enrolled'] ?? 0);
                        $remaining  = max(0, $totalSeats - $enrolled);
                        $seatColor  = $remaining > 0 ? '#22c55e' : '#e53935';
                    ?>
                    <label class="aorder-option" data-id="<?= $o['id'] ?>" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:2px solid #e2e8f0;border-radius:9px;cursor:pointer;transition:all 0.15s;">
                        <input type="radio" name="order_pick" value="<?= $o['id'] ?>" style="accent-color:#7C3AED;width:15px;height:15px;flex-shrink:0;"
                            onchange="selectOrder(<?= $o['id'] ?>)">
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:#1a202c;"><?= h($o['title']) ?></div>
                            <div style="font-size:11px;color:#64748b;margin-top:1px;">
                                <?= h($o['reference_file'] ?? '') ?>
                                <?php if ($o['departure_year'] || $o['departure_month']): ?>
                                · <?= h($o['departure_month'] ?? '') ?> <?= h($o['departure_year'] ?? '') ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="text-align:center;flex-shrink:0;">
                            <div style="font-size:15px;font-weight:800;color:<?= $seatColor ?>;"><?= $remaining ?></div>
                            <div style="font-size:9px;color:#94a3b8;text-transform:uppercase;"><?= __('seats left') ?></div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                    <?php if (empty($apprenticeOrders)): ?>
                    <div style="padding:14px;text-align:center;color:#94a3b8;font-size:12px;border:1.5px dashed #e2e8f0;border-radius:8px;">
                        <?= __('No apprentice orders available. Please create one first.') ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div id="orderError" style="display:none;color:#e53935;font-size:12px;margin-top:6px;">
                    <i class="fas fa-exclamation-circle"></i> <?= __('Please select an apprentice order before promoting.') ?>
                </div>
            </div>

            <!-- Remarks -->
            <div style="margin-top:14px;">
                <label class="remarks-label"><?= __('Remarks / Notes') ?> <span style="font-weight:400;color:#94a3b8;">(<?= __('optional') ?>)</span></label>
                <textarea id="modal-remarks" class="remarks-input" rows="2"
                    placeholder="<?= __('Training performance summary, special notes…') ?>"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-cancel" onclick="closeModal()"><?= __('Cancel') ?></button>
            <button class="btn-modal-confirm" onclick="submitPromotion()">
                <i class="fas fa-level-up-alt"></i> <?= __('Confirm Promotion') ?>
            </button>
        </div>
    </div>
</div>

<style>
.aorder-option:hover { border-color:#7C3AED !important; background:#f5f3ff; }
.aorder-option.selected { border-color:#7C3AED !important; background:#f3f0ff; }
</style>

<form id="promoteForm" method="post" style="display:none;">
    <?= $this->Form->hidden('_method', ['value' => 'POST']) ?>
    <?= $this->Form->hidden('grading_remarks', ['id' => 'form-remarks']) ?>
    <?= $this->Form->hidden('apprentice_order_id', ['id' => 'form-order-id']) ?>
    <?= $this->Form->hidden('_csrfToken', ['value' => $this->request->getAttribute('csrfToken')]) ?>
</form>

<script>
// Card data keyed by trainee ID — built server-side to avoid inline onclick escaping issues
var _cardData = <?php
$map = [];
foreach ($trainees as $t) {
    $sc   = $scoreSummary[$t->id] ?? null;
    $rels = $relCounts[$t->id] ?? [];
    $avg  = $sc ? (float)$sc['avg_score'] : null;
    $map[$t->id] = [
        'name'      => $t->name,
        'copyItems' => [
            ['icon'=>'fa-user',          'label'=>__('Core profile, address, contacts'),                               'has'=>true],
            ['icon'=>'fa-graduation-cap','label'=>__('Education')      .' ('.($rels['educations']??0).')',        'has'=>($rels['educations']??0)>0],
            ['icon'=>'fa-briefcase',     'label'=>__('Work experience') .' ('.($rels['experiences']??0).')',      'has'=>($rels['experiences']??0)>0],
            ['icon'=>'fa-certificate',   'label'=>__('Certifications') .' ('.($rels['certifications']??0).')',   'has'=>($rels['certifications']??0)>0],
            ['icon'=>'fa-book',          'label'=>__('Courses')        .' ('.($rels['courses']??0).')',           'has'=>($rels['courses']??0)>0],
            ['icon'=>'fa-users',         'label'=>__('Family data')    .' ('.($rels['families']??0).')',          'has'=>($rels['families']??0)>0],
        ],
    ];
}
echo json_encode($map, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>;

var _tid = null;
var _selectedOrderId = null;

function selectOrder(id) {
    _selectedOrderId = id;
    document.querySelectorAll('.aorder-option').forEach(function(el) {
        el.classList.toggle('selected', parseInt(el.dataset.id) === id);
    });
    document.getElementById('orderError').style.display = 'none';
}

function openModal(id) {
    var d = _cardData[id];
    if (!d) return;
    _tid = id;
    _selectedOrderId = null;
    document.getElementById('modal-subtitle').textContent = d.name;
    document.getElementById('modal-remarks').value = '';
    document.querySelectorAll('.aorder-option').forEach(function(el) { el.classList.remove('selected'); });
    document.querySelectorAll('input[name="order_pick"]').forEach(function(el) { el.checked = false; });
    document.getElementById('orderError').style.display = 'none';

    // Build copy checklist
    var html = '';
    d.copyItems.forEach(function(item) {
        var iconClass = item.has ? '' : ' warning';
        var checkIcon = item.has ? 'fa-check-circle' : 'fa-minus-circle';
        var checkColor = item.has ? '#22c55e' : '#f59e0b';
        html += '<div class="dcl-item' + iconClass + '">'
              + '<i class="fas ' + checkIcon + '" style="color:' + checkColor + ';"></i>'
              + '<span>' + item.label + '</span></div>';
    });
    document.getElementById('modal-copy-list').innerHTML = html;
    document.getElementById('promoteModal').classList.add('show');
}

function closeModal() {
    document.getElementById('promoteModal').classList.remove('show');
    _tid = null;
}

function submitPromotion() {
    if (!_tid) return;
    if (!_selectedOrderId) {
        document.getElementById('orderError').style.display = 'block';
        return;
    }
    document.getElementById('form-remarks').value = document.getElementById('modal-remarks').value;
    document.getElementById('form-order-id').value = _selectedOrderId;
    var form = document.getElementById('promoteForm');
    form.action = '/trainees/do-promote-to-apprentice/' + _tid;
    form.submit();
}

document.getElementById('promoteModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>

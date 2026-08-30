<?php
/**
 * Promotion history — candidates promoted to trainee (enriched)
 *
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet $histories  Trainees (dengan Candidates)
 * @var array $pstats  total / with_order / passed
 */
?>
<style>
.ph-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #00BCD4;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.ph-stat .v{font-size:24px;font-weight:800;color:#0E2A32}
.ph-stat .l{font-size:12px;color:#6c757d}
.stage-chip{display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:700}
.stage-on{background:#d4edda;color:#155724}
.stage-off{background:#f1f3f5;color:#98a2ad}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-history"></i> <?= __('Promotion History') ?> <span style="font-size:13px;color:#6c757d;font-weight:normal"><?= __('Candidate → Trainee') ?></span></h2>
        <div>
            <?= $this->Html->link('<i class="fas fa-arrow-left"></i> ' . __('Back to Promotion Dashboard'), ['action' => 'promoteToTrainee'],
                ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="fas fa-users"></i> ' . __('All Trainees'), '/trainees', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
        </div>
    </div>
</div>

<!-- Ringkasan tahap lanjutan -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="ph-stat"><div class="v"><?= (int)($pstats['total'] ?? 0) ?></div><div class="l"><i class="fas fa-user-graduate"></i> <?= __('Promoted to Trainee') ?></div></div>
    <div class="ph-stat" style="border-left-color:#667eea"><div class="v"><?= (int)($pstats['with_order'] ?? 0) ?></div><div class="l"><i class="fas fa-file-signature"></i> <?= __('Assigned to Order') ?></div></div>
    <div class="ph-stat" style="border-left-color:#28a745"><div class="v"><?= (int)($pstats['passed'] ?? 0) ?></div><div class="l"><i class="fas fa-award"></i> <?= __('Passed → Apprentice') ?></div></div>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="phSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / code…') ?>"
           style="max-width:320px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div style="overflow-x:auto">
<table class="table" style="width:100%;border-collapse:collapse" id="phTable">
    <thead style="background:linear-gradient(135deg,rgba(0,188,212,.1),rgba(0,131,143,.1))">
        <tr>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Trainee') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Name') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Origin Candidate') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4;text-align:center"><?= __('Pipeline Stage') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Grading Remarks') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($histories as $h): ?>
    <tr style="border-bottom:1px solid #e9ecef">
        <td style="padding:9px 12px">
            <?= $this->Html->link('#' . h($h->id), '/trainees/view/' . $h->id, ['class' => 'btn btn-sm btn-outline-info']) ?>
        </td>
        <td style="padding:9px 12px"><strong><?= $this->Html->link(h($h->name), '/trainees/view/' . $h->id, ['style' => 'color:#00838f']) ?></strong></td>
        <td style="padding:9px 12px;font-size:12px">
            <?php if ($h->has('candidate') && $h->candidate): ?>
                <?= $this->Html->link(h($h->candidate->candidate_code ?: $h->candidate->name), ['action' => 'view', $h->candidate->id], ['style' => 'color:#888']) ?>
            <?php else: ?>
                <span style="color:#888"><?= h($h->applicant_code) ?></span>
            <?php endif; ?>
        </td>
        <td style="padding:9px 12px;text-align:center;white-space:nowrap">
            <span class="stage-chip stage-on" title="<?= __('Promoted to trainee') ?>"><?= __('Trainee') ?></span>
            <span class="stage-chip <?= $h->apprenticeship_order_id ? 'stage-on' : 'stage-off' ?>" title="<?= __('Assigned to apprenticeship order') ?>"><?= __('Order') ?></span>
            <span class="stage-chip <?= $h->is_apprenticeship_pass ? 'stage-on' : 'stage-off' ?>" title="<?= __('Passed apprenticeship selection') ?>"><?= __('Apprentice') ?></span>
        </td>
        <td style="padding:9px 12px;font-size:12px"><?= h($h->grading_remarks) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($histories->toArray())): ?>
    <tr><td colspan="5" style="text-align:center;padding:30px;color:#6c757d"><?= __('No promotions recorded yet.') ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<script>
document.getElementById('phSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#phTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

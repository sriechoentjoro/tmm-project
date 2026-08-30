<?php
/**
 * Trainee → Apprentice Promotion History (enriched)
 *
 * @var \App\View\AppView $this
 * @var array[] $histories
 * @var array $hstats total / full_pass / with_coe
 */
?>
<style>
.tph-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #764ba2;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.tph-stat .v{font-size:24px;font-weight:800;color:#2c3e50}
.tph-stat .l{font-size:12px;color:#6c757d}
.tph-pass{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:600}
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
    <h2 style="margin:0;color:#2c3e50"><i class="fas fa-history"></i> <?= __('Trainee → Apprentice Promotion History') ?></h2>
    <div>
        <?= $this->Html->link('<i class="fas fa-arrow-left"></i> ' . __('Back to Promote'), ['action' => 'promoteToApprentice'],
            ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="fas fa-user-tie"></i> ' . __('All Apprentices'), '/apprentices', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
        <?= $this->Html->link('<i class="fas fa-passport"></i> ' . __('COE / Visa'), '/trainee-record-coe-visas', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
    </div>
</div>

<!-- Ringkasan -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="tph-stat"><div class="v"><?= (int)($hstats['total'] ?? 0) ?></div><div class="l"><i class="fas fa-user-tie"></i> <?= __('Apprentices') ?></div></div>
    <div class="tph-stat" style="border-left-color:#28a745"><div class="v"><?= (int)($hstats['full_pass'] ?? 0) ?></div><div class="l"><i class="fas fa-check-double"></i> <?= __('Full Pass (3 stages)') ?></div></div>
    <div class="tph-stat" style="border-left-color:#00BCD4"><div class="v"><?= (int)($hstats['with_coe'] ?? 0) ?></div><div class="l"><i class="fas fa-passport"></i> <?= __('Has COE / Visa Record') ?></div></div>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="tphSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / code…') ?>"
           style="max-width:320px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div style="background:#fff;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,.08);overflow:hidden">
<div style="overflow-x:auto">
<table style="width:100%;border-collapse:collapse" id="tphTable">
    <thead>
        <tr style="background:linear-gradient(135deg,rgba(118,75,162,.12),rgba(102,126,234,.12))">
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef"><?= __('Apprentice') ?></th>
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef"><?= __('Name') ?></th>
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef"><?= __('TMM Code') ?></th>
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef"><?= __('Origin') ?></th>
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef;text-align:center"><?= __('Candidate') ?></th>
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef;text-align:center"><?= __('Training') ?></th>
            <th style="padding:12px 14px;font-size:.8rem;font-weight:700;text-transform:uppercase;border-bottom:2px solid #e9ecef;text-align:center"><?= __('Apprenticeship') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($histories)): ?>
        <tr><td colspan="7" style="text-align:center;color:#6c757d;padding:50px"><?= __('No promotion records yet.') ?></td></tr>
    <?php else: ?>
        <?php foreach ($histories as $row): ?>
        <tr style="border-bottom:1px solid #f1f3f5">
            <td style="padding:10px 14px">
                <?= $this->Html->link('#' . h($row['id']), '/apprentices/view/' . $row['id'], ['class' => 'btn btn-sm btn-outline-info']) ?>
            </td>
            <td style="padding:10px 14px;font-weight:600">
                <?= $this->Html->link(h($row['name']), '/apprentices/view/' . $row['id'], ['style' => 'color:#5b3f8f']) ?>
            </td>
            <td style="padding:10px 14px"><?= h($row['tmm_code']) ?></td>
            <td style="padding:10px 14px;font-size:12px;white-space:nowrap">
                <?php if (!empty($row['trainee_id'])): ?>
                    <?= $this->Html->link(__('Trainee #{0}', $row['trainee_id']), '/trainees/view/' . $row['trainee_id'], ['style' => 'color:#00838f']) ?>
                <?php endif; ?>
                <?php if (!empty($row['applicant_code'])): ?>
                    <span style="color:#98a2ad"> · <?= h($row['applicant_code']) ?></span>
                <?php endif; ?>
            </td>
            <?php foreach (['is_candidate_pass', 'is_training_pass', 'is_apprenticeship_pass'] as $flag): ?>
            <td style="padding:10px 14px;text-align:center">
                <span class="tph-pass" style="background:<?= $row[$flag] ? '#d4edda' : '#f8d7da' ?>;color:<?= $row[$flag] ? '#155724' : '#721c24' ?>">
                    <?= $row[$flag] ? '✓' : '✗' ?>
                </span>
            </td>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>
</div>

<script>
document.getElementById('tphSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#tphTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

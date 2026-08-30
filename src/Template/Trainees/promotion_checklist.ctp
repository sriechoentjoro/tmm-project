<?php
/**
 * Promotion Checklist (enriched) — kesiapan trainee menuju apprentice
 *
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet $trainees
 * @var array $cstats   total / ready / promoted
 * @var array $scoreMap avg skor test training per trainee_id
 */
?>
<style>
.pc-stat{flex:1;min-width:150px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.pc-stat .v{font-size:24px;font-weight:800;color:#212a3e}
.pc-stat .l{font-size:12px;color:#6c757d}
.pass-chip{display:inline-block;padding:3px 10px;border-radius:20px;font-size:.75rem;font-weight:600}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-clipboard-list"></i> <?= __('Promotion Checklist') ?> <span style="font-size:13px;color:#6c757d;font-weight:normal"><?= __('Trainee → Apprentice') ?></span></h2>
        <div>
            <?= $this->Html->link('<i class="fas fa-user-tie"></i> ' . __('Promote to Apprentice'), ['action' => 'promoteToApprentice'], ['class' => 'btn btn-sm btn-outline-success', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="fas fa-chart-bar"></i> ' . __('Test Scores'), '/trainee-training-test-scores', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
        </div>
    </div>
</div>

<!-- Ringkasan -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="pc-stat"><div class="v"><?= (int)($cstats['total'] ?? 0) ?></div><div class="l"><i class="fas fa-users"></i> <?= __('Trainees') ?></div></div>
    <div class="pc-stat" style="border-left-color:#ffc107"><div class="v"><?= (int)($cstats['ready'] ?? 0) ?></div><div class="l"><i class="fas fa-hourglass-half"></i> <?= __('Ready for Promotion') ?></div></div>
    <div class="pc-stat" style="border-left-color:#28a745"><div class="v"><?= (int)($cstats['promoted'] ?? 0) ?></div><div class="l"><i class="fas fa-award"></i> <?= __('Passed Apprenticeship') ?></div></div>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="pcSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / code…') ?>"
           style="max-width:320px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto">
    <table class="table" style="border-collapse:collapse;width:100%" id="pcTable">
        <thead style="background:linear-gradient(135deg,rgba(102,126,234,.15),rgba(118,75,162,.15))">
            <tr>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Actions') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('ID') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('TMM Code') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap"><?= __('Name') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap;text-align:center"><?= __('Training Avg') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap;text-align:center"><?= __('Candidate Pass') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap;text-align:center"><?= __('Training Pass') ?></th>
                <th style="padding:12px;border-bottom:2px solid #667eea;white-space:nowrap;text-align:center"><?= __('Apprenticeship Pass') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trainees as $row): ?>
            <?php
                $sc = $scoreMap[$row['id']] ?? null;
                $ready = $row['is_candidate_pass'] && $row['is_training_pass'] && !$row['is_apprenticeship_pass'];
            ?>
            <tr style="border-bottom:1px solid #e9ecef;<?= $ready ? 'background:#fffbea;' : '' ?>">
                <td style="padding:10px 12px;white-space:nowrap">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $row['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                    <?php if ($ready): ?>
                        <?= $this->Html->link('<i class="fas fa-user-tie"></i>', ['action' => 'promoteToApprentice'], [
                            'class' => 'btn btn-sm btn-outline-success', 'escape' => false,
                            'title' => __('Candidate & training passed — ready to promote'),
                        ]) ?>
                    <?php endif; ?>
                </td>
                <td style="padding:10px 12px"><?= h($row['id']) ?></td>
                <td style="padding:10px 12px"><?= h($row['tmm_code']) ?></td>
                <td style="padding:10px 12px"><strong><?= $this->Html->link(h($row['name']), ['action' => 'view', $row['id']], ['style' => 'color:#4c5bd4']) ?></strong></td>
                <td style="padding:10px 12px;text-align:center">
                    <?php if ($sc): ?>
                        <span title="<?= __('{0} test(s)', $sc['n']) ?>" style="font-weight:700;color:<?= $sc['avg_score'] >= 60 ? '#28a745' : '#dc3545' ?>"><?= h($sc['avg_score']) ?></span>
                        <small style="color:#98a2ad">(<?= h($sc['n']) ?>)</small>
                    <?php else: ?><span style="color:#c3ccd6">—</span><?php endif; ?>
                </td>
                <td style="padding:10px 12px;text-align:center"><?= $row['is_candidate_pass'] ? '<span class="pass-chip" style="background:#d4edda;color:#155724">✓</span>' : '<span class="pass-chip" style="background:#f8d7da;color:#721c24">✗</span>' ?></td>
                <td style="padding:10px 12px;text-align:center"><?= $row['is_training_pass'] ? '<span class="pass-chip" style="background:#d4edda;color:#155724">✓</span>' : '<span class="pass-chip" style="background:#f8d7da;color:#721c24">✗</span>' ?></td>
                <td style="padding:10px 12px;text-align:center"><?= $row['is_apprenticeship_pass'] ? '<span class="pass-chip" style="background:#d4edda;color:#155724">✓</span>' : '<span class="pass-chip" style="background:#f1f3f5;color:#98a2ad">–</span>' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="paginator" style="margin-top:15px">
    <ul class="pagination">
        <?= $this->Paginator->prev('< ' . __('previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' >') ?>
    </ul>
</div>

<script>
document.getElementById('pcSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#pcTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

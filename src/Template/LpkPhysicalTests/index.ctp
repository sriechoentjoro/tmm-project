<?php
/**
 * LPK Physical Test Scoring — candidate list (enriched)
 *
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet $candidates
 * @var \Cake\Datasource\EntityInterface|null $stats
 * @var array $ptMap  jumlah placement test per candidate_id
 * @var array $ivMap  jumlah interview per candidate_id
 * @var array $mcuMap jumlah MCU per candidate_id
 */
$total  = (int)($stats->total ?? 0);
$tested = (int)($stats->tested ?? 0);
?>
<style>
.score-badge{display:inline-block;min-width:48px;padding:3px 10px;border-radius:12px;font-weight:700;font-size:13px;text-align:center}
.score-high{background:#d4edda;color:#155724}
.score-mid{background:#fff3cd;color:#856404}
.score-low{background:#f8d7da;color:#721c24}
.score-none{background:#e9ecef;color:#6c757d}
.pft-table th{padding:10px 12px;border-bottom:2px solid #00BCD4;background:linear-gradient(135deg,rgba(0,188,212,.1),rgba(0,131,143,.1));white-space:nowrap}
.pft-table td{padding:9px 12px;border-bottom:1px solid #e9ecef;vertical-align:middle}
.pft-stat{flex:1;min-width:130px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #00BCD4;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.pft-stat .v{font-size:24px;font-weight:800;color:#0E2A32}
.pft-stat .l{font-size:12px;color:#6c757d;letter-spacing:.4px}
.pipe-chip{display:inline-block;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:700;margin-right:3px;text-decoration:none}
.pipe-on{background:#d4edda;color:#155724;border:1px solid #b7dfc2}
.pipe-off{background:#f1f3f5;color:#98a2ad;border:1px dashed #ced4da}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-dumbbell"></i> <?= __('Physical Fitness Test Scoring') ?></h2>
        <div style="font-size:13px;color:#6c757d;padding:6px 12px;background:#f8f9fa;border-radius:8px">
            <?= __('Score: Push-ups (max 40 pts) + Sit-ups (max 30 pts) + 2.4 km Run (30 pts) = 100 pts total') ?>
        </div>
    </div>
</div>

<!-- Ringkasan -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="pft-stat"><div class="v"><?= $total ?></div><div class="l"><i class="fas fa-users"></i> <?= __('Candidates') ?></div></div>
    <div class="pft-stat" style="border-left-color:#28a745"><div class="v"><?= $tested ?></div><div class="l"><i class="fas fa-check"></i> <?= __('Tested') ?></div></div>
    <div class="pft-stat" style="border-left-color:#dc3545"><div class="v"><?= max(0, $total - $tested) ?></div><div class="l"><i class="fas fa-hourglass-half"></i> <?= __('Not Tested Yet') ?></div></div>
    <div class="pft-stat" style="border-left-color:#667eea"><div class="v"><?= $stats->avg_score !== null ? h($stats->avg_score) : '—' ?></div><div class="l"><i class="fas fa-chart-line"></i> <?= __('Average Score') ?></div></div>
    <div class="pft-stat" style="border-left-color:#20c997"><div class="v" style="font-size:16px;padding-top:6px">
        <span class="score-badge score-high"><?= (int)($stats->high ?? 0) ?></span>
        <span class="score-badge score-mid"><?= (int)($stats->mid ?? 0) ?></span>
        <span class="score-badge score-low"><?= (int)($stats->low ?? 0) ?></span>
    </div><div class="l"><?= __('High ≥75 · Mid 50–74 · Low <50') ?></div></div>
</div>

<!-- Pencarian cepat -->
<div style="margin-bottom:10px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
    <input type="text" id="pftSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / code / LPK…') ?>"
           style="max-width:340px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
    <span style="font-size:12px;color:#6c757d"><?= __('Pipeline chips: PT = Placement Test, IV = Interview, MCU = Medical Check-Up') ?></span>
</div>

<div style="overflow-x:auto">
<table class="table pft-table" style="width:100%;border-collapse:collapse" id="pftTable">
    <thead>
        <tr>
            <th><?= __('Action') ?></th>
            <th><?= __('Code') ?></th>
            <th><?= __('Name') ?></th>
            <th><?= __('LPK') ?></th>
            <th style="text-align:center"><?= __('Push-ups') ?></th>
            <th style="text-align:center"><?= __('Sit-ups') ?></th>
            <th style="text-align:center"><?= __('Run (min:sec)') ?></th>
            <th style="text-align:center"><?= __('Fitness Score /100') ?></th>
            <th style="text-align:center"><?= __('Next Steps') ?></th>
            <th><?= __('Notes') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($candidates as $c): ?>
        <?php
            $fs = $c->fitness_score ?? 0;
            if ($fs == 0 && !$c->fitness_pushups && !$c->fitness_situps) {
                $cls = 'score-none'; $label = __('No test');
            } elseif ($fs >= 75) { $cls = 'score-high'; $label = number_format($fs,1); }
            elseif ($fs >= 50)   { $cls = 'score-mid';  $label = number_format($fs,1); }
            else                 { $cls = 'score-low';  $label = number_format($fs,1); }
            $nPt  = (int)($ptMap[$c->id]  ?? 0);
            $nIv  = (int)($ivMap[$c->id]  ?? 0);
            $nMcu = (int)($mcuMap[$c->id] ?? 0);
        ?>
        <tr>
            <td>
                <?= $this->Html->link('<i class="fas fa-edit"></i> ' . __('Enter Score'), ['action' => 'score', $c->id], [
                    'class' => 'btn btn-sm btn-outline-primary', 'escape' => false
                ]) ?>
            </td>
            <td><?= h($c->candidate_code) ?></td>
            <td><strong><?= $this->Html->link(h($c->name), ['controller' => 'Candidates', 'action' => 'view', $c->id], ['style' => 'color:#00838f']) ?></strong></td>
            <td style="font-size:12px;color:#555"><?= h($c->has('vocational_training_institution') ? ($c->vocational_training_institution->name ?? '') : '') ?></td>
            <td style="text-align:center"><?= $c->fitness_pushups ? h($c->fitness_pushups) : '<span style="color:#ccc">—</span>' ?></td>
            <td style="text-align:center"><?= $c->fitness_situps ? h($c->fitness_situps) : '<span style="color:#ccc">—</span>' ?></td>
            <td style="text-align:center">
                <?php if ($c->fitness_running_minutes || $c->fitness_running_seconds): ?>
                    <?= h($c->fitness_running_minutes) ?>:<?= str_pad($c->fitness_running_seconds, 2, '0', STR_PAD_LEFT) ?>
                <?php else: ?><span style="color:#ccc">—</span><?php endif; ?>
            </td>
            <td style="text-align:center"><span class="score-badge <?= $cls ?>"><?= $label ?></span></td>
            <td style="text-align:center;white-space:nowrap">
                <a class="pipe-chip <?= $nPt ? 'pipe-on' : 'pipe-off' ?>" href="/lpk-candidate-scoring"
                   title="<?= $nPt ? __('{0} placement test(s) recorded', $nPt) : __('No placement test yet') ?>">PT<?= $nPt ? " $nPt" : '' ?></a>
                <a class="pipe-chip <?= $nIv ? 'pipe-on' : 'pipe-off' ?>" href="/candidate-record-interviews"
                   title="<?= $nIv ? __('{0} interview(s) recorded', $nIv) : __('No interview yet') ?>">IV<?= $nIv ? " $nIv" : '' ?></a>
                <a class="pipe-chip <?= $nMcu ? 'pipe-on' : 'pipe-off' ?>" href="/candidate-record-medical-check-ups"
                   title="<?= $nMcu ? __('{0} medical check-up(s) recorded', $nMcu) : __('No MCU yet') ?>">MCU<?= $nMcu ? " $nMcu" : '' ?></a>
            </td>
            <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= h($c->fitness_notes) ?>">
                <?= h($c->fitness_notes) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (iterator_count($candidates) === 0): ?>
        <tr><td colspan="10" style="text-align:center;padding:30px;color:#6c757d"><?= __('No candidates found.') ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<div class="paginator" style="margin-top:15px">
    <ul class="pagination">
        <?= $this->Paginator->prev('‹ ' . __('prev')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('next') . ' ›') ?>
    </ul>
</div>

<script>
document.getElementById('pftSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#pftTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

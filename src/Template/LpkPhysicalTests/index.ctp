<?php
/**
 * LPK Physical Test Scoring — candidate list
 */
?>
<style>
.score-badge{display:inline-block;min-width:48px;padding:3px 10px;border-radius:12px;font-weight:700;font-size:13px;text-align:center}
.score-high{background:#d4edda;color:#155724}
.score-mid{background:#fff3cd;color:#856404}
.score-low{background:#f8d7da;color:#721c24}
.score-none{background:#e9ecef;color:#6c757d}
.pft-table th{padding:10px 12px;border-bottom:2px solid #00BCD4;background:linear-gradient(135deg,rgba(0,188,212,.1),rgba(0,131,143,.1));white-space:nowrap}
.pft-table td{padding:9px 12px;border-bottom:1px solid #e9ecef;vertical-align:middle}
</style>

<div class="index-header" style="margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <h2 style="margin:0"><i class="fas fa-dumbbell"></i> <?= __('Physical Fitness Test Scoring') ?></h2>
        <div style="font-size:13px;color:#6c757d;padding:6px 12px;background:#f8f9fa;border-radius:8px">
            <?= __('Score: Push-ups (max 40 pts) + Sit-ups (max 30 pts) + 2.4 km Run (30 pts) = 100 pts total') ?>
        </div>
    </div>
</div>

<div style="overflow-x:auto">
<table class="table pft-table" style="width:100%;border-collapse:collapse">
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
        ?>
        <tr>
            <td>
                <?= $this->Html->link('<i class="fas fa-edit"></i> ' . __('Enter Score'), ['action' => 'score', $c->id], [
                    'class' => 'btn btn-sm btn-outline-primary', 'escape' => false
                ]) ?>
            </td>
            <td><?= h($c->candidate_code) ?></td>
            <td><strong><?= h($c->name) ?></strong></td>
            <td style="font-size:12px;color:#555"><?= h($c->has('vocational_training_institution') ? ($c->vocational_training_institution->name ?? '') : '') ?></td>
            <td style="text-align:center"><?= $c->fitness_pushups ? h($c->fitness_pushups) : '<span style="color:#ccc">—</span>' ?></td>
            <td style="text-align:center"><?= $c->fitness_situps ? h($c->fitness_situps) : '<span style="color:#ccc">—</span>' ?></td>
            <td style="text-align:center">
                <?php if ($c->fitness_running_minutes || $c->fitness_running_seconds): ?>
                    <?= h($c->fitness_running_minutes) ?>:<?= str_pad($c->fitness_running_seconds, 2, '0', STR_PAD_LEFT) ?>
                <?php else: ?><span style="color:#ccc">—</span><?php endif; ?>
            </td>
            <td style="text-align:center"><span class="score-badge <?= $cls ?>"><?= $label ?></span></td>
            <td style="font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= h($c->fitness_notes) ?>">
                <?= h($c->fitness_notes) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (iterator_count($candidates) === 0): ?>
        <tr><td colspan="9" style="text-align:center;padding:30px;color:#6c757d"><?= __('No candidates found.') ?></td></tr>
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

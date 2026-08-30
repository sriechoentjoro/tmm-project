<?php
/**
 * Candidate Integrated Scoring (enriched)
 * Menggabungkan placement test, interview, MCU + fitness — dengan indikator
 * kesiapan pipeline menuju promosi trainee.
 *
 * @var \App\View\AppView $this
 * @var array $rows
 * @var array $stats
 */
?>
<style>
.lcs-stat{flex:1;min-width:140px;background:#fff;border:1px solid #e3e8ef;border-left:4px solid #667eea;border-radius:10px;padding:12px 16px;box-shadow:0 1px 3px rgba(0,0,0,.05)}
.lcs-stat .v{font-size:24px;font-weight:800;color:#212a3e}
.lcs-stat .l{font-size:12px;color:#6c757d;letter-spacing:.4px}
.lcs-table th{padding:11px 12px;border-bottom:2px solid #667eea;background:linear-gradient(135deg,rgba(102,126,234,.12),rgba(118,75,162,.12));white-space:nowrap}
.lcs-table td{padding:9px 12px;border-bottom:1px solid #e9ecef;vertical-align:middle}
.readiness{display:inline-block;padding:3px 10px;border-radius:12px;font-size:12px;font-weight:700;white-space:nowrap}
.ready-full{background:#d4edda;color:#155724}
.ready-part{background:#fff3cd;color:#856404}
.ready-none{background:#f1f3f5;color:#98a2ad}
.cell-score{font-weight:700}
.cell-empty{color:#c3ccd6}
</style>

<div class="index-header" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
        <div>
            <h2 style="margin:0"><i class="fas fa-clipboard-check"></i> <?= __('Candidate Integrated Scoring') ?></h2>
            <p style="color:#6c757d;margin:4px 0 0"><?= __('Combined placement test, interview, medical check-up and fitness scores per candidate.') ?></p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            <?= $this->Html->link('<i class="fas fa-dumbbell"></i> ' . __('Physical Tests'), '/lpk-physical-tests', ['class' => 'btn btn-sm btn-outline-info', 'escape' => false]) ?>
            <?= $this->Html->link('<i class="fas fa-user-graduate"></i> ' . __('Promote to Trainee'), '/candidates/promote-to-trainee', ['class' => 'btn btn-sm btn-outline-success', 'escape' => false]) ?>
        </div>
    </div>
</div>

<!-- Ringkasan kesiapan -->
<div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:16px">
    <div class="lcs-stat"><div class="v"><?= $stats['total'] ?></div><div class="l"><i class="fas fa-users"></i> <?= __('Candidates') ?></div></div>
    <div class="lcs-stat" style="border-left-color:#28a745"><div class="v"><?= $stats['complete'] ?></div><div class="l"><i class="fas fa-check-double"></i> <?= __('Complete (PT+IV+MCU)') ?></div></div>
    <div class="lcs-stat" style="border-left-color:#ffc107"><div class="v"><?= $stats['partial'] ?></div><div class="l"><i class="fas fa-adjust"></i> <?= __('Partially Scored') ?></div></div>
    <div class="lcs-stat" style="border-left-color:#dc3545"><div class="v"><?= $stats['none'] ?></div><div class="l"><i class="far fa-circle"></i> <?= __('Not Scored Yet') ?></div></div>
    <div class="lcs-stat" style="border-left-color:#20c997"><div class="v" style="font-size:15px;line-height:1.9">
        PT <?= $stats['avg_test'] ?? '—' ?> · IV <?= $stats['avg_interview'] ?? '—' ?> · MCU <?= $stats['avg_mcu'] ?? '—' ?>
    </div><div class="l"><i class="fas fa-chart-line"></i> <?= __('Averages') ?></div></div>
</div>

<div style="margin-bottom:10px">
    <input type="text" id="lcsSearch" class="form-control" placeholder="🔍 <?= __('Filter by name / code…') ?>"
           style="max-width:340px;padding:8px 12px;border-radius:8px;border:1px solid #ced4da">
</div>

<div class="table-scroll-wrapper" style="overflow-x:auto">
    <table class="table lcs-table" style="border-collapse:collapse;width:100%;min-width:1000px" id="lcsTable">
        <thead>
            <tr>
                <th><?= __('Actions') ?></th>
                <th><?= __('Code') ?></th>
                <th><?= __('Name') ?></th>
                <th style="text-align:center"><?= __('Readiness') ?></th>
                <th style="text-align:center"><?= __('Fitness') ?></th>
                <th style="text-align:center"><?= __('Tests') ?></th>
                <th style="text-align:center"><?= __('Avg Test') ?></th>
                <th style="text-align:center"><?= __('Passed') ?></th>
                <th style="text-align:center"><?= __('Interviews') ?></th>
                <th style="text-align:center"><?= __('Avg Interview') ?></th>
                <th style="text-align:center"><?= __('MCU') ?></th>
                <th style="text-align:center"><?= __('Avg MCU') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <?php
                $have = (int)!empty($r['tests']) + (int)!empty($r['interviews']) + (int)!empty($r['mcu_count']);
                if ($have === 3)      { $rCls = 'ready-full'; $rTxt = __('Ready'); }
                elseif ($have > 0)    { $rCls = 'ready-part'; $rTxt = $have . '/3'; }
                else                  { $rCls = 'ready-none'; $rTxt = __('None'); }
            ?>
            <tr>
                <td style="white-space:nowrap">
                    <?= $this->Html->link(__('View'), ['controller' => 'Candidates', 'action' => 'view', $r['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                    <?php if ($have === 3): ?>
                        <?= $this->Html->link('<i class="fas fa-user-graduate"></i>', '/candidates/promote-to-trainee', [
                            'class' => 'btn btn-sm btn-outline-success', 'escape' => false,
                            'title' => __('All scores complete — candidate can be promoted to trainee'),
                        ]) ?>
                    <?php endif; ?>
                </td>
                <td><?= h($r['candidate_code']) ?></td>
                <td><strong><?= $this->Html->link(h($r['name']), ['controller' => 'Candidates', 'action' => 'view', $r['id']], ['style' => 'color:#4c5bd4']) ?></strong></td>
                <td style="text-align:center"><span class="readiness <?= $rCls ?>"><?= $rTxt ?></span></td>
                <td style="text-align:center" class="<?= $r['fitness_score'] ? 'cell-score' : 'cell-empty' ?>"><?= $r['fitness_score'] ? h(number_format($r['fitness_score'], 1)) : '—' ?></td>
                <td style="text-align:center"><?= (int)($r['tests'] ?: 0) ?></td>
                <td style="text-align:center" class="<?= $r['avg_test_score'] !== null ? 'cell-score' : 'cell-empty' ?>"><?= $r['avg_test_score'] !== null ? h($r['avg_test_score']) : '—' ?></td>
                <td style="text-align:center"><?= $r['passed_tests'] !== null ? h($r['passed_tests']) : '—' ?></td>
                <td style="text-align:center"><?= (int)($r['interviews'] ?: 0) ?></td>
                <td style="text-align:center" class="<?= $r['avg_overall_score'] !== null ? 'cell-score' : 'cell-empty' ?>"><?= $r['avg_overall_score'] !== null ? h($r['avg_overall_score']) : '—' ?></td>
                <td style="text-align:center"><?= (int)($r['mcu_count'] ?: 0) ?></td>
                <td style="text-align:center" class="<?= $r['avg_mcu_score'] !== null ? 'cell-score' : 'cell-empty' ?>"><?= $r['avg_mcu_score'] !== null ? h($r['avg_mcu_score']) : '—' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
            <tr><td colspan="12" style="padding:20px;color:#6c757d;text-align:center"><?= __('No candidates yet.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('lcsSearch').addEventListener('input', function () {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#lcsTable tbody tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
    });
});
</script>

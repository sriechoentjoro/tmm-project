<?php
/**
 * @var \App\View\AppView $this
 * @var array $rows
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <h2 style="margin: 0;"><?= __('Candidate Integrated Scoring') ?></h2>
    <p style="color: #6c757d;"><?= __('Combined placement test, interview and medical check-up scores per candidate.') ?></p>
</div>

<div class="table-scroll-wrapper" style="overflow-x: auto;">
    <table class="table" style="border-collapse: collapse; width: 100%; min-width: 900px;">
        <thead style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);">
            <tr>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Actions') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Code') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Name') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Tests') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Avg Test') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Passed') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Interviews') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Avg Interview') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('MCU') ?></th>
                <th style="padding: 12px; border-bottom: 2px solid #667eea;"><?= __('Avg MCU') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $r): ?>
            <tr style="border-bottom: 1px solid #e9ecef;">
                <td style="padding: 10px 12px; white-space: nowrap;">
                    <?= $this->Html->link(__('View'), ['controller' => 'Candidates', 'action' => 'view', $r['id']], ['class' => 'btn btn-sm btn-outline-info']) ?>
                </td>
                <td style="padding: 10px 12px;"><?= h($r['candidate_code']) ?></td>
                <td style="padding: 10px 12px;"><?= h($r['name']) ?></td>
                <td style="padding: 10px 12px;"><?= h($r['tests'] ?: 0) ?></td>
                <td style="padding: 10px 12px;"><?= h($r['avg_test_score'] !== null ? $r['avg_test_score'] : '-') ?></td>
                <td style="padding: 10px 12px;"><?= h($r['passed_tests'] !== null ? $r['passed_tests'] : '-') ?></td>
                <td style="padding: 10px 12px;"><?= h($r['interviews'] ?: 0) ?></td>
                <td style="padding: 10px 12px;"><?= h($r['avg_overall_score'] !== null ? $r['avg_overall_score'] : '-') ?></td>
                <td style="padding: 10px 12px;"><?= h($r['mcu_count'] ?: 0) ?></td>
                <td style="padding: 10px 12px;"><?= h($r['avg_mcu_score'] !== null ? $r['avg_mcu_score'] : '-') ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
            <tr><td colspan="10" style="padding: 15px; color: #6c757d;"><?= __('No candidates yet.') ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

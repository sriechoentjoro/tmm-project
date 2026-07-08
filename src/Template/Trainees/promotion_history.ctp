<?php
/**
 * @var \App\View\AppView $this
 * @var array[] $histories
 */
?>
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px;">
    <h2 style="margin:0; color:#2c3e50;"><i class="fas fa-history"></i> <?= __('Trainee → Apprentice Promotion History') ?></h2>
    <?= $this->Html->link('<i class="fas fa-arrow-left"></i> ' . __('Back to Promote'),
        ['action' => 'promoteToApprentice'],
        ['style' => 'background:none; border:1px solid #6c757d; color:#6c757d; padding:6px 14px; border-radius:6px; font-size:.82rem; text-decoration:none;',
         'escape' => false]) ?>
</div>

<div style="background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); overflow:hidden;">
<div style="overflow-x:auto;">
<table style="width:100%; border-collapse:collapse;">
    <thead>
        <tr style="background:linear-gradient(135deg,rgba(118,75,162,.12),rgba(102,126,234,.12));">
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('Apprentice ID') ?></th>
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('Name') ?></th>
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('TMM Code') ?></th>
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('Applicant Code') ?></th>
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('Candidate Pass') ?></th>
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('Training Pass') ?></th>
            <th style="padding:12px 14px; font-size:.8rem; font-weight:700; text-transform:uppercase; border-bottom:2px solid #e9ecef;"><?= __('Apprentice Pass') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($histories)): ?>
        <tr><td colspan="7" style="text-align:center; color:#6c757d; padding:50px;"><?= __('No promotion records yet.') ?></td></tr>
    <?php else: ?>
        <?php foreach ($histories as $row): ?>
        <tr style="border-bottom:1px solid #f1f3f5;">
            <td style="padding:10px 14px;"><?= h($row['id']) ?></td>
            <td style="padding:10px 14px; font-weight:600;"><?= h($row['name']) ?></td>
            <td style="padding:10px 14px;"><?= h($row['tmm_code']) ?></td>
            <td style="padding:10px 14px;"><?= h($row['applicant_code']) ?></td>
            <td style="padding:10px 14px; text-align:center;">
                <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600;
                    background:<?= $row['is_candidate_pass'] ? '#d4edda' : '#f8d7da' ?>;
                    color:<?= $row['is_candidate_pass'] ? '#155724' : '#721c24' ?>;">
                    <?= $row['is_candidate_pass'] ? '✓' : '✗' ?>
                </span>
            </td>
            <td style="padding:10px 14px; text-align:center;">
                <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600;
                    background:<?= $row['is_training_pass'] ? '#d4edda' : '#f8d7da' ?>;
                    color:<?= $row['is_training_pass'] ? '#155724' : '#721c24' ?>;">
                    <?= $row['is_training_pass'] ? '✓' : '✗' ?>
                </span>
            </td>
            <td style="padding:10px 14px; text-align:center;">
                <span style="display:inline-block; padding:3px 10px; border-radius:20px; font-size:.75rem; font-weight:600;
                    background:<?= $row['is_apprenticeship_pass'] ? '#d4edda' : '#f8d7da' ?>;
                    color:<?= $row['is_apprenticeship_pass'] ? '#155724' : '#721c24' ?>;">
                    <?= $row['is_apprenticeship_pass'] ? '✓' : '✗' ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</div>
</div>

<?php
/**
 * Promotion history — candidates promoted to trainee
 */
?>
<div class="index-header" style="margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between">
        <h2 style="margin:0"><i class="fas fa-history"></i> <?= __('Promotion History') ?></h2>
        <?= $this->Html->link('<i class="fas fa-arrow-left"></i> ' . __('Back to Promotion Dashboard'), ['action' => 'promoteToTrainee'],
            ['class' => 'btn btn-sm btn-outline-secondary', 'escape' => false]) ?>
    </div>
</div>

<div style="overflow-x:auto">
<table class="table" style="width:100%;border-collapse:collapse">
    <thead style="background:linear-gradient(135deg,rgba(0,188,212,.1),rgba(0,131,143,.1))">
        <tr>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Trainee ID') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Name') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Candidate Code') ?></th>
            <th style="padding:10px 12px;border-bottom:2px solid #00BCD4"><?= __('Grading Remarks') ?></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($histories as $h): ?>
    <tr style="border-bottom:1px solid #e9ecef">
        <td style="padding:9px 12px"><?= h($h->id) ?></td>
        <td style="padding:9px 12px"><strong><?= h($h->name) ?></strong></td>
        <td style="padding:9px 12px;color:#888;font-size:12px"><?= h($h->applicant_code) ?></td>
        <td style="padding:9px 12px;font-size:12px"><?= h($h->grading_remarks) ?></td>
    </tr>
    <?php endforeach; ?>
    <?php if (empty($histories->toArray())): ?>
    <tr><td colspan="4" style="text-align:center;padding:30px;color:#6c757d"><?= __('No promotions recorded yet.') ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

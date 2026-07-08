<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeNameCard $traineeNameCard
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Trainee Name Card') ?> #<?= h($traineeNameCard->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $traineeNameCard['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeNameCard['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Trainee Id') ?></th><td style="padding: 8px 12px;"><?= h($traineeNameCard['trainee_id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Issue Date') ?></th><td style="padding: 8px 12px;"><?= h($traineeNameCard['issue_date']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Valid Until') ?></th><td style="padding: 8px 12px;"><?= h($traineeNameCard['valid_until']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Created') ?></th><td style="padding: 8px 12px;"><?= h($traineeNameCard['created']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Modified') ?></th><td style="padding: 8px 12px;"><?= h($traineeNameCard['modified']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ChartOfAccount $chartOfAccount
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Chart Of Account') ?> #<?= h($chartOfAccount->id) ?></h2>
        <div style="display: flex; gap: 8px;">
            <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
            <?= $this->Html->link(__('Edit'), ['action' => 'edit', $chartOfAccount['id']], ['class' => 'btn btn-sm btn-outline-primary']) ?>
        </div>
    </div>
</div>

<?php $this->start('detailPane'); ?>
<div class="content">
    <table class="table" style="width: 100%; border-collapse: collapse;">
        <tbody>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Id') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['id']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Code') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['code']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Name') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['name']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Type') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['type']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Description') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['description']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Is Active') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['is_active']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Created') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['created']) ?></td></tr>
            <tr><th style="padding: 8px 12px; text-align: left; width: 220px;"><?= __('Modified') ?></th><td style="padding: 8px 12px;"><?= h($chartOfAccount['modified']) ?></td></tr>
        </tbody>
    </table>
</div>
<?php $this->end(); ?>

<?= $this->element('view_tabs') ?>

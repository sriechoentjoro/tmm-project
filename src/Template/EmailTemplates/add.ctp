<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EmailTemplate $emailTemplate
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('New Email Template') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<?= $this->element('email_template_form') ?>

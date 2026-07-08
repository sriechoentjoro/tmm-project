<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\EmailTemplate $emailTemplate
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Email Template') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($emailTemplate) ?>
    <fieldset>
        <?php
        echo $this->Form->control('template_key');
        echo $this->Form->control('subject');
        echo $this->Form->control('body_html');
        echo $this->Form->control('body_text');
        echo $this->Form->control('variables');
        echo $this->Form->control('description');
        echo $this->Form->control('is_active');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Menu $menu
 */
?>
<div class="menus form content">
    <div class="index-header" style="margin-bottom: 20px;">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <h2 style="margin: 0;"><?= __('Add Menu') ?></h2>
            <?= $this->Html->link(__('Back to Menus'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </div>
    </div>
    <?= $this->Form->create($menu) ?>
    <?= $this->element('menu_form_fields') ?>
    <div class="form-actions">
        <?= $this->Form->button(__('Save Menu'), ['type' => 'submit']) ?>
        <?= $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn-cand-cancel']) ?>
    </div>
    <?= $this->Form->end() ?>
</div>

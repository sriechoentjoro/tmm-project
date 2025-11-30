<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Role $role
 */
?>
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary"><?= __('Edit Role') ?></h6>
                <div>
                    <?= $this->Form->postLink(
                        __('Delete'),
                        ['action' => 'delete', $role->id],
                        ['confirm' => __('Are you sure you want to delete # {0}?', $role->id), 'class' => 'btn btn-sm btn-danger']
                    ) ?>
                    <?= $this->Html->link(__('List Roles'), ['action' => 'index'], ['class' => 'btn btn-sm btn-secondary']) ?>
                </div>
            </div>
            <div class="card-body">
                <?= $this->Form->create($role) ?>
                <fieldset>
                    <div class="form-group">
                        <?= $this->Form->control('name', ['class' => 'form-control']) ?>
                    </div>
                    <div class="form-group">
                        <?= $this->Form->control('description', ['class' => 'form-control', 'type' => 'textarea']) ?>
                    </div>
                </fieldset>
                <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

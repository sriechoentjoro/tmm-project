<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 */
?>
<div class="candidates form large-9 medium-8 columns content">
    <?= $this->Form->create(null, ['type' => 'post']) ?>
    <fieldset>
        <legend><?= __('Add MCU Result for {0}', h($candidate->full_name)) ?></legend>
        
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->control('mcu_date', [
                    'type' => 'date',
                    'label' => 'MCU Date',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('mcu_exit_date', [
                    'type' => 'date',
                    'label' => 'MCU Exit Date',
                    'class' => 'form-control'
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->control('mcu_result_id', [
                    'options' => [
                        1 => 'Pass',
                        2 => 'Not Pass',
                        3 => 'Pass with Conditions'
                    ],
                    'label' => 'MCU Result',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->control('notes', [
                    'type' => 'textarea',
                    'label' => 'Notes',
                    'class' => 'form-control',
                    'rows' => 4
                ]) ?>
            </div>
        </div>
    </fieldset>
    
    <div class="form-group">
        <?= $this->Form->button(__('Save MCU Result'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link(__('Cancel'), ['action' => 'view', $candidate->id], ['class' => 'btn btn-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
</div>

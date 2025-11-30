<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 */
?>
<div class="candidates form large-9 medium-8 columns content">
    <?= $this->Form->create(null, ['type' => 'post']) ?>
    <fieldset>
        <legend><?= __('Add Interview for {0}', h($candidate->full_name)) ?></legend>
        
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->control('interview_type_id', [
                    'options' => [
                        1 => 'Offline Wonogiri',
                        2 => 'Online Wonogiri',
                        3 => 'Offline Bekasi',
                        4 => 'Online Bekasi'
                    ],
                    'label' => 'Interview Type',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('planned_date', [
                    'type' => 'date',
                    'label' => 'Planned Date',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->control('planned_time', [
                    'type' => 'time',
                    'label' => 'Planned Time',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('apprenticeship_order_id', [
                    'label' => 'Apprenticeship Order ID',
                    'class' => 'form-control'
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->control('male_count', [
                    'type' => 'number',
                    'label' => 'Male Count',
                    'class' => 'form-control',
                    'min' => 0
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->control('female_count', [
                    'type' => 'number',
                    'label' => 'Female Count',
                    'class' => 'form-control',
                    'min' => 0
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->control('reference_file', [
                    'type' => 'file',
                    'label' => 'Reference File',
                    'class' => 'form-control'
                ]) ?>
            </div>
        </div>
    </fieldset>
    
    <div class="form-group">
        <?= $this->Form->button(__('Save Interview'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link(__('Cancel'), ['action' => 'view', $candidate->id], ['class' => 'btn btn-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
</div>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeCertificate $traineeCertificate
 */
?>
<div class="index-header" style="margin-bottom: 20px;">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <h2 style="margin: 0;"><?= __('Edit Trainee Certificate') ?></h2>
        <?= $this->Html->link(__('Back to List'), ['action' => 'index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<div class="content" style="max-width: 760px;">
    <?= $this->Form->create($traineeCertificate) ?>
    <fieldset>
        <?php
        echo $this->Form->control('trainee_id');
        echo $this->Form->control('issue_date', [
            'type' => 'text',
            'class' => 'form-control datepicker',
            'placeholder' => 'YYYY-MM-DD',
            'autocomplete' => 'off'
        ]);
        echo $this->Form->control('certificate_number');
        echo $this->Form->control('result_score');
        echo $this->Form->control('grade');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Save')) ?>
    <?= $this->Form->end() ?>
</div>

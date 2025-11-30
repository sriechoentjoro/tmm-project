<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 */
?>
<div class="candidates form large-9 medium-8 columns content">
    <?= $this->Form->create(null, ['type' => 'file']) ?>
    <fieldset>
        <legend><?= __('Upload Document for {0}', h($candidate->full_name)) ?></legend>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Upload supporting documents for this candidate. Accepted formats: PDF, DOC, DOCX, JPG, PNG (Max 5MB)
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->control('document_file', [
                    'type' => 'file',
                    'label' => 'Select Document',
                    'class' => 'form-control',
                    'required' => true,
                    'accept' => '.pdf,.doc,.docx,.jpg,.jpeg,.png'
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->control('document_type', [
                    'options' => [
                        'passport' => 'Passport',
                        'certificate' => 'Certificate',
                        'transcript' => 'Transcript',
                        'id_card' => 'ID Card',
                        'other' => 'Other'
                    ],
                    'label' => 'Document Type',
                    'class' => 'form-control',
                    'required' => true
                ]) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->control('description', [
                    'type' => 'textarea',
                    'label' => 'Description',
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Brief description of the document'
                ]) ?>
            </div>
        </div>
    </fieldset>
    
    <div class="form-group">
        <?= $this->Form->button(__('Upload Document'), ['class' => 'btn btn-primary']) ?>
        <?= $this->Html->link(__('Cancel'), ['action' => 'view', $candidate->id], ['class' => 'btn btn-secondary']) ?>
    </div>
    <?= $this->Form->end() ?>
</div>

<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.alert-info {
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bce8f1;
}
</style>

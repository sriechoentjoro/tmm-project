<?php
/**
 * Add / edit form for a candidate's submitted document.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\CandidateSubmissionDocument $candidateSubmissionDocument
 * @var array|\Cake\ORM\Query $applicants
 * @var array|\Cake\ORM\Query $documents
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $candidateSubmissionDocument,
    'icon' => 'fa-file-signature',
    'title' => $isEdit ? __('Edit Submission') : __('Record a Submission'),
    'subtitle' => __('Which document a candidate has handed in, and when.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Record Submission'),
    'sections' => [[
        'title' => __('Submission'),
        'icon' => 'fa-file-signature',
        'fields' => [
            'applicant_id' => [
                'label' => __('Candidate'),
                'type' => 'select',
                'options' => $applicants,
                'empty' => __('- choose a candidate -'),
                'required' => true,
                'help' => __('Who handed the document in.'),
            ],
            'document_id' => [
                'label' => __('Document'),
                'type' => 'select',
                'options' => $documents,
                'empty' => __('- choose a document -'),
                'required' => true,
                'help' => __('From the master list of candidate documents.'),
            ],
            // No 'type' on purpose: the column decides how this renders, which
            // is what the scaffolded form did. Forcing a checkbox here would
            // change what gets posted if the column is not a boolean.
            'submitted' => [
                'label' => __('Submitted'),
                'help' => __('Whether the document has actually been received.'),
            ],
            'submission_date' => [
                'label' => __('Submission date'),
                'type' => 'text',
                'class' => 'form-control datepicker',
                'placeholder' => 'YYYY-MM-DD',
                'autocomplete' => 'off',
                'help' => __('The day it arrived.'),
            ],
        ],
    ]],
]) ?>

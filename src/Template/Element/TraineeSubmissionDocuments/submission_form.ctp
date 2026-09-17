<?php
/**
 * Edit form for a trainee's submitted document.
 *
 * Adding one goes through TraineeSubmissionDocuments/add.ctp, which uploads the
 * file and fills file_path, uploaded_by and uploaded_at itself. This page is for
 * correcting a record that already exists, so those three are editable here -
 * and file_path is a path to a file that is already on the server, not an
 * upload box.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeSubmissionDocument $traineeSubmissionDocument
 * @var array $trainees
 * @var array $documents
 * @var array $statuses
 * @var array $users
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? true;
?>
<?= $this->element('entity_form', [
    'entity' => $traineeSubmissionDocument,
    'icon' => 'fa-file-alt',
    'title' => __('Edit Document Record'),
    'subtitle' => __('Correct a document already recorded against a trainee.'),
    'submitLabel' => __('Save Changes'),
    'sections' => [
        [
            'title' => __('Which document, and whose'),
            'icon' => 'fa-user',
            'fields' => [
                'trainee_id' => [
                    'label' => __('Trainee'),
                    'type' => 'select',
                    'options' => $trainees,
                    'empty' => __('- choose a trainee -'),
                    'required' => true,
                    'help' => __('Moving a document to another trainee changes both their checklists.'),
                ],
                'apprenticeship_submission_document_id' => [
                    'label' => __('Document'),
                    'type' => 'select',
                    'options' => $documents,
                    'empty' => __('- choose a document -'),
                    'required' => true,
                    'help' => __('From the master list of documents a trainee must submit.'),
                ],
            ],
        ],
        [
            'title' => __('The file'),
            'icon' => 'fa-paperclip',
            'fields' => [
                'file_path' => [
                    'label' => __('File path'),
                    'type' => 'text',
                    'width' => 12,
                    'placeholder' => 'files/uploads/trainee_documents/…',
                    'help' => __('Where the stored copy lives, relative to webroot. To replace the file itself, upload it again from the document list.'),
                ],
                'master_document_submission_status_id' => [
                    'label' => __('Status'),
                    'type' => 'select',
                    'options' => $statuses,
                    'empty' => __('- choose a status -'),
                    'help' => __('What the summary counts on the list page are grouped by.'),
                ],
                'uploaded_by' => [
                    'label' => __('Uploaded by'),
                    'type' => 'select',
                    'options' => $users,
                    'empty' => __('- choose a user -'),
                    'help' => __('Filled in automatically when the file was uploaded.'),
                ],
                'uploaded_at' => [
                    'label' => __('Uploaded at'),
                    'type' => 'text',
                    'class' => 'form-control datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                ],
                'notes' => [
                    'label' => __('Notes'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'width' => 12,
                ],
            ],
        ],
    ],
]) ?>

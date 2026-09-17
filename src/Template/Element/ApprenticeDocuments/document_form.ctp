<?php
/**
 * Add / edit form for an apprentice's submitted document.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ApprenticeDocument $apprenticeDocument
 * @var array $apprentices
 * @var array $documents
 * @var array $statuses
 * @var array $users
 * @var bool $isEdit
 */
$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $apprenticeDocument,
    'icon' => 'fa-file-alt',
    'title' => $isEdit ? __('Edit Document Record') : __('Record a Document'),
    'subtitle' => __('One row per document an apprentice has handed in, and where it stands.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Record Document'),
    'sections' => [
        [
            'title' => __('Which document, and whose'),
            'icon' => 'fa-user',
            'fields' => [
                'apprentice_id' => [
                    'label' => __('Apprentice'),
                    'type' => 'select',
                    'options' => $apprentices,
                    'empty' => __('- choose an apprentice -'),
                    'required' => true,
                    'help' => __('The person the document belongs to.'),
                ],
                'apprenticeship_submission_document_id' => [
                    'label' => __('Document'),
                    'type' => 'select',
                    'options' => $documents,
                    'empty' => __('- choose a document -'),
                    'required' => true,
                    'help' => __('From the master list of documents an apprentice must submit.'),
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
                    'placeholder' => 'files/uploads/apprentice_documents/…',
                    'help' => __('Where the stored copy lives, relative to webroot.'),
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
                    'help' => __('Who put the file on file.'),
                ],
                'uploaded_at' => [
                    'label' => __('Uploaded at'),
                    'type' => 'text',
                    'class' => 'form-control datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                    'help' => __('The date the document was received.'),
                ],
                'notes' => [
                    'label' => __('Notes'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'width' => 12,
                    'help' => __('Anything the next person to look at this record should know.'),
                ],
            ],
        ],
    ],
]) ?>

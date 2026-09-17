<?php
/**
 * Add / edit form for a trainee certificate.
 *
 * The fields here are the columns the table actually has. The scaffolded form
 * this replaces asked for certificate_number, result_score and grade - none of
 * which exist in cms_tmm_trainee_trainings.trainee_certificates - so everything
 * typed into those three boxes was thrown away on save, while batch_id and
 * notes, which the list, the detail page and the printed certificate all read,
 * could not be entered at all. The column list is the one the controller's own
 * queries use: trainee_id, batch_id, certificate_no, issue_date, notes.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\TraineeCertificate $traineeCertificate
 * @var array $trainees
 * @var array $batches
 * @var bool $isEdit
 */

$isEdit = $isEdit ?? false;
?>
<?= $this->element('entity_form', [
    'entity' => $traineeCertificate,
    'icon' => 'fa-certificate',
    'title' => $isEdit ? __('Edit Certificate') : __('Issue a Certificate'),
    'subtitle' => $isEdit
        ? __('Correct the details of a certificate that has already been issued.')
        : __('Record the certificate a trainee receives at the end of a training batch.'),
    'submitLabel' => $isEdit ? __('Save Changes') : __('Issue Certificate'),
    'sections' => [
        [
            'title' => __('Who it is for'),
            'subtitle' => __('A certificate belongs to one trainee and the batch they trained in.'),
            'icon' => 'fa-user-graduate',
            'fields' => [
                'trainee_id' => [
                    'label' => __('Trainee'),
                    'type' => 'select',
                    'options' => $trainees,
                    'empty' => __('- choose a trainee -'),
                    'required' => true,
                    'help' => __('Their test scores are gathered automatically on the certificate page.'),
                ],
                'batch_id' => [
                    'label' => __('Training batch'),
                    'type' => 'select',
                    'options' => $batches,
                    'empty' => __('- choose a batch -'),
                    'help' => __('Shown on the list and on the printed certificate.'),
                ],
            ],
        ],
        [
            'title' => __('The certificate'),
            'icon' => 'fa-id-card',
            'fields' => [
                'certificate_no' => [
                    'label' => __('Certificate number'),
                    'type' => 'text',
                    'required' => true,
                    'placeholder' => 'TMM/CERT/2026/0001',
                    'help' => __('Printed on the certificate, so give it the number the paper carries.'),
                ],
                'issue_date' => [
                    'label' => __('Issue date'),
                    'type' => 'text',
                    'class' => 'form-control datepicker',
                    'placeholder' => 'YYYY-MM-DD',
                    'autocomplete' => 'off',
                    'help' => __('The date the certificate was handed over.'),
                ],
                'notes' => [
                    'label' => __('Notes'),
                    'type' => 'textarea',
                    'rows' => 3,
                    'width' => 12,
                    'help' => __('Kept on file only - notes do not appear on the printed certificate.'),
                ],
            ],
        ],
    ],
]) ?>

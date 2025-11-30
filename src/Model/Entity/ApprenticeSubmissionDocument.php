<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeSubmissionDocument Entity
 *
 * @property int $id
 * @property int $apprentice_id
 * @property int $apprenticeship_submission_document_id
 * @property string $file_path
 * @property int|null $master_document_submission_status_id
 * @property int|null $uploaded_by
 * @property \Cake\I18n\FrozenTime|null $uploaded_at
 * @property string|null $notes
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Apprentice $apprentice
 * @property \App\Model\Entity\ApprenticeshipSubmissionDocument $apprenticeship_submission_document
 * @property \App\Model\Entity\MasterDocumentSubmissionStatus $master_document_submission_status
 */
class ApprenticeSubmissionDocument extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'apprentice_id' => true,
        'apprenticeship_submission_document_id' => true,
        'file_path' => true,
        'master_document_submission_status_id' => true,
        'uploaded_by' => true,
        'uploaded_at' => true,
        'notes' => true,
        'created' => true,
        'modified' => true,
        'apprentice' => true,
        'apprenticeship_submission_document' => true,
        'master_document_submission_status' => true,
    ];
}

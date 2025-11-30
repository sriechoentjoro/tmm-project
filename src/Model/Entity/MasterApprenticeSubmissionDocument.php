<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterApprenticeSubmissionDocument Entity
 *
 * @property int $id
 * @property string $title
 * @property string|null $title_jp
 * @property string|null $format
 * @property bool|null $is_translation_required
 * @property bool|null $is_required
 * @property int|null $master_apprenticeship_submission_document_category_id
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\MasterApprenticeshipSubmissionDocumentCategory $master_apprenticeship_submission_document_category
 */
class MasterApprenticeSubmissionDocument extends Entity
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
        'title' => true,
        'title_jp' => true,
        'format' => true,
        'is_translation_required' => true,
        'is_required' => true,
        'master_apprenticeship_submission_document_category_id' => true,
        'created' => true,
        'modified' => true,
        'master_apprenticeship_submission_document_category' => true,
    ];
}

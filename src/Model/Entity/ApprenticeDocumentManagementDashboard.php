<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeDocumentManagementDashboard Entity
 *
 * @property int $id
 * @property int $candidate_id
 * @property int|null $total_documents
 * @property int|null $total_ready
 * @property int|null $total_pending
 * @property int|null $total_missing
 * @property \Cake\I18n\FrozenTime|null $last_updated
 *
 * @property \App\Model\Entity\Candidate $candidate
 */
class ApprenticeDocumentManagementDashboard extends Entity
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
        'candidate_id' => true,
        'total_documents' => true,
        'total_ready' => true,
        'total_pending' => true,
        'total_missing' => true,
        'last_updated' => true,
        'candidate' => true,
    ];
}

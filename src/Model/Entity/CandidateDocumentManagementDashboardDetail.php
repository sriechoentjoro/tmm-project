<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * CandidateDocumentManagementDashboardDetail Entity
 *
 * @property int $id
 * @property int $dashboard_id
 * @property string|null $document_type
 * @property string|null $status
 * @property string|null $notes
 * @property \Cake\I18n\FrozenTime|null $updated_at
 *
 * @property \App\Model\Entity\Dashboard $dashboard
 */
class CandidateDocumentManagementDashboardDetail extends Entity
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
        'dashboard_id' => true,
        'document_type' => true,
        'status' => true,
        'notes' => true,
        'updated_at' => true,
        'dashboard' => true,
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterKecamatan Entity
 *
 * @property int $id
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property string $kode_propinsi
 * @property string $kode_kabupaten
 * @property string $kode_kecamatan
 * @property string $title
 *
 * @property \App\Model\Entity\MasterPropinsi $master_propinsi
 * @property \App\Model\Entity\MasterKabupaten $master_kabupaten
 */
class MasterKecamatan extends Entity
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
        'propinsi_id' => true,
        'kabupaten_id' => true,
        'kode_propinsi' => true,
        'kode_kabupaten' => true,
        'kode_kecamatan' => true,
        'title' => true,
        'master_propinsi' => true,
        'master_kabupaten' => true,
    ];
}

<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterKelurahan Entity
 *
 * @property int $id
 * @property int $propinsi_id
 * @property int $kabupaten_id
 * @property int $kecamatan_id
 * @property string $kode_propinsi
 * @property string $kode_kabupaten
 * @property string $kode_kecamatan
 * @property string $kode_kelurahan
 * @property string $kode_pos
 * @property string $title
 *
 * @property \App\Model\Entity\MasterPropinsi $master_propinsi
 * @property \App\Model\Entity\MasterKabupaten $master_kabupaten
 * @property \App\Model\Entity\MasterKecamatan $master_kecamatan
 */
class MasterKelurahan extends Entity
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
        'kecamatan_id' => true,
        'kode_propinsi' => true,
        'kode_kabupaten' => true,
        'kode_kecamatan' => true,
        'kode_kelurahan' => true,
        'kode_pos' => true,
        'title' => true,
        'master_propinsi' => true,
        'master_kabupaten' => true,
        'master_kecamatan' => true,
    ];
}

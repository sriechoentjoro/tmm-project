<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MasterMedicalCheckUpResult Entity
 *
 * @property int $id
 * @property string $title
 * @property int|null $is_fit Whether this result means the candidate is
 *     medically fit. 1 fit, 0 not fit, null nobody has said - and null must
 *     keep nobody out.
 */
class MasterMedicalCheckUpResult extends Entity
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
        // Without this line the form saves the title and drops is_fit without
        // a word: patchEntity() ignores anything not listed here, so an
        // administrator would choose "Tidak Fit", press save, see a success
        // message, and nothing would have changed.
        'is_fit' => true,
    ];
}

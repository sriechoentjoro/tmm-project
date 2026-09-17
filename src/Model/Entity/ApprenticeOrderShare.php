<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * One apprentice order, offered to one vocational training institution.
 *
 * @property int $id
 * @property int $apprentice_order_id
 * @property int $vocational_training_institution_id
 * @property string|null $lpk_name
 * @property string|null $lpk_email
 * @property string $status 'shared' or 'cancelled'
 * @property int|null $shared_by_user_id
 * @property string|null $shared_by_name
 * @property int $notified 1 once the email actually went out.
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $cancelled_at
 */
class ApprenticeOrderShare extends Entity
{
    /**
     * @var array
     */
    protected $_accessible = [
        'apprentice_order_id' => true,
        'vocational_training_institution_id' => true,
        'lpk_name' => true,
        'lpk_email' => true,
        'status' => true,
        'shared_by_user_id' => true,
        'shared_by_name' => true,
        'notified' => true,
        'created' => true,
        'cancelled_at' => true,
    ];

    /**
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }
}

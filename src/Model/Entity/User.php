<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property bool $is_active
 * @property int|null $institution_id
 * @property string|null $institution_type
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Role[] $roles
 * @property array $role_names
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'username' => true,
        'email' => true,
        'password' => true,
        'full_name' => true,
        'is_active' => true,
        'institution_id' => true,
        'institution_type' => true,
        'roles' => true,
        'created' => true,
        'modified' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];

    /**
     * Password setter - automatically hash passwords
     *
     * @param string $password Password to hash
     * @return string|null
     */
    protected function _setPassword(string $password)
    {
        if (strlen($password) > 0) {
            return (new DefaultPasswordHasher())->hash($password);
        }
        return null;
    }

    /**
     * Virtual field to get role names as array
     *
     * @return array
     */
    protected function _getRoleNames()
    {
        if (!empty($this->roles)) {
            return collection($this->roles)->extract('name')->toArray();
        }
        return [];
    }
}

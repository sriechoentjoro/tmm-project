<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Role Entity
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string|null $description
 * @property bool $is_system
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User[] $users
 * @property \App\Model\Entity\DatabaseConnectionScope[] $database_connection_scopes
 * @property \App\Model\Entity\ControllerPermission[] $controller_permissions
 */
class Role extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'name' => true,
        'display_name' => true,
        'description' => true,
        'is_system' => true,
        'users' => true,
        'database_connection_scopes' => true,
        'controller_permissions' => true,
        'created' => true,
        'modified' => true,
    ];
}

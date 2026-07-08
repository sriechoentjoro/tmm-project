<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * RoleMenu Entity
 */
class RoleMenu extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

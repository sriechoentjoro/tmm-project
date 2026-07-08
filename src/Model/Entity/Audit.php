<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Audit Entity
 */
class Audit extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

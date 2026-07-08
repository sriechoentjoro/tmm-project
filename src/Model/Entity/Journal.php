<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Journal Entity
 */
class Journal extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

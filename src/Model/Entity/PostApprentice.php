<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PostApprentice Entity
 */
class PostApprentice extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

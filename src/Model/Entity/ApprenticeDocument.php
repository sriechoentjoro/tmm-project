<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeDocument Entity
 */
class ApprenticeDocument extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

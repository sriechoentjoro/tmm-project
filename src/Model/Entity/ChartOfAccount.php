<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ChartOfAccount Entity
 */
class ChartOfAccount extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

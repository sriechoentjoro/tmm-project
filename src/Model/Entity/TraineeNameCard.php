<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeNameCard Entity
 */
class TraineeNameCard extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

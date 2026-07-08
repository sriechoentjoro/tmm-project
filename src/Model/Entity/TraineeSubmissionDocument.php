<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeSubmissionDocument Entity
 */
class TraineeSubmissionDocument extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

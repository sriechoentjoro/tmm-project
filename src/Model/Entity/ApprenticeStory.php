<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeStory Entity
 */
class ApprenticeStory extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

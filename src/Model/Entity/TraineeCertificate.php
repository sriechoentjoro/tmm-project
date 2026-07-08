<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * TraineeCertificate Entity
 */
class TraineeCertificate extends Entity
{
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}

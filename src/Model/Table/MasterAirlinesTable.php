<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * MasterAirlines Model (apprentice ticketing database)
 */
class MasterAirlinesTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('master_airlines');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentice_document_ticketings';
    }
}

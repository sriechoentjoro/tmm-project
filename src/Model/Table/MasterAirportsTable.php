<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * MasterAirports Model (apprentice ticketing database)
 */
class MasterAirportsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('master_airports');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');
    }

    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentice_document_ticketings';
    }
}

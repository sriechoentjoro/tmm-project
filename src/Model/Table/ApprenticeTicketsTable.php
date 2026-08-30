<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * ApprenticeTickets Model
 *
 * Maps the `tickets` table of the apprentice ticketing database so that
 * ApprenticeFlights associations resolve on the correct connection.
 */
class ApprenticeTicketsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('tickets');
        $this->setDisplayField('ticket_number');
        $this->setPrimaryKey('id');
    }

    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentice_document_ticketings';
    }
}

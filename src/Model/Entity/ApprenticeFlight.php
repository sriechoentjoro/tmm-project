<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ApprenticeFlight Entity
 *
 * @property int $id
 * @property int $apprentice_ticket_id
 * @property int $master_airline_id
 * @property string $flight_number
 * @property int $departure_airport_id
 * @property int $arrival_airport_id
 * @property \Cake\I18n\FrozenTime $departure_datetime
 * @property \Cake\I18n\FrozenTime $arrival_datetime
 *
 * @property \App\Model\Entity\ApprenticeTicket $apprentice_ticket
 * @property \App\Model\Entity\MasterAirline $master_airline
 * @property \App\Model\Entity\DepartureAirport $departure_airport
 * @property \App\Model\Entity\ArrivalAirport $arrival_airport
 */
class ApprenticeFlight extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'apprentice_ticket_id' => true,
        'master_airline_id' => true,
        'flight_number' => true,
        'departure_airport_id' => true,
        'arrival_airport_id' => true,
        'departure_datetime' => true,
        'arrival_datetime' => true,
        'apprentice_ticket' => true,
        'master_airline' => true,
        'departure_airport' => true,
        'arrival_airport' => true,
    ];
}

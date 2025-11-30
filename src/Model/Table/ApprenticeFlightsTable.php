<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ApprenticeFlights Model
 *
 * @property \App\Model\Table\ApprenticeTicketsTable&\Cake\ORM\Association\BelongsTo $ApprenticeTickets
 * @property \App\Model\Table\MasterAirlinesTable&\Cake\ORM\Association\BelongsTo $MasterAirlines
 * @property \App\Model\Table\DepartureAirportsTable&\Cake\ORM\Association\BelongsTo $DepartureAirports
 * @property \App\Model\Table\ArrivalAirportsTable&\Cake\ORM\Association\BelongsTo $ArrivalAirports
 *
 * @method \App\Model\Entity\ApprenticeFlight get($primaryKey, $options = [])
 * @method \App\Model\Entity\ApprenticeFlight newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ApprenticeFlight[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeFlight|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeFlight saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ApprenticeFlight patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeFlight[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ApprenticeFlight findOrCreate($search, callable $callback = null, $options = [])
 */
class ApprenticeFlightsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('apprentice_flights');

        $this->belongsTo('ApprenticeTickets', [
            'foreignKey' => 'apprentice_ticket_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('MasterAirlines', [
            'foreignKey' => 'master_airline_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('DepartureAirports', [
            'foreignKey' => 'departure_airport_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ArrivalAirports', [
            'foreignKey' => 'arrival_airport_id',
            'strategy' => 'select',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->requirePresence('id', 'create')
            ->notEmptyString('id');

        $validator
            ->scalar('flight_number')
            ->maxLength('flight_number', 20)
            ->requirePresence('flight_number', 'create')
            ->notEmptyString('flight_number');

        $validator
            ->dateTime('departure_datetime')
            ->requirePresence('departure_datetime', 'create')
            ->notEmptyDateTime('departure_datetime');

        $validator
            ->dateTime('arrival_datetime')
            ->requirePresence('arrival_datetime', 'create')
            ->notEmptyDateTime('arrival_datetime');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['apprentice_ticket_id'], 'ApprenticeTickets'));
        $rules->add($rules->existsIn(['master_airline_id'], 'MasterAirlines'));
        $rules->add($rules->existsIn(['departure_airport_id'], 'DepartureAirports'));
        $rules->add($rules->existsIn(['arrival_airport_id'], 'ArrivalAirports'));

        return $rules;
    }

    /**
     * Returns the database connection name to use by default.
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'cms_tmm_apprentice_document_ticketings';
    }
}

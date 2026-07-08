<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Tickets Controller
 *
 * @property \App\Model\Table\TicketsTable $Tickets
 */
class TicketsController extends AppController
{
    public function index()
    {
        $this->prepareTicketIndex();
    }

    /**
     * Shared data preparation for the rich ticket list (index + costs).
     */
    protected function prepareTicketIndex()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        // Optional filters
        $filterTrainee = (int)$this->request->getQuery('trainee_id');
        $filterType = (int)$this->request->getQuery('type_id');
        $filterStatus = (int)$this->request->getQuery('status_id');

        $query = $this->Tickets->find();
        if ($filterTrainee) {
            $query->where(['trainee_id' => $filterTrainee]);
        }
        if ($filterType) {
            $query->where(['master_ticket_type_id' => $filterType]);
        }
        if ($filterStatus) {
            $query->where(['master_ticket_status_id' => $filterStatus]);
        }

        $this->paginate = ['order' => ['Tickets.id' => 'DESC']];
        $tickets = $this->paginate($query);

        // Lookup maps to display names instead of raw ids
        $traineeNames = [];
        foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
            $traineeNames[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
        }

        $ticketingConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_document_ticketings');
        $typeNames = [];
        $statusNames = [];
        $currencyNames = [];
        $flightsByTicket = [];
        try {
            foreach ($ticketingConn->execute('SELECT id, title FROM master_ticket_types')->fetchAll('assoc') as $row) {
                $typeNames[$row['id']] = $row['title'];
            }
            foreach ($ticketingConn->execute('SELECT id, title FROM master_ticket_statuses')->fetchAll('assoc') as $row) {
                $statusNames[$row['id']] = str_replace('_', ' ', $row['title']);
            }
            foreach ($ticketingConn->execute(
                'SELECT f.ticket_id, f.flight_number, f.departure_datetime,
                        dep.code AS dep_code, arr.code AS arr_code
                 FROM trainee_flights f
                 LEFT JOIN master_airports dep ON dep.id = f.departure_airport_id
                 LEFT JOIN master_airports arr ON arr.id = f.arrival_airport_id
                 ORDER BY f.departure_datetime ASC'
            )->fetchAll('assoc') as $row) {
                $flightsByTicket[$row['ticket_id']][] = $row;
            }
        } catch (\Exception $e) {
            // display maps are cosmetic - the list still renders with raw values
        }
        try {
            $currencyNames = $locator->get('MasterCurrencies')->find()
                ->enableHydration(false)->all()->combine('id', 'title')->toArray();
        } catch (\Exception $e) {
        }

        // Summary counts by status (unfiltered)
        $summaryQuery = $this->Tickets->find();
        $byStatus = $summaryQuery
            ->select([
                'status_id' => 'master_ticket_status_id',
                'total' => $summaryQuery->func()->count('*'),
            ])
            ->group(['master_ticket_status_id'])
            ->enableHydration(false)
            ->toArray();
        $summary = ['total' => 0];
        foreach ($byStatus as $row) {
            $name = isset($statusNames[$row['status_id']]) ? $statusNames[$row['status_id']] : 'Unknown';
            $summary[$name] = (int)$row['total'];
            $summary['total'] += (int)$row['total'];
        }

        $this->set(compact('tickets', 'traineeNames', 'typeNames', 'statusNames', 'currencyNames',
            'flightsByTicket', 'summary', 'filterTrainee', 'filterType', 'filterStatus'));
    }

    public function view($id = null)
    {
        $ticket = $this->Tickets->get($id);
        $this->set('ticket', $ticket);
    }

    public function add()
    {
        $ticket = $this->Tickets->newEntity();
        if ($this->request->is('post')) {
            $ticket = $this->Tickets->patchEntity($ticket, $this->request->getData());
            if ($this->Tickets->save($ticket)) {
                $this->Flash->success(__('The Ticket has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Ticket could not be saved. Please, try again.'));
        }
        $this->loadTicketFormOptions();
        $this->set('ticket', $ticket);
    }

    public function edit($id = null)
    {
        $ticket = $this->Tickets->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $ticket = $this->Tickets->patchEntity($ticket, $this->request->getData());
            if ($this->Tickets->save($ticket)) {
                $this->Flash->success(__('The Ticket has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Ticket could not be saved. Please, try again.'));
        }
        $this->loadTicketFormOptions();
        $this->loadFlightFormOptions();

        // Flights linked to this ticket, for the sidebar (with ids for inline editing)
        $linkedFlights = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_document_ticketings');
            $linkedFlights = $conn->execute(
                'SELECT f.id, f.ticket_id, f.flight_number, f.departure_datetime, f.arrival_datetime,
                        f.master_airline_id, f.departure_airport_id, f.arrival_airport_id,
                        a.name AS airline_name,
                        dep.code AS dep_code, dep.cityName AS dep_city,
                        arr.code AS arr_code, arr.cityName AS arr_city
                 FROM trainee_flights f
                 LEFT JOIN master_airlines a ON a.id = f.master_airline_id
                 LEFT JOIN master_airports dep ON dep.id = f.departure_airport_id
                 LEFT JOIN master_airports arr ON arr.id = f.arrival_airport_id
                 WHERE f.ticket_id = ' . (int)$id . '
                 ORDER BY f.departure_datetime ASC'
            )->fetchAll('assoc');
        } catch (\Exception $e) {
            // sidebar info only
        }

        $this->set(compact('ticket', 'linkedFlights'));
    }

    /**
     * Option lists shared by the add/edit ticket forms.
     */
    protected function loadTicketFormOptions()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        $traineeOptions = [];
        foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
            $traineeOptions[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
        }

        $typeOptions = [];
        $statusOptions = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_document_ticketings');
            foreach ($conn->execute('SELECT id, title FROM master_ticket_types ORDER BY id')->fetchAll('assoc') as $row) {
                $typeOptions[$row['id']] = $row['title'];
            }
            foreach ($conn->execute('SELECT id, title FROM master_ticket_statuses ORDER BY id')->fetchAll('assoc') as $row) {
                $statusOptions[$row['id']] = str_replace('_', ' ', $row['title']);
            }
        } catch (\Exception $e) {
        }

        $currencyOptions = [];
        try {
            foreach ($locator->get('MasterCurrencies')->find()->order(['title' => 'ASC']) as $currency) {
                $currencyOptions[$currency->id] = $currency->title
                    . ($currency->currency_code ? ' — ' . $currency->currency_code : '');
            }
        } catch (\Exception $e) {
        }

        $this->set(compact('traineeOptions', 'typeOptions', 'statusOptions', 'currencyOptions'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $ticket = $this->Tickets->get($id);
        if ($this->Tickets->delete($ticket)) {
            $this->Flash->success(__('The Ticket has been deleted.'));
        } else {
            $this->Flash->error(__('The Ticket could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Departure management - menu alias for the ticket list
     */
    public function departures()
    {
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Transit routes - manage flight legs (itineraries) per ticket.
     *
     * GET  : itineraries grouped by ticket with layover times
     * POST : action=add_leg    - add a flight leg to a ticket
     *        action=delete_leg - remove a flight leg
     */
    public function transit()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_document_ticketings');
        $flightsTable = $locator->get('TraineeFlights', ['connection' => $conn]);

        if ($this->request->is('post')) {
            $postAction = $this->request->getData('form_action');

            // Forms embedded on the ticket edit page send redirect_ticket so the
            // user returns there instead of the transit page.
            $redirectTicket = (int)$this->request->getData('redirect_ticket');
            $redirectUrl = $redirectTicket ? ['action' => 'edit', $redirectTicket] : ['action' => 'transit'];

            if ($postAction === 'delete_leg') {
                $legId = (int)$this->request->getData('flight_id');
                try {
                    $leg = $flightsTable->get($legId);
                    if ($flightsTable->delete($leg)) {
                        $this->Flash->success(__('The flight leg has been removed.'));
                    } else {
                        $this->Flash->error(__('The flight leg could not be removed.'));
                    }
                } catch (\Exception $e) {
                    $this->Flash->error(__('The flight leg could not be found.'));
                }

                return $this->redirect($redirectUrl);
            }

            if ($postAction === 'add_leg' || $postAction === 'edit_leg') {
                $normalize = function ($value) {
                    // datetime-local inputs post "YYYY-MM-DDTHH:MM"
                    $value = str_replace('T', ' ', (string)$value);
                    return strlen($value) === 16 ? $value . ':00' : $value;
                };

                $data = [
                    'ticket_id' => (int)$this->request->getData('ticket_id'),
                    'master_airline_id' => (int)$this->request->getData('master_airline_id'),
                    'flight_number' => trim((string)$this->request->getData('flight_number')),
                    'departure_airport_id' => (int)$this->request->getData('departure_airport_id'),
                    'arrival_airport_id' => (int)$this->request->getData('arrival_airport_id'),
                    'departure_datetime' => $normalize($this->request->getData('departure_datetime')),
                    'arrival_datetime' => $normalize($this->request->getData('arrival_datetime')),
                ];

                if (!$data['ticket_id'] || !$data['master_airline_id'] || $data['flight_number'] === ''
                    || !$data['departure_airport_id'] || !$data['arrival_airport_id']
                    || !$data['departure_datetime'] || !$data['arrival_datetime']) {
                    $this->Flash->error(__('Please fill in all flight leg fields.'));
                } elseif ($data['departure_airport_id'] === $data['arrival_airport_id']) {
                    $this->Flash->error(__('Departure and arrival airport must be different.'));
                } elseif (strtotime($data['arrival_datetime']) !== false
                    && strtotime($data['departure_datetime']) !== false
                    && strtotime($data['arrival_datetime']) <= strtotime($data['departure_datetime'])) {
                    $this->Flash->error(__('Arrival must be after departure.'));
                } else {
                    if ($postAction === 'edit_leg') {
                        try {
                            $leg = $flightsTable->get((int)$this->request->getData('flight_id'));
                        } catch (\Exception $e) {
                            $this->Flash->error(__('The flight leg could not be found.'));

                            return $this->redirect($redirectUrl);
                        }
                        // Editing never moves the leg to another ticket
                        unset($data['ticket_id']);
                    } else {
                        $leg = $flightsTable->newEntity();
                    }
                    foreach ($data as $field => $value) {
                        $leg->set($field, $value);
                    }
                    if ($flightsTable->save($leg)) {
                        $this->Flash->success($postAction === 'edit_leg'
                            ? __('Flight leg {0} has been updated.', $data['flight_number'])
                            : __('Flight leg {0} has been added.', $data['flight_number']));
                    } else {
                        $this->Flash->error(__('The flight leg could not be saved. Please, try again.'));
                    }
                }

                return $this->redirect($redirectUrl);
            }
        }

        // Trainee names for ticket headers
        $traineeNames = [];
        foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
            $traineeNames[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
        }

        // Tickets (newest first) and their legs, ordered by departure time
        $tickets = $conn->execute(
            'SELECT t.id, t.trainee_id, t.ticket_number, t.booking_ref, t.master_ticket_type_id,
                    ty.title AS type_title, st.title AS status_title
             FROM tickets t
             LEFT JOIN master_ticket_types ty ON ty.id = t.master_ticket_type_id
             LEFT JOIN master_ticket_statuses st ON st.id = t.master_ticket_status_id
             ORDER BY t.id DESC'
        )->fetchAll('assoc');

        $legsByTicket = [];
        foreach ($conn->execute(
            'SELECT f.id, f.ticket_id, f.flight_number, f.departure_datetime, f.arrival_datetime,
                    f.master_airline_id, f.departure_airport_id, f.arrival_airport_id,
                    a.name AS airline_name, a.code AS airline_code,
                    dep.code AS dep_code, dep.cityName AS dep_city,
                    arr.code AS arr_code, arr.cityName AS arr_city
             FROM trainee_flights f
             LEFT JOIN master_airlines a ON a.id = f.master_airline_id
             LEFT JOIN master_airports dep ON dep.id = f.departure_airport_id
             LEFT JOIN master_airports arr ON arr.id = f.arrival_airport_id
             ORDER BY f.departure_datetime ASC, f.id ASC'
        )->fetchAll('assoc') as $row) {
            $legsByTicket[$row['ticket_id']][] = $row;
        }

        $this->loadFlightFormOptions();
        $this->set(compact('tickets', 'legsByTicket', 'traineeNames'));
    }

    /**
     * Airline/airport option lists for the flight leg forms (transit + ticket edit).
     */
    protected function loadFlightFormOptions()
    {
        $airlineOptions = [];
        $airportOptions = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_document_ticketings');
            foreach ($conn->execute('SELECT id, code, name FROM master_airlines ORDER BY name ASC')->fetchAll('assoc') as $row) {
                $airlineOptions[$row['id']] = $row['name'] . ' (' . $row['code'] . ')';
            }
            foreach ($conn->execute(
                "SELECT id, code, cityName, countryName FROM master_airports
                 WHERE code IS NOT NULL AND code <> '' ORDER BY code ASC"
            )->fetchAll('assoc') as $row) {
                $airportOptions[$row['id']] = $row['code'] . ' — ' . $row['cityName']
                    . ($row['countryName'] ? ', ' . $row['countryName'] : '');
            }
        } catch (\Exception $e) {
        }
        $this->set(compact('airlineOptions', 'airportOptions'));
    }

    /**
     * Ticket cost tracking - tickets with prices
     */
    public function costs()
    {
        $this->prepareTicketIndex();
        $this->render('index');
    }
}

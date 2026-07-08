<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeDocuments Controller - landing pages grouping the trainee
 * document modules (departure docs and ticketing)
 */
class TraineeDocumentsController extends AppController
{
    /**
     * Departure documents hub - live readiness overview per trainee across
     * the four pre-departure requirements: passport, COE/visa, medical
     * check-up and flight ticket.
     */
    public function departure()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        // Trainees
        $trainees = [];
        try {
            foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
                $trainees[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }

        // Coverage sets per requirement (trainee ids that have a record)
        $coveredIds = function ($sql, $connection) {
            try {
                $rows = \Cake\Datasource\ConnectionManager::get($connection)->execute($sql)->fetchAll('assoc');

                return array_column($rows, 'trainee_id');
            } catch (\Exception $e) {
                return [];
            }
        };
        $coverage = [
            'passport' => $coveredIds('SELECT DISTINCT trainee_id FROM trainee_record_pasports', 'cms_tmm_trainee_documents'),
            'coe_visa' => $coveredIds('SELECT DISTINCT trainee_id FROM trainee_record_coe_visas', 'cms_tmm_trainee_documents'),
            'medical' => $coveredIds('SELECT DISTINCT trainee_id FROM trainee_record_medical_check_ups', 'cms_tmm_trainee_documents'),
            'ticket' => $coveredIds('SELECT DISTINCT trainee_id FROM tickets', 'cms_tmm_trainee_document_ticketings'),
        ];

        // Per-trainee readiness matrix
        $readiness = [];
        $readyCount = 0;
        foreach ($trainees as $traineeId => $name) {
            $checks = [
                'passport' => in_array($traineeId, $coverage['passport']),
                'coe_visa' => in_array($traineeId, $coverage['coe_visa']),
                'medical' => in_array($traineeId, $coverage['medical']),
                'ticket' => in_array($traineeId, $coverage['ticket']),
            ];
            $done = count(array_filter($checks));
            if ($done === 4) {
                $readyCount++;
            }
            $readiness[] = [
                'id' => $traineeId,
                'name' => $name,
                'checks' => $checks,
                'done' => $done,
            ];
        }
        // Closest to departure first
        usort($readiness, function ($a, $b) {
            return $b['done'] - $a['done'];
        });

        // Module cards with live coverage
        $traineeTotal = count($trainees);
        $sections = [
            [
                'title' => 'Passport Management', 'icon' => 'fa-id-card-alt',
                'controller' => 'TraineeRecordPasports',
                'description' => 'Trainee passport records',
                'covered' => count(array_intersect(array_keys($trainees), $coverage['passport'])),
            ],
            [
                'title' => 'COE/Visa Management', 'icon' => 'fa-stamp',
                'controller' => 'TraineeRecordCoeVisas',
                'description' => 'Certificate of Eligibility and visa records',
                'covered' => count(array_intersect(array_keys($trainees), $coverage['coe_visa'])),
            ],
            [
                'title' => 'Health Certificates', 'icon' => 'fa-heartbeat',
                'controller' => 'TraineeRecordMedicalCheckUps',
                'description' => 'Medical check-up records',
                'covered' => count(array_intersect(array_keys($trainees), $coverage['medical'])),
            ],
            [
                'title' => 'Tickets & Flights', 'icon' => 'fa-plane-departure',
                'controller' => 'Tickets',
                'description' => 'Flight tickets and itineraries',
                'covered' => count(array_intersect(array_keys($trainees), $coverage['ticket'])),
            ],
        ];

        // Official required-documents reference (what goes in the departure folder)
        $requiredDocuments = [];
        try {
            $requiredDocuments = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_documents')
                ->execute('SELECT title, format, is_required FROM master_trainee_departure_documents ORDER BY id')
                ->fetchAll('assoc');
        } catch (\Exception $e) {
        }

        $summary = [
            'trainees' => $traineeTotal,
            'ready' => $readyCount,
            'notReady' => $traineeTotal - $readyCount,
        ];

        $this->set(compact('sections', 'readiness', 'summary', 'requiredDocuments', 'traineeTotal'));
        $this->set('pageTitle', 'Departure Documents');
    }

    /**
     * Ticketing and transit hub
     */
    public function ticketing()
    {
        $sections = [
            ['title' => 'Tickets', 'icon' => 'fa-plane-departure', 'controller' => 'Tickets', 'description' => 'Flight ticket records'],
            ['title' => 'Transit Routes', 'icon' => 'fa-route', 'controller' => 'Tickets', 'action' => 'transit', 'description' => 'Trainee flight legs and transit routes'],
            ['title' => 'Ticket Costs', 'icon' => 'fa-money-bill-wave', 'controller' => 'Tickets', 'action' => 'costs', 'description' => 'Ticket cost tracking'],
        ];
        $this->set(compact('sections'));
        $this->set('pageTitle', 'Ticketing & Transit');
        $this->render('hub');
    }
}

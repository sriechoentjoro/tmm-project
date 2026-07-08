<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;

/**
 * TraineeNameCards Controller — print-only, no DB records needed
 */
class TraineeNameCardsController extends AppController
{
    public function index()
    {
        $conn = ConnectionManager::get('cms_tmm_trainee_trainings');

        // All batches for the group-print selector
        $batches = $conn->execute(
            'SELECT ttb.id, ttb.batch_name,
                    COUNT(t.id) AS trainee_count
             FROM cms_tmm_trainee_trainings.training_batches ttb
             LEFT JOIN cms_tmm_trainees.trainees t ON t.trainee_training_batch_id = ttb.id
             GROUP BY ttb.id, ttb.batch_name
             ORDER BY ttb.id DESC'
        )->fetchAll('assoc');

        // All trainees with their batch name
        $trainees = $conn->execute(
            'SELECT t.id, t.name, t.tmm_code, t.master_gender_id,
                    ttb.id AS batch_id, ttb.batch_name
             FROM cms_tmm_trainees.trainees t
             LEFT JOIN cms_tmm_trainee_trainings.training_batches ttb ON ttb.id = t.trainee_training_batch_id
             ORDER BY ttb.id, t.name'
        )->fetchAll('assoc');

        $this->set(compact('trainees', 'batches'));
    }

    /** Print a single trainee's name card */
    public function printCard($traineeId = null)
    {
        $conn = ConnectionManager::get('cms_tmm_trainee_trainings');
        $row  = $conn->execute(
            'SELECT t.id, t.name, t.tmm_code, t.master_gender_id, t.identity_number,
                    ttb.batch_name
             FROM cms_tmm_trainees.trainees t
             LEFT JOIN cms_tmm_trainee_trainings.training_batches ttb ON ttb.id = t.trainee_training_batch_id
             WHERE t.id = ?',
            [$traineeId]
        )->fetch('assoc');

        if (!$row) {
            throw new \Cake\Http\Exception\NotFoundException();
        }
        $this->viewBuilder()->setLayout('print');
        $this->set(compact('row'));
    }

    /** Print all trainees in a batch */
    public function printBatch($batchId = null)
    {
        $conn = ConnectionManager::get('cms_tmm_trainee_trainings');

        $batch = $conn->execute(
            'SELECT id, batch_name FROM cms_tmm_trainee_trainings.training_batches WHERE id = ?',
            [$batchId]
        )->fetch('assoc');

        if (!$batch) {
            throw new \Cake\Http\Exception\NotFoundException();
        }

        $trainees = $conn->execute(
            'SELECT t.id, t.name, t.tmm_code, t.master_gender_id, t.identity_number,
                    ttb.batch_name
             FROM cms_tmm_trainees.trainees t
             LEFT JOIN cms_tmm_trainee_trainings.training_batches ttb ON ttb.id = t.trainee_training_batch_id
             WHERE t.trainee_training_batch_id = ?
             ORDER BY t.name',
            [$batchId]
        )->fetchAll('assoc');

        $this->viewBuilder()->setLayout('print');
        $this->set(compact('batch', 'trainees'));
    }
}

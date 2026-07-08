<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * LpkPhysicalTests Controller
 *
 * Lists candidates and allows LPK staff to record physical fitness test scores.
 * Scores (push-ups, sit-ups, running) are saved to the candidates table.
 */
class LpkPhysicalTestsController extends AppController
{
    use \App\Controller\LpkDataFilterTrait;

    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Candidates');
    }

    public function index()
    {
        $candidatesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Candidates');
        $query = $this->getInstitutionFilteredQuery('Candidates', 'vocational_training_institution_id');
        $query = $query->select([
                'Candidates.id', 'Candidates.candidate_code', 'Candidates.name',
                'Candidates.vocational_training_institution_id',
                'Candidates.fitness_pushups', 'Candidates.fitness_situps',
                'Candidates.fitness_running_minutes', 'Candidates.fitness_running_seconds',
                'Candidates.fitness_score', 'Candidates.fitness_notes',
                'Candidates.status_flag',
            ])
            ->contain(['VocationalTrainingInstitutions'])
            ->order(['Candidates.name' => 'ASC']);

        $this->paginate = ['limit' => 50];
        $candidates = $this->paginate($query);
        $this->set(compact('candidates'));
    }

    /**
     * Score physical fitness test for a candidate
     */
    public function score($candidateId = null)
    {
        $candidatesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Candidates');
        $candidate = $candidatesTable->get($candidateId, ['contain' => ['VocationalTrainingInstitutions']]);

        // LPK access check
        if ($this->hasRole('lpk-penyangga')) {
            $myLpkId = $this->getUserInstitutionId();
            if ($candidate->vocational_training_institution_id != $myLpkId) {
                $this->Flash->error(__('Access denied: this candidate does not belong to your institution.'));
                return $this->redirect(['action' => 'index']);
            }
        }

        // tmm-recruitment is view-only
        $viewOnly = $this->hasRole('tmm-recruitment');
        if ($viewOnly && $this->request->is(['post', 'put'])) {
            $this->Flash->error(__('Your role does not have permission to change scores.'));
            return $this->redirect(['action' => 'score', $candidateId]);
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            $pushups  = (int)($data['fitness_pushups'] ?? 0);
            $situps   = (int)($data['fitness_situps'] ?? 0);
            $runMins  = (int)($data['fitness_running_minutes'] ?? 0);
            $runSecs  = (int)($data['fitness_running_seconds'] ?? 0);

            // Score formula (100 pts total)
            // Push-ups: 40 pts max  (capped at 40 reps)
            $pushScore = min($pushups, 40) * 1.0;
            // Sit-ups: 30 pts max  (capped at 30 reps)
            $sitScore  = min($situps, 30) * 1.0;
            // Running 2.4km: 30 pts based on total seconds
            $totalSecs = ($runMins * 60) + $runSecs;
            if ($totalSecs <= 600)        $runScore = 30;
            elseif ($totalSecs <= 660)    $runScore = 25;
            elseif ($totalSecs <= 720)    $runScore = 20;
            elseif ($totalSecs <= 780)    $runScore = 15;
            elseif ($totalSecs <= 840)    $runScore = 10;
            else                          $runScore = 5;

            $fitnessScore = round($pushScore + $sitScore + $runScore, 2);

            $candidate->fitness_pushups         = $pushups;
            $candidate->fitness_situps          = $situps;
            $candidate->fitness_running_minutes = $runMins;
            $candidate->fitness_running_seconds = $runSecs;
            $candidate->fitness_score           = $fitnessScore;
            $candidate->fitness_notes           = $data['fitness_notes'] ?? null;

            if ($candidatesTable->save($candidate)) {
                $this->Flash->success(__('Physical test score saved. Fitness score: {0}/100', $fitnessScore));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Could not save score. Please try again.'));
        }

        $this->set(compact('candidate', 'viewOnly'));
    }
}

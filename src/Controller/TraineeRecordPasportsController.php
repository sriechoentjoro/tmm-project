<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeRecordPasports Controller
 *
 * @property \App\Model\Table\TraineeRecordPasportsTable $TraineeRecordPasports
 */
class TraineeRecordPasportsController extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['TraineeRecordPasports.id' => 'DESC']];
        $traineeRecordPasports = $this->paginate($this->TraineeRecordPasports);

        // Trainee names + passport coverage (who still has no passport record)
        $traineeNames = $this->getTraineeNames();
        $coveredTraineeIds = $this->TraineeRecordPasports->find()
            ->select(['trainee_id'])
            ->enableHydration(false)
            ->extract('trainee_id')
            ->toList();
        $missingTrainees = array_diff_key($traineeNames, array_flip($coveredTraineeIds));

        $summary = [
            'trainees' => count($traineeNames),
            'covered' => count($traineeNames) - count($missingTrainees),
            'missing' => count($missingTrainees),
            'records' => $this->TraineeRecordPasports->find()->count(),
        ];

        $this->set(compact('traineeRecordPasports', 'traineeNames', 'missingTrainees', 'summary'));
    }

    /**
     * Helper: trainee id => "Name (TMM-code)"
     */
    protected function getTraineeNames()
    {
        $names = [];
        try {
            $trainees = \Cake\ORM\TableRegistry::getTableLocator()->get('Trainees')
                ->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']);
            foreach ($trainees as $trainee) {
                $names[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }

        return $names;
    }

    public function view($id = null)
    {
        $traineeRecordPasport = $this->TraineeRecordPasports->get($id);
        $this->set('traineeRecordPasport', $traineeRecordPasport);
    }

    public function add()
    {
        $traineeRecordPasport = $this->TraineeRecordPasports->newEntity();
        if ($this->request->is('post')) {
            $traineeRecordPasport = $this->TraineeRecordPasports->patchEntity($traineeRecordPasport, $this->request->getData());
            if ($this->TraineeRecordPasports->save($traineeRecordPasport)) {
                $this->Flash->success(__('The Trainee Record Pasport has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Trainee Record Pasport could not be saved. Please, try again.'));
        }
        // Allow prefilling the trainee from ?trainee_id= (used by the "missing" list)
        $preselectedTrainee = (int)$this->request->getQuery('trainee_id');
        $traineeOptions = $this->getTraineeNames();
        $this->set(compact('traineeRecordPasport', 'traineeOptions', 'preselectedTrainee'));
    }

    public function edit($id = null)
    {
        $traineeRecordPasport = $this->TraineeRecordPasports->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $traineeRecordPasport = $this->TraineeRecordPasports->patchEntity($traineeRecordPasport, $this->request->getData());
            if ($this->TraineeRecordPasports->save($traineeRecordPasport)) {
                $this->Flash->success(__('The Trainee Record Pasport has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Trainee Record Pasport could not be saved. Please, try again.'));
        }
        $traineeOptions = $this->getTraineeNames();
        $this->set(compact('traineeRecordPasport', 'traineeOptions'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeRecordPasport = $this->TraineeRecordPasports->get($id);
        if ($this->TraineeRecordPasports->delete($traineeRecordPasport)) {
            $this->Flash->success(__('The Trainee Record Pasport has been deleted.'));
        } else {
            $this->Flash->error(__('The Trainee Record Pasport could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

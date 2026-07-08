<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeRecordCoeVisas Controller
 *
 * @property \App\Model\Table\TraineeRecordCoeVisasTable $TraineeRecordCoeVisas
 */
class TraineeRecordCoeVisasController extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['TraineeRecordCoeVisas.id' => 'DESC']];
        $traineeRecordCoeVisas = $this->paginate($this->TraineeRecordCoeVisas);

        // Trainee names + COE/visa coverage (who still has no record)
        $traineeNames = $this->getTraineeNames();
        $coveredTraineeIds = $this->TraineeRecordCoeVisas->find()
            ->select(['trainee_id'])
            ->enableHydration(false)
            ->extract('trainee_id')
            ->toList();
        $missingTrainees = array_diff_key($traineeNames, array_flip($coveredTraineeIds));

        $summary = [
            'trainees' => count($traineeNames),
            'covered' => count($traineeNames) - count($missingTrainees),
            'missing' => count($missingTrainees),
            'records' => $this->TraineeRecordCoeVisas->find()->count(),
        ];

        $coeTypeNames = $this->getCoeTypeNames();

        $this->set(compact('traineeRecordCoeVisas', 'traineeNames', 'missingTrainees', 'summary', 'coeTypeNames'));
    }

    public function view($id = null)
    {
        $traineeRecordCoeVisa = $this->TraineeRecordCoeVisas->get($id);
        $this->set('traineeRecordCoeVisa', $traineeRecordCoeVisa);
    }

    public function add()
    {
        $traineeRecordCoeVisa = $this->TraineeRecordCoeVisas->newEntity();
        if ($this->request->is('post')) {
            $traineeRecordCoeVisa = $this->TraineeRecordCoeVisas->patchEntity($traineeRecordCoeVisa, $this->request->getData());
            if ($this->TraineeRecordCoeVisas->save($traineeRecordCoeVisa)) {
                $this->Flash->success(__('The COE / Visa record has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The COE / Visa record could not be saved. Please, try again.'));
        }
        // Allow prefilling the trainee from ?trainee_id= (used by the "missing" list)
        $preselectedTrainee = (int)$this->request->getQuery('trainee_id');
        $traineeOptions = $this->getTraineeNames();
        $coeTypeOptions = $this->getCoeTypeNames();
        $this->set(compact('traineeRecordCoeVisa', 'traineeOptions', 'coeTypeOptions', 'preselectedTrainee'));
    }

    public function edit($id = null)
    {
        $traineeRecordCoeVisa = $this->TraineeRecordCoeVisas->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $traineeRecordCoeVisa = $this->TraineeRecordCoeVisas->patchEntity($traineeRecordCoeVisa, $this->request->getData());
            if ($this->TraineeRecordCoeVisas->save($traineeRecordCoeVisa)) {
                $this->Flash->success(__('The COE / Visa record has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The COE / Visa record could not be saved. Please, try again.'));
        }
        $traineeOptions = $this->getTraineeNames();
        $coeTypeOptions = $this->getCoeTypeNames();
        $this->set(compact('traineeRecordCoeVisa', 'traineeOptions', 'coeTypeOptions'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeRecordCoeVisa = $this->TraineeRecordCoeVisas->get($id);
        if ($this->TraineeRecordCoeVisas->delete($traineeRecordCoeVisa)) {
            $this->Flash->success(__('The COE / Visa record has been deleted.'));
        } else {
            $this->Flash->error(__('The COE / Visa record could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
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

    /**
     * Helper: COE type id => title (COE Biasa / COE Electronic)
     */
    protected function getCoeTypeNames()
    {
        $types = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_documents');
            foreach ($conn->execute('SELECT id, title FROM master_trainee_coe_types ORDER BY id')->fetchAll('assoc') as $row) {
                $types[$row['id']] = $row['title'];
            }
        } catch (\Exception $e) {
        }

        return $types;
    }
}

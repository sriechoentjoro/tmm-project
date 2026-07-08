<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeRecordMedicalCheckUps Controller
 *
 * @property \App\Model\Table\TraineeRecordMedicalCheckUpsTable $TraineeRecordMedicalCheckUps
 */
class TraineeRecordMedicalCheckUpsController extends AppController
{
    public function index()
    {
        $this->paginate = ['order' => ['TraineeRecordMedicalCheckUps.id' => 'DESC']];
        $traineeRecordMedicalCheckUps = $this->paginate($this->TraineeRecordMedicalCheckUps);

        // Trainee names + MCU coverage (who has no medical check-up yet)
        $traineeNames = $this->getTraineeNames();
        $coveredTraineeIds = $this->TraineeRecordMedicalCheckUps->find()
            ->select(['trainee_id'])
            ->enableHydration(false)
            ->extract('trainee_id')
            ->toList();
        $missingTrainees = array_diff_key($traineeNames, array_flip($coveredTraineeIds));

        $summary = [
            'trainees' => count($traineeNames),
            'covered' => count($traineeNames) - count($missingTrainees),
            'missing' => count($missingTrainees),
            'records' => $this->TraineeRecordMedicalCheckUps->find()->count(),
        ];

        $resultNames = $this->getResultNames();

        $this->set(compact('traineeRecordMedicalCheckUps', 'traineeNames', 'missingTrainees', 'summary', 'resultNames'));
    }

    public function view($id = null)
    {
        $traineeRecordMedicalCheckUp = $this->TraineeRecordMedicalCheckUps->get($id);
        $this->set('traineeRecordMedicalCheckUp', $traineeRecordMedicalCheckUp);
    }

    public function add()
    {
        $traineeRecordMedicalCheckUp = $this->TraineeRecordMedicalCheckUps->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            if ($this->handleMcuUpload($data)) {
                $traineeRecordMedicalCheckUp = $this->TraineeRecordMedicalCheckUps->patchEntity($traineeRecordMedicalCheckUp, $data);
                if ($this->TraineeRecordMedicalCheckUps->save($traineeRecordMedicalCheckUp)) {
                    $this->Flash->success(__('The medical check-up record has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The medical check-up record could not be saved. Please, try again.'));
            }
        }
        // Allow prefilling the trainee from ?trainee_id= (used by the "missing" list)
        $preselectedTrainee = (int)$this->request->getQuery('trainee_id');
        $traineeOptions = $this->getTraineeNames();
        $resultOptions = $this->getResultNames();
        $this->set(compact('traineeRecordMedicalCheckUp', 'traineeOptions', 'resultOptions', 'preselectedTrainee'));
    }

    public function edit($id = null)
    {
        $traineeRecordMedicalCheckUp = $this->TraineeRecordMedicalCheckUps->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            if ($this->handleMcuUpload($data)) {
                $traineeRecordMedicalCheckUp = $this->TraineeRecordMedicalCheckUps->patchEntity($traineeRecordMedicalCheckUp, $data);
                if ($this->TraineeRecordMedicalCheckUps->save($traineeRecordMedicalCheckUp)) {
                    $this->Flash->success(__('The medical check-up record has been saved.'));

                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The medical check-up record could not be saved. Please, try again.'));
            }
        }
        $traineeOptions = $this->getTraineeNames();
        $resultOptions = $this->getResultNames();
        $this->set(compact('traineeRecordMedicalCheckUp', 'traineeOptions', 'resultOptions'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeRecordMedicalCheckUp = $this->TraineeRecordMedicalCheckUps->get($id);
        if ($this->TraineeRecordMedicalCheckUps->delete($traineeRecordMedicalCheckUp)) {
            $this->Flash->success(__('The medical check-up record has been deleted.'));
        } else {
            $this->Flash->error(__('The medical check-up record could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Handle the optional MCU file upload. Stores the file under
     * webroot/files/medicalCheckUp (same folder as existing records) and puts
     * the relative path into $data['mcu_files'].
     *
     * @param array $data POST data (modified in place)
     * @return bool false when an invalid file was rejected
     */
    protected function handleMcuUpload(array &$data)
    {
        $file = isset($data['mcu_file_upload']) ? $data['mcu_file_upload'] : null;
        unset($data['mcu_file_upload']);

        if (!is_array($file) || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            // No new file chosen - keep the existing mcu_files value
            unset($data['mcu_files']);

            return true;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        if (!in_array($extension, $allowed)) {
            $this->Flash->error(__('File type .{0} is not allowed.', $extension));

            return false;
        }

        $dir = WWW_ROOT . 'files' . DS . 'medicalCheckUp';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $base = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $fileName = $base . '_' . substr(md5(uniqid('', true)), 0, 12) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $dir . DS . $fileName)) {
            $this->Flash->error(__('The file could not be stored. Please, try again.'));

            return false;
        }
        $data['mcu_files'] = 'files/medicalCheckUp/' . $fileName;

        return true;
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
     * Helper: result id => title (Fit / Tidak Fit / Fit dengan keterangan)
     */
    protected function getResultNames()
    {
        $results = [];
        try {
            $rows = \Cake\ORM\TableRegistry::getTableLocator()->get('MasterMedicalCheckUpResults')
                ->find()->order(['id' => 'ASC']);
            foreach ($rows as $row) {
                $results[$row->id] = $row->title;
            }
        } catch (\Exception $e) {
        }

        return $results;
    }
}

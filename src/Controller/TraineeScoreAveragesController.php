<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeScoreAverages Controller
 *
 * @property \App\Model\Table\TraineeScoreAveragesTable $TraineeScoreAverages
 *
 * @method \App\Model\Entity\TraineeScoreAverage[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeScoreAveragesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades'],
        ];
        $traineeScoreAverages = $this->paginate($this->TraineeScoreAverages);

        // Load dropdown data for filters
        $trainees = $this->TraineeScoreAverages->Trainees->find('list')->limit(200)->toArray();
        $mastertrainingcompetencies = $this->TraineeScoreAverages->MasterTrainingCompetencies->find('list')->limit(200)->toArray();
        $mastertrainingtestscoregrades = $this->TraineeScoreAverages->MasterTrainingTestScoreGrades->find('list')->limit(200)->toArray();
        $this->set(compact('traineeScoreAverages', 'trainees', 'mastertrainingcompetencies', 'mastertrainingtestscoregrades'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Score Average id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterTrainingCompetencies';
        $contain[] = 'MasterTrainingTestScoreGrades';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeScoreAverage = $this->TraineeScoreAverages->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeScoreAverage', $traineeScoreAverage);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeScoreAverage = $this->TraineeScoreAverages->newEntity();
        if ($this->request->is('post')) {
            // Get request data
            $data = $this->request->getData();
            
            // Auto-detect and handle image/file uploads
            $imageFields = [];
            $fileFields = [];
            foreach ($data as $fieldName => $value) {
                if (is_array($value) && isset($value['tmp_name'])) {
                    // Check if it's an image field
                    if (preg_match('/(image|photo|gambar|foto)/i', $fieldName)) {
                        $imageFields[] = $fieldName;
                    } else {
                        $fileFields[] = $fieldName;
                    }
                }
            }
            
            // Upload images with thumbnail and watermark
            foreach ($imageFields as $fieldName) {
                $imagePath = $this->uploadImage('TraineeScoreAverages', $fieldName, 'traineescoreaverages');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeScoreAverages', $fieldName, 'traineescoreaverages');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeScoreAverage = $this->TraineeScoreAverages->patchEntity($traineeScoreAverage, $data);
            if ($this->TraineeScoreAverages->save($traineeScoreAverage)) {
                $this->Flash->success(__('The trainee score average has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee score average could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeScoreAverages->Trainees->find('list', ['limit' => 200]);
        $masterTrainingCompetencies = $this->TraineeScoreAverages->MasterTrainingCompetencies->find('list', ['limit' => 200]);
        $masterTrainingTestScoreGrades = $this->TraineeScoreAverages->MasterTrainingTestScoreGrades->find('list', ['limit' => 200]);
        $this->set(compact('traineeScoreAverage', 'trainees', 'masterTrainingCompetencies', 'masterTrainingTestScoreGrades'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Score Average id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeScoreAverage = $this->TraineeScoreAverages->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Get POST data
            $data = $this->request->getData();
            
            // Auto-detect and handle image/file uploads
            $imageFields = [];
            $fileFields = [];
            foreach ($data as $fieldName => $value) {
                if (is_array($value) && isset($value['tmp_name'])) {
                    // Check if it's an image field
                    if (preg_match('/(image|photo|gambar|foto)/i', $fieldName)) {
                        $imageFields[] = $fieldName;
                    } else {
                        $fileFields[] = $fieldName;
                    }
                }
            }
            
            // Upload images with thumbnail and watermark
            foreach ($imageFields as $fieldName) {
                $imagePath = $this->uploadImage('TraineeScoreAverages', $fieldName, 'traineescoreaverages');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeScoreAverages', $fieldName, 'traineescoreaverages');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeScoreAverage = $this->TraineeScoreAverages->patchEntity($traineeScoreAverage, $data);
            if ($this->TraineeScoreAverages->save($traineeScoreAverage)) {
                $this->Flash->success(__('The trainee score average has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee score average could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeScoreAverages->Trainees->find('list', ['limit' => 200]);
        $masterTrainingCompetencies = $this->TraineeScoreAverages->MasterTrainingCompetencies->find('list', ['limit' => 200]);
        $masterTrainingTestScoreGrades = $this->TraineeScoreAverages->MasterTrainingTestScoreGrades->find('list', ['limit' => 200]);
        $this->set(compact('traineeScoreAverage', 'trainees', 'masterTrainingCompetencies', 'masterTrainingTestScoreGrades'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Score Average id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeScoreAverage = $this->TraineeScoreAverages->get($id);
        if ($this->TraineeScoreAverages->delete($traineeScoreAverage)) {
            $this->Flash->success(__('The trainee score average has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee score average could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->TraineeScoreAverages->find('all')
            ->contain(['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeScoreAverages', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeScoreAverages->find('all')
            ->contain(['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeScoreAverages', $headers, $fields);
    }
    /**
     * Print Report
     *
     * @return \Cake\Http\Response|null
     */
    public function printReport()
    {
        // Override AppController's layout to use print layout
        $this->layout = 'print';
        $this->viewBuilder()->setLayout('print');
        
        $query = $this->TraineeScoreAverages->find('all')
            ->contain(['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades']);
        
        // Define report configuration
        $title = 'TraineeScoreAverages Report';
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportPrint($query, $title, $headers, $fields);
    }

    /**
     * Export PDF method (alias for printReport)
     *
     * @return \Cake\Http\Response|null
     */
    public function exportPdf()
    {
        // Override AppController's layout to use print layout
        $this->layout = 'print';
        $this->viewBuilder()->setLayout('print');
        return $this->printReport();
    }
}


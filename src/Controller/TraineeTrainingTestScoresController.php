<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeTrainingTestScores Controller
 *
 * @property \App\Model\Table\TraineeTrainingTestScoresTable $TraineeTrainingTestScores
 *
 * @method \App\Model\Entity\TraineeTrainingTestScore[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeTrainingTestScoresController extends AppController
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
        $traineeTrainingTestScores = $this->paginate($this->TraineeTrainingTestScores);

        // Load dropdown data for filters
        $trainees = $this->TraineeTrainingTestScores->Trainees->find('list')->limit(200)->toArray();
        $mastertrainingcompetencies = $this->TraineeTrainingTestScores->MasterTrainingCompetencies->find('list')->limit(200)->toArray();
        $mastertrainingtestscoregrades = $this->TraineeTrainingTestScores->MasterTrainingTestScoreGrades->find('list')->limit(200)->toArray();
        $this->set(compact('traineeTrainingTestScores', 'trainees', 'mastertrainingcompetencies', 'mastertrainingtestscoregrades'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Training Test Score id.
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
        $traineeTrainingTestScore = $this->TraineeTrainingTestScores->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeTrainingTestScore', $traineeTrainingTestScore);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeTrainingTestScore = $this->TraineeTrainingTestScores->newEntity();
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
                $imagePath = $this->uploadImage('TraineeTrainingTestScores', $fieldName, 'traineetrainingtestscores');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeTrainingTestScores', $fieldName, 'traineetrainingtestscores');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeTrainingTestScore = $this->TraineeTrainingTestScores->patchEntity($traineeTrainingTestScore, $data);
            if ($this->TraineeTrainingTestScores->save($traineeTrainingTestScore)) {
                $this->Flash->success(__('The trainee training test score has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee training test score could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeTrainingTestScores->Trainees->find('list', ['limit' => 200]);
        $masterTrainingCompetencies = $this->TraineeTrainingTestScores->MasterTrainingCompetencies->find('list', ['limit' => 200]);
        $masterTrainingTestScoreGrades = $this->TraineeTrainingTestScores->MasterTrainingTestScoreGrades->find('list', ['limit' => 200]);
        $this->set(compact('traineeTrainingTestScore', 'trainees', 'masterTrainingCompetencies', 'masterTrainingTestScoreGrades'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Training Test Score id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeTrainingTestScore = $this->TraineeTrainingTestScores->get($id, [
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
                $imagePath = $this->uploadImage('TraineeTrainingTestScores', $fieldName, 'traineetrainingtestscores');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeTrainingTestScores', $fieldName, 'traineetrainingtestscores');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeTrainingTestScore = $this->TraineeTrainingTestScores->patchEntity($traineeTrainingTestScore, $data);
            if ($this->TraineeTrainingTestScores->save($traineeTrainingTestScore)) {
                $this->Flash->success(__('The trainee training test score has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee training test score could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeTrainingTestScores->Trainees->find('list', ['limit' => 200]);
        $masterTrainingCompetencies = $this->TraineeTrainingTestScores->MasterTrainingCompetencies->find('list', ['limit' => 200]);
        $masterTrainingTestScoreGrades = $this->TraineeTrainingTestScores->MasterTrainingTestScoreGrades->find('list', ['limit' => 200]);
        $this->set(compact('traineeTrainingTestScore', 'trainees', 'masterTrainingCompetencies', 'masterTrainingTestScoreGrades'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Training Test Score id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeTrainingTestScore = $this->TraineeTrainingTestScores->get($id);
        if ($this->TraineeTrainingTestScores->delete($traineeTrainingTestScore)) {
            $this->Flash->success(__('The trainee training test score has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee training test score could not be deleted. Please, try again.'));
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
        $query = $this->TraineeTrainingTestScores->find('all')
            ->contain(['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeTrainingTestScores', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeTrainingTestScores->find('all')
            ->contain(['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeTrainingTestScores', $headers, $fields);
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
        
        $query = $this->TraineeTrainingTestScores->find('all')
            ->contain(['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades']);
        
        // Define report configuration
        $title = 'TraineeTrainingTestScores Report';
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

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        // Handle language switching
        if ($lang = $this->request->getQuery('lang')) {
            if (in_array($lang, ['ind', 'eng', 'jpn'])) {
                $this->request->getSession()->write('Config.language', $lang);
                return $this->redirect(['action' => 'processFlow']);
            }
        }
        
        $this->viewBuilder()->setLayout('process_flow');
    }
}
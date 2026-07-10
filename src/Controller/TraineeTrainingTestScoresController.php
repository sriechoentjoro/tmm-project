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
    /**
     * Returns a set of trainee_ids that have at least one issued certificate,
     * used to show certificate status badges across all scoring views.
     */
    private function _certifiedTraineeIds()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $rows = $conn->execute('SELECT DISTINCT trainee_id FROM trainee_certificates')->fetchAll('assoc');
        return array_column($rows, 'trainee_id');
    }

    public function index()
    {
        $this->paginate = [
            'contain' => ['Trainees', 'MasterTrainingCompetencies', 'MasterTrainingTestScoreGrades'],
        ];
        $traineeTrainingTestScores = $this->paginate($this->TraineeTrainingTestScores);
        $certifiedIds = $this->_certifiedTraineeIds();
        $this->set(compact('traineeTrainingTestScores', 'certifiedIds'));
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

    /**
     * Daily score entry - scores of the most recent test date
     */
    public function daily()
    {
        $conn = $this->TraineeTrainingTestScores->getConnection();

        // Most recent test date
        $latestRow  = $conn->execute('SELECT MAX(test_date) AS d FROM trainee_training_test_scores')
                           ->fetch('assoc');
        $latestDate = ($latestRow && $latestRow['d']) ? $latestRow['d'] : null;

        // All distinct test dates for the date picker
        $allDates = $conn->execute(
            'SELECT DISTINCT test_date FROM trainee_training_test_scores ORDER BY test_date DESC LIMIT 60'
        )->fetchAll('assoc');

        // Selected date: from query string or default to latest
        $selectedDate = $this->request->getQuery('date', $latestDate);

        $scores = [];
        if ($selectedDate) {
            $scores = $conn->execute(
                'SELECT tts.id, tts.score, tts.test_date,
                        t.name AS trainee_name, t.tmm_code,
                        mc.title AS competency,
                        g.title AS grade, g.description AS grade_desc
                 FROM trainee_training_test_scores tts
                 LEFT JOIN cms_tmm_trainees.trainees t ON t.id = tts.trainee_id
                 LEFT JOIN master_training_competencies mc ON mc.id = tts.master_training_competency_id
                 LEFT JOIN master_training_test_score_grades g ON g.id = tts.master_training_test_score_grade_id
                 WHERE tts.test_date = ?
                 ORDER BY t.name, mc.title',
                [$selectedDate]
            )->fetchAll('assoc');
        }

        $certifiedIds = $this->_certifiedTraineeIds();
        $this->set(compact('scores', 'latestDate', 'selectedDate', 'allDates', 'certifiedIds'));
    }

    /**
     * Pass/fail report - average score per trainee
     */
    public function report()
    {
        $conn  = $this->TraineeTrainingTestScores->getConnection();
        $rows  = $conn->execute(
            'SELECT
                tts.trainee_id,
                t.name           AS trainee_name,
                t.tmm_code       AS trainee_code,
                COUNT(*)         AS tests,
                ROUND(AVG(tts.score), 1) AS avg_score,
                MIN(tts.score)   AS min_score,
                MAX(tts.score)   AS max_score,
                SUM(CASE WHEN tts.score >= 60 THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN tts.score < 60  THEN 1 ELSE 0 END) AS failed
             FROM trainee_training_test_scores tts
             LEFT JOIN cms_tmm_trainees.trainees t ON t.id = tts.trainee_id
             GROUP BY tts.trainee_id, t.name, t.tmm_code
             ORDER BY avg_score DESC'
        )->fetchAll('assoc');
        $certifiedIds = $this->_certifiedTraineeIds();
        $this->set(compact('rows', 'certifiedIds'));
    }
}

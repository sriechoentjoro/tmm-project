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
     * Who may recompute the averages.
     *
     * The tests are tmm-training's, so the figures drawn from them are too.
     *
     * @var array
     */
    const RECOUNT_ROLES = ['administrator', 'tmm-training'];

    /**
     * Let those roles reach an action the menu permissions cannot know about.
     *
     * granted_actions "*" expands only to the menu's own action plus index and
     * view, so a brand-new action is in nobody's list and the refusal would
     * look like a permissions bug rather than a missing row.
     *
     * @param array|null $user The authenticated user.
     * @return bool
     */
    public function isAuthorized($user = null)
    {
        if ($this->request->getParam('action') !== 'refresh') {
            return parent::isAuthorized($user);
        }

        foreach (self::RECOUNT_ROLES as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * Write the computed averages and grades onto the rows.
     *
     * @return \Cake\Http\Response
     */
    public function refresh()
    {
        $this->request->allowMethod(['post']);
        $table = $this->TraineeScoreAverages;

        $rows = $table->find()
            ->select(['trainee_id', 'master_training_competency_id'])
            ->enableHydration(false)->toArray();

        $changed = 0;
        foreach ($rows as $row) {
            if ($table->refresh($row['trainee_id'], $row['master_training_competency_id']) !== null) {
                $changed++;
            }
        }

        if ($changed) {
            $this->Flash->success(__('{0} row(s) now say what the test scores say.', $changed));
        } else {
            $this->Flash->error(__('Nothing could be recomputed. There may be no test scores to average yet.'));
        }

        return $this->redirect(['action' => 'index']);
    }
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

        // Both figures on each row are typed and never recomputed. Working
        // them out from the test scores here puts the real one beside the
        // stored one, so a row that has drifted says so.
        $ids = [];
        foreach ($traineeScoreAverages as $row) {
            $ids[] = (int)$row->trainee_id;
        }
        $standing = $this->TraineeScoreAverages->standingFor($ids);

        $drifted = 0;
        foreach ($traineeScoreAverages as $row) {
            $key = (int)$row->trainee_id . ':' . (int)$row->master_training_competency_id;
            if (isset($standing[$key])
                && $this->TraineeScoreAverages->disagreements($row, $standing[$key])) {
                $drifted++;
            }
        }
        $this->set(compact('standing', 'drifted'));

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
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
    }
}
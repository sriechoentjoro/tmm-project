<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeEducations Controller
 *
 * @property \App\Model\Table\TraineeEducationsTable $TraineeEducations
 *
 * @method \App\Model\Entity\TraineeEducation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeEducationsController extends AppController
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
            'contain' => ['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens'],
        ];
        $traineeEducations = $this->paginate($this->TraineeEducations);

        // Load dropdown data for filters
        $trainees = $this->TraineeEducations->Trainees->find('list')->limit(200)->toArray();
        $masterstratas = $this->TraineeEducations->MasterStratas->find('list')->limit(200)->toArray();
        // Scoped by the filter already chosen rather than capped at two
        // hundred by id: every province, and below one only what belongs
        // to it. See AppController::regionListsForFilters().
        $regions = $this->regionListsForFilters();
        $masterpropinsis = $regions['masterPropinsis'];
        $masterkabupatens = $regions['masterKabupatens'];
                $master_stratas = $masterstratas;
        $master_propinsis = $masterpropinsis;
        $master_kabupatens = $masterkabupatens;
$this->set(compact('traineeEducations', 'trainees', 'masterstratas', 'masterpropinsis', 'masterkabupatens', 'master_stratas', 'master_propinsis', 'master_kabupatens'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Education id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterStratas';
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeEducation = $this->TraineeEducations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeEducation', $traineeEducation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeEducation = $this->TraineeEducations->newEntity();
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
                $imagePath = $this->uploadImage('TraineeEducations', $fieldName, 'traineeeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeEducations', $fieldName, 'traineeeducations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeEducation = $this->TraineeEducations->patchEntity($traineeEducation, $data);
            if ($this->TraineeEducations->save($traineeEducation)) {
                $this->Flash->success(__('The trainee education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee education could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeEducations->Trainees->find('list', ['limit' => 200]);
        $masterStratas = $this->TraineeEducations->MasterStratas->find('list', ['limit' => 200]);
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($traineeEducation);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $this->set(compact('traineeEducation', 'trainees', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Education id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeEducation = $this->TraineeEducations->get($id, [
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
                $imagePath = $this->uploadImage('TraineeEducations', $fieldName, 'traineeeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeEducations', $fieldName, 'traineeeducations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeEducation = $this->TraineeEducations->patchEntity($traineeEducation, $data);
            if ($this->TraineeEducations->save($traineeEducation)) {
                $this->Flash->success(__('The trainee education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee education could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeEducations->Trainees->find('list', ['limit' => 200]);
        $masterStratas = $this->TraineeEducations->MasterStratas->find('list', ['limit' => 200]);
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($traineeEducation);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $this->set(compact('traineeEducation', 'trainees', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Education id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeEducation = $this->TraineeEducations->get($id);
        if ($this->TraineeEducations->delete($traineeEducation)) {
            $this->Flash->success(__('The trainee education has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee education could not be deleted. Please, try again.'));
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
        $query = $this->TraineeEducations->find('all')
            ->contain(['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
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
        
        return $this->doExportCsv($query, 'TraineeEducations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeEducations->find('all')
            ->contain(['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
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
        
        return $this->doExportExcel($query, 'TraineeEducations', $headers, $fields);
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
        
        $query = $this->TraineeEducations->find('all')
            ->contain(['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define report configuration
        $title = 'TraineeEducations Report';
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
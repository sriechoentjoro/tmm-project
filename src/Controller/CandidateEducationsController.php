<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateEducations Controller
 *
 * @property \App\Model\Table\CandidateEducationsTable $CandidateEducations
 *
 * @method \App\Model\Entity\CandidateEducation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateEducationsController extends AppController
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
            'contain' => ['Candidates', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens'],
        ];
        $candidateEducations = $this->paginate($this->CandidateEducations);

        // Load dropdown data for filters
        $candidates = $this->CandidateEducations->Candidates->find('list')->limit(200)->toArray();
        $masterstratas = $this->CandidateEducations->MasterStratas->find('list')->limit(200)->toArray();
        $masterpropinsis = $this->CandidateEducations->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->CandidateEducations->MasterKabupatens->find('list')->limit(200)->toArray();
                $master_stratas = $masterstratas;
        $master_propinsis = $masterpropinsis;
        $master_kabupatens = $masterkabupatens;
$this->set(compact('candidateEducations', 'candidates', 'masterstratas', 'masterpropinsis', 'masterkabupatens', 'master_stratas', 'master_propinsis', 'master_kabupatens'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Education id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'MasterStratas';
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateEducation = $this->CandidateEducations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateEducation', $candidateEducation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateEducation = $this->CandidateEducations->newEntity();
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
                $imagePath = $this->uploadImage('CandidateEducations', $fieldName, 'candidateeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateEducations', $fieldName, 'candidateeducations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateEducation = $this->CandidateEducations->patchEntity($candidateEducation, $data);
            if ($this->CandidateEducations->save($candidateEducation)) {
                $this->Flash->success(__('The candidate education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate education could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateEducations->Candidates->find('list', ['limit' => 200]);
        $masterStratas = $this->CandidateEducations->MasterStratas->find('list', ['limit' => 200]);
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($candidateEducation);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $this->set(compact('candidateEducation', 'candidates', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Education id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateEducation = $this->CandidateEducations->get($id, [
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
                $imagePath = $this->uploadImage('CandidateEducations', $fieldName, 'candidateeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateEducations', $fieldName, 'candidateeducations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateEducation = $this->CandidateEducations->patchEntity($candidateEducation, $data);
            if ($this->CandidateEducations->save($candidateEducation)) {
                $this->Flash->success(__('The candidate education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate education could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateEducations->Candidates->find('list', ['limit' => 200]);
        $masterStratas = $this->CandidateEducations->MasterStratas->find('list', ['limit' => 200]);
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($candidateEducation);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $this->set(compact('candidateEducation', 'candidates', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Education id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateEducation = $this->CandidateEducations->get($id);
        if ($this->CandidateEducations->delete($candidateEducation)) {
            $this->Flash->success(__('The candidate education has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate education could not be deleted. Please, try again.'));
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
        $query = $this->CandidateEducations->find('all')
            ->contain(['Candidates', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
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
        
        return $this->doExportCsv($query, 'CandidateEducations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateEducations->find('all')
            ->contain(['Candidates', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
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
        
        return $this->doExportExcel($query, 'CandidateEducations', $headers, $fields);
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
        
        $query = $this->CandidateEducations->find('all')
            ->contain(['Candidates', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define report configuration
        $title = 'CandidateEducations Report';
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
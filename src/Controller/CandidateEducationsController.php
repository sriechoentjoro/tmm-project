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
        $this->set(compact('candidateEducations', 'candidates', 'masterstratas', 'masterpropinsis', 'masterkabupatens'));
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
        $masterPropinsis = $this->CandidateEducations->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->CandidateEducations->MasterKabupatens->find('list', ['limit' => 200]);
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
        $masterPropinsis = $this->CandidateEducations->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->CandidateEducations->MasterKabupatens->find('list', ['limit' => 200]);
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
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
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
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
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
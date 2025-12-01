<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateFamilies Controller
 *
 * @property \App\Model\Table\CandidateFamiliesTable $CandidateFamilies
 *
 * @method \App\Model\Entity\CandidateFamily[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateFamiliesController extends AppController
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
            'contain' => ['Candidates', 'MasterFamilyConnections', 'MasterOccupations'],
        ];
        $candidateFamilies = $this->paginate($this->CandidateFamilies);

        // Load dropdown data for filters
        $candidates = $this->CandidateFamilies->Candidates->find('list')->limit(200)->toArray();
        $masterfamilyconnections = $this->CandidateFamilies->MasterFamilyConnections->find('list')->limit(200)->toArray();
        $masteroccupations = $this->CandidateFamilies->MasterOccupations->find('list')->limit(200)->toArray();
        $this->set(compact('candidateFamilies', 'candidates', 'masterfamilyconnections', 'masteroccupations'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Family id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'MasterFamilyConnections';
        $contain[] = 'MasterOccupations';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateFamily = $this->CandidateFamilies->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateFamily', $candidateFamily);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateFamily = $this->CandidateFamilies->newEntity();
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
                $imagePath = $this->uploadImage('CandidateFamilies', $fieldName, 'candidatefamilies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateFamilies', $fieldName, 'candidatefamilies');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateFamily = $this->CandidateFamilies->patchEntity($candidateFamily, $data);
            if ($this->CandidateFamilies->save($candidateFamily)) {
                $this->Flash->success(__('The candidate family has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate family could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateFamilies->Candidates->find('list', ['limit' => 200]);
        $masterFamilyConnections = $this->CandidateFamilies->MasterFamilyConnections->find('list', ['limit' => 200]);
        $masterOccupations = $this->CandidateFamilies->MasterOccupations->find('list', ['limit' => 200]);
        $this->set(compact('candidateFamily', 'candidates', 'masterFamilyConnections', 'masterOccupations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Family id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateFamily = $this->CandidateFamilies->get($id, [
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
                $imagePath = $this->uploadImage('CandidateFamilies', $fieldName, 'candidatefamilies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateFamilies', $fieldName, 'candidatefamilies');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateFamily = $this->CandidateFamilies->patchEntity($candidateFamily, $data);
            if ($this->CandidateFamilies->save($candidateFamily)) {
                $this->Flash->success(__('The candidate family has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate family could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateFamilies->Candidates->find('list', ['limit' => 200]);
        $masterFamilyConnections = $this->CandidateFamilies->MasterFamilyConnections->find('list', ['limit' => 200]);
        $masterOccupations = $this->CandidateFamilies->MasterOccupations->find('list', ['limit' => 200]);
        $this->set(compact('candidateFamily', 'candidates', 'masterFamilyConnections', 'masterOccupations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Family id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateFamily = $this->CandidateFamilies->get($id);
        if ($this->CandidateFamilies->delete($candidateFamily)) {
            $this->Flash->success(__('The candidate family has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate family could not be deleted. Please, try again.'));
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
        $query = $this->CandidateFamilies->find('all')
            ->contain(['Candidates', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateFamilies', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateFamilies->find('all')
            ->contain(['Candidates', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateFamilies', $headers, $fields);
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
        
        $query = $this->CandidateFamilies->find('all')
            ->contain(['Candidates', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define report configuration
        $title = 'CandidateFamilies Report';
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
        $this->viewBuilder()->setLayout('process_flow');
    }
}
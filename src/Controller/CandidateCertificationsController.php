<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateCertifications Controller
 *
 * @property \App\Model\Table\CandidateCertificationsTable $CandidateCertifications
 *
 * @method \App\Model\Entity\CandidateCertification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateCertificationsController extends AppController
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
            'contain' => ['Candidates'],
        ];
        $candidateCertifications = $this->paginate($this->CandidateCertifications);

        // Load dropdown data for filters
        $candidates = $this->CandidateCertifications->Candidates->find('list')->limit(200)->toArray();
        $this->set(compact('candidateCertifications', 'candidates'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Certification id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateCertification = $this->CandidateCertifications->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateCertification', $candidateCertification);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateCertification = $this->CandidateCertifications->newEntity();
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
                $imagePath = $this->uploadImage('CandidateCertifications', $fieldName, 'candidatecertifications');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateCertifications', $fieldName, 'candidatecertifications');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateCertification = $this->CandidateCertifications->patchEntity($candidateCertification, $data);
            if ($this->CandidateCertifications->save($candidateCertification)) {
                $this->Flash->success(__('The candidate certification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate certification could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateCertifications->Candidates->find('list', ['limit' => 200]);
        $this->set(compact('candidateCertification', 'candidates'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Certification id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateCertification = $this->CandidateCertifications->get($id, [
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
                $imagePath = $this->uploadImage('CandidateCertifications', $fieldName, 'candidatecertifications');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateCertifications', $fieldName, 'candidatecertifications');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateCertification = $this->CandidateCertifications->patchEntity($candidateCertification, $data);
            if ($this->CandidateCertifications->save($candidateCertification)) {
                $this->Flash->success(__('The candidate certification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate certification could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateCertifications->Candidates->find('list', ['limit' => 200]);
        $this->set(compact('candidateCertification', 'candidates'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Certification id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateCertification = $this->CandidateCertifications->get($id);
        if ($this->CandidateCertifications->delete($candidateCertification)) {
            $this->Flash->success(__('The candidate certification has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate certification could not be deleted. Please, try again.'));
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
        $query = $this->CandidateCertifications->find('all')
            ->contain(['Candidates']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateCertifications', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateCertifications->find('all')
            ->contain(['Candidates']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateCertifications', $headers, $fields);
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
        
        $query = $this->CandidateCertifications->find('all')
            ->contain(['Candidates']);
        
        // Define report configuration
        $title = 'CandidateCertifications Report';
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
<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateSubmissionDocuments Controller
 *
 * @property \App\Model\Table\CandidateSubmissionDocumentsTable $CandidateSubmissionDocuments
 *
 * @method \App\Model\Entity\CandidateSubmissionDocument[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateSubmissionDocumentsController extends AppController
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
            'contain' => ['Candidates', 'CandidateDocuments'],
        ];
        $candidateSubmissionDocuments = $this->paginate($this->CandidateSubmissionDocuments);

        // Load dropdown data for filters
        $applicants = $this->CandidateSubmissionDocuments->Applicants->find('list')->limit(200)->toArray();
        $documents = $this->CandidateSubmissionDocuments->Documents->find('list')->limit(200)->toArray();
        $this->set(compact('candidateSubmissionDocuments', 'Candidates', 'CandidateDocuments'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Submission Document id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'CandidateDocuments';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateSubmissionDocument = $this->CandidateSubmissionDocuments->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateSubmissionDocument', $candidateSubmissionDocument);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateSubmissionDocument = $this->CandidateSubmissionDocuments->newEntity();
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
                $imagePath = $this->uploadImage('CandidateSubmissionDocuments', $fieldName, 'candidatesubmissiondocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateSubmissionDocuments', $fieldName, 'candidatesubmissiondocuments');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateSubmissionDocument = $this->CandidateSubmissionDocuments->patchEntity($candidateSubmissionDocument, $data);
            if ($this->CandidateSubmissionDocuments->save($candidateSubmissionDocument)) {
                $this->Flash->success(__('The candidate submission document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate submission document could not be saved. Please, try again.'));
        }
        $applicants = $this->CandidateSubmissionDocuments->Applicants->find('list', ['limit' => 200]);
        $documents = $this->CandidateSubmissionDocuments->Documents->find('list', ['limit' => 200]);
        $this->set(compact('candidateSubmissionDocument', 'Candidates', 'CandidateDocuments'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Submission Document id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateSubmissionDocument = $this->CandidateSubmissionDocuments->get($id, [
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
                $imagePath = $this->uploadImage('CandidateSubmissionDocuments', $fieldName, 'candidatesubmissiondocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateSubmissionDocuments', $fieldName, 'candidatesubmissiondocuments');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateSubmissionDocument = $this->CandidateSubmissionDocuments->patchEntity($candidateSubmissionDocument, $data);
            if ($this->CandidateSubmissionDocuments->save($candidateSubmissionDocument)) {
                $this->Flash->success(__('The candidate submission document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate submission document could not be saved. Please, try again.'));
        }
        $applicants = $this->CandidateSubmissionDocuments->Applicants->find('list', ['limit' => 200]);
        $documents = $this->CandidateSubmissionDocuments->Documents->find('list', ['limit' => 200]);
        $this->set(compact('candidateSubmissionDocument', 'Candidates', 'CandidateDocuments'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Submission Document id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateSubmissionDocument = $this->CandidateSubmissionDocuments->get($id);
        if ($this->CandidateSubmissionDocuments->delete($candidateSubmissionDocument)) {
            $this->Flash->success(__('The candidate submission document has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate submission document could not be deleted. Please, try again.'));
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
        $query = $this->CandidateSubmissionDocuments->find('all')
            ->contain(['Candidates', 'CandidateDocuments']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateSubmissionDocuments', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateSubmissionDocuments->find('all')
            ->contain(['Candidates', 'CandidateDocuments']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateSubmissionDocuments', $headers, $fields);
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
        
        $query = $this->CandidateSubmissionDocuments->find('all')
            ->contain(['Candidates', 'CandidateDocuments']);
        
        // Define report configuration
        $title = 'CandidateSubmissionDocuments Report';
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


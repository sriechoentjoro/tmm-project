<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateDocuments Controller
 *
 * @property \App\Model\Table\CandidateDocumentsTable $CandidateDocuments
 *
 * @method \App\Model\Entity\CandidateDocument[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateDocumentsController extends AppController
{
    use \App\Controller\ExportTrait;
    use \App\Controller\LpkDataFilterTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // For LPK users, filter documents through Candidates relationship
        $query = $this->CandidateDocuments->find()
            ->contain(['Candidates']);
        
        // Apply institution filter if LPK user
        if ($this->hasRole('lpk-penyangga')) {
            $institutionId = $this->getUserInstitutionId();
            if ($institutionId) {
                $query->matching('Candidates', function($q) use ($institutionId) {
                    return $q->where(['Candidates.vocational_training_institution_id' => $institutionId]);
                });
            } else {
                $query->where(['1' => 0]); // Empty result if no institution
            }
        }
        
        $this->paginate = [
            'contain' => ['Candidates'],
        ];
        $candidateDocuments = $this->paginate($query);

        // Load dropdown data for filters
        $candidates = $this->CandidateDocuments->Candidates->find('list')->limit(200)->toArray();
        $this->set(compact('candidateDocuments', 'candidates'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Document id.
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
        $candidateDocument = $this->CandidateDocuments->get($id, [
            'contain' => $contain,
        ]);
        
        // Check access through Candidate's institution
        if ($this->hasRole('lpk-penyangga') && !empty($candidateDocument->candidate)) {
            if (!$this->canAccessRecord($candidateDocument->candidate, 'vocational_training_institution_id')) {
                $this->Flash->error(__('You are not authorized to view this document.'));
                return $this->redirect(['action' => 'index']);
            }
        }

        $this->set('candidateDocument', $candidateDocument);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateDocument = $this->CandidateDocuments->newEntity();
        if ($this->request->is('post')) {
            // Get request data
            $data = $this->request->getData();
            
            // Convert date formats automatically
            $data = $this->convertDateFormats($data);
            
            // Auto-detect and handle image/file uploads
            $imageFields = [];
            $fileFields = [];
            foreach ($data as $fieldName => $value) {
                // Check if it's an uploaded file (array with tmp_name)
                if (is_array($value) && isset($value['tmp_name']) && !empty($value['tmp_name']) && $value['error'] === UPLOAD_ERR_OK) {
                    // Check if it's an image field
                    if (preg_match('/(image|photo|gambar|foto)/i', $fieldName)) {
                        $imageFields[] = $fieldName;
                    } else {
                        $fileFields[] = $fieldName;
                    }
                }
            }
            
            $this->log('Image fields detected: ' . implode(', ', $imageFields), 'debug');
            $this->log('File fields detected: ' . implode(', ', $fileFields), 'debug');
            
            // Upload images with thumbnail and watermark
            foreach ($imageFields as $fieldName) {
                $imagePath = $this->uploadImage('CandidateDocuments', $fieldName, 'candidatedocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                    $this->log("Image uploaded: {$fieldName} -> {$imagePath}", 'debug');
                } else {
                    $this->log("Image upload failed for: {$fieldName}", 'error');
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->log("Attempting to upload file: {$fieldName}", 'debug');
                $uploadSuccess = $this->uploadFile('CandidateDocuments', $fieldName, 'candidatedocuments');
                if ($uploadSuccess) {
                    // Get updated data after upload (uploadFile updates request data)
                    $uploadedData = $this->request->getData();
                    $data[$fieldName] = $uploadedData[$fieldName];
                    $this->log("File uploaded successfully: {$fieldName} -> {$data[$fieldName]}", 'debug');
                } else {
                    $this->log("File upload failed for: {$fieldName}", 'error');
                }
            }
            
            $this->log('Final data to save: ' . print_r($data, true), 'debug');
            
            $candidateDocument = $this->CandidateDocuments->patchEntity($candidateDocument, $data);
            
            // Debug: Log entity state before save
            $this->log('=== BEFORE SAVE DEBUG ===', 'debug');
            $this->log('Entity data: ' . print_r($candidateDocument->toArray(), true), 'debug');
            $this->log('Has errors: ' . ($candidateDocument->hasErrors() ? 'YES' : 'NO'), 'debug');
            $this->log('Errors: ' . print_r($candidateDocument->getErrors(), true), 'debug');
            $this->log('Is new: ' . ($candidateDocument->isNew() ? 'YES' : 'NO'), 'debug');
            $this->log('Dirty fields: ' . print_r($candidateDocument->getDirty(), true), 'debug');
            
            if ($this->CandidateDocuments->save($candidateDocument)) {
                $this->log('Save SUCCESS - ID: ' . $candidateDocument->id, 'debug');
                $this->Flash->success(__('The candidate document has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            
            $this->log('Save FAILED', 'error');
            
            // Log validation errors for debugging
            $errors = $candidateDocument->getErrors();
            if (!empty($errors)) {
                $this->log('Validation errors: ' . print_r($errors, true), 'debug');
                foreach ($errors as $field => $fieldErrors) {
                    foreach ($fieldErrors as $error) {
                        $this->Flash->error(__('Field {0}: {1}', $field, $error));
                    }
                }
            }
            $this->Flash->error(__('The candidate document could not be saved. Please, try again.'));
        }
        
        // Filter candidates by institution for LPK users
        $candidatesQuery = $this->CandidateDocuments->Candidates->find('list', ['limit' => 200]);
        if ($this->hasRole('lpk-penyangga')) {
            $institutionId = $this->getUserInstitutionId();
            if ($institutionId) {
                $candidatesQuery->where(['Candidates.vocational_training_institution_id' => $institutionId]);
            } else {
                $candidatesQuery->where(['1' => 0]); // Empty result
            }
        }
        $candidates = $candidatesQuery->toArray();
        
        // Add warning if no candidates available
        if (empty($candidates)) {
            $this->Flash->warning(__('No candidates available for your institution. Please contact administrator.'));
        }
        
        $this->set(compact('candidateDocument', 'candidates'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Document id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateDocument = $this->CandidateDocuments->get($id, [
            'contain' => ['Candidates'],
        ]);
        
        // Check access through Candidate's institution
        if ($this->hasRole('lpk-penyangga') && !empty($candidateDocument->candidate)) {
            if (!$this->canAccessRecord($candidateDocument->candidate, 'vocational_training_institution_id')) {
                $this->Flash->error(__('You are not authorized to edit this document.'));
                return $this->redirect(['action' => 'index']);
            }
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Get POST data
            $data = $this->request->getData();
            
            // Convert date formats automatically
            $data = $this->convertDateFormats($data);
            
            // Auto-detect and handle image/file uploads
            $imageFields = [];
            $fileFields = [];
            foreach ($data as $fieldName => $value) {
                // Check if it's an uploaded file (array with tmp_name)
                if (is_array($value) && isset($value['tmp_name']) && !empty($value['tmp_name']) && $value['error'] === UPLOAD_ERR_OK) {
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
                $imagePath = $this->uploadImage('CandidateDocuments', $fieldName, 'candidatedocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateDocuments', $fieldName, 'candidatedocuments');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateDocument = $this->CandidateDocuments->patchEntity($candidateDocument, $data);
            if ($this->CandidateDocuments->save($candidateDocument)) {
                $this->Flash->success(__('The candidate document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate document could not be saved. Please, try again.'));
        }
        
        // Filter candidates by institution for LPK users
        $candidatesQuery = $this->CandidateDocuments->Candidates->find('list', ['limit' => 200]);
        if ($this->hasRole('lpk-penyangga')) {
            $institutionId = $this->getUserInstitutionId();
            if ($institutionId) {
                $candidatesQuery->where(['Candidates.vocational_training_institution_id' => $institutionId]);
            } else {
                $candidatesQuery->where(['1' => 0]); // Empty result
            }
        }
        $candidates = $candidatesQuery->toArray();
        
        $this->set(compact('candidateDocument', 'candidates'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Document id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateDocument = $this->CandidateDocuments->get($id, [
            'contain' => ['Candidates']
        ]);
        
        // Check access through Candidate's institution
        if ($this->hasRole('lpk-penyangga') && !empty($candidateDocument->candidate)) {
            if (!$this->canAccessRecord($candidateDocument->candidate, 'vocational_training_institution_id')) {
                $this->Flash->error(__('You are not authorized to delete this document.'));
                return $this->redirect(['action' => 'index']);
            }
        }
        
        if ($this->CandidateDocuments->delete($candidateDocument)) {
            $this->Flash->success(__('The candidate document has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate document could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Help method - Document Submission Guide
     *
     * @return void
     */
    public function help()
    {
        // This is just a view, no data processing needed
    }

    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->CandidateDocuments->find('all')
            ->contain(['Candidates']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateDocuments', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateDocuments->find('all')
            ->contain(['Candidates']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateDocuments', $headers, $fields);
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
        
        $query = $this->CandidateDocuments->find('all')
            ->contain(['Candidates']);
        
        // Define report configuration
        $title = 'CandidateDocuments Report';
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
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
                // A string, not ['1' => 0]: a numeric key is dropped, and
                // the query then returns everything. See LpkDataFilterTrait.
                $query->where(['1 = 0']);
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
     * Submission Checklist method - per-candidate document checklist against
     * the master document list, with upload/submission status per document.
     *
     * @return void
     */
    public function submissionChecklist()
    {
        $candidateId = (int)$this->request->getQuery('candidate_id');

        $candidatesQuery = $this->CandidateDocuments->Candidates->find('list');
        if ($this->hasRole('lpk-penyangga')) {
            $institutionId = $this->getUserInstitutionId();
            $candidatesQuery->where($institutionId
                ? ['vocational_training_institution_id' => $institutionId]
                : ['1 = 0']);
        }
        $candidates = $candidatesQuery->order(['name' => 'ASC'])->toArray();

        $masterListTable = $this->loadModel('CandidateDocumentsMasterList');
        $masterDocuments = $masterListTable->find()
            ->contain(['CandidateDocumentCategories'])
            ->order(['CandidateDocumentsMasterList.id' => 'ASC'])
            ->all();

        $submissions = [];
        $uploadedDocs = [];
        if ($candidateId) {
            $submissionsTable = $this->loadModel('CandidateSubmissionDocuments');
            foreach ($submissionsTable->find()->where(['applicant_id' => $candidateId]) as $sub) {
                $submissions[$sub->document_id] = $sub;
            }
            $uploaded = $this->CandidateDocuments->find()
                ->where(['candidate_id' => $candidateId, 'candidate_document_master_list_id IS NOT' => null]);
            foreach ($uploaded as $docRow) {
                $uploadedDocs[$docRow->candidate_document_master_list_id] = $docRow;
            }
        }

        $this->set(compact('candidates', 'candidateId', 'masterDocuments', 'submissions', 'uploadedDocs'));
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
     * @param string|null $candidateId The candidate this document is for,
     *  when the form was opened from that candidate's own page.
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($candidateId = null)
    {
        $candidateDocument = $this->CandidateDocuments->newEntity();
        // Reached from a candidate's own page, which names the candidate in the
        // URL. The record belongs to them, so the form says so rather than
        // asking again - and asking again was not always answerable: the list
        // below holds the first two hundred candidates by id, and nothing put
        // a candidate outside them into it.
        if ($candidateId !== null) {
            $candidateDocument->candidate_id = (int)$candidateId;
        }
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
                // Back where the button was pressed, not to a list the person
                // was never on.
                return $this->redirect($candidateId !== null
                    ? ['controller' => 'Candidates', 'action' => 'view', $candidateId]
                    : ['action' => 'index']);
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
                $candidatesQuery->where(['1 = 0']);
            }
        }
        $candidates = $this->listIncluding($candidatesQuery, $candidateId);
        
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
                $candidatesQuery->where(['1 = 0']);
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
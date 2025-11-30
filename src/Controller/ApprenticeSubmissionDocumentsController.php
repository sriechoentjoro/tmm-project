<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeSubmissionDocuments Controller
 *
 * @property \App\Model\Table\ApprenticeSubmissionDocumentsTable $ApprenticeSubmissionDocuments
 *
 * @method \App\Model\Entity\ApprenticeSubmissionDocument[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeSubmissionDocumentsController extends AppController
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
            'contain' => ['Apprentices', 'MasterApprenticeSubmissionDocuments', 'MasterDocumentSubmissionStatuses'],
        ];
        $apprenticeSubmissionDocuments = $this->paginate($this->ApprenticeSubmissionDocuments);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeSubmissionDocuments->Apprentices->find('list')->limit(200)->toArray();
        $apprenticeshipsubmissiondocuments = $this->ApprenticeSubmissionDocuments->ApprenticeshipSubmissionDocuments->find('list')->limit(200)->toArray();
        $masterdocumentsubmissionstatuses = $this->ApprenticeSubmissionDocuments->MasterDocumentSubmissionStatuses->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeSubmissionDocuments', 'apprentices', 'MasterApprenticeSubmissionDocuments', 'masterdocumentsubmissionstatuses'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Submission Document id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'MasterApprenticeSubmissionDocuments';
        $contain[] = 'MasterDocumentSubmissionStatuses';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeSubmissionDocument = $this->ApprenticeSubmissionDocuments->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeSubmissionDocument', $apprenticeSubmissionDocument);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeSubmissionDocument = $this->ApprenticeSubmissionDocuments->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeSubmissionDocuments', $fieldName, 'apprenticesubmissiondocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeSubmissionDocuments', $fieldName, 'apprenticesubmissiondocuments');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeSubmissionDocument = $this->ApprenticeSubmissionDocuments->patchEntity($apprenticeSubmissionDocument, $data);
            if ($this->ApprenticeSubmissionDocuments->save($apprenticeSubmissionDocument)) {
                $this->Flash->success(__('The apprentice submission document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice submission document could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeSubmissionDocuments->Apprentices->find('list', ['limit' => 200]);
        $apprenticeshipSubmissionDocuments = $this->ApprenticeSubmissionDocuments->ApprenticeshipSubmissionDocuments->find('list', ['limit' => 200]);
        $masterDocumentSubmissionStatuses = $this->ApprenticeSubmissionDocuments->MasterDocumentSubmissionStatuses->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeSubmissionDocument', 'apprentices', 'MasterApprenticeSubmissionDocuments', 'masterDocumentSubmissionStatuses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Submission Document id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeSubmissionDocument = $this->ApprenticeSubmissionDocuments->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeSubmissionDocuments', $fieldName, 'apprenticesubmissiondocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeSubmissionDocuments', $fieldName, 'apprenticesubmissiondocuments');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeSubmissionDocument = $this->ApprenticeSubmissionDocuments->patchEntity($apprenticeSubmissionDocument, $data);
            if ($this->ApprenticeSubmissionDocuments->save($apprenticeSubmissionDocument)) {
                $this->Flash->success(__('The apprentice submission document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice submission document could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeSubmissionDocuments->Apprentices->find('list', ['limit' => 200]);
        $apprenticeshipSubmissionDocuments = $this->ApprenticeSubmissionDocuments->ApprenticeshipSubmissionDocuments->find('list', ['limit' => 200]);
        $masterDocumentSubmissionStatuses = $this->ApprenticeSubmissionDocuments->MasterDocumentSubmissionStatuses->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeSubmissionDocument', 'apprentices', 'MasterApprenticeSubmissionDocuments', 'masterDocumentSubmissionStatuses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Submission Document id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeSubmissionDocument = $this->ApprenticeSubmissionDocuments->get($id);
        if ($this->ApprenticeSubmissionDocuments->delete($apprenticeSubmissionDocument)) {
            $this->Flash->success(__('The apprentice submission document has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice submission document could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeSubmissionDocuments->find('all')
            ->contain(['Apprentices', 'MasterApprenticeSubmissionDocuments', 'MasterDocumentSubmissionStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeSubmissionDocuments', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeSubmissionDocuments->find('all')
            ->contain(['Apprentices', 'MasterApprenticeSubmissionDocuments', 'MasterDocumentSubmissionStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeSubmissionDocuments', $headers, $fields);
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
        
        $query = $this->ApprenticeSubmissionDocuments->find('all')
            ->contain(['Apprentices', 'MasterApprenticeSubmissionDocuments', 'MasterDocumentSubmissionStatuses']);
        
        // Define report configuration
        $title = 'ApprenticeSubmissionDocuments Report';
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


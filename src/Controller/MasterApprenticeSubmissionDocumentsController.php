<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterApprenticeSubmissionDocuments Controller
 *
 * @property \App\Model\Table\MasterApprenticeSubmissionDocumentsTable $MasterApprenticeSubmissionDocuments
 *
 * @method \App\Model\Entity\MasterApprenticeSubmissionDocument[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterApprenticeSubmissionDocumentsController extends AppController
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
            'contain' => ['MasterApprenticeSubmissionDocumentCategories'],
        ];
        $masterApprenticeSubmissionDocuments = $this->paginate($this->MasterApprenticeSubmissionDocuments);

        // Load dropdown data for filters
        $masterapprenticeshipsubmissiondocumentcategories = $this->MasterApprenticeSubmissionDocuments->MasterApprenticeshipSubmissionDocumentCategories->find('list')->limit(200)->toArray();
        $this->set(compact('masterApprenticeSubmissionDocuments', 'MasterApprenticeSubmissionDocumentCategories'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Apprentice Submission Document id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'MasterApprenticeSubmissionDocumentCategories';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterApprenticeSubmissionDocument = $this->MasterApprenticeSubmissionDocuments->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterApprenticeSubmissionDocument', $masterApprenticeSubmissionDocument);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterApprenticeSubmissionDocument = $this->MasterApprenticeSubmissionDocuments->newEntity();
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
                $imagePath = $this->uploadImage('MasterApprenticeSubmissionDocuments', $fieldName, 'masterapprenticesubmissiondocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterApprenticeSubmissionDocuments', $fieldName, 'masterapprenticesubmissiondocuments');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterApprenticeSubmissionDocument = $this->MasterApprenticeSubmissionDocuments->patchEntity($masterApprenticeSubmissionDocument, $data);
            if ($this->MasterApprenticeSubmissionDocuments->save($masterApprenticeSubmissionDocument)) {
                $this->Flash->success(__('The master apprentice submission document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master apprentice submission document could not be saved. Please, try again.'));
        }
        $masterApprenticeshipSubmissionDocumentCategories = $this->MasterApprenticeSubmissionDocuments->MasterApprenticeshipSubmissionDocumentCategories->find('list', ['limit' => 200]);
        $this->set(compact('masterApprenticeSubmissionDocument', 'MasterApprenticeSubmissionDocumentCategories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Apprentice Submission Document id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterApprenticeSubmissionDocument = $this->MasterApprenticeSubmissionDocuments->get($id, [
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
                $imagePath = $this->uploadImage('MasterApprenticeSubmissionDocuments', $fieldName, 'masterapprenticesubmissiondocuments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterApprenticeSubmissionDocuments', $fieldName, 'masterapprenticesubmissiondocuments');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterApprenticeSubmissionDocument = $this->MasterApprenticeSubmissionDocuments->patchEntity($masterApprenticeSubmissionDocument, $data);
            if ($this->MasterApprenticeSubmissionDocuments->save($masterApprenticeSubmissionDocument)) {
                $this->Flash->success(__('The master apprentice submission document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master apprentice submission document could not be saved. Please, try again.'));
        }
        $masterApprenticeshipSubmissionDocumentCategories = $this->MasterApprenticeSubmissionDocuments->MasterApprenticeshipSubmissionDocumentCategories->find('list', ['limit' => 200]);
        $this->set(compact('masterApprenticeSubmissionDocument', 'MasterApprenticeSubmissionDocumentCategories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Apprentice Submission Document id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterApprenticeSubmissionDocument = $this->MasterApprenticeSubmissionDocuments->get($id);
        if ($this->MasterApprenticeSubmissionDocuments->delete($masterApprenticeSubmissionDocument)) {
            $this->Flash->success(__('The master apprentice submission document has been deleted.'));
        } else {
            $this->Flash->error(__('The master apprentice submission document could not be deleted. Please, try again.'));
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
        $query = $this->MasterApprenticeSubmissionDocuments->find('all')
            ->contain(['MasterApprenticeSubmissionDocumentCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterApprenticeSubmissionDocuments', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterApprenticeSubmissionDocuments->find('all')
            ->contain(['MasterApprenticeSubmissionDocumentCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterApprenticeSubmissionDocuments', $headers, $fields);
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
        
        $query = $this->MasterApprenticeSubmissionDocuments->find('all')
            ->contain(['MasterApprenticeSubmissionDocumentCategories']);
        
        // Define report configuration
        $title = 'MasterApprenticeSubmissionDocuments Report';
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
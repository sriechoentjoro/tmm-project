<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateDocumentCategories Controller
 *
 * @property \App\Model\Table\CandidateDocumentCategoriesTable $CandidateDocumentCategories
 *
 * @method \App\Model\Entity\CandidateDocumentCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateDocumentCategoriesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $candidateDocumentCategories = $this->paginate($this->CandidateDocumentCategories);

        $this->set(compact('candidateDocumentCategories'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Document Category id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateDocumentCategory = $this->CandidateDocumentCategories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateDocumentCategory', $candidateDocumentCategory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateDocumentCategory = $this->CandidateDocumentCategories->newEntity();
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
                $imagePath = $this->uploadImage('CandidateDocumentCategories', $fieldName, 'candidatedocumentcategories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateDocumentCategories', $fieldName, 'candidatedocumentcategories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateDocumentCategory = $this->CandidateDocumentCategories->patchEntity($candidateDocumentCategory, $data);
            if ($this->CandidateDocumentCategories->save($candidateDocumentCategory)) {
                $this->Flash->success(__('The candidate document category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate document category could not be saved. Please, try again.'));
        }
        $this->set(compact('candidateDocumentCategory'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Document Category id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateDocumentCategory = $this->CandidateDocumentCategories->get($id, [
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
                $imagePath = $this->uploadImage('CandidateDocumentCategories', $fieldName, 'candidatedocumentcategories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateDocumentCategories', $fieldName, 'candidatedocumentcategories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateDocumentCategory = $this->CandidateDocumentCategories->patchEntity($candidateDocumentCategory, $data);
            if ($this->CandidateDocumentCategories->save($candidateDocumentCategory)) {
                $this->Flash->success(__('The candidate document category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate document category could not be saved. Please, try again.'));
        }
        $this->set(compact('candidateDocumentCategory'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Document Category id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateDocumentCategory = $this->CandidateDocumentCategories->get($id);
        if ($this->CandidateDocumentCategories->delete($candidateDocumentCategory)) {
            $this->Flash->success(__('The candidate document category has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate document category could not be deleted. Please, try again.'));
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
        $query = $this->CandidateDocumentCategories->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateDocumentCategories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateDocumentCategories->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateDocumentCategories', $headers, $fields);
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
        
        $query = $this->CandidateDocumentCategories->find('all');
        
        // Define report configuration
        $title = 'CandidateDocumentCategories Report';
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
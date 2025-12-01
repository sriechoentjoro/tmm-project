<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterJobCategories Controller
 *
 * @property \App\Model\Table\MasterJobCategoriesTable $MasterJobCategories
 *
 * @method \App\Model\Entity\MasterJobCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterJobCategoriesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterJobCategories = $this->paginate($this->MasterJobCategories);

        $this->set(compact('masterJobCategories'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Job Category id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterJobCategory = $this->MasterJobCategories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterJobCategory', $masterJobCategory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterJobCategory = $this->MasterJobCategories->newEntity();
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
                $imagePath = $this->uploadImage('MasterJobCategories', $fieldName, 'masterjobcategories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterJobCategories', $fieldName, 'masterjobcategories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterJobCategory = $this->MasterJobCategories->patchEntity($masterJobCategory, $data);
            if ($this->MasterJobCategories->save($masterJobCategory)) {
                $this->Flash->success(__('The master job category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master job category could not be saved. Please, try again.'));
        }
        $this->set(compact('masterJobCategory'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Job Category id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterJobCategory = $this->MasterJobCategories->get($id, [
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
                $imagePath = $this->uploadImage('MasterJobCategories', $fieldName, 'masterjobcategories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterJobCategories', $fieldName, 'masterjobcategories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterJobCategory = $this->MasterJobCategories->patchEntity($masterJobCategory, $data);
            if ($this->MasterJobCategories->save($masterJobCategory)) {
                $this->Flash->success(__('The master job category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master job category could not be saved. Please, try again.'));
        }
        $this->set(compact('masterJobCategory'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Job Category id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterJobCategory = $this->MasterJobCategories->get($id);
        if ($this->MasterJobCategories->delete($masterJobCategory)) {
            $this->Flash->success(__('The master job category has been deleted.'));
        } else {
            $this->Flash->error(__('The master job category could not be deleted. Please, try again.'));
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
        $query = $this->MasterJobCategories->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterJobCategories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterJobCategories->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterJobCategories', $headers, $fields);
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
        
        $query = $this->MasterJobCategories->find('all');
        
        // Define report configuration
        $title = 'MasterJobCategories Report';
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
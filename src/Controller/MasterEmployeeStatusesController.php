<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterEmployeeStatuses Controller
 *
 * @property \App\Model\Table\MasterEmployeeStatusesTable $MasterEmployeeStatuses
 *
 * @method \App\Model\Entity\MasterEmployeeStatus[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterEmployeeStatusesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterEmployeeStatuses = $this->paginate($this->MasterEmployeeStatuses);

        $this->set(compact('masterEmployeeStatuses'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Employee Status id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterEmployeeStatus = $this->MasterEmployeeStatuses->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterEmployeeStatus', $masterEmployeeStatus);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterEmployeeStatus = $this->MasterEmployeeStatuses->newEntity();
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
                $imagePath = $this->uploadImage('MasterEmployeeStatuses', $fieldName, 'masteremployeestatuses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterEmployeeStatuses', $fieldName, 'masteremployeestatuses');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterEmployeeStatus = $this->MasterEmployeeStatuses->patchEntity($masterEmployeeStatus, $data);
            if ($this->MasterEmployeeStatuses->save($masterEmployeeStatus)) {
                $this->Flash->success(__('The master employee status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master employee status could not be saved. Please, try again.'));
        }
        $this->set(compact('masterEmployeeStatus'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Employee Status id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterEmployeeStatus = $this->MasterEmployeeStatuses->get($id, [
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
                $imagePath = $this->uploadImage('MasterEmployeeStatuses', $fieldName, 'masteremployeestatuses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterEmployeeStatuses', $fieldName, 'masteremployeestatuses');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterEmployeeStatus = $this->MasterEmployeeStatuses->patchEntity($masterEmployeeStatus, $data);
            if ($this->MasterEmployeeStatuses->save($masterEmployeeStatus)) {
                $this->Flash->success(__('The master employee status has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master employee status could not be saved. Please, try again.'));
        }
        $this->set(compact('masterEmployeeStatus'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Employee Status id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterEmployeeStatus = $this->MasterEmployeeStatuses->get($id);
        if ($this->MasterEmployeeStatuses->delete($masterEmployeeStatus)) {
            $this->Flash->success(__('The master employee status has been deleted.'));
        } else {
            $this->Flash->error(__('The master employee status could not be deleted. Please, try again.'));
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
        $query = $this->MasterEmployeeStatuses->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterEmployeeStatuses', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterEmployeeStatuses->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterEmployeeStatuses', $headers, $fields);
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
        
        $query = $this->MasterEmployeeStatuses->find('all');
        
        // Define report configuration
        $title = 'MasterEmployeeStatuses Report';
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
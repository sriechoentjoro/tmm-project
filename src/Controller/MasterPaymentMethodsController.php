<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterPaymentMethods Controller
 *
 * @property \App\Model\Table\MasterPaymentMethodsTable $MasterPaymentMethods
 *
 * @method \App\Model\Entity\MasterPaymentMethod[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterPaymentMethodsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterPaymentMethods = $this->paginate($this->MasterPaymentMethods);

        $this->set(compact('masterPaymentMethods'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Payment Method id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterPaymentMethod = $this->MasterPaymentMethods->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterPaymentMethod', $masterPaymentMethod);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterPaymentMethod = $this->MasterPaymentMethods->newEntity();
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
                $imagePath = $this->uploadImage('MasterPaymentMethods', $fieldName, 'masterpaymentmethods');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterPaymentMethods', $fieldName, 'masterpaymentmethods');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterPaymentMethod = $this->MasterPaymentMethods->patchEntity($masterPaymentMethod, $data);
            if ($this->MasterPaymentMethods->save($masterPaymentMethod)) {
                $this->Flash->success(__('The master payment method has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master payment method could not be saved. Please, try again.'));
        }
        $this->set(compact('masterPaymentMethod'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Payment Method id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterPaymentMethod = $this->MasterPaymentMethods->get($id, [
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
                $imagePath = $this->uploadImage('MasterPaymentMethods', $fieldName, 'masterpaymentmethods');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterPaymentMethods', $fieldName, 'masterpaymentmethods');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterPaymentMethod = $this->MasterPaymentMethods->patchEntity($masterPaymentMethod, $data);
            if ($this->MasterPaymentMethods->save($masterPaymentMethod)) {
                $this->Flash->success(__('The master payment method has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master payment method could not be saved. Please, try again.'));
        }
        $this->set(compact('masterPaymentMethod'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Payment Method id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterPaymentMethod = $this->MasterPaymentMethods->get($id);
        if ($this->MasterPaymentMethods->delete($masterPaymentMethod)) {
            $this->Flash->success(__('The master payment method has been deleted.'));
        } else {
            $this->Flash->error(__('The master payment method could not be deleted. Please, try again.'));
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
        $query = $this->MasterPaymentMethods->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterPaymentMethods', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterPaymentMethods->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterPaymentMethods', $headers, $fields);
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
        
        $query = $this->MasterPaymentMethods->find('all');
        
        // Define report configuration
        $title = 'MasterPaymentMethods Report';
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
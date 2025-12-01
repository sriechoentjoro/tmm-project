<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterCurrencies Controller
 *
 * @property \App\Model\Table\MasterCurrenciesTable $MasterCurrencies
 *
 * @method \App\Model\Entity\MasterCurrency[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterCurrenciesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterCurrencies = $this->paginate($this->MasterCurrencies);

        $this->set(compact('masterCurrencies'));
    }


                
    /**
     * View method
     *
     * @param string|null $id Master Currency id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
            // TraineeInstallments with its associations
        $traineeInstallmentsAssociations = [];
        try {
            $traineeInstallmentsTable = $this->MasterCurrencies->TraineeInstallments;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeInstallmentsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeInstallmentsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeInstallmentsAssociations)) {
            $contain['TraineeInstallments'] = $traineeInstallmentsAssociations;
        } else {
            $contain[] = 'TraineeInstallments';
        }
        
        $masterCurrency = $this->MasterCurrencies->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterCurrency', $masterCurrency);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterCurrency = $this->MasterCurrencies->newEntity();
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
                $imagePath = $this->uploadImage('MasterCurrencies', $fieldName, 'mastercurrencies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterCurrencies', $fieldName, 'mastercurrencies');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterCurrency = $this->MasterCurrencies->patchEntity($masterCurrency, $data);
            if ($this->MasterCurrencies->save($masterCurrency)) {
                $this->Flash->success(__('The master currency has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master currency could not be saved. Please, try again.'));
        }
        $this->set(compact('masterCurrency'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Currency id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterCurrency = $this->MasterCurrencies->get($id, [
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
                $imagePath = $this->uploadImage('MasterCurrencies', $fieldName, 'mastercurrencies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterCurrencies', $fieldName, 'mastercurrencies');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterCurrency = $this->MasterCurrencies->patchEntity($masterCurrency, $data);
            if ($this->MasterCurrencies->save($masterCurrency)) {
                $this->Flash->success(__('The master currency has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master currency could not be saved. Please, try again.'));
        }
        $this->set(compact('masterCurrency'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Currency id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterCurrency = $this->MasterCurrencies->get($id);
        if ($this->MasterCurrencies->delete($masterCurrency)) {
            $this->Flash->success(__('The master currency has been deleted.'));
        } else {
            $this->Flash->error(__('The master currency could not be deleted. Please, try again.'));
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
        $query = $this->MasterCurrencies->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterCurrencies', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterCurrencies->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterCurrencies', $headers, $fields);
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
        
        $query = $this->MasterCurrencies->find('all');
        
        // Define report configuration
        $title = 'MasterCurrencies Report';
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
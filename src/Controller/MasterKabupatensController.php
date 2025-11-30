<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterKabupatens Controller
 *
 * @property \App\Model\Table\MasterKabupatensTable $MasterKabupatens
 *
 * @method \App\Model\Entity\MasterKabupaten[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterKabupatensController extends AppController
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
            'contain' => ['MasterPropinsis'],
        ];
        $masterKabupatens = $this->paginate($this->MasterKabupatens);

        // Load dropdown data for filters
        $masterpropinsis = $this->MasterKabupatens->MasterPropinsis->find('list')->limit(200)->toArray();
        $this->set(compact('masterKabupatens', 'masterpropinsis'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Kabupaten id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'MasterPropinsis';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterKabupaten = $this->MasterKabupatens->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterKabupaten', $masterKabupaten);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterKabupaten = $this->MasterKabupatens->newEntity();
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
                $imagePath = $this->uploadImage('MasterKabupatens', $fieldName, 'masterkabupatens');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterKabupatens', $fieldName, 'masterkabupatens');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterKabupaten = $this->MasterKabupatens->patchEntity($masterKabupaten, $data);
            if ($this->MasterKabupatens->save($masterKabupaten)) {
                $this->Flash->success(__('The master kabupaten has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master kabupaten could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->MasterKabupatens->MasterPropinsis->find('list', ['limit' => 200]);
        $this->set(compact('masterKabupaten', 'masterPropinsis'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Kabupaten id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterKabupaten = $this->MasterKabupatens->get($id, [
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
                $imagePath = $this->uploadImage('MasterKabupatens', $fieldName, 'masterkabupatens');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterKabupatens', $fieldName, 'masterkabupatens');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterKabupaten = $this->MasterKabupatens->patchEntity($masterKabupaten, $data);
            if ($this->MasterKabupatens->save($masterKabupaten)) {
                $this->Flash->success(__('The master kabupaten has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master kabupaten could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->MasterKabupatens->MasterPropinsis->find('list', ['limit' => 200]);
        $this->set(compact('masterKabupaten', 'masterPropinsis'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Kabupaten id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterKabupaten = $this->MasterKabupatens->get($id);
        if ($this->MasterKabupatens->delete($masterKabupaten)) {
            $this->Flash->success(__('The master kabupaten has been deleted.'));
        } else {
            $this->Flash->error(__('The master kabupaten could not be deleted. Please, try again.'));
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
        $query = $this->MasterKabupatens->find('all')
            ->contain(['MasterPropinsis']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterKabupatens', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterKabupatens->find('all')
            ->contain(['MasterPropinsis']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterKabupatens', $headers, $fields);
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
        
        $query = $this->MasterKabupatens->find('all')
            ->contain(['MasterPropinsis']);
        
        // Define report configuration
        $title = 'MasterKabupatens Report';
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
     * Get list of kabupatens by province ID
     * Used for AJAX cascading dropdowns
     */
    public function getByProvince()
    {
        return $this->getRegionsByParent('MasterKabupatens', 'master_propinsi_id', 'propinsi_id');
    }
}


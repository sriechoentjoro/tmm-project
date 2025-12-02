<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterKecamatans Controller
 *
 * @property \App\Model\Table\MasterKecamatansTable $MasterKecamatans
 *
 * @method \App\Model\Entity\MasterKecamatan[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterKecamatansController extends AppController
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
            'contain' => ['MasterPropinsis', 'MasterKabupatens'],
        ];
        $masterKecamatans = $this->paginate($this->MasterKecamatans);

        // Load dropdown data for filters
        $masterpropinsis = $this->MasterKecamatans->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->MasterKecamatans->MasterKabupatens->find('list')->limit(200)->toArray();
        $this->set(compact('masterKecamatans', 'masterpropinsis', 'masterkabupatens'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Kecamatan id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterKecamatan = $this->MasterKecamatans->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterKecamatan', $masterKecamatan);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterKecamatan = $this->MasterKecamatans->newEntity();
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
                $imagePath = $this->uploadImage('MasterKecamatans', $fieldName, 'masterkecamatans');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterKecamatans', $fieldName, 'masterkecamatans');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterKecamatan = $this->MasterKecamatans->patchEntity($masterKecamatan, $data);
            if ($this->MasterKecamatans->save($masterKecamatan)) {
                $this->Flash->success(__('The master kecamatan has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master kecamatan could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->MasterKecamatans->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->MasterKecamatans->MasterKabupatens->find('list', ['limit' => 200]);
        $this->set(compact('masterKecamatan', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Kecamatan id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterKecamatan = $this->MasterKecamatans->get($id, [
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
                $imagePath = $this->uploadImage('MasterKecamatans', $fieldName, 'masterkecamatans');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterKecamatans', $fieldName, 'masterkecamatans');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterKecamatan = $this->MasterKecamatans->patchEntity($masterKecamatan, $data);
            if ($this->MasterKecamatans->save($masterKecamatan)) {
                $this->Flash->success(__('The master kecamatan has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master kecamatan could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->MasterKecamatans->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->MasterKecamatans->MasterKabupatens->find('list', ['limit' => 200]);
        $this->set(compact('masterKecamatan', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Kecamatan id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterKecamatan = $this->MasterKecamatans->get($id);
        if ($this->MasterKecamatans->delete($masterKecamatan)) {
            $this->Flash->success(__('The master kecamatan has been deleted.'));
        } else {
            $this->Flash->error(__('The master kecamatan could not be deleted. Please, try again.'));
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
        $query = $this->MasterKecamatans->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterKecamatans', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterKecamatans->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterKecamatans', $headers, $fields);
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
        
        $query = $this->MasterKecamatans->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens']);
        
        // Define report configuration
        $title = 'MasterKecamatans Report';
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
     * Get list of kecamatans by kabupaten ID
     * Used for AJAX cascading dropdowns
     */
    public function getByKabupaten()
    {
        return $this->getRegionsByParent('MasterKecamatans', 'master_kabupaten_id', 'kabupaten_id');
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
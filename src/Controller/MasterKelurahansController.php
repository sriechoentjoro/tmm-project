<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterKelurahans Controller
 *
 * @property \App\Model\Table\MasterKelurahansTable $MasterKelurahans
 *
 * @method \App\Model\Entity\MasterKelurahan[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterKelurahansController extends AppController
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
            'contain' => ['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans'],
        ];
        $masterKelurahans = $this->paginate($this->MasterKelurahans);

        // Load dropdown data for filters
        $masterpropinsis = $this->MasterKelurahans->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->MasterKelurahans->MasterKabupatens->find('list')->limit(200)->toArray();
        $masterkecamatans = $this->MasterKelurahans->MasterKecamatans->find('list')->limit(200)->toArray();
        $this->set(compact('masterKelurahans', 'masterpropinsis', 'masterkabupatens', 'masterkecamatans'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Kelurahan id.
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
        $contain[] = 'MasterKecamatans';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterKelurahan = $this->MasterKelurahans->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterKelurahan', $masterKelurahan);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterKelurahan = $this->MasterKelurahans->newEntity();
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
                $imagePath = $this->uploadImage('MasterKelurahans', $fieldName, 'masterkelurahans');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterKelurahans', $fieldName, 'masterkelurahans');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterKelurahan = $this->MasterKelurahans->patchEntity($masterKelurahan, $data);
            if ($this->MasterKelurahans->save($masterKelurahan)) {
                $this->Flash->success(__('The master kelurahan has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master kelurahan could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->MasterKelurahans->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->MasterKelurahans->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->MasterKelurahans->MasterKecamatans->find('list', ['limit' => 200]);
        $this->set(compact('masterKelurahan', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Kelurahan id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterKelurahan = $this->MasterKelurahans->get($id, [
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
                $imagePath = $this->uploadImage('MasterKelurahans', $fieldName, 'masterkelurahans');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterKelurahans', $fieldName, 'masterkelurahans');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterKelurahan = $this->MasterKelurahans->patchEntity($masterKelurahan, $data);
            if ($this->MasterKelurahans->save($masterKelurahan)) {
                $this->Flash->success(__('The master kelurahan has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master kelurahan could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->MasterKelurahans->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->MasterKelurahans->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->MasterKelurahans->MasterKecamatans->find('list', ['limit' => 200]);
        $this->set(compact('masterKelurahan', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Kelurahan id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterKelurahan = $this->MasterKelurahans->get($id);
        if ($this->MasterKelurahans->delete($masterKelurahan)) {
            $this->Flash->success(__('The master kelurahan has been deleted.'));
        } else {
            $this->Flash->error(__('The master kelurahan could not be deleted. Please, try again.'));
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
        $query = $this->MasterKelurahans->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterKelurahans', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterKelurahans->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterKelurahans', $headers, $fields);
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
        
        $query = $this->MasterKelurahans->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans']);
        
        // Define report configuration
        $title = 'MasterKelurahans Report';
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
     * Get list of kelurahans by kecamatan ID
     * Used for AJAX cascading dropdowns
     */
    public function getByKecamatan()
    {
        return $this->getRegionsByParent('MasterKelurahans', 'master_kecamatan_id', 'kecamatan_id');
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
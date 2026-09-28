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
     * The address cascade's lookup, open to anyone already signed in.
     *
     * getMenuRolePermissions() expands granted_actions = '*' to exactly
     * [the menu's own action, index, view], so an action with no menu of its
     * own is refused to every role but administrator. That is the right
     * default for a screen. It is the wrong one here: this action is not a
     * screen but the list of children behind a dropdown, asked for by
     * webroot/js/address-cascade.js while somebody fills in an address on a
     * form they are already allowed to use. Refused, it answers a redirect
     * where the script expects JSON, and the dropdown reads
     * "-- Error Loading --" with nothing to say why.
     *
     * What it returns is the id and name of Indonesian administrative regions
     * - public reference data, the same on every installation, carrying
     * nothing about any candidate, trainee or institution. So being signed in
     * is enough.
     *
     * @param array|null $user The identity, unused here.
     * @return bool
     */
    public function isAuthorized($user = null)
    {
        if ($this->request->getParam('action') === 'getByKabupaten') {
            return (bool)$this->Auth->user('id');
        }

        return parent::isAuthorized($user);
    }

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
                $propinsis = $masterpropinsis;
        $kabupatens = $masterkabupatens;
$this->set(compact('masterKecamatans', 'masterpropinsis', 'masterkabupatens', 'propinsis', 'kabupatens'));
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
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($masterKecamatan);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
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
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($masterKecamatan);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
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
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
    }
}
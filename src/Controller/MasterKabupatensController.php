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
        if ($this->request->getParam('action') === 'getByProvince') {
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
            'contain' => ['MasterPropinsis'],
        ];
        $masterKabupatens = $this->paginate($this->MasterKabupatens);

        // Load dropdown data for filters
        // Scoped by the filter already chosen rather than capped at two
        // hundred by id: every province, and below one only what belongs
        // to it. See AppController::regionListsForFilters().
        $regions = $this->regionListsForFilters();
        $masterpropinsis = $regions['masterPropinsis'];
        $propinsis = $masterpropinsis;
        $this->set(compact('masterKabupatens', 'masterpropinsis', 'propinsis'));
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
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($masterKabupaten);
        $masterPropinsis = $regions['masterPropinsis'];
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
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($masterKabupaten);
        $masterPropinsis = $regions['masterPropinsis'];
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
     * Get list of kabupatens by province ID
     * Used for AJAX cascading dropdowns
     */
    public function getByProvince()
    {
        return $this->getRegionsByParent('MasterKabupatens', 'master_propinsi_id', 'propinsi_id');
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
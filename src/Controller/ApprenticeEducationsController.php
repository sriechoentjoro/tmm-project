<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeEducations Controller
 *
 * @property \App\Model\Table\ApprenticeEducationsTable $ApprenticeEducations
 *
 * @method \App\Model\Entity\ApprenticeEducation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeEducationsController extends AppController
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
            'contain' => ['Apprentices', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens'],
        ];
        $apprenticeEducations = $this->paginate($this->ApprenticeEducations);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeEducations->Apprentices->find('list')->limit(200)->toArray();
        $masterstratas = $this->ApprenticeEducations->MasterStratas->find('list')->limit(200)->toArray();
        $masterpropinsis = $this->ApprenticeEducations->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->ApprenticeEducations->MasterKabupatens->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeEducations', 'apprentices', 'masterstratas', 'masterpropinsis', 'masterkabupatens'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Education id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'MasterStratas';
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeEducation = $this->ApprenticeEducations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeEducation', $apprenticeEducation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeEducation = $this->ApprenticeEducations->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeEducations', $fieldName, 'apprenticeeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeEducations', $fieldName, 'apprenticeeducations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeEducation = $this->ApprenticeEducations->patchEntity($apprenticeEducation, $data);
            if ($this->ApprenticeEducations->save($apprenticeEducation)) {
                $this->Flash->success(__('The apprentice education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice education could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeEducations->Apprentices->find('list', ['limit' => 200]);
        $masterStratas = $this->ApprenticeEducations->MasterStratas->find('list', ['limit' => 200]);
        $masterPropinsis = $this->ApprenticeEducations->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->ApprenticeEducations->MasterKabupatens->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeEducation', 'apprentices', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Education id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeEducation = $this->ApprenticeEducations->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeEducations', $fieldName, 'apprenticeeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeEducations', $fieldName, 'apprenticeeducations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeEducation = $this->ApprenticeEducations->patchEntity($apprenticeEducation, $data);
            if ($this->ApprenticeEducations->save($apprenticeEducation)) {
                $this->Flash->success(__('The apprentice education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice education could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeEducations->Apprentices->find('list', ['limit' => 200]);
        $masterStratas = $this->ApprenticeEducations->MasterStratas->find('list', ['limit' => 200]);
        $masterPropinsis = $this->ApprenticeEducations->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->ApprenticeEducations->MasterKabupatens->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeEducation', 'apprentices', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Education id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeEducation = $this->ApprenticeEducations->get($id);
        if ($this->ApprenticeEducations->delete($apprenticeEducation)) {
            $this->Flash->success(__('The apprentice education has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice education could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeEducations->find('all')
            ->contain(['Apprentices', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeEducations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeEducations->find('all')
            ->contain(['Apprentices', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeEducations', $headers, $fields);
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
        
        $query = $this->ApprenticeEducations->find('all')
            ->contain(['Apprentices', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define report configuration
        $title = 'ApprenticeEducations Report';
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
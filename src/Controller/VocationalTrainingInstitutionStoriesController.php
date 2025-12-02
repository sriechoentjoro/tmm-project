<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * VocationalTrainingInstitutionStories Controller
 *
 * @property \App\Model\Table\VocationalTrainingInstitutionStoriesTable $VocationalTrainingInstitutionStories
 *
 * @method \App\Model\Entity\VocationalTrainingInstitutionStory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VocationalTrainingInstitutionStoriesController extends AppController
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
            'contain' => ['VocationalTrainingInstitutions'],
        ];
        $vocationalTrainingInstitutionStories = $this->paginate($this->VocationalTrainingInstitutionStories);

        // Load dropdown data for filters
        $vocationaltraininginstitutions = $this->VocationalTrainingInstitutionStories->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();
        $this->set(compact('vocationalTrainingInstitutionStories', 'vocationaltraininginstitutions'));
    }



    /**
     * View method
     *
     * @param string|null $id Vocational Training Institution Story id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'VocationalTrainingInstitutions';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $vocationalTrainingInstitutionStory = $this->VocationalTrainingInstitutionStories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('vocationalTrainingInstitutionStory', $vocationalTrainingInstitutionStory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $vocationalTrainingInstitutionStory = $this->VocationalTrainingInstitutionStories->newEntity();
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
                $imagePath = $this->uploadImage('VocationalTrainingInstitutionStories', $fieldName, 'vocationaltraininginstitutionstories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('VocationalTrainingInstitutionStories', $fieldName, 'vocationaltraininginstitutionstories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $vocationalTrainingInstitutionStory = $this->VocationalTrainingInstitutionStories->patchEntity($vocationalTrainingInstitutionStory, $data);
            if ($this->VocationalTrainingInstitutionStories->save($vocationalTrainingInstitutionStory)) {
                $this->Flash->success(__('The vocational training institution story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vocational training institution story could not be saved. Please, try again.'));
        }
        $vocationalTrainingInstitutions = $this->VocationalTrainingInstitutionStories->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $this->set(compact('vocationalTrainingInstitutionStory', 'vocationalTrainingInstitutions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Vocational Training Institution Story id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $vocationalTrainingInstitutionStory = $this->VocationalTrainingInstitutionStories->get($id, [
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
                $imagePath = $this->uploadImage('VocationalTrainingInstitutionStories', $fieldName, 'vocationaltraininginstitutionstories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('VocationalTrainingInstitutionStories', $fieldName, 'vocationaltraininginstitutionstories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $vocationalTrainingInstitutionStory = $this->VocationalTrainingInstitutionStories->patchEntity($vocationalTrainingInstitutionStory, $data);
            if ($this->VocationalTrainingInstitutionStories->save($vocationalTrainingInstitutionStory)) {
                $this->Flash->success(__('The vocational training institution story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vocational training institution story could not be saved. Please, try again.'));
        }
        $vocationalTrainingInstitutions = $this->VocationalTrainingInstitutionStories->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $this->set(compact('vocationalTrainingInstitutionStory', 'vocationalTrainingInstitutions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Vocational Training Institution Story id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vocationalTrainingInstitutionStory = $this->VocationalTrainingInstitutionStories->get($id);
        if ($this->VocationalTrainingInstitutionStories->delete($vocationalTrainingInstitutionStory)) {
            $this->Flash->success(__('The vocational training institution story has been deleted.'));
        } else {
            $this->Flash->error(__('The vocational training institution story could not be deleted. Please, try again.'));
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
        $query = $this->VocationalTrainingInstitutionStories->find('all')
            ->contain(['VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'VocationalTrainingInstitutionStories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->VocationalTrainingInstitutionStories->find('all')
            ->contain(['VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'VocationalTrainingInstitutionStories', $headers, $fields);
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
        
        $query = $this->VocationalTrainingInstitutionStories->find('all')
            ->contain(['VocationalTrainingInstitutions']);
        
        // Define report configuration
        $title = 'VocationalTrainingInstitutionStories Report';
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
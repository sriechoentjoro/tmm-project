<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeFamilyStories Controller
 *
 * @property \App\Model\Table\ApprenticeFamilyStoriesTable $ApprenticeFamilyStories
 *
 * @method \App\Model\Entity\ApprenticeFamilyStory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeFamilyStoriesController extends AppController
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
            'contain' => ['Apprentices'],
        ];
        $apprenticeFamilyStories = $this->paginate($this->ApprenticeFamilyStories);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeFamilyStories->Apprentices->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeFamilyStories', 'apprentices'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Family Story id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeFamilyStory = $this->ApprenticeFamilyStories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeFamilyStory', $apprenticeFamilyStory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeFamilyStory = $this->ApprenticeFamilyStories->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeFamilyStories', $fieldName, 'apprenticefamilystories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeFamilyStories', $fieldName, 'apprenticefamilystories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeFamilyStory = $this->ApprenticeFamilyStories->patchEntity($apprenticeFamilyStory, $data);
            if ($this->ApprenticeFamilyStories->save($apprenticeFamilyStory)) {
                $this->Flash->success(__('The apprentice family story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice family story could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeFamilyStories->Apprentices->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeFamilyStory', 'apprentices'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Family Story id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeFamilyStory = $this->ApprenticeFamilyStories->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeFamilyStories', $fieldName, 'apprenticefamilystories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeFamilyStories', $fieldName, 'apprenticefamilystories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeFamilyStory = $this->ApprenticeFamilyStories->patchEntity($apprenticeFamilyStory, $data);
            if ($this->ApprenticeFamilyStories->save($apprenticeFamilyStory)) {
                $this->Flash->success(__('The apprentice family story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice family story could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeFamilyStories->Apprentices->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeFamilyStory', 'apprentices'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Family Story id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeFamilyStory = $this->ApprenticeFamilyStories->get($id);
        if ($this->ApprenticeFamilyStories->delete($apprenticeFamilyStory)) {
            $this->Flash->success(__('The apprentice family story has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice family story could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeFamilyStories->find('all')
            ->contain(['Apprentices']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeFamilyStories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeFamilyStories->find('all')
            ->contain(['Apprentices']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeFamilyStories', $headers, $fields);
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
        
        $query = $this->ApprenticeFamilyStories->find('all')
            ->contain(['Apprentices']);
        
        // Define report configuration
        $title = 'ApprenticeFamilyStories Report';
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
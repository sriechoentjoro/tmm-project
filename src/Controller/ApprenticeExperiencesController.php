<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeExperiences Controller
 *
 * @property \App\Model\Table\ApprenticeExperiencesTable $ApprenticeExperiences
 *
 * @method \App\Model\Entity\ApprenticeExperience[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeExperiencesController extends AppController
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
            'contain' => ['Apprentices', 'MasterEmployeeStatuses'],
        ];
        $apprenticeExperiences = $this->paginate($this->ApprenticeExperiences);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeExperiences->Apprentices->find('list')->limit(200)->toArray();
        $masteremployeestatuses = $this->ApprenticeExperiences->MasterEmployeeStatuses->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeExperiences', 'apprentices', 'masteremployeestatuses'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Experience id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'MasterEmployeeStatuses';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeExperience = $this->ApprenticeExperiences->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeExperience', $apprenticeExperience);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeExperience = $this->ApprenticeExperiences->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeExperiences', $fieldName, 'apprenticeexperiences');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeExperiences', $fieldName, 'apprenticeexperiences');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeExperience = $this->ApprenticeExperiences->patchEntity($apprenticeExperience, $data);
            if ($this->ApprenticeExperiences->save($apprenticeExperience)) {
                $this->Flash->success(__('The apprentice experience has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice experience could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeExperiences->Apprentices->find('list', ['limit' => 200]);
        $masterEmployeeStatuses = $this->ApprenticeExperiences->MasterEmployeeStatuses->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeExperience', 'apprentices', 'masterEmployeeStatuses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Experience id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeExperience = $this->ApprenticeExperiences->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeExperiences', $fieldName, 'apprenticeexperiences');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeExperiences', $fieldName, 'apprenticeexperiences');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeExperience = $this->ApprenticeExperiences->patchEntity($apprenticeExperience, $data);
            if ($this->ApprenticeExperiences->save($apprenticeExperience)) {
                $this->Flash->success(__('The apprentice experience has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice experience could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeExperiences->Apprentices->find('list', ['limit' => 200]);
        $masterEmployeeStatuses = $this->ApprenticeExperiences->MasterEmployeeStatuses->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeExperience', 'apprentices', 'masterEmployeeStatuses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Experience id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeExperience = $this->ApprenticeExperiences->get($id);
        if ($this->ApprenticeExperiences->delete($apprenticeExperience)) {
            $this->Flash->success(__('The apprentice experience has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice experience could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeExperiences->find('all')
            ->contain(['Apprentices', 'MasterEmployeeStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeExperiences', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeExperiences->find('all')
            ->contain(['Apprentices', 'MasterEmployeeStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeExperiences', $headers, $fields);
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
        
        $query = $this->ApprenticeExperiences->find('all')
            ->contain(['Apprentices', 'MasterEmployeeStatuses']);
        
        // Define report configuration
        $title = 'ApprenticeExperiences Report';
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
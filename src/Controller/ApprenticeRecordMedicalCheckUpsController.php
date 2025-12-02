<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeRecordMedicalCheckUps Controller
 *
 * @property \App\Model\Table\ApprenticeRecordMedicalCheckUpsTable $ApprenticeRecordMedicalCheckUps
 *
 * @method \App\Model\Entity\ApprenticeRecordMedicalCheckUp[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeRecordMedicalCheckUpsController extends AppController
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
            'contain' => ['Apprentices', 'MasterMedicalCheckUpResults'],
        ];
        $apprenticeRecordMedicalCheckUps = $this->paginate($this->ApprenticeRecordMedicalCheckUps);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeRecordMedicalCheckUps->Apprentices->find('list')->limit(200)->toArray();
        $mastermedicalcheckupresults = $this->ApprenticeRecordMedicalCheckUps->MasterMedicalCheckUpResults->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeRecordMedicalCheckUps', 'apprentices', 'mastermedicalcheckupresults'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Record Medical Check Up id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'MasterMedicalCheckUpResults';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeRecordMedicalCheckUp = $this->ApprenticeRecordMedicalCheckUps->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeRecordMedicalCheckUp', $apprenticeRecordMedicalCheckUp);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeRecordMedicalCheckUp = $this->ApprenticeRecordMedicalCheckUps->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeRecordMedicalCheckUps', $fieldName, 'apprenticerecordmedicalcheckups');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeRecordMedicalCheckUps', $fieldName, 'apprenticerecordmedicalcheckups');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeRecordMedicalCheckUp = $this->ApprenticeRecordMedicalCheckUps->patchEntity($apprenticeRecordMedicalCheckUp, $data);
            if ($this->ApprenticeRecordMedicalCheckUps->save($apprenticeRecordMedicalCheckUp)) {
                $this->Flash->success(__('The apprentice record medical check up has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice record medical check up could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeRecordMedicalCheckUps->Apprentices->find('list', ['limit' => 200]);
        $masterMedicalCheckUpResults = $this->ApprenticeRecordMedicalCheckUps->MasterMedicalCheckUpResults->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeRecordMedicalCheckUp', 'apprentices', 'masterMedicalCheckUpResults'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Record Medical Check Up id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeRecordMedicalCheckUp = $this->ApprenticeRecordMedicalCheckUps->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeRecordMedicalCheckUps', $fieldName, 'apprenticerecordmedicalcheckups');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeRecordMedicalCheckUps', $fieldName, 'apprenticerecordmedicalcheckups');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeRecordMedicalCheckUp = $this->ApprenticeRecordMedicalCheckUps->patchEntity($apprenticeRecordMedicalCheckUp, $data);
            if ($this->ApprenticeRecordMedicalCheckUps->save($apprenticeRecordMedicalCheckUp)) {
                $this->Flash->success(__('The apprentice record medical check up has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice record medical check up could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeRecordMedicalCheckUps->Apprentices->find('list', ['limit' => 200]);
        $masterMedicalCheckUpResults = $this->ApprenticeRecordMedicalCheckUps->MasterMedicalCheckUpResults->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeRecordMedicalCheckUp', 'apprentices', 'masterMedicalCheckUpResults'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Record Medical Check Up id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeRecordMedicalCheckUp = $this->ApprenticeRecordMedicalCheckUps->get($id);
        if ($this->ApprenticeRecordMedicalCheckUps->delete($apprenticeRecordMedicalCheckUp)) {
            $this->Flash->success(__('The apprentice record medical check up has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice record medical check up could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeRecordMedicalCheckUps->find('all')
            ->contain(['Apprentices', 'MasterMedicalCheckUpResults']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeRecordMedicalCheckUps', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeRecordMedicalCheckUps->find('all')
            ->contain(['Apprentices', 'MasterMedicalCheckUpResults']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeRecordMedicalCheckUps', $headers, $fields);
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
        
        $query = $this->ApprenticeRecordMedicalCheckUps->find('all')
            ->contain(['Apprentices', 'MasterMedicalCheckUpResults']);
        
        // Define report configuration
        $title = 'ApprenticeRecordMedicalCheckUps Report';
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
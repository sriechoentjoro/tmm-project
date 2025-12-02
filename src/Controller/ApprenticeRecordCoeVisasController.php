<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeRecordCoeVisas Controller
 *
 * @property \App\Model\Table\ApprenticeRecordCoeVisasTable $ApprenticeRecordCoeVisas
 *
 * @method \App\Model\Entity\ApprenticeRecordCoeVisa[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeRecordCoeVisasController extends AppController
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
            'contain' => ['Apprentices', 'MasterApprenticeCoeTypes'],
        ];
        $apprenticeRecordCoeVisas = $this->paginate($this->ApprenticeRecordCoeVisas);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeRecordCoeVisas->Apprentices->find('list')->limit(200)->toArray();
        $mastercoetypes = $this->ApprenticeRecordCoeVisas->MasterCoeTypes->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeRecordCoeVisas', 'apprentices', 'MasterApprenticeCoeTypes'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Record Coe Visa id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'MasterApprenticeCoeTypes';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeRecordCoeVisa = $this->ApprenticeRecordCoeVisas->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeRecordCoeVisa', $apprenticeRecordCoeVisa);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeRecordCoeVisa = $this->ApprenticeRecordCoeVisas->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeRecordCoeVisas', $fieldName, 'apprenticerecordcoevisas');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeRecordCoeVisas', $fieldName, 'apprenticerecordcoevisas');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeRecordCoeVisa = $this->ApprenticeRecordCoeVisas->patchEntity($apprenticeRecordCoeVisa, $data);
            if ($this->ApprenticeRecordCoeVisas->save($apprenticeRecordCoeVisa)) {
                $this->Flash->success(__('The apprentice record coe visa has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice record coe visa could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeRecordCoeVisas->Apprentices->find('list', ['limit' => 200]);
        $masterCoeTypes = $this->ApprenticeRecordCoeVisas->MasterCoeTypes->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeRecordCoeVisa', 'apprentices', 'MasterApprenticeCoeTypes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Record Coe Visa id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeRecordCoeVisa = $this->ApprenticeRecordCoeVisas->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeRecordCoeVisas', $fieldName, 'apprenticerecordcoevisas');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeRecordCoeVisas', $fieldName, 'apprenticerecordcoevisas');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeRecordCoeVisa = $this->ApprenticeRecordCoeVisas->patchEntity($apprenticeRecordCoeVisa, $data);
            if ($this->ApprenticeRecordCoeVisas->save($apprenticeRecordCoeVisa)) {
                $this->Flash->success(__('The apprentice record coe visa has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice record coe visa could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeRecordCoeVisas->Apprentices->find('list', ['limit' => 200]);
        $masterCoeTypes = $this->ApprenticeRecordCoeVisas->MasterCoeTypes->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeRecordCoeVisa', 'apprentices', 'MasterApprenticeCoeTypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Record Coe Visa id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeRecordCoeVisa = $this->ApprenticeRecordCoeVisas->get($id);
        if ($this->ApprenticeRecordCoeVisas->delete($apprenticeRecordCoeVisa)) {
            $this->Flash->success(__('The apprentice record coe visa has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice record coe visa could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeRecordCoeVisas->find('all')
            ->contain(['Apprentices', 'MasterApprenticeCoeTypes']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeRecordCoeVisas', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeRecordCoeVisas->find('all')
            ->contain(['Apprentices', 'MasterApprenticeCoeTypes']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeRecordCoeVisas', $headers, $fields);
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
        
        $query = $this->ApprenticeRecordCoeVisas->find('all')
            ->contain(['Apprentices', 'MasterApprenticeCoeTypes']);
        
        // Define report configuration
        $title = 'ApprenticeRecordCoeVisas Report';
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
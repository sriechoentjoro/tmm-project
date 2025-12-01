<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeExperiences Controller
 *
 * @property \App\Model\Table\TraineeExperiencesTable $TraineeExperiences
 *
 * @method \App\Model\Entity\TraineeExperience[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeExperiencesController extends AppController
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
            'contain' => ['Trainees', 'MasterEmployeeStatuses'],
        ];
        $traineeExperiences = $this->paginate($this->TraineeExperiences);

        // Load dropdown data for filters
        $trainees = $this->TraineeExperiences->Trainees->find('list')->limit(200)->toArray();
        $masteremployeestatuses = $this->TraineeExperiences->MasterEmployeeStatuses->find('list')->limit(200)->toArray();
        $this->set(compact('traineeExperiences', 'trainees', 'masteremployeestatuses'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Experience id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterEmployeeStatuses';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeExperience = $this->TraineeExperiences->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeExperience', $traineeExperience);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeExperience = $this->TraineeExperiences->newEntity();
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
                $imagePath = $this->uploadImage('TraineeExperiences', $fieldName, 'traineeexperiences');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeExperiences', $fieldName, 'traineeexperiences');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeExperience = $this->TraineeExperiences->patchEntity($traineeExperience, $data);
            if ($this->TraineeExperiences->save($traineeExperience)) {
                $this->Flash->success(__('The trainee experience has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee experience could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeExperiences->Trainees->find('list', ['limit' => 200]);
        $masterEmployeeStatuses = $this->TraineeExperiences->MasterEmployeeStatuses->find('list', ['limit' => 200]);
        $this->set(compact('traineeExperience', 'trainees', 'masterEmployeeStatuses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Experience id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeExperience = $this->TraineeExperiences->get($id, [
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
                $imagePath = $this->uploadImage('TraineeExperiences', $fieldName, 'traineeexperiences');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeExperiences', $fieldName, 'traineeexperiences');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeExperience = $this->TraineeExperiences->patchEntity($traineeExperience, $data);
            if ($this->TraineeExperiences->save($traineeExperience)) {
                $this->Flash->success(__('The trainee experience has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee experience could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeExperiences->Trainees->find('list', ['limit' => 200]);
        $masterEmployeeStatuses = $this->TraineeExperiences->MasterEmployeeStatuses->find('list', ['limit' => 200]);
        $this->set(compact('traineeExperience', 'trainees', 'masterEmployeeStatuses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Experience id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeExperience = $this->TraineeExperiences->get($id);
        if ($this->TraineeExperiences->delete($traineeExperience)) {
            $this->Flash->success(__('The trainee experience has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee experience could not be deleted. Please, try again.'));
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
        $query = $this->TraineeExperiences->find('all')
            ->contain(['Trainees', 'MasterEmployeeStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeExperiences', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeExperiences->find('all')
            ->contain(['Trainees', 'MasterEmployeeStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeExperiences', $headers, $fields);
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
        
        $query = $this->TraineeExperiences->find('all')
            ->contain(['Trainees', 'MasterEmployeeStatuses']);
        
        // Define report configuration
        $title = 'TraineeExperiences Report';
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
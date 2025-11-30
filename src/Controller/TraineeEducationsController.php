<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeEducations Controller
 *
 * @property \App\Model\Table\TraineeEducationsTable $TraineeEducations
 *
 * @method \App\Model\Entity\TraineeEducation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeEducationsController extends AppController
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
            'contain' => ['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens'],
        ];
        $traineeEducations = $this->paginate($this->TraineeEducations);

        // Load dropdown data for filters
        $trainees = $this->TraineeEducations->Trainees->find('list')->limit(200)->toArray();
        $masterstratas = $this->TraineeEducations->MasterStratas->find('list')->limit(200)->toArray();
        $masterpropinsis = $this->TraineeEducations->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->TraineeEducations->MasterKabupatens->find('list')->limit(200)->toArray();
        $this->set(compact('traineeEducations', 'trainees', 'masterstratas', 'masterpropinsis', 'masterkabupatens'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Education id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterStratas';
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeEducation = $this->TraineeEducations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeEducation', $traineeEducation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeEducation = $this->TraineeEducations->newEntity();
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
                $imagePath = $this->uploadImage('TraineeEducations', $fieldName, 'traineeeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeEducations', $fieldName, 'traineeeducations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeEducation = $this->TraineeEducations->patchEntity($traineeEducation, $data);
            if ($this->TraineeEducations->save($traineeEducation)) {
                $this->Flash->success(__('The trainee education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee education could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeEducations->Trainees->find('list', ['limit' => 200]);
        $masterStratas = $this->TraineeEducations->MasterStratas->find('list', ['limit' => 200]);
        $masterPropinsis = $this->TraineeEducations->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->TraineeEducations->MasterKabupatens->find('list', ['limit' => 200]);
        $this->set(compact('traineeEducation', 'trainees', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Education id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeEducation = $this->TraineeEducations->get($id, [
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
                $imagePath = $this->uploadImage('TraineeEducations', $fieldName, 'traineeeducations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeEducations', $fieldName, 'traineeeducations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeEducation = $this->TraineeEducations->patchEntity($traineeEducation, $data);
            if ($this->TraineeEducations->save($traineeEducation)) {
                $this->Flash->success(__('The trainee education has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee education could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeEducations->Trainees->find('list', ['limit' => 200]);
        $masterStratas = $this->TraineeEducations->MasterStratas->find('list', ['limit' => 200]);
        $masterPropinsis = $this->TraineeEducations->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->TraineeEducations->MasterKabupatens->find('list', ['limit' => 200]);
        $this->set(compact('traineeEducation', 'trainees', 'masterStratas', 'masterPropinsis', 'masterKabupatens'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Education id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeEducation = $this->TraineeEducations->get($id);
        if ($this->TraineeEducations->delete($traineeEducation)) {
            $this->Flash->success(__('The trainee education has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee education could not be deleted. Please, try again.'));
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
        $query = $this->TraineeEducations->find('all')
            ->contain(['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeEducations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeEducations->find('all')
            ->contain(['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeEducations', $headers, $fields);
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
        
        $query = $this->TraineeEducations->find('all')
            ->contain(['Trainees', 'MasterStratas', 'MasterPropinsis', 'MasterKabupatens']);
        
        // Define report configuration
        $title = 'TraineeEducations Report';
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
}


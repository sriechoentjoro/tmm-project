<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeFamilies Controller
 *
 * @property \App\Model\Table\TraineeFamiliesTable $TraineeFamilies
 *
 * @method \App\Model\Entity\TraineeFamily[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeFamiliesController extends AppController
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
            'contain' => ['Trainees', 'MasterFamilyConnections', 'MasterOccupations'],
        ];
        $traineeFamilies = $this->paginate($this->TraineeFamilies);

        // Load dropdown data for filters
        $trainees = $this->TraineeFamilies->Trainees->find('list')->limit(200)->toArray();
        $masterfamilyconnections = $this->TraineeFamilies->MasterFamilyConnections->find('list')->limit(200)->toArray();
        $masteroccupations = $this->TraineeFamilies->MasterOccupations->find('list')->limit(200)->toArray();
        $this->set(compact('traineeFamilies', 'trainees', 'masterfamilyconnections', 'masteroccupations'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Family id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterFamilyConnections';
        $contain[] = 'MasterOccupations';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeFamily = $this->TraineeFamilies->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeFamily', $traineeFamily);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeFamily = $this->TraineeFamilies->newEntity();
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
                $imagePath = $this->uploadImage('TraineeFamilies', $fieldName, 'traineefamilies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeFamilies', $fieldName, 'traineefamilies');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeFamily = $this->TraineeFamilies->patchEntity($traineeFamily, $data);
            if ($this->TraineeFamilies->save($traineeFamily)) {
                $this->Flash->success(__('The trainee family has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee family could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeFamilies->Trainees->find('list', ['limit' => 200]);
        $masterFamilyConnections = $this->TraineeFamilies->MasterFamilyConnections->find('list', ['limit' => 200]);
        $masterOccupations = $this->TraineeFamilies->MasterOccupations->find('list', ['limit' => 200]);
        $this->set(compact('traineeFamily', 'trainees', 'masterFamilyConnections', 'masterOccupations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Family id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeFamily = $this->TraineeFamilies->get($id, [
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
                $imagePath = $this->uploadImage('TraineeFamilies', $fieldName, 'traineefamilies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeFamilies', $fieldName, 'traineefamilies');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeFamily = $this->TraineeFamilies->patchEntity($traineeFamily, $data);
            if ($this->TraineeFamilies->save($traineeFamily)) {
                $this->Flash->success(__('The trainee family has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee family could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeFamilies->Trainees->find('list', ['limit' => 200]);
        $masterFamilyConnections = $this->TraineeFamilies->MasterFamilyConnections->find('list', ['limit' => 200]);
        $masterOccupations = $this->TraineeFamilies->MasterOccupations->find('list', ['limit' => 200]);
        $this->set(compact('traineeFamily', 'trainees', 'masterFamilyConnections', 'masterOccupations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Family id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeFamily = $this->TraineeFamilies->get($id);
        if ($this->TraineeFamilies->delete($traineeFamily)) {
            $this->Flash->success(__('The trainee family has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee family could not be deleted. Please, try again.'));
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
        $query = $this->TraineeFamilies->find('all')
            ->contain(['Trainees', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeFamilies', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeFamilies->find('all')
            ->contain(['Trainees', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeFamilies', $headers, $fields);
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
        
        $query = $this->TraineeFamilies->find('all')
            ->contain(['Trainees', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define report configuration
        $title = 'TraineeFamilies Report';
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


<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeTrainingBatches Controller
 *
 * @property \App\Model\Table\TraineeTrainingBatchesTable $TraineeTrainingBatches
 *
 * @method \App\Model\Entity\TraineeTrainingBatch[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeTrainingBatchesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $traineeTrainingBatches = $this->paginate($this->TraineeTrainingBatches);

        $this->set(compact('traineeTrainingBatches'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Training Batch id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeTrainingBatch = $this->TraineeTrainingBatches->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeTrainingBatch', $traineeTrainingBatch);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeTrainingBatch = $this->TraineeTrainingBatches->newEntity();
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
                $imagePath = $this->uploadImage('TraineeTrainingBatches', $fieldName, 'traineetrainingbatches');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeTrainingBatches', $fieldName, 'traineetrainingbatches');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeTrainingBatch = $this->TraineeTrainingBatches->patchEntity($traineeTrainingBatch, $data);
            if ($this->TraineeTrainingBatches->save($traineeTrainingBatch)) {
                $this->Flash->success(__('The trainee training batch has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee training batch could not be saved. Please, try again.'));
        }
        $this->set(compact('traineeTrainingBatch'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Training Batch id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeTrainingBatch = $this->TraineeTrainingBatches->get($id, [
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
                $imagePath = $this->uploadImage('TraineeTrainingBatches', $fieldName, 'traineetrainingbatches');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeTrainingBatches', $fieldName, 'traineetrainingbatches');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeTrainingBatch = $this->TraineeTrainingBatches->patchEntity($traineeTrainingBatch, $data);
            if ($this->TraineeTrainingBatches->save($traineeTrainingBatch)) {
                $this->Flash->success(__('The trainee training batch has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee training batch could not be saved. Please, try again.'));
        }
        $this->set(compact('traineeTrainingBatch'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Training Batch id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeTrainingBatch = $this->TraineeTrainingBatches->get($id);
        if ($this->TraineeTrainingBatches->delete($traineeTrainingBatch)) {
            $this->Flash->success(__('The trainee training batch has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee training batch could not be deleted. Please, try again.'));
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
        $query = $this->TraineeTrainingBatches->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeTrainingBatches', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeTrainingBatches->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeTrainingBatches', $headers, $fields);
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
        
        $query = $this->TraineeTrainingBatches->find('all');
        
        // Define report configuration
        $title = 'TraineeTrainingBatches Report';
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
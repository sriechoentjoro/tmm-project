<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeInstallments Controller
 *
 * @property \App\Model\Table\TraineeInstallmentsTable $TraineeInstallments
 *
 * @method \App\Model\Entity\TraineeInstallment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeInstallmentsController extends AppController
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
            'contain' => ['Trainees', 'MasterTransactionCategories', 'MasterCurrencies'],
        ];
        $traineeInstallments = $this->paginate($this->TraineeInstallments);

        // Load dropdown data for filters
        $trainees = $this->TraineeInstallments->Trainees->find('list')->limit(200)->toArray();
        $mastertransactioncategories = $this->TraineeInstallments->MasterTransactionCategories->find('list')->limit(200)->toArray();
        $mastercurrencies = $this->TraineeInstallments->MasterCurrencies->find('list')->limit(200)->toArray();
        $this->set(compact('traineeInstallments', 'trainees', 'mastertransactioncategories', 'mastercurrencies'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Installment id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterTransactionCategories';
        $contain[] = 'MasterCurrencies';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeInstallment = $this->TraineeInstallments->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeInstallment', $traineeInstallment);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeInstallment = $this->TraineeInstallments->newEntity();
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
                $imagePath = $this->uploadImage('TraineeInstallments', $fieldName, 'traineeinstallments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeInstallments', $fieldName, 'traineeinstallments');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeInstallment = $this->TraineeInstallments->patchEntity($traineeInstallment, $data);
            if ($this->TraineeInstallments->save($traineeInstallment)) {
                $this->Flash->success(__('The trainee installment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee installment could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeInstallments->Trainees->find('list', ['limit' => 200]);
        $masterTransactionCategories = $this->TraineeInstallments->MasterTransactionCategories->find('list', ['limit' => 200]);
        $masterCurrencies = $this->TraineeInstallments->MasterCurrencies->find('list', ['limit' => 200]);
        $this->set(compact('traineeInstallment', 'trainees', 'masterTransactionCategories', 'masterCurrencies'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Installment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeInstallment = $this->TraineeInstallments->get($id, [
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
                $imagePath = $this->uploadImage('TraineeInstallments', $fieldName, 'traineeinstallments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeInstallments', $fieldName, 'traineeinstallments');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeInstallment = $this->TraineeInstallments->patchEntity($traineeInstallment, $data);
            if ($this->TraineeInstallments->save($traineeInstallment)) {
                $this->Flash->success(__('The trainee installment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee installment could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeInstallments->Trainees->find('list', ['limit' => 200]);
        $masterTransactionCategories = $this->TraineeInstallments->MasterTransactionCategories->find('list', ['limit' => 200]);
        $masterCurrencies = $this->TraineeInstallments->MasterCurrencies->find('list', ['limit' => 200]);
        $this->set(compact('traineeInstallment', 'trainees', 'masterTransactionCategories', 'masterCurrencies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Installment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeInstallment = $this->TraineeInstallments->get($id);
        if ($this->TraineeInstallments->delete($traineeInstallment)) {
            $this->Flash->success(__('The trainee installment has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee installment could not be deleted. Please, try again.'));
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
        $query = $this->TraineeInstallments->find('all')
            ->contain(['Trainees', 'MasterTransactionCategories', 'MasterCurrencies']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeInstallments', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeInstallments->find('all')
            ->contain(['Trainees', 'MasterTransactionCategories', 'MasterCurrencies']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeInstallments', $headers, $fields);
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
        
        $query = $this->TraineeInstallments->find('all')
            ->contain(['Trainees', 'MasterTransactionCategories', 'MasterCurrencies']);
        
        // Define report configuration
        $title = 'TraineeInstallments Report';
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


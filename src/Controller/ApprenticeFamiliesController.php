<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeFamilies Controller
 *
 * @property \App\Model\Table\ApprenticeFamiliesTable $ApprenticeFamilies
 *
 * @method \App\Model\Entity\ApprenticeFamily[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeFamiliesController extends AppController
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
            'contain' => ['Apprentices', 'MasterFamilyConnections', 'MasterOccupations'],
        ];
        $apprenticeFamilies = $this->paginate($this->ApprenticeFamilies);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeFamilies->Apprentices->find('list')->limit(200)->toArray();
        $masterfamilyconnections = $this->ApprenticeFamilies->MasterFamilyConnections->find('list')->limit(200)->toArray();
        $masteroccupations = $this->ApprenticeFamilies->MasterOccupations->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeFamilies', 'apprentices', 'masterfamilyconnections', 'masteroccupations'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Family id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'MasterFamilyConnections';
        $contain[] = 'MasterOccupations';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeFamily = $this->ApprenticeFamilies->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeFamily', $apprenticeFamily);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeFamily = $this->ApprenticeFamilies->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeFamilies', $fieldName, 'apprenticefamilies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeFamilies', $fieldName, 'apprenticefamilies');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeFamily = $this->ApprenticeFamilies->patchEntity($apprenticeFamily, $data);
            if ($this->ApprenticeFamilies->save($apprenticeFamily)) {
                $this->Flash->success(__('The apprentice family has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice family could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeFamilies->Apprentices->find('list', ['limit' => 200]);
        $masterFamilyConnections = $this->ApprenticeFamilies->MasterFamilyConnections->find('list', ['limit' => 200]);
        $masterOccupations = $this->ApprenticeFamilies->MasterOccupations->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeFamily', 'apprentices', 'masterFamilyConnections', 'masterOccupations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Family id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeFamily = $this->ApprenticeFamilies->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeFamilies', $fieldName, 'apprenticefamilies');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeFamilies', $fieldName, 'apprenticefamilies');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeFamily = $this->ApprenticeFamilies->patchEntity($apprenticeFamily, $data);
            if ($this->ApprenticeFamilies->save($apprenticeFamily)) {
                $this->Flash->success(__('The apprentice family has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice family could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeFamilies->Apprentices->find('list', ['limit' => 200]);
        $masterFamilyConnections = $this->ApprenticeFamilies->MasterFamilyConnections->find('list', ['limit' => 200]);
        $masterOccupations = $this->ApprenticeFamilies->MasterOccupations->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeFamily', 'apprentices', 'masterFamilyConnections', 'masterOccupations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Family id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeFamily = $this->ApprenticeFamilies->get($id);
        if ($this->ApprenticeFamilies->delete($apprenticeFamily)) {
            $this->Flash->success(__('The apprentice family has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice family could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeFamilies->find('all')
            ->contain(['Apprentices', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeFamilies', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeFamilies->find('all')
            ->contain(['Apprentices', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeFamilies', $headers, $fields);
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
        
        $query = $this->ApprenticeFamilies->find('all')
            ->contain(['Apprentices', 'MasterFamilyConnections', 'MasterOccupations']);
        
        // Define report configuration
        $title = 'ApprenticeFamilies Report';
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


<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterOccupations Controller
 *
 * @property \App\Model\Table\MasterOccupationsTable $MasterOccupations
 *
 * @method \App\Model\Entity\MasterOccupation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterOccupationsController extends AppController
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
            'contain' => ['MasterOccupationCategories'],
        ];
        $masterOccupations = $this->paginate($this->MasterOccupations);

        // Load dropdown data for filters
        $occupationcategories = $this->MasterOccupations->OccupationCategories->find('list')->limit(200)->toArray();
        $this->set(compact('masterOccupations', 'MasterOccupationCategories'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Occupation id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'MasterOccupationCategories';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterOccupation = $this->MasterOccupations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterOccupation', $masterOccupation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterOccupation = $this->MasterOccupations->newEntity();
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
                $imagePath = $this->uploadImage('MasterOccupations', $fieldName, 'masteroccupations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterOccupations', $fieldName, 'masteroccupations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterOccupation = $this->MasterOccupations->patchEntity($masterOccupation, $data);
            if ($this->MasterOccupations->save($masterOccupation)) {
                $this->Flash->success(__('The master occupation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master occupation could not be saved. Please, try again.'));
        }
        $occupationCategories = $this->MasterOccupations->OccupationCategories->find('list', ['limit' => 200]);
        $this->set(compact('masterOccupation', 'MasterOccupationCategories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Occupation id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterOccupation = $this->MasterOccupations->get($id, [
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
                $imagePath = $this->uploadImage('MasterOccupations', $fieldName, 'masteroccupations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterOccupations', $fieldName, 'masteroccupations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterOccupation = $this->MasterOccupations->patchEntity($masterOccupation, $data);
            if ($this->MasterOccupations->save($masterOccupation)) {
                $this->Flash->success(__('The master occupation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master occupation could not be saved. Please, try again.'));
        }
        $occupationCategories = $this->MasterOccupations->OccupationCategories->find('list', ['limit' => 200]);
        $this->set(compact('masterOccupation', 'MasterOccupationCategories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Occupation id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterOccupation = $this->MasterOccupations->get($id);
        if ($this->MasterOccupations->delete($masterOccupation)) {
            $this->Flash->success(__('The master occupation has been deleted.'));
        } else {
            $this->Flash->error(__('The master occupation could not be deleted. Please, try again.'));
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
        $query = $this->MasterOccupations->find('all')
            ->contain(['MasterOccupationCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterOccupations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterOccupations->find('all')
            ->contain(['MasterOccupationCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterOccupations', $headers, $fields);
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
        
        $query = $this->MasterOccupations->find('all')
            ->contain(['MasterOccupationCategories']);
        
        // Define report configuration
        $title = 'MasterOccupations Report';
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


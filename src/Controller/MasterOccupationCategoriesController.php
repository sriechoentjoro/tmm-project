<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterOccupationCategories Controller
 *
 * @property \App\Model\Table\MasterOccupationCategoriesTable $MasterOccupationCategories
 *
 * @method \App\Model\Entity\MasterOccupationCategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterOccupationCategoriesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterOccupationCategories = $this->paginate($this->MasterOccupationCategories);

        $this->set(compact('masterOccupationCategories'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Occupation Category id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterOccupationCategory = $this->MasterOccupationCategories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterOccupationCategory', $masterOccupationCategory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterOccupationCategory = $this->MasterOccupationCategories->newEntity();
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
                $imagePath = $this->uploadImage('MasterOccupationCategories', $fieldName, 'masteroccupationcategories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterOccupationCategories', $fieldName, 'masteroccupationcategories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterOccupationCategory = $this->MasterOccupationCategories->patchEntity($masterOccupationCategory, $data);
            if ($this->MasterOccupationCategories->save($masterOccupationCategory)) {
                $this->Flash->success(__('The master occupation category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master occupation category could not be saved. Please, try again.'));
        }
        $this->set(compact('masterOccupationCategory'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Occupation Category id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterOccupationCategory = $this->MasterOccupationCategories->get($id, [
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
                $imagePath = $this->uploadImage('MasterOccupationCategories', $fieldName, 'masteroccupationcategories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterOccupationCategories', $fieldName, 'masteroccupationcategories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterOccupationCategory = $this->MasterOccupationCategories->patchEntity($masterOccupationCategory, $data);
            if ($this->MasterOccupationCategories->save($masterOccupationCategory)) {
                $this->Flash->success(__('The master occupation category has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master occupation category could not be saved. Please, try again.'));
        }
        $this->set(compact('masterOccupationCategory'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Occupation Category id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterOccupationCategory = $this->MasterOccupationCategories->get($id);
        if ($this->MasterOccupationCategories->delete($masterOccupationCategory)) {
            $this->Flash->success(__('The master occupation category has been deleted.'));
        } else {
            $this->Flash->error(__('The master occupation category could not be deleted. Please, try again.'));
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
        $query = $this->MasterOccupationCategories->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterOccupationCategories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterOccupationCategories->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterOccupationCategories', $headers, $fields);
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
        
        $query = $this->MasterOccupationCategories->find('all');
        
        // Define report configuration
        $title = 'MasterOccupationCategories Report';
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


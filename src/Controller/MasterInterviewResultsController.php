<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterInterviewResults Controller
 *
 * @property \App\Model\Table\MasterInterviewResultsTable $MasterInterviewResults
 *
 * @method \App\Model\Entity\MasterInterviewResult[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterInterviewResultsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterInterviewResults = $this->paginate($this->MasterInterviewResults);

        $this->set(compact('masterInterviewResults'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Interview Result id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterInterviewResult = $this->MasterInterviewResults->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterInterviewResult', $masterInterviewResult);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterInterviewResult = $this->MasterInterviewResults->newEntity();
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
                $imagePath = $this->uploadImage('MasterInterviewResults', $fieldName, 'masterinterviewresults');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterInterviewResults', $fieldName, 'masterinterviewresults');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterInterviewResult = $this->MasterInterviewResults->patchEntity($masterInterviewResult, $data);
            if ($this->MasterInterviewResults->save($masterInterviewResult)) {
                $this->Flash->success(__('The master interview result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master interview result could not be saved. Please, try again.'));
        }
        $this->set(compact('masterInterviewResult'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Interview Result id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterInterviewResult = $this->MasterInterviewResults->get($id, [
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
                $imagePath = $this->uploadImage('MasterInterviewResults', $fieldName, 'masterinterviewresults');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterInterviewResults', $fieldName, 'masterinterviewresults');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterInterviewResult = $this->MasterInterviewResults->patchEntity($masterInterviewResult, $data);
            if ($this->MasterInterviewResults->save($masterInterviewResult)) {
                $this->Flash->success(__('The master interview result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master interview result could not be saved. Please, try again.'));
        }
        $this->set(compact('masterInterviewResult'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Interview Result id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterInterviewResult = $this->MasterInterviewResults->get($id);
        if ($this->MasterInterviewResults->delete($masterInterviewResult)) {
            $this->Flash->success(__('The master interview result has been deleted.'));
        } else {
            $this->Flash->error(__('The master interview result could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Preview method - Validate and show data before saving
     *
     * @return \Cake\Http\Response|null Redirects on invalid request.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function preview()
    {
        if (!$this->request->is(['post', 'put', 'patch'])) {
            return $this->redirect(['action' => 'index']);
        }

        $data = $this->request->getData();
        
        // Process date fields - convert HTML5 date array to string
        foreach ($data as $field => $value) {
            if (is_array($value) && isset($value['year'], $value['month'], $value['day'])) {
                $data[$field] = sprintf(
                    '%04d-%02d-%02d',
                    $value['year'],
                    $value['month'],
                    $value['day']
                );
            }
        }
        
        // Determine mode: edit (has ID) vs add (no ID)
        $isEditMode = !empty($data['id']);
        
        // Create entity for validation (no save)
        if ($isEditMode) {
            $masterInterviewResult = $this->MasterInterviewResults->get($data['id'], ['contain' => []]);
            $masterInterviewResult = $this->MasterInterviewResults->patchEntity($masterInterviewResult, $data);
        } else {
            $masterInterviewResult = $this->MasterInterviewResults->newEntity($data);
        }

        // Get validation errors
        $validationErrors = $masterInterviewResult->getErrors();
        
        // Manual check: Edit mode requires ID in data
        if ($isEditMode && empty($data['id'])) {
            $validationErrors['id'] = ['ID is required for updating existing records'];
        }
        
        // Get database schema metadata
        $schema = $this->MasterInterviewResults->getSchema();
        $fieldMetadata = [];
        
        foreach ($schema->columns() as $column) {
            $columnData = $schema->getColumn($column);
            $fieldMetadata[$column] = [
                'type' => $schema->getColumnType($column),
                'nullable' => $schema->isNullable($column),
                'length' => isset($columnData['length']) ? $columnData['length'] : null,
                'default' => isset($columnData['default']) ? $columnData['default'] : null,
            ];
        }
        
        // Ensure ID in data for edit mode display
        if (!empty($masterInterviewResult->id) && empty($data['id'])) {
            $data['id'] = $masterInterviewResult->id;
        }
        
        // Load associations for display
        if (!empty($masterInterviewResult->id)) {
            $masterInterviewResult = $this->MasterInterviewResults->get($masterInterviewResult->id, [
                'contain' => [                ]
            ]);
        }

        $this->set(compact('masterInterviewResult', 'validationErrors', 'fieldMetadata', 'data'));
    }
    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->MasterInterviewResults->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterInterviewResults', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterInterviewResults->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterInterviewResults', $headers, $fields);
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
        
        $query = $this->MasterInterviewResults->find('all');
        
        // Define report configuration
        $title = 'MasterInterviewResults Report';
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


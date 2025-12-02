<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Trainings Controller
 *
 * @property \App\Model\Table\TrainingsTable $Trainings
 *
 * @method \App\Model\Entity\Training[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TrainingsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $trainings = $this->paginate($this->Trainings);

        $this->set(compact('trainings'));
    }



    /**
     * View method
     *
     * @param string|null $id Training id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $training = $this->Trainings->get($id, [
            'contain' => $contain,
        ]);

        $this->set('training', $training);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $training = $this->Trainings->newEntity();
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
                $imagePath = $this->uploadImage('Trainings', $fieldName, 'trainings');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('Trainings', $fieldName, 'trainings');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $training = $this->Trainings->patchEntity($training, $data);
            if ($this->Trainings->save($training)) {
                $this->Flash->success(__('The training has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The training could not be saved. Please, try again.'));
        }
        $this->set(compact('training'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Training id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $training = $this->Trainings->get($id, [
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
                $imagePath = $this->uploadImage('Trainings', $fieldName, 'trainings');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('Trainings', $fieldName, 'trainings');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $training = $this->Trainings->patchEntity($training, $data);
            if ($this->Trainings->save($training)) {
                $this->Flash->success(__('The training has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The training could not be saved. Please, try again.'));
        }
        $this->set(compact('training'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Training id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $training = $this->Trainings->get($id);
        if ($this->Trainings->delete($training)) {
            $this->Flash->success(__('The training has been deleted.'));
        } else {
            $this->Flash->error(__('The training could not be deleted. Please, try again.'));
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
            $training = $this->Trainings->get($data['id'], ['contain' => []]);
            $training = $this->Trainings->patchEntity($training, $data);
        } else {
            $training = $this->Trainings->newEntity($data);
        }

        // Get validation errors
        $validationErrors = $training->getErrors();
        
        // Manual check: Edit mode requires ID in data
        if ($isEditMode && empty($data['id'])) {
            $validationErrors['id'] = ['ID is required for updating existing records'];
        }
        
        // Get database schema metadata
        $schema = $this->Trainings->getSchema();
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
        if (!empty($training->id) && empty($data['id'])) {
            $data['id'] = $training->id;
        }
        
        // Load associations for display
        if (!empty($training->id)) {
            $training = $this->Trainings->get($training->id, [
                'contain' => [                ]
            ]);
        }

        $this->set(compact('training', 'validationErrors', 'fieldMetadata', 'data'));
    }
    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->Trainings->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'Trainings', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->Trainings->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'Trainings', $headers, $fields);
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
        
        $query = $this->Trainings->find('all');
        
        // Define report configuration
        $title = 'Trainings Report';
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
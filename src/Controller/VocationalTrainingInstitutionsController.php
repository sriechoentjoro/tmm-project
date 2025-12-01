<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * VocationalTrainingInstitutions Controller
 *
 * @property \App\Model\Table\VocationalTrainingInstitutionsTable $VocationalTrainingInstitutions
 *
 * @method \App\Model\Entity\VocationalTrainingInstitution[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VocationalTrainingInstitutionsController extends AppController
{
    use \App\Controller\ExportTrait;
    
    /**
     * Initialize method
     */
    public function initialize()
    {
        parent::initialize();
        
        // Load Email component with error handling
        try {
            $this->loadComponent('Email');
        } catch (\Exception $e) {
            // Email component failed to load, continue without it
            // This can happen if database tables aren't set up yet
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans'],
        ];
        $vocationalTrainingInstitutions = $this->paginate($this->VocationalTrainingInstitutions);

        // Load dropdown data for filters
        $masterpropinsis = $this->VocationalTrainingInstitutions->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->VocationalTrainingInstitutions->MasterKabupatens->find('list')->limit(200)->toArray();
        $masterkecamatans = $this->VocationalTrainingInstitutions->MasterKecamatans->find('list')->limit(200)->toArray();
        $masterkelurahans = $this->VocationalTrainingInstitutions->MasterKelurahans->find('list')->limit(200)->toArray();
        $this->set(compact('vocationalTrainingInstitutions', 'masterpropinsis', 'masterkabupatens', 'masterkecamatans', 'masterkelurahans'));
    }


                
    /**
     * View method
     *
     * @param string|null $id Vocational Training Institution id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        $contain[] = 'MasterKecamatans';
        $contain[] = 'MasterKelurahans';
        
        // Add HasMany with nested BelongsTo for foreign key display
            // VocationalTrainingInstitutionStories with its associations
        $vocationalTrainingInstitutionStoriesAssociations = [];
        try {
            $vocationalTrainingInstitutionStoriesTable = $this->VocationalTrainingInstitutions->VocationalTrainingInstitutionStories;
            // Get all BelongsTo associations for nested contain
            foreach ($vocationalTrainingInstitutionStoriesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $vocationalTrainingInstitutionStoriesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($vocationalTrainingInstitutionStoriesAssociations)) {
            $contain['VocationalTrainingInstitutionStories'] = $vocationalTrainingInstitutionStoriesAssociations;
        } else {
            $contain[] = 'VocationalTrainingInstitutionStories';
        }
        
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->get($id, [
            'contain' => $contain,
        ]);

        $this->set('vocationalTrainingInstitution', $vocationalTrainingInstitution);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->newEntity();
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
                $imagePath = $this->uploadImage('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->patchEntity($vocationalTrainingInstitution, $data);
            if ($this->VocationalTrainingInstitutions->save($vocationalTrainingInstitution)) {
                // Generate registration token
                $vocationalTrainingInstitution->generateRegistrationToken(48); // 48 hours expiry
                $this->VocationalTrainingInstitutions->save($vocationalTrainingInstitution);
                
                // Send registration email (if Email component is loaded)
                $emailSent = false;
                if (isset($this->Email)) {
                    $registrationUrl = \Cake\Routing\Router::url([
                        'controller' => 'InstitutionRegistration',
                        'action' => 'complete',
                        $vocationalTrainingInstitution->registration_token
                    ], true);
                    
                    $emailSent = $this->Email->sendRegistrationEmail($vocationalTrainingInstitution, $registrationUrl);
                }
                
                if ($emailSent) {
                    $this->Flash->success(__(
                        'Institution saved! Registration email sent to {0}',
                        $vocationalTrainingInstitution->email
                    ));
                } else {
                    $this->Flash->success(__(
                        'Institution saved! Registration token: {0}. Use this URL: {1}',
                        $vocationalTrainingInstitution->registration_token,
                        'http://localhost/tmm/institution-registration/complete/' . $vocationalTrainingInstitution->registration_token
                    ));
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vocational training institution could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->VocationalTrainingInstitutions->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->VocationalTrainingInstitutions->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->VocationalTrainingInstitutions->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->VocationalTrainingInstitutions->MasterKelurahans->find('list', ['limit' => 200]);
        $this->set(compact('vocationalTrainingInstitution', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Vocational Training Institution id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->get($id, [
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
                $imagePath = $this->uploadImage('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->patchEntity($vocationalTrainingInstitution, $data);
            if ($this->VocationalTrainingInstitutions->save($vocationalTrainingInstitution)) {
                $this->Flash->success(__('The vocational training institution has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vocational training institution could not be saved. Please, try again.'));
        }
        $masterPropinsis = $this->VocationalTrainingInstitutions->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->VocationalTrainingInstitutions->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->VocationalTrainingInstitutions->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->VocationalTrainingInstitutions->MasterKelurahans->find('list', ['limit' => 200]);
        $this->set(compact('vocationalTrainingInstitution', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Vocational Training Institution id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->get($id);
        if ($this->VocationalTrainingInstitutions->delete($vocationalTrainingInstitution)) {
            $this->Flash->success(__('The vocational training institution has been deleted.'));
        } else {
            $this->Flash->error(__('The vocational training institution could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Help method - Stakeholder Management Guide
     *
     * @return void
     */
    public function help()
    {
        // This is just a view, no data processing needed
    }

    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->VocationalTrainingInstitutions->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'VocationalTrainingInstitutions', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->VocationalTrainingInstitutions->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'VocationalTrainingInstitutions', $headers, $fields);
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
        
        $query = $this->VocationalTrainingInstitutions->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans']);
        
        // Define report configuration
        $title = 'VocationalTrainingInstitutions Report';
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
     * AJAX method to get related records with filtering and pagination
     * Used by related_records_table element for lazy loading
     */
    public function getRelated()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json')
            ->withCharset('UTF-8');
        
        try {
            // Get filter parameters
            $filterField = $this->request->getQuery('filterField');
            $filterValue = $this->request->getQuery('filterValue');
            $association = $this->request->getQuery('association', 'VocationalTrainingInstitutionStories');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Get the association table
            $associationTable = $this->VocationalTrainingInstitutions->{$association};
            
            // Build query with primary filter from association table
            $query = $associationTable->find()
                ->where([$filterField => $filterValue])
                ->limit($limit)
                ->offset(($page - 1) * $limit);
            
            // Apply column filters
            if ($columnFilters && is_array($columnFilters)) {
                foreach ($columnFilters as $column => $filter) {
                    if (empty($filter['value'])) continue;
                    
                    $operator = isset($filter['operator']) ? $filter['operator'] : 'contains';
                    $value = $filter['value'];
                    
                    switch ($operator) {
                        case 'equals':
                            $query->where([$column => $value]);
                            break;
                        case 'contains':
                            $query->where([$column . ' LIKE' => '%' . $value . '%']);
                            break;
                        case 'starts_with':
                            $query->where([$column . ' LIKE' => $value . '%']);
                            break;
                        case 'ends_with':
                            $query->where([$column . ' LIKE' => '%' . $value]);
                            break;
                        case 'greater_than':
                            $query->where([$column . ' >' => $value]);
                            break;
                        case 'less_than':
                            $query->where([$column . ' <' => $value]);
                            break;
                        case 'not_empty':
                            $query->where([$column . ' IS NOT' => null]);
                            break;
                    }
                }
            }
            
            // Get total count for pagination
            $total = $query->count();
            
            // Execute query
            $results = $query->toArray();
            
            // Check file existence for image/photo/file fields
            foreach ($results as $result) {
                foreach ($result->toArray() as $field => $value) {
                    if (preg_match('/(image|photo|file|document)/i', $field) && !empty($value)) {
                        $fullPath = WWW_ROOT . $value;
                        $result->{$field . '_exists'} = file_exists($fullPath);
                    }
                }
            }
            
            // Build response
            $response = [
                'success' => true,
                'data' => $results,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            return $this->response->withStringBody(json_encode($response));
            
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            return $this->response->withStringBody(json_encode($response));
        }
    }
    
    /**
     * Resend registration email
     *
     * @param string|null $id Institution id
     * @return \Cake\Http\Response|null
     */
    public function resendRegistrationEmail($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $institution = $this->VocationalTrainingInstitutions->get($id);
        
        // Regenerate token
        $institution->generateRegistrationToken(48);
        $this->VocationalTrainingInstitutions->save($institution);
        
        // Send email
        $registrationUrl = \Cake\Routing\Router::url([
            'controller' => 'InstitutionRegistration',
            'action' => 'complete',
            $institution->registration_token
        ], true);
        
        if ($this->Email->sendRegistrationEmail($institution, $registrationUrl)) {
            $this->Flash->success(__('Registration email resent successfully.'));
        } else {
            $this->Flash->error(__('Failed to send email. Please check email configuration.'));
        }
        
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Process Flow Documentation
     */
    public function processFlow()
    {
        $this->viewBuilder()->setLayout('process_flow');
    }
}
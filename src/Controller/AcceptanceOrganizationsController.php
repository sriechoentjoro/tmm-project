<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AcceptanceOrganizations Controller
 *
 * @property \App\Model\Table\AcceptanceOrganizationsTable $AcceptanceOrganizations
 *
 * @method \App\Model\Entity\AcceptanceOrganization[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AcceptanceOrganizationsController extends AppController
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
            'contain' => ['MasterJapanPrefectures'],
        ];
        $acceptanceOrganizations = $this->paginate($this->AcceptanceOrganizations);

        // Load dropdown data for filters
        $masterjapanprefectures = $this->AcceptanceOrganizations->MasterJapanPrefectures->find('list')->limit(200)->toArray();
        $this->set(compact('acceptanceOrganizations', 'masterjapanprefectures'));
    }


                
    /**
     * View method
     *
     * @param string|null $id Acceptance Organization id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [
            'MasterJapanPrefectures'
        ];
        
        // Add HasMany with nested BelongsTo for foreign key display
        // AcceptanceOrganizationStories with its associations
        $acceptanceOrganizationStoriesAssociations = [];
        try {
            $acceptanceOrganizationStoriesTable = $this->AcceptanceOrganizations->AcceptanceOrganizationStories;
            // Get all BelongsTo associations for nested contain
            foreach ($acceptanceOrganizationStoriesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $acceptanceOrganizationStoriesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        // Add AcceptanceOrganizationStories association
        if (!empty($acceptanceOrganizationStoriesAssociations)) {
            $contain['AcceptanceOrganizationStories'] = $acceptanceOrganizationStoriesAssociations;
        } else {
            $contain[] = 'AcceptanceOrganizationStories';
        }
        
        $acceptanceOrganization = $this->AcceptanceOrganizations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('acceptanceOrganization', $acceptanceOrganization);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $acceptanceOrganization = $this->AcceptanceOrganizations->newEntity();
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
                $imagePath = $this->uploadImage('AcceptanceOrganizations', $fieldName, 'acceptanceorganizations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('AcceptanceOrganizations', $fieldName, 'acceptanceorganizations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $acceptanceOrganization = $this->AcceptanceOrganizations->patchEntity($acceptanceOrganization, $data);
            if ($this->AcceptanceOrganizations->save($acceptanceOrganization)) {
                // Log activity
                $this->loadModel('StakeholderActivities');
                $this->StakeholderActivities->logActivity(
                    'registration',
                    'acceptance_org',
                    $acceptanceOrganization->id,
                    'New acceptance organization registered: ' . $acceptanceOrganization->title,
                    array(
                        'status' => $acceptanceOrganization->status,
                        'title' => $acceptanceOrganization->title
                    ),
                    null,
                    $this->Auth->user('id')
                );
                
                $this->Flash->success(__('The acceptance organization has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The acceptance organization could not be saved. Please, try again.'));
        }
        $masterJapanPrefectures = $this->AcceptanceOrganizations->MasterJapanPrefectures->find('list', ['limit' => 200]);
        $this->set(compact('acceptanceOrganization', 'masterJapanPrefectures'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Acceptance Organization id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $acceptanceOrganization = $this->AcceptanceOrganizations->get($id, [
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
                $imagePath = $this->uploadImage('AcceptanceOrganizations', $fieldName, 'acceptanceorganizations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('AcceptanceOrganizations', $fieldName, 'acceptanceorganizations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            // Store original values for change detection
            $originalStatus = $acceptanceOrganization->status;
            $originalTitle = $acceptanceOrganization->title;
            
            $acceptanceOrganization = $this->AcceptanceOrganizations->patchEntity($acceptanceOrganization, $data);
            if ($this->AcceptanceOrganizations->save($acceptanceOrganization)) {
                // Log activity
                $this->loadModel('StakeholderActivities');
                $changes = array();
                
                // Track status change
                if ($originalStatus !== $acceptanceOrganization->status) {
                    $changes['status'] = array(
                        'from' => $originalStatus,
                        'to' => $acceptanceOrganization->status
                    );
                }
                
                // Track title change
                if ($originalTitle !== $acceptanceOrganization->title) {
                    $changes['title'] = array(
                        'from' => $originalTitle,
                        'to' => $acceptanceOrganization->title
                    );
                }
                
                $this->StakeholderActivities->logActivity(
                    'profile_update',
                    'acceptance_org',
                    $acceptanceOrganization->id,
                    'Acceptance organization updated: ' . $acceptanceOrganization->title,
                    array('changes' => $changes),
                    null,
                    $this->Auth->user('id')
                );
                
                $this->Flash->success(__('The acceptance organization has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The acceptance organization could not be saved. Please, try again.'));
        }
        $masterJapanPrefectures = $this->AcceptanceOrganizations->MasterJapanPrefectures->find('list', ['limit' => 200]);
        $this->set(compact('acceptanceOrganization', 'masterJapanPrefectures'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Acceptance Organization id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $acceptanceOrganization = $this->AcceptanceOrganizations->get($id);
        
        // Store title for logging before deletion
        $organizationTitle = $acceptanceOrganization->title;
        $organizationId = $acceptanceOrganization->id;
        
        if ($this->AcceptanceOrganizations->delete($acceptanceOrganization)) {
            // Log activity
            $this->loadModel('StakeholderActivities');
            $this->StakeholderActivities->logActivity(
                'admin_approval', // Using admin_approval as deletion type
                'acceptance_org',
                $organizationId,
                'Acceptance organization deleted: ' . $organizationTitle,
                array('title' => $organizationTitle),
                null,
                $this->Auth->user('id')
            );
            
            $this->Flash->success(__('The acceptance organization has been deleted.'));
        } else {
            $this->Flash->error(__('The acceptance organization could not be deleted. Please, try again.'));
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
        $query = $this->AcceptanceOrganizations->find('all')
            ->contain(['MasterJapanPrefectures']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'AcceptanceOrganizations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->AcceptanceOrganizations->find('all')
            ->contain(['MasterJapanPrefectures']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'AcceptanceOrganizations', $headers, $fields);
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
        
        $query = $this->AcceptanceOrganizations->find('all')
            ->contain(['MasterJapanPrefectures']);
        
        // Define report configuration
        $title = 'AcceptanceOrganizations Report';
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
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Build query with primary filter
            $query = $this->AcceptanceOrganizations->find()
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
                            $query->where([$column . ' IS NOT NULL']);
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
     * AJAX method to get related AcceptanceOrganizationStories
     * Used by related_records_table element for lazy loading
     */
    public function getRelatedStories()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json')
            ->withCharset('UTF-8');
        
        // Debug log
        $this->log('getRelatedStories called', 'debug');
        $this->log('Query params: ' . json_encode($this->request->getQueryParams()), 'debug');
        
        try {
            // Get filter parameters
            $filterField = $this->request->getQuery('filterField');
            $filterValue = $this->request->getQuery('filterValue');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            $this->log("Filter: $filterField = $filterValue", 'debug');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Build query for AcceptanceOrganizationStories
            $query = $this->AcceptanceOrganizations->AcceptanceOrganizationStories->find()
                ->where([$filterField => $filterValue])
                ->contain(['AcceptanceOrganizations'])
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->order(['AcceptanceOrganizationStories.id' => 'DESC']);
            
            // Apply column filters
            if ($columnFilters && is_array($columnFilters)) {
                foreach ($columnFilters as $column => $filter) {
                    if (empty($filter['value'])) continue;
                    
                    $operator = isset($filter['operator']) ? $filter['operator'] : 'contains';
                    $value = $filter['value'];
                    
                    // Prefix column with table name if not already prefixed
                    if (strpos($column, '.') === false) {
                        $column = 'AcceptanceOrganizationStories.' . $column;
                    }
                    
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
                            $query->where([$column . ' IS NOT NULL']);
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
<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CooperativeAssociations Controller
 *
 * @property \App\Model\Table\CooperativeAssociationsTable $CooperativeAssociations
 *
 * @method \App\Model\Entity\CooperativeAssociation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CooperativeAssociationsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $cooperativeAssociations = $this->paginate($this->CooperativeAssociations);

        $this->set(compact('cooperativeAssociations'));
    }


                
    /**
     * View method
     *
     * @param string|null $id Cooperative Association id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
            // CooperativeAssociationStories with its associations
        $cooperativeAssociationStoriesAssociations = [];
        try {
            $cooperativeAssociationStoriesTable = $this->CooperativeAssociations->CooperativeAssociationStories;
            // Get all BelongsTo associations for nested contain
            foreach ($cooperativeAssociationStoriesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $cooperativeAssociationStoriesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($cooperativeAssociationStoriesAssociations)) {
            $contain['CooperativeAssociationStories'] = $cooperativeAssociationStoriesAssociations;
        } else {
            $contain[] = 'CooperativeAssociationStories';
        }
        
        $cooperativeAssociation = $this->CooperativeAssociations->get($id, [
            'contain' => $contain,
        ]);

        $this->set('cooperativeAssociation', $cooperativeAssociation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cooperativeAssociation = $this->CooperativeAssociations->newEntity();
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
                $imagePath = $this->uploadImage('CooperativeAssociations', $fieldName, 'cooperativeassociations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CooperativeAssociations', $fieldName, 'cooperativeassociations');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $cooperativeAssociation = $this->CooperativeAssociations->patchEntity($cooperativeAssociation, $data);
            if ($this->CooperativeAssociations->save($cooperativeAssociation)) {
                // Log activity
                $this->loadModel('StakeholderActivities');
                $this->StakeholderActivities->logActivity(
                    'registration',
                    'cooperative_assoc',
                    $cooperativeAssociation->id,
                    'New cooperative association registered: ' . $cooperativeAssociation->name,
                    array(
                        'status' => $cooperativeAssociation->status,
                        'name' => $cooperativeAssociation->name
                    ),
                    null,
                    $this->Auth->user('id')
                );
                
                $this->Flash->success(__('The cooperative association has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cooperative association could not be saved. Please, try again.'));
        }
        $this->set(compact('cooperativeAssociation'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cooperative Association id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cooperativeAssociation = $this->CooperativeAssociations->get($id, [
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
                $imagePath = $this->uploadImage('CooperativeAssociations', $fieldName, 'cooperativeassociations');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CooperativeAssociations', $fieldName, 'cooperativeassociations');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            // Store original values for change detection
            $originalStatus = $cooperativeAssociation->status;
            $originalName = $cooperativeAssociation->name;
            
            $cooperativeAssociation = $this->CooperativeAssociations->patchEntity($cooperativeAssociation, $data);
            if ($this->CooperativeAssociations->save($cooperativeAssociation)) {
                // Log activity
                $this->loadModel('StakeholderActivities');
                $changes = array();
                
                // Track status change
                if ($originalStatus !== $cooperativeAssociation->status) {
                    $changes['status'] = array(
                        'from' => $originalStatus,
                        'to' => $cooperativeAssociation->status
                    );
                }
                
                // Track name change
                if ($originalName !== $cooperativeAssociation->name) {
                    $changes['name'] = array(
                        'from' => $originalName,
                        'to' => $cooperativeAssociation->name
                    );
                }
                
                $this->StakeholderActivities->logActivity(
                    'profile_update',
                    'cooperative_assoc',
                    $cooperativeAssociation->id,
                    'Cooperative association updated: ' . $cooperativeAssociation->name,
                    array('changes' => $changes),
                    null,
                    $this->Auth->user('id')
                );
                
                $this->Flash->success(__('The cooperative association has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cooperative association could not be saved. Please, try again.'));
        }
        $this->set(compact('cooperativeAssociation'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cooperative Association id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cooperativeAssociation = $this->CooperativeAssociations->get($id);
        
        // Store name for logging before deletion
        $associationName = $cooperativeAssociation->name;
        $associationId = $cooperativeAssociation->id;
        
        if ($this->CooperativeAssociations->delete($cooperativeAssociation)) {
            // Log activity
            $this->loadModel('StakeholderActivities');
            $this->StakeholderActivities->logActivity(
                'admin_approval', // Using admin_approval as deletion type
                'cooperative_assoc',
                $associationId,
                'Cooperative association deleted: ' . $associationName,
                array('name' => $associationName),
                null,
                $this->Auth->user('id')
            );
            
            $this->Flash->success(__('The cooperative association has been deleted.'));
        } else {
            $this->Flash->error(__('The cooperative association could not be deleted. Please, try again.'));
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
        $query = $this->CooperativeAssociations->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CooperativeAssociations', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CooperativeAssociations->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CooperativeAssociations', $headers, $fields);
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
        
        $query = $this->CooperativeAssociations->find('all');
        
        // Define report configuration
        $title = 'CooperativeAssociations Report';
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
            $association = $this->request->getQuery('association', 'CooperativeAssociationStories');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Get the association table
            $associationTable = $this->CooperativeAssociations->{$association};
            
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
     * Process Flow Documentation
     */
    public function processFlow()
    {
        $this->viewBuilder()->setLayout('process_flow');
    }
}
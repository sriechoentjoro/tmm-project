<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeOrders Controller
 *
 * @property \App\Model\Table\ApprenticeOrdersTable $ApprenticeOrders
 *
 * @method \App\Model\Entity\ApprenticeOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeOrdersController extends AppController
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
            'contain' => ['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories'],
        ];
        $apprenticeOrders = $this->paginate($this->ApprenticeOrders);

        // Load dropdown data for filters
        $cooperativeassociations = $this->ApprenticeOrders->CooperativeAssociations->find('list')->limit(200)->toArray();
        $acceptanceorganizations = $this->ApprenticeOrders->AcceptanceOrganizations->find('list')->limit(200)->toArray();
        $masterjobcategories = $this->ApprenticeOrders->MasterJobCategories->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeOrders', 'cooperativeassociations', 'acceptanceorganizations', 'masterjobcategories'));
    }


                
    /**
     * View method
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'CooperativeAssociations';
        $contain[] = 'AcceptanceOrganizations';
        $contain[] = 'MasterJobCategories';
        
        // Add HasMany with nested BelongsTo for foreign key display
            // Apprentices with its associations
        $apprenticesAssociations = [];
        try {
            $apprenticesTable = $this->ApprenticeOrders->Apprentices;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticesAssociations)) {
            $contain['Apprentices'] = $apprenticesAssociations;
        } else {
            $contain[] = 'Apprentices';
        }
        
        $apprenticeOrder = $this->ApprenticeOrders->get($id, [
            'contain' => $contain,
        ]);

        // Debug: Check if apprentices are loaded
        $apprenticesCount = !empty($apprenticeOrder->apprentices) ? count($apprenticeOrder->apprentices) : 0;
        $this->log('ApprenticeOrder ID: ' . $id . ' - Apprentices loaded: ' . $apprenticesCount, 'debug');
        
        // If no apprentices loaded via contain, try direct query with pagination
        $apprenticesPage = $this->request->getQuery('apprentices_page', 1);
        $apprenticesLimit = 50;
        
        if (empty($apprenticeOrder->apprentices)) {
            $apprenticesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Apprentices');
            
            // Get total count
            $totalApprentices = $apprenticesTable->find()
                ->where(['apprentice_order_id' => $id])
                ->count();
            
            // Get paginated data
            $apprenticeOrder->apprentices = $apprenticesTable->find()
                ->where(['apprentice_order_id' => $id])
                ->limit($apprenticesLimit)
                ->offset(($apprenticesPage - 1) * $apprenticesLimit)
                ->all()
                ->toArray();
                
            $this->log('Direct query found: ' . count($apprenticeOrder->apprentices) . ' apprentices (page ' . $apprenticesPage . ' of ' . ceil($totalApprentices / $apprenticesLimit) . ')', 'debug');
            
            // Pass pagination info to view
            $this->set('apprenticesPage', $apprenticesPage);
            $this->set('apprenticesTotal', $totalApprentices);
        } else {
            // If loaded via contain, calculate pagination info
            $this->set('apprenticesPage', 1);
            $this->set('apprenticesTotal', count($apprenticeOrder->apprentices));
        }

        $this->set('apprenticeOrder', $apprenticeOrder);
    }

    /**
     * AJAX Search for related apprentices with server-side filtering
     *
     * @return \Cake\Http\Response|null JSON response
     */
    public function searchApprentices()
    {
        $this->request->allowMethod(['get', 'post']);
        $this->autoRender = false;
        
        try {
            $apprenticeOrderId = $this->request->getQuery('apprentice_order_id');
            $page = (int) $this->request->getQuery('page', 1);
            $limit = (int) $this->request->getQuery('limit', 50);
            $filtersJson = $this->request->getQuery('filters', '{}');
            
            // Decode filters JSON
            $filters = [];
            if (is_string($filtersJson)) {
                $filters = json_decode($filtersJson, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $filters = [];
                }
            } elseif (is_array($filtersJson)) {
                $filters = $filtersJson;
            }
            
            if (empty($apprenticeOrderId)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'error' => 'Missing apprentice_order_id']));
            }
            
            $apprenticesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Apprentices');
            $query = $apprenticesTable->find()
                ->where(['apprentice_order_id' => $apprenticeOrderId]);
            
            // Apply filters with OR logic (match ANY filter, not ALL)
            if (!empty($filters) && is_array($filters)) {
                $orConditions = [];
                
                foreach ($filters as $field => $filterData) {
                    if (!is_array($filterData)) continue;
                    
                    $value = isset($filterData['value']) ? trim($filterData['value']) : '';
                    $operator = isset($filterData['operator']) ? $filterData['operator'] : 'contains';
                    
                    // Skip if value is empty and operator requires a value
                    if ($value === '' && !in_array($operator, ['file_exists', 'file_not_exists'])) {
                        continue;
                    }
                    
                    switch ($operator) {
                        case 'contains':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => '%' . $value . '%'];
                            }
                            break;
                        case 'equals':
                            $orConditions[] = [$field => $value];
                            break;
                        case 'not_equals':
                            $orConditions[] = [$field . ' !=' => $value];
                            break;
                        case 'starts_with':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => $value . '%'];
                            }
                            break;
                        case 'ends_with':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => '%' . $value];
                            }
                            break;
                        case 'greater_than':
                            $orConditions[] = [$field . ' >' => $value];
                            break;
                        case 'less_than':
                            $orConditions[] = [$field . ' <' => $value];
                            break;
                        case 'greater_equal':
                            $orConditions[] = [$field . ' >=' => $value];
                            break;
                        case 'less_equal':
                            $orConditions[] = [$field . ' <=' => $value];
                            break;
                        case 'file_exists':
                            // Records where field is NOT NULL and NOT empty
                            $orConditions[] = function ($exp, $q) use ($field) {
                                return $exp->and_([
                                    $exp->isNotNull($field),
                                    $exp->notEq($field, '')
                                ]);
                            };
                            break;
                        case 'file_not_exists':
                            // Records where field IS NULL or empty
                            $orConditions[] = function ($exp, $q) use ($field) {
                                return $exp->or_([
                                    $exp->isNull($field),
                                    $exp->eq($field, '')
                                ]);
                            };
                            break;
                    }
                }
                
                // Apply OR conditions if any filters were added
                if (!empty($orConditions)) {
                    $query->where(['OR' => $orConditions]);
                }
            }
            
            // Log query before execution
            $sql = $query->sql();
            $this->log('=== AJAX Search Debug ===', 'debug');
            $this->log('Filters: ' . json_encode($filters), 'debug');
            $this->log('SQL: ' . $sql, 'debug');
            
            // Get total count with filters
            $total = $query->count();
            $this->log('Total found: ' . $total, 'debug');
            
            // Get paginated results
            $records = $query
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->all()
                ->toArray();
            
            // Format records for JSON
            $formattedRecords = [];
            foreach ($records as $record) {
                $formattedRecords[] = [
                    'id' => $record->id,
                    'tmm_code' => $record->tmm_code,
                    'name' => $record->name,
                    'identity_number' => $record->identity_number,
                    'birth_date' => $record->birth_date ? $record->birth_date->format('Y-m-d') : null,
                    'image_photo' => $record->image_photo
                ];
            }
            
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'records' => $formattedRecords,
                    'total' => $total,
                    'page' => $page,
                    'pages' => ceil($total / $limit)
                ]));
                
        } catch (\Exception $e) {
            $this->log('SearchApprentices Error: ' . $e->getMessage(), 'error');
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeOrder = $this->ApprenticeOrders->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeOrders', $fieldName, 'apprenticeorders');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeOrders', $fieldName, 'apprenticeorders');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeOrder = $this->ApprenticeOrders->patchEntity($apprenticeOrder, $data);
            if ($this->ApprenticeOrders->save($apprenticeOrder)) {
                $this->Flash->success(__('The apprentice order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice order could not be saved. Please, try again.'));
        }
        $cooperativeAssociations = $this->ApprenticeOrders->CooperativeAssociations->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->ApprenticeOrders->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterJobCategories = $this->ApprenticeOrders->MasterJobCategories->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeOrder', 'cooperativeAssociations', 'acceptanceOrganizations', 'masterJobCategories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeOrder = $this->ApprenticeOrders->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeOrders', $fieldName, 'apprenticeorders');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeOrders', $fieldName, 'apprenticeorders');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeOrder = $this->ApprenticeOrders->patchEntity($apprenticeOrder, $data);
            if ($this->ApprenticeOrders->save($apprenticeOrder)) {
                $this->Flash->success(__('The apprentice order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice order could not be saved. Please, try again.'));
        }
        $cooperativeAssociations = $this->ApprenticeOrders->CooperativeAssociations->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->ApprenticeOrders->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterJobCategories = $this->ApprenticeOrders->MasterJobCategories->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeOrder', 'cooperativeAssociations', 'acceptanceOrganizations', 'masterJobCategories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeOrder = $this->ApprenticeOrders->get($id);
        if ($this->ApprenticeOrders->delete($apprenticeOrder)) {
            $this->Flash->success(__('The apprentice order has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice order could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeOrders->find('all')
            ->contain(['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeOrders', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeOrders->find('all')
            ->contain(['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeOrders', $headers, $fields);
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
        
        $query = $this->ApprenticeOrders->find('all')
            ->contain(['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories']);
        
        // Define report configuration
        $title = 'ApprenticeOrders Report';
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
            $association = $this->request->getQuery('association', 'Apprentices');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Load the association table directly using TableRegistry for cross-database support
            $associationTable = \Cake\ORM\TableRegistry::getTableLocator()->get($association);
            
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
            
            // Debug log
            $this->log('getRelated Response: ' . json_encode([
                'success' => true,
                'dataCount' => count($results),
                'total' => $total,
                'filterField' => $filterField,
                'filterValue' => $filterValue,
                'association' => $association
            ]), 'debug');
            
            return $this->response->withStringBody(json_encode($response));
            
        } catch (\Exception $e) {
            $this->log('getRelated Error: ' . $e->getMessage(), 'error');
            $this->log('Stack trace: ' . $e->getTraceAsString(), 'error');
            
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
            return $this->response->withStringBody(json_encode($response));
        }
    }
}


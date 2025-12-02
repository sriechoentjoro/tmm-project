<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterCandidateInterviewResults Controller
 *
 * @property \App\Model\Table\MasterCandidateInterviewResultsTable $MasterCandidateInterviewResults
 *
 * @method \App\Model\Entity\MasterCandidateInterviewResult[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterCandidateInterviewResultsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterCandidateInterviewResults = $this->paginate($this->MasterCandidateInterviewResults);

        $this->set(compact('masterCandidateInterviewResults'));
    }


                                
    /**
     * View method
     *
     * @param string|null $id Master Candidate Interview Result id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
            // CandidateRecordInterviews with its associations
        $candidateRecordInterviewsAssociations = [];
        try {
            $candidateRecordInterviewsTable = $this->MasterCandidateInterviewResults->CandidateRecordInterviews;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateRecordInterviewsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateRecordInterviewsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateRecordInterviewsAssociations)) {
            $contain['CandidateRecordInterviews'] = $candidateRecordInterviewsAssociations;
        } else {
            $contain[] = 'CandidateRecordInterviews';
        }
        
            // Candidates with its associations
        $candidatesAssociations = [];
        try {
            $candidatesTable = $this->MasterCandidateInterviewResults->Candidates;
            // Get all BelongsTo associations for nested contain
            foreach ($candidatesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidatesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidatesAssociations)) {
            $contain['Candidates'] = $candidatesAssociations;
        } else {
            $contain[] = 'Candidates';
        }
        
        $masterCandidateInterviewResult = $this->MasterCandidateInterviewResults->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterCandidateInterviewResult', $masterCandidateInterviewResult);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterCandidateInterviewResult = $this->MasterCandidateInterviewResults->newEntity();
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
                $imagePath = $this->uploadImage('MasterCandidateInterviewResults', $fieldName, 'mastercandidateinterviewresults');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterCandidateInterviewResults', $fieldName, 'mastercandidateinterviewresults');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterCandidateInterviewResult = $this->MasterCandidateInterviewResults->patchEntity($masterCandidateInterviewResult, $data);
            if ($this->MasterCandidateInterviewResults->save($masterCandidateInterviewResult)) {
                $this->Flash->success(__('The master candidate interview result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master candidate interview result could not be saved. Please, try again.'));
        }
        $this->set(compact('masterCandidateInterviewResult'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Candidate Interview Result id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterCandidateInterviewResult = $this->MasterCandidateInterviewResults->get($id, [
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
                $imagePath = $this->uploadImage('MasterCandidateInterviewResults', $fieldName, 'mastercandidateinterviewresults');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterCandidateInterviewResults', $fieldName, 'mastercandidateinterviewresults');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterCandidateInterviewResult = $this->MasterCandidateInterviewResults->patchEntity($masterCandidateInterviewResult, $data);
            if ($this->MasterCandidateInterviewResults->save($masterCandidateInterviewResult)) {
                $this->Flash->success(__('The master candidate interview result has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master candidate interview result could not be saved. Please, try again.'));
        }
        $this->set(compact('masterCandidateInterviewResult'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Candidate Interview Result id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterCandidateInterviewResult = $this->MasterCandidateInterviewResults->get($id);
        if ($this->MasterCandidateInterviewResults->delete($masterCandidateInterviewResult)) {
            $this->Flash->success(__('The master candidate interview result has been deleted.'));
        } else {
            $this->Flash->error(__('The master candidate interview result could not be deleted. Please, try again.'));
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
        $query = $this->MasterCandidateInterviewResults->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterCandidateInterviewResults', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterCandidateInterviewResults->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterCandidateInterviewResults', $headers, $fields);
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
        
        $query = $this->MasterCandidateInterviewResults->find('all');
        
        // Define report configuration
        $title = 'MasterCandidateInterviewResults Report';
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
            $filterField = $this->request->getQuery('filter_field');
            $filterValue = $this->request->getQuery('filter_value');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Build query with primary filter
            $query = $this->MasterCandidateInterviewResults->find()
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
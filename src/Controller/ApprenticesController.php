<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Apprentices Controller
 *
 * @property \App\Model\Table\ApprenticesTable $Apprentices
 *
 * @method \App\Model\Entity\Apprentice[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticesController extends AppController
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
            'contain' => ['Candidates', 'Trainees', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes'],
        ];
        $apprentices = $this->paginate($this->Apprentices);

        // Load dropdown data for filters
        $candidates = $this->Apprentices->Candidates->find('list')->limit(200)->toArray();
        $trainees = $this->Apprentices->Trainees->find('list')->limit(200)->toArray();
        $apprenticeorders = $this->Apprentices->ApprenticeOrders->find('list')->limit(200)->toArray();
        $vocationaltraininginstitutions = $this->Apprentices->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();
        $acceptanceorganizations = $this->Apprentices->AcceptanceOrganizations->find('list')->limit(200)->toArray();
        $mastergenders = $this->Apprentices->MasterGenders->find('list')->limit(200)->toArray();
        $masterreligions = $this->Apprentices->MasterReligions->find('list')->limit(200)->toArray();
        $mastermarriagestatuses = $this->Apprentices->MasterMarriageStatuses->find('list')->limit(200)->toArray();
        $masterpropinsis = $this->Apprentices->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->Apprentices->MasterKabupatens->find('list')->limit(200)->toArray();
        $masterkecamatans = $this->Apprentices->MasterKecamatans->find('list')->limit(200)->toArray();
        $masterkelurahans = $this->Apprentices->MasterKelurahans->find('list')->limit(200)->toArray();
        $masterbloodtypes = $this->Apprentices->MasterBloodTypes->find('list')->limit(200)->toArray();
        $this->set(compact('apprentices', 'candidates', 'trainees', 'apprenticeorders', 'vocationaltraininginstitutions', 'acceptanceorganizations', 'mastergenders', 'masterreligions', 'mastermarriagestatuses', 'masterpropinsis', 'masterkabupatens', 'masterkecamatans', 'masterkelurahans', 'masterbloodtypes'));
    }


                                                                                                
    /**
     * View method
     *
     * @param string|null $id Apprentice id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'Trainees';
        $contain[] = 'ApprenticeOrders';
        $contain[] = 'VocationalTrainingInstitutions';
        $contain[] = 'AcceptanceOrganizations';
        $contain[] = 'MasterGenders';
        $contain[] = 'MasterReligions';
        $contain[] = 'MasterMarriageStatuses';
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        $contain[] = 'MasterKecamatans';
        $contain[] = 'MasterKelurahans';
        $contain[] = 'MasterBloodTypes';
        
        // Add HasMany with nested BelongsTo for foreign key display
            // ApprenticeCertifications with its associations
        $apprenticeCertificationsAssociations = [];
        try {
            $apprenticeCertificationsTable = $this->Apprentices->ApprenticeCertifications;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticeCertificationsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticeCertificationsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticeCertificationsAssociations)) {
            $contain['ApprenticeCertifications'] = $apprenticeCertificationsAssociations;
        } else {
            $contain[] = 'ApprenticeCertifications';
        }
        
            // ApprenticeCourses with its associations
        $apprenticeCoursesAssociations = [];
        try {
            $apprenticeCoursesTable = $this->Apprentices->ApprenticeCourses;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticeCoursesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticeCoursesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticeCoursesAssociations)) {
            $contain['ApprenticeCourses'] = $apprenticeCoursesAssociations;
        } else {
            $contain[] = 'ApprenticeCourses';
        }
        
            // ApprenticeEducations with its associations
        $apprenticeEducationsAssociations = [];
        try {
            $apprenticeEducationsTable = $this->Apprentices->ApprenticeEducations;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticeEducationsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticeEducationsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticeEducationsAssociations)) {
            $contain['ApprenticeEducations'] = $apprenticeEducationsAssociations;
        } else {
            $contain[] = 'ApprenticeEducations';
        }
        
            // ApprenticeExperiences with its associations
        $apprenticeExperiencesAssociations = [];
        try {
            $apprenticeExperiencesTable = $this->Apprentices->ApprenticeExperiences;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticeExperiencesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticeExperiencesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticeExperiencesAssociations)) {
            $contain['ApprenticeExperiences'] = $apprenticeExperiencesAssociations;
        } else {
            $contain[] = 'ApprenticeExperiences';
        }
        
            // ApprenticeFamilies with its associations
        $apprenticeFamiliesAssociations = [];
        try {
            $apprenticeFamiliesTable = $this->Apprentices->ApprenticeFamilies;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticeFamiliesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticeFamiliesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticeFamiliesAssociations)) {
            $contain['ApprenticeFamilies'] = $apprenticeFamiliesAssociations;
        } else {
            $contain[] = 'ApprenticeFamilies';
        }
        
            // ApprenticeFamilyStories with its associations
        $apprenticeFamilyStoriesAssociations = [];
        try {
            $apprenticeFamilyStoriesTable = $this->Apprentices->ApprenticeFamilyStories;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticeFamilyStoriesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticeFamilyStoriesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticeFamilyStoriesAssociations)) {
            $contain['ApprenticeFamilyStories'] = $apprenticeFamilyStoriesAssociations;
        } else {
            $contain[] = 'ApprenticeFamilyStories';
        }
        
        $apprentice = $this->Apprentices->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprentice', $apprentice);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprentice = $this->Apprentices->newEntity();
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
                $imagePath = $this->uploadImage('Apprentices', $fieldName, 'apprentices');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('Apprentices', $fieldName, 'apprentices');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprentice = $this->Apprentices->patchEntity($apprentice, $data);
            if ($this->Apprentices->save($apprentice)) {
                $this->Flash->success(__('The apprentice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice could not be saved. Please, try again.'));
        }
        $candidates = $this->Apprentices->Candidates->find('list', ['limit' => 200]);
        $trainees = $this->Apprentices->Trainees->find('list', ['limit' => 200]);
        $apprenticeOrders = $this->Apprentices->ApprenticeOrders->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->Apprentices->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->Apprentices->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterGenders = $this->Apprentices->MasterGenders->find('list', ['limit' => 200]);
        $masterReligions = $this->Apprentices->MasterReligions->find('list', ['limit' => 200]);
        $masterMarriageStatuses = $this->Apprentices->MasterMarriageStatuses->find('list', ['limit' => 200]);
        $masterPropinsis = $this->Apprentices->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->Apprentices->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->Apprentices->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->Apprentices->MasterKelurahans->find('list', ['limit' => 200]);
        $masterBloodTypes = $this->Apprentices->MasterBloodTypes->find('list', ['limit' => 200]);
        $this->set(compact('apprentice', 'candidates', 'trainees', 'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations', 'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprentice = $this->Apprentices->get($id, [
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
                $imagePath = $this->uploadImage('Apprentices', $fieldName, 'apprentices');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('Apprentices', $fieldName, 'apprentices');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprentice = $this->Apprentices->patchEntity($apprentice, $data);
            if ($this->Apprentices->save($apprentice)) {
                $this->Flash->success(__('The apprentice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice could not be saved. Please, try again.'));
        }
        $candidates = $this->Apprentices->Candidates->find('list', ['limit' => 200]);
        $trainees = $this->Apprentices->Trainees->find('list', ['limit' => 200]);
        $apprenticeOrders = $this->Apprentices->ApprenticeOrders->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->Apprentices->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->Apprentices->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterGenders = $this->Apprentices->MasterGenders->find('list', ['limit' => 200]);
        $masterReligions = $this->Apprentices->MasterReligions->find('list', ['limit' => 200]);
        $masterMarriageStatuses = $this->Apprentices->MasterMarriageStatuses->find('list', ['limit' => 200]);
        $masterPropinsis = $this->Apprentices->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->Apprentices->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->Apprentices->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->Apprentices->MasterKelurahans->find('list', ['limit' => 200]);
        $masterBloodTypes = $this->Apprentices->MasterBloodTypes->find('list', ['limit' => 200]);
        $this->set(compact('apprentice', 'candidates', 'trainees', 'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations', 'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprentice = $this->Apprentices->get($id);
        if ($this->Apprentices->delete($apprentice)) {
            $this->Flash->success(__('The apprentice has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice could not be deleted. Please, try again.'));
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
        $query = $this->Apprentices->find('all')
            ->contain(['Candidates', 'Trainees', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'Apprentices', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->Apprentices->find('all')
            ->contain(['Candidates', 'Trainees', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'Apprentices', $headers, $fields);
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
        
        $query = $this->Apprentices->find('all')
            ->contain(['Candidates', 'Trainees', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes']);
        
        // Define report configuration
        $title = 'Apprentices Report';
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
            $query = $this->Apprentices->find()
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
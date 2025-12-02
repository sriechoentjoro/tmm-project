<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Trainees Controller
 *
 * @property \App\Model\Table\TraineesTable $Trainees
 *
 * @method \App\Model\Entity\Trainee[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineesController extends AppController
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
            'contain' => ['Candidates', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes'],
        ];
        $trainees = $this->paginate($this->Trainees);

        // Load dropdown data for filters
        $candidates = $this->Trainees->Candidates->find('list')->limit(200)->toArray();
        $apprenticeorders = $this->Trainees->ApprenticeOrders->find('list')->limit(200)->toArray();
        // REMOVED - Table 'trainings' doesn't exist
        // $trainings = $this->Trainees->Trainings->find('list', ['limit' => 200]);
        $vocationaltraininginstitutions = $this->Trainees->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();
        $acceptanceorganizations = $this->Trainees->AcceptanceOrganizations->find('list')->limit(200)->toArray();
        $mastergenders = $this->Trainees->MasterGenders->find('list')->limit(200)->toArray();
        $masterreligions = $this->Trainees->MasterReligions->find('list')->limit(200)->toArray();
        $mastermarriagestatuses = $this->Trainees->MasterMarriageStatuses->find('list')->limit(200)->toArray();
        $masterpropinsis = $this->Trainees->MasterPropinsis->find('list')->limit(200)->toArray();
        $masterkabupatens = $this->Trainees->MasterKabupatens->find('list')->limit(200)->toArray();
        $masterkecamatans = $this->Trainees->MasterKecamatans->find('list')->limit(200)->toArray();
        $masterkelurahans = $this->Trainees->MasterKelurahans->find('list')->limit(200)->toArray();
        $masterbloodtypes = $this->Trainees->MasterBloodTypes->find('list')->limit(200)->toArray();
        // REMOVED - Table 'master_interview_results' doesn't exist
        // $masterInterviewResults = $this->Trainees->MasterInterviewResults->find('list', ['limit' => 200]);
        // REMOVED - Table 'master_rejected_reasons' doesn't exist
        // $masterRejectedReasons = $this->Trainees->MasterRejectedReasons->find('list', ['limit' => 200]);
        $this->set(compact('trainees', 'candidates', 'apprenticeorders', 'vocationaltraininginstitutions', 'acceptanceorganizations', 'mastergenders', 'masterreligions', 'mastermarriagestatuses', 'masterpropinsis', 'masterkabupatens', 'masterkecamatans', 'masterkelurahans', 'masterbloodtypes'));
    }


                                                                                                
    /**
     * View method
     *
     * @param string|null $id Trainee id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'ApprenticeOrders';
        // REMOVED - Table 'trainings' doesn't exist
        // $contain[] = 'Trainings';
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
        // REMOVED - Tables don't exist
        // $contain[] = 'MasterInterviewResults';
        // $contain[] = 'MasterRejectedReasons';
        
        // Add HasMany with nested BelongsTo for foreign key display
            // TraineeCertifications with its associations
        $traineeCertificationsAssociations = [];
        try {
            $traineeCertificationsTable = $this->Trainees->TraineeCertifications;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeCertificationsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeCertificationsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeCertificationsAssociations)) {
            $contain['TraineeCertifications'] = $traineeCertificationsAssociations;
        } else {
            $contain[] = 'TraineeCertifications';
        }
        
            // TraineeCourses with its associations
        $traineeCoursesAssociations = [];
        try {
            $traineeCoursesTable = $this->Trainees->TraineeCourses;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeCoursesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeCoursesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeCoursesAssociations)) {
            $contain['TraineeCourses'] = $traineeCoursesAssociations;
        } else {
            $contain[] = 'TraineeCourses';
        }
        
            // TraineeEducations with its associations
        $traineeEducationsAssociations = [];
        try {
            $traineeEducationsTable = $this->Trainees->TraineeEducations;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeEducationsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeEducationsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeEducationsAssociations)) {
            $contain['TraineeEducations'] = $traineeEducationsAssociations;
        } else {
            $contain[] = 'TraineeEducations';
        }
        
            // TraineeExperiences with its associations
        $traineeExperiencesAssociations = [];
        try {
            $traineeExperiencesTable = $this->Trainees->TraineeExperiences;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeExperiencesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeExperiencesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeExperiencesAssociations)) {
            $contain['TraineeExperiences'] = $traineeExperiencesAssociations;
        } else {
            $contain[] = 'TraineeExperiences';
        }
        
            // TraineeFamilies with its associations
        $traineeFamiliesAssociations = [];
        try {
            $traineeFamiliesTable = $this->Trainees->TraineeFamilies;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeFamiliesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeFamiliesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeFamiliesAssociations)) {
            $contain['TraineeFamilies'] = $traineeFamiliesAssociations;
        } else {
            $contain[] = 'TraineeFamilies';
        }
        
            // TraineeFamilyStories with its associations
        $traineeFamilyStoriesAssociations = [];
        try {
            $traineeFamilyStoriesTable = $this->Trainees->TraineeFamilyStories;
            // Get all BelongsTo associations for nested contain
            foreach ($traineeFamilyStoriesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $traineeFamilyStoriesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($traineeFamilyStoriesAssociations)) {
            $contain['TraineeFamilyStories'] = $traineeFamilyStoriesAssociations;
        } else {
            $contain[] = 'TraineeFamilyStories';
        }
        
        $trainee = $this->Trainees->get($id, [
            'contain' => $contain,
        ]);

        $this->set('trainee', $trainee);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $trainee = $this->Trainees->newEntity();
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
                $imagePath = $this->uploadImage('Trainees', $fieldName, 'trainees');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('Trainees', $fieldName, 'trainees');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $trainee = $this->Trainees->patchEntity($trainee, $data);
            if ($this->Trainees->save($trainee)) {
                $this->Flash->success(__('The trainee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee could not be saved. Please, try again.'));
        }
        $candidates = $this->Trainees->Candidates->find('list', ['limit' => 200]);
        $apprenticeOrders = $this->Trainees->ApprenticeOrders->find('list', ['limit' => 200]);
        // REMOVED - Table 'trainings' doesn't exist
        // $trainings = $this->Trainees->Trainings->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->Trainees->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->Trainees->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterGenders = $this->Trainees->MasterGenders->find('list', ['limit' => 200]);
        $masterReligions = $this->Trainees->MasterReligions->find('list', ['limit' => 200]);
        $masterMarriageStatuses = $this->Trainees->MasterMarriageStatuses->find('list', ['limit' => 200]);
        $masterPropinsis = $this->Trainees->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->Trainees->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->Trainees->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->Trainees->MasterKelurahans->find('list', ['limit' => 200]);
        $masterBloodTypes = $this->Trainees->MasterBloodTypes->find('list', ['limit' => 200]);
        // REMOVED - Table 'master_interview_results' doesn't exist
        // $masterInterviewResults = $this->Trainees->MasterInterviewResults->find('list', ['limit' => 200]);
        // REMOVED - Table 'master_rejected_reasons' doesn't exist
        // $masterRejectedReasons = $this->Trainees->MasterRejectedReasons->find('list', ['limit' => 200]);
        $this->set(compact('trainee', 'candidates', 'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations', 'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $trainee = $this->Trainees->get($id, [
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
                $imagePath = $this->uploadImage('Trainees', $fieldName, 'trainees');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('Trainees', $fieldName, 'trainees');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $trainee = $this->Trainees->patchEntity($trainee, $data);
            if ($this->Trainees->save($trainee)) {
                $this->Flash->success(__('The trainee has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee could not be saved. Please, try again.'));
        }
        $candidates = $this->Trainees->Candidates->find('list', ['limit' => 200]);
        $apprenticeOrders = $this->Trainees->ApprenticeOrders->find('list', ['limit' => 200]);
        // REMOVED - Table 'trainings' doesn't exist
        // $trainings = $this->Trainees->Trainings->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->Trainees->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->Trainees->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterGenders = $this->Trainees->MasterGenders->find('list', ['limit' => 200]);
        $masterReligions = $this->Trainees->MasterReligions->find('list', ['limit' => 200]);
        $masterMarriageStatuses = $this->Trainees->MasterMarriageStatuses->find('list', ['limit' => 200]);
        $masterPropinsis = $this->Trainees->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->Trainees->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->Trainees->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->Trainees->MasterKelurahans->find('list', ['limit' => 200]);
        $masterBloodTypes = $this->Trainees->MasterBloodTypes->find('list', ['limit' => 200]);
        // REMOVED - Table 'master_interview_results' doesn't exist
        // $masterInterviewResults = $this->Trainees->MasterInterviewResults->find('list', ['limit' => 200]);
        // REMOVED - Table 'master_rejected_reasons' doesn't exist
        // $masterRejectedReasons = $this->Trainees->MasterRejectedReasons->find('list', ['limit' => 200]);
        $this->set(compact('trainee', 'candidates', 'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations', 'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $trainee = $this->Trainees->get($id);
        if ($this->Trainees->delete($trainee)) {
            $this->Flash->success(__('The trainee has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee could not be deleted. Please, try again.'));
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
        $query = $this->Trainees->find('all')
            ->contain(['Candidates', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'Trainees', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->Trainees->find('all')
            ->contain(['Candidates', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'Trainees', $headers, $fields);
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
        
        $query = $this->Trainees->find('all')
            ->contain(['Candidates', 'ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes']);
        
        // Define report configuration
        $title = 'Trainees Report';
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
            $query = $this->Trainees->find()
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
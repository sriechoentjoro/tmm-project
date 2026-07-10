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
     * Training dashboard - menu alias. The dashboard itself lives in
     * Dashboard::training (this action was missing, so /trainees/dashboard
     * produced a MissingAction error).
     */
    public function dashboard()
    {
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'training']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders'],
            'limit'   => 50,
        ];
        $trainees = $this->paginate($this->Trainees);

        $traineeIds = array_column($trainees->toArray(), 'id');

        // Score summaries per trainee
        $scoreSummaries = [];
        if (!empty($traineeIds)) {
            $rows = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_training_scorings')->execute(
                'SELECT trainee_id, COUNT(*) AS tests, ROUND(AVG(score),1) AS avg_score,
                        SUM(CASE WHEN score>=60 THEN 1 ELSE 0 END) AS passed
                 FROM trainee_training_test_scores
                 WHERE trainee_id IN (' . implode(',', array_fill(0, count($traineeIds), '?')) . ')
                 GROUP BY trainee_id',
                $traineeIds
            )->fetchAll('assoc');
            foreach ($rows as $r) {
                $scoreSummaries[(int)$r['trainee_id']] = $r;
            }
        }

        // Batch names
        $batchConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $batchRows = $batchConn->execute('SELECT id, batch_name FROM trainee_training_batches')->fetchAll('assoc');
        $batchNames = array_column($batchRows, 'batch_name', 'id');

        // Acceptance orgs & LPKs for filter dropdowns
        $acceptanceOrgs = $this->Trainees->AcceptanceOrganizations->find('list')->limit(200)->toArray();
        $lpks           = $this->Trainees->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();

        $this->set(compact('trainees', 'scoreSummaries', 'batchNames', 'acceptanceOrgs', 'lpks'));
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

        // Score summary from scoring DB
        $scoreRows = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_training_scorings')->execute(
            'SELECT tts.id, tts.score, tts.test_date,
                    mc.title AS competency,
                    g.title  AS grade,
                    CASE WHEN tts.score>=60 THEN 1 ELSE 0 END AS is_pass
             FROM trainee_training_test_scores tts
             LEFT JOIN cms_tmm_trainee_training_scorings.master_training_competencies mc
                    ON mc.id = tts.master_training_competency_id
             LEFT JOIN cms_tmm_trainee_training_scorings.master_training_test_score_grades g
                    ON g.id  = tts.master_training_test_score_grade_id
             WHERE tts.trainee_id = ?
             ORDER BY tts.test_date DESC, mc.title',
            [$id]
        )->fetchAll('assoc');

        $scoreSummary = null;
        if (!empty($scoreRows)) {
            $avg    = array_sum(array_column($scoreRows, 'score')) / count($scoreRows);
            $passed = array_sum(array_column($scoreRows, 'is_pass'));
            $letter = $avg >= 81 ? 'A' : ($avg >= 61 ? 'B' : ($avg >= 41 ? 'C' : ($avg >= 21 ? 'D' : 'E')));
            $scoreSummary = ['avg' => round($avg, 1), 'tests' => count($scoreRows), 'passed' => $passed, 'grade' => $letter];
        }

        // Certificate info
        $certRows = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings')->execute(
            'SELECT tc.id, tc.certificate_no, tc.notes, tc.created,
                    ttb.batch_name
             FROM trainee_certificates tc
             LEFT JOIN trainee_training_batches ttb ON ttb.id = tc.batch_id
             WHERE tc.trainee_id = ?
             ORDER BY tc.created DESC',
            [$id]
        )->fetchAll('assoc');

        // Batch name
        $batchName = null;
        if ($trainee->trainee_training_batch_id) {
            $batchRow = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings')->execute(
                'SELECT batch_name FROM trainee_training_batches WHERE id = ?',
                [$trainee->trainee_training_batch_id]
            )->fetch('assoc');
            $batchName = $batchRow ? $batchRow['batch_name'] : null;
        }

        $this->set(compact('trainee', 'scoreRows', 'scoreSummary', 'certRows', 'batchName'));
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

    /**
     * Additional trainings list
     */
    public function additionalTraining($trainingId = null)
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');

        // All training batches for context / selector
        $batchRows = $conn->execute(
            'SELECT id, batch_name, batch_code, status FROM training_batches ORDER BY id DESC'
        )->fetchAll('assoc');
        $batchMap = array_column($batchRows, null, 'id');

        // Active batch filter
        $trainingId = $trainingId ? (int)$trainingId : null;
        $currentBatch = $trainingId ? ($batchMap[$trainingId] ?? null) : null;

        // Load additional trainings (optionally filtered by training_id)
        if ($trainingId) {
            $rows = $conn->execute(
                'SELECT * FROM additional_trainings WHERE training_id = ? ORDER BY start_date DESC',
                [$trainingId]
            )->fetchAll('assoc');
        } else {
            $rows = $conn->execute(
                'SELECT * FROM additional_trainings ORDER BY training_id ASC, start_date DESC'
            )->fetchAll('assoc');
        }
        $additionalTrainings = $rows;

        // Enrollment data per program
        $progIds = array_column($additionalTrainings, 'id');
        $enrollmentCounts    = [];
        $enrollmentsByProgram = [];
        if (!empty($progIds)) {
            $inList    = implode(',', array_fill(0, count($progIds), '?'));
            $enrollRows = $conn->execute(
                "SELECT additional_training_id, trainee_id, status, attendance_percentage, score, is_passed, enrollment_date, notes
                 FROM additional_training_enrollments
                 WHERE additional_training_id IN ($inList)
                 ORDER BY enrollment_date ASC",
                $progIds
            )->fetchAll('assoc');

            $traineeIds = array_unique(array_column($enrollRows, 'trainee_id'));
            $traineeMap = [];
            if (!empty($traineeIds)) {
                $tConn = $this->Trainees->getConnection();
                $tRows = $tConn->execute(
                    'SELECT id, name, tmm_code FROM trainees WHERE id IN (' .
                    implode(',', array_fill(0, count($traineeIds), '?')) . ')',
                    $traineeIds
                )->fetchAll('assoc');
                $traineeMap = array_column($tRows, null, 'id');
            }
            foreach ($enrollRows as $r) {
                $pid = (int)$r['additional_training_id'];
                $enrollmentCounts[$pid] = ($enrollmentCounts[$pid] ?? 0) + 1;
                $r['trainee'] = $traineeMap[$r['trainee_id']] ?? ['name' => 'Unknown', 'tmm_code' => ''];
                $enrollmentsByProgram[$pid][] = $r;
            }
        }

        // All trainees in the current batch (or all active trainees) for enroll modal
        $tConn2 = $this->Trainees->getConnection();
        if ($trainingId) {
            // Trainees enrolled in the parent training batch
            $batchTrainees = $conn->execute(
                'SELECT trainee_id FROM training_batch_enrollments WHERE training_batch_id = ? AND status NOT IN (\'dropped\',\'transferred\')',
                [$trainingId]
            )->fetchAll('assoc');
            $batchTraineeIds = array_column($batchTrainees, 'trainee_id');
            if (!empty($batchTraineeIds)) {
                $allTrainees = $tConn2->execute(
                    'SELECT id, name, tmm_code FROM trainees WHERE id IN (' .
                    implode(',', array_fill(0, count($batchTraineeIds), '?')) . ') ORDER BY name ASC',
                    $batchTraineeIds
                )->fetchAll('assoc');
            } else {
                // Fallback: all passed trainees if no batch enrollment yet
                $allTrainees = $tConn2->execute(
                    'SELECT id, name, tmm_code FROM trainees WHERE is_candidate_pass = 1 ORDER BY name ASC'
                )->fetchAll('assoc');
            }
        } else {
            $allTrainees = $tConn2->execute(
                'SELECT id, name, tmm_code FROM trainees WHERE is_candidate_pass = 1 ORDER BY name ASC'
            )->fetchAll('assoc');
        }

        // Status counts
        $statusCounts = [];
        foreach ($additionalTrainings as $at) {
            $s = $at['status'] ?? 'planned';
            $statusCounts[$s] = ($statusCounts[$s] ?? 0) + 1;
        }

        // Training modules by category
        $modRows = $conn->execute(
            'SELECT module_code, module_name, module_name_japanese, category, duration_hours, passing_score, weight_percentage
             FROM training_modules ORDER BY category, module_name'
        )->fetchAll('assoc');

        $categoryMeta = [
            'technical'  => ['label'=>'Technical',   'icon'=>'fa-cogs',       'color'=>'#1565c0','bg'=>'#e3f2fd'],
            'language'   => ['label'=>'Language',    'icon'=>'fa-language',   'color'=>'#2e7d32','bg'=>'#e8f5e9'],
            'culture'    => ['label'=>'Culture',     'icon'=>'fa-torii-gate', 'color'=>'#e65100','bg'=>'#fff3e0'],
            'safety'     => ['label'=>'Safety',      'icon'=>'fa-shield-alt', 'color'=>'#b71c1c','bg'=>'#fbe9e7'],
            'soft_skill' => ['label'=>'Soft Skills', 'icon'=>'fa-handshake',  'color'=>'#6a1b9a','bg'=>'#f3e5f5'],
        ];
        $modulesByCategory = [];
        $totalHours = 0;
        foreach ($modRows as $mod) {
            $cat = $mod['category'] ?? 'technical';
            $modulesByCategory[$cat][] = $mod;
            $totalHours += (int)$mod['duration_hours'];
        }
        $totalModules = count($modRows);

        $this->set(compact(
            'additionalTrainings', 'enrollmentCounts', 'enrollmentsByProgram', 'allTrainees',
            'statusCounts', 'modulesByCategory', 'categoryMeta', 'totalHours', 'totalModules',
            'batchRows', 'batchMap', 'trainingId', 'currentBatch'
        ));
    }

    public function saveAdditionalTraining()
    {
        $this->request->allowMethod(['post']);
        $session = $this->request->session();
        $roles   = (array)$session->read('Auth.User.role_names');
        $isAdmin    = in_array('administrator', $roles);
        $isTraining = in_array('tmm-training', $roles);
        if (!$isAdmin && !$isTraining) {
            return $this->redirect(['action' => 'additionalTraining']);
        }

        $data = $this->request->getData();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $now  = date('Y-m-d H:i:s');
        $id   = (int)($data['id'] ?? 0);

        $trainingId = !empty($data['training_id']) ? (int)$data['training_id'] : null;

        $fields = [
            'training_id' => $trainingId,
            'title'       => $data['title'] ?? '',
            'description' => $data['description'] ?? '',
            'start_date'  => $data['start_date'] ?: null,
            'end_date'    => $data['end_date'] ?: null,
            'location'    => $data['location'] ?? '',
            'status'      => $data['status'] ?? 'planned',
            'modified'    => $now,
        ];

        if ($id > 0) {
            $set = implode(', ', array_map(fn($k) => "$k = ?", array_keys($fields)));
            $conn->execute("UPDATE additional_trainings SET $set WHERE id = ?",
                array_merge(array_values($fields), [$id]));
        } else {
            $fields['created'] = $now;
            $cols = implode(', ', array_keys($fields));
            $phs  = implode(', ', array_fill(0, count($fields), '?'));
            $conn->execute("INSERT INTO additional_trainings ($cols) VALUES ($phs)", array_values($fields));
        }

        $redirect = $trainingId
            ? ['action' => 'additionalTraining', $trainingId]
            : ['action' => 'additionalTraining'];
        return $this->redirect($redirect);
    }

    public function enrollToAdditionalTraining()
    {
        $this->request->allowMethod(['post']);
        $session = $this->request->session();
        $roles   = (array)$session->read('Auth.User.role_names');
        $isAdmin    = in_array('administrator', $roles);
        $isTraining = in_array('tmm-training', $roles);
        if (!$isAdmin && !$isTraining) {
            $this->response->getBody()->write(json_encode(['ok' => false, 'msg' => 'Unauthorized']));
            return $this->response->withType('application/json');
        }

        $data     = $this->request->getData();
        $progId   = (int)($data['additional_training_id'] ?? 0);
        $traineeId = (int)($data['trainee_id'] ?? 0);
        if (!$progId || !$traineeId) {
            $this->response->getBody()->write(json_encode(['ok' => false, 'msg' => 'Missing fields']));
            return $this->response->withType('application/json');
        }

        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $now  = date('Y-m-d H:i:s');
        $today = date('Y-m-d');

        // Insert or update enrollment
        $conn->execute(
            "INSERT INTO additional_training_enrollments
                (additional_training_id, trainee_id, enrollment_date, status, created, modified)
             VALUES (?, ?, ?, 'enrolled', ?, ?)
             ON DUPLICATE KEY UPDATE status = 'enrolled', modified = ?",
            [$progId, $traineeId, $today, $now, $now, $now]
        );

        $this->response->getBody()->write(json_encode(['ok' => true]));
        return $this->response->withType('application/json');
    }

    public function updateAdditionalTrainingEnrollment()
    {
        $this->request->allowMethod(['post']);
        $session = $this->request->session();
        $roles   = (array)$session->read('Auth.User.role_names');
        $isAdmin    = in_array('administrator', $roles);
        $isTraining = in_array('tmm-training', $roles);
        if (!$isAdmin && !$isTraining) {
            $this->response->getBody()->write(json_encode(['ok' => false, 'msg' => 'Unauthorized']));
            return $this->response->withType('application/json');
        }

        $data      = $this->request->getData();
        $progId    = (int)($data['additional_training_id'] ?? 0);
        $traineeId = (int)($data['trainee_id'] ?? 0);
        $status    = $data['status'] ?? 'enrolled';
        $attend    = isset($data['attendance_percentage']) && $data['attendance_percentage'] !== '' ? (float)$data['attendance_percentage'] : null;
        $score     = isset($data['score']) && $data['score'] !== '' ? (float)$data['score'] : null;
        $isPassed  = isset($data['is_passed']) ? (int)$data['is_passed'] : null;
        $notes     = $data['notes'] ?? null;

        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $conn->execute(
            "UPDATE additional_training_enrollments
             SET status = ?, attendance_percentage = ?, score = ?, is_passed = ?, notes = ?, modified = NOW()
             WHERE additional_training_id = ? AND trainee_id = ?",
            [$status, $attend, $score, $isPassed, $notes, $progId, $traineeId]
        );

        $this->response->getBody()->write(json_encode(['ok' => true]));
        return $this->response->withType('application/json');
    }

    public function unenrollFromAdditionalTraining()
    {
        $this->request->allowMethod(['post']);
        $session = $this->request->session();
        $roles   = (array)$session->read('Auth.User.role_names');
        $isAdmin    = in_array('administrator', $roles);
        $isTraining = in_array('tmm-training', $roles);
        if (!$isAdmin && !$isTraining) {
            $this->response->getBody()->write(json_encode(['ok' => false, 'msg' => 'Unauthorized']));
            return $this->response->withType('application/json');
        }

        $data      = $this->request->getData();
        $progId    = (int)($data['additional_training_id'] ?? 0);
        $traineeId = (int)($data['trainee_id'] ?? 0);

        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $conn->execute(
            "DELETE FROM additional_training_enrollments WHERE additional_training_id = ? AND trainee_id = ?",
            [$progId, $traineeId]
        );

        $this->response->getBody()->write(json_encode(['ok' => true]));
        return $this->response->withType('application/json');
    }

    /**
     * Promotion worklist: trainees who passed training
     */
    /**
     * Dashboard for TMM Training to review and promote trainees to apprentices.
     * Shows active trainees (is_candidate_pass=1, is_training_pass=0) with training scores.
     */
    public function promoteToApprentice()
    {
        // All trainees eligible for promotion (candidate pass, not yet training pass)
        $trainees = $this->Trainees->find()
            ->contain(['AcceptanceOrganizations', 'VocationalTrainingInstitutions'])
            ->where(['Trainees.is_candidate_pass' => 1, 'Trainees.is_training_pass' => 0])
            ->order(['Trainees.name' => 'ASC'])
            ->all()
            ->toArray();

        // Also include already-promoted trainees for the history section
        $promoted = $this->Trainees->find()
            ->contain(['AcceptanceOrganizations', 'VocationalTrainingInstitutions'])
            ->where(['Trainees.is_candidate_pass' => 1, 'Trainees.is_training_pass' => 1])
            ->order(['Trainees.name' => 'ASC'])
            ->all()
            ->toArray();

        $allTrainees = array_merge($trainees, $promoted);
        $traineeIds  = array_column($allTrainees, 'id');

        // Score summaries per trainee
        $scoreSummary = [];
        if (!empty($traineeIds)) {
            $scoreRows = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_training_scorings')->execute(
                'SELECT trainee_id, COUNT(*) AS tests,
                        ROUND(AVG(score),1) AS avg_score,
                        SUM(CASE WHEN score>=60 THEN 1 ELSE 0 END) AS passed
                 FROM trainee_training_test_scores
                 WHERE trainee_id IN (' . implode(',', array_fill(0, count($traineeIds), '?')) . ')
                 GROUP BY trainee_id',
                $traineeIds
            )->fetchAll('assoc');
            foreach ($scoreRows as $r) {
                $scoreSummary[(int)$r['trainee_id']] = $r;
            }
        }

        // Certificate status
        $certTraineeIds = [];
        $certConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $certRows  = $certConn->execute('SELECT DISTINCT trainee_id FROM trainee_certificates')->fetchAll('assoc');
        $certTraineeIds = array_map('intval', array_column($certRows, 'trainee_id'));

        // Batch names
        $batchRows = $certConn->execute(
            'SELECT ttb.id, ttb.batch_name FROM trainee_training_batches ttb'
        )->fetchAll('assoc');
        $batchNames = array_column($batchRows, 'batch_name', 'id');

        // Relation counts per trainee
        $relCounts = [];
        if (!empty($traineeIds)) {
            $inList = implode(',', array_fill(0, count($traineeIds), '?'));
            $trConn = $this->Trainees->getConnection();
            foreach ([
                'educations'     => 'trainee_educations',
                'experiences'    => 'trainee_experiences',
                'certifications' => 'trainee_certifications',
                'courses'        => 'trainee_courses',
                'families'       => 'trainee_families',
            ] as $key => $table) {
                $rows = $trConn->execute(
                    "SELECT trainee_id, COUNT(*) AS cnt FROM {$table} WHERE trainee_id IN ({$inList}) GROUP BY trainee_id",
                    $traineeIds
                )->fetchAll('assoc');
                foreach ($rows as $r) {
                    $relCounts[(int)$r['trainee_id']][$key] = (int)$r['cnt'];
                }
            }
        }

        // Apprentice records already created
        $apprenticesConn       = \Cake\Datasource\ConnectionManager::get('cms_tmm_apprentices');
        $existingApprenticeIds = array_map('intval', array_column(
            $apprenticesConn->execute("SELECT trainee_id FROM apprentices")->fetchAll('assoc'),
            'trainee_id'
        ));

        // Apprentice orders (placement slots) with current enrollment
        $trConn2 = $this->Trainees->getConnection();
        $apprenticeOrders = $trConn2->execute(
            "SELECT ao.id, ao.title, ao.reference_file,
                    ao.male_trainee_number, ao.female_trainee_number,
                    ao.departure_year, ao.departure_month,
                    COUNT(a.id) AS enrolled
             FROM apprentice_orders ao
             LEFT JOIN cms_tmm_apprentices.apprentices a ON a.apprentice_order_id = ao.id
             GROUP BY ao.id, ao.title, ao.reference_file,
                      ao.male_trainee_number, ao.female_trainee_number,
                      ao.departure_year, ao.departure_month
             ORDER BY ao.id DESC"
        )->fetchAll('assoc');

        $this->set(compact('trainees', 'promoted', 'scoreSummary', 'certTraineeIds',
                           'batchNames', 'relCounts', 'existingApprenticeIds', 'apprenticeOrders'));
    }

    /**
     * Execute trainee → apprentice promotion.
     * Copies all profile data and relations into the apprentices DB.
     */
    public function doPromoteToApprentice($traineeId = null)
    {
        $this->request->allowMethod(['post']);

        if (!$this->hasRole('administrator') && !$this->hasRole('tmm-training')) {
            $this->Flash->error(__('Only TMM Training role can promote trainees to apprentices.'));
            return $this->redirect(['action' => 'promoteToApprentice']);
        }

        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        $trainee = $this->Trainees->get($traineeId, [
            'contain' => [
                'TraineeEducations',
                'TraineeExperiences',
                'TraineeCertifications',
                'TraineeCourses',
                'TraineeFamilies',
                'TraineeFamilyStories',
            ]
        ]);

        $apprenticesConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_apprentices');

        // Guard: already promoted
        $existing = $apprenticesConn->execute(
            "SELECT id FROM apprentices WHERE trainee_id = ? LIMIT 1", [$traineeId]
        )->fetch('assoc');

        if ($existing) {
            $this->Flash->warning(__('Trainee "{0}" is already an apprentice.', $trainee->name));
            return $this->redirect(['action' => 'promoteToApprentice']);
        }

        $gradingRemarks  = $this->request->getData('grading_remarks') ?? '';
        $apprenticeOrderId = $this->request->getData('apprentice_order_id') ?: null;

        // Insert apprentice core record
        try {
        $apprenticesConn->execute(
            "INSERT INTO apprentices (
                candidate_id, trainee_id, applicant_code, tmm_code,
                apprentice_order_id,
                vocational_training_institution_id, acceptance_organization_id,
                identity_number, is_candidate_pass, is_training_pass, is_apprenticeship_pass,
                name, name_katakana, master_gender_id, master_religion_id, master_marriage_status_id,
                birth_place, birth_place_katakana, birth_date,
                telephone_mobile, telephone_emergency, email,
                master_propinsi_id, master_kabupaten_id, master_kecamatan_id, master_kelurahan_id,
                post_code, address, image_photo,
                strengths, weaknesses, hobby, last_salary_amount, application_reasons,
                is_ever_went_to_japan, will_go_to_japan_after_finished,
                expected_work_upon_returning_to_japan, is_holding_passport, saving_goal_amount,
                blood_type_id, body_weight, body_height,
                is_wear_eye_glasses, explain_eye_condition, is_color_blind, explain_color_blind,
                is_right_handed, is_smoking, is_drinking_alcohol, is_tattooed,
                link_whatsapp, link_line, link_instagram, link_facebook, link_tiktok
            ) VALUES (
                ?, ?, ?, ?,
                ?,
                ?, ?,
                ?, 1, 1, 0,
                ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?, ?
            )",
            [
                $trainee->candidate_id, $traineeId, $trainee->applicant_code, $trainee->tmm_code,
                $apprenticeOrderId,
                $trainee->vocational_training_institution_id, $trainee->acceptance_organization_id,
                $trainee->identity_number,
                $trainee->name, $trainee->name_katakana,
                $trainee->master_gender_id, $trainee->master_religion_id, $trainee->master_marriage_status_id,
                $trainee->birth_place, $trainee->birth_place_katakana, $trainee->birth_date,
                $trainee->telephone_mobile, $trainee->telephone_emergency ?? '-', $trainee->email,
                $trainee->master_propinsi_id, $trainee->master_kabupaten_id,
                $trainee->master_kecamatan_id, $trainee->master_kelurahan_id,
                $trainee->post_code, $trainee->address ?? '-', $trainee->image_photo,
                $trainee->strengths, $trainee->weaknesses, $trainee->hobby,
                $trainee->last_salary_amount, $trainee->application_reasons,
                $trainee->is_ever_went_to_japan, $trainee->will_go_to_japan_after_finished,
                $trainee->expected_work_upon_returning_to_japan,
                $trainee->is_holding_passport, $trainee->saving_goal_amount,
                $trainee->blood_type_id, $trainee->body_weight, $trainee->body_height,
                $trainee->is_wear_eye_glasses, $trainee->explain_eye_condition,
                $trainee->is_color_blind, $trainee->explain_color_blind,
                $trainee->is_right_handed, $trainee->is_smoking,
                $trainee->is_drinking_alcohol, $trainee->is_tattooed,
                $trainee->link_whatsapp, $trainee->link_line,
                $trainee->link_instagram, $trainee->link_facebook, $trainee->link_tiktok,
            ]
        );
        $apprenticeId = $apprenticesConn->execute("SELECT LAST_INSERT_ID() as id")->fetchColumn(0);

        // Copy relations
        foreach ($trainee->trainee_educations ?? [] as $row) {
            $apprenticesConn->execute(
                "INSERT INTO apprentice_educations (apprentice_id, master_strata_id, master_propinsi_id, master_kabupaten_id, college_entry_date, college_graduate_date, college_name, college_major)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$apprenticeId, $row->master_strata_id, $row->master_propinsi_id, $row->master_kabupaten_id,
                 $row->college_entry_date, $row->college_graduate_date, $row->college_name, $row->college_major]
            );
        }
        foreach ($trainee->trainee_experiences ?? [] as $row) {
            $apprenticesConn->execute(
                "INSERT INTO apprentice_experiences (apprentice_id, employment_start_year, employment_end_year, company_name, title, master_employee_status_id, employment_awards, last_salary_amount, job_detail, termination_reasons)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$apprenticeId, $row->employment_start_year, $row->employment_end_year, $row->company_name,
                 $row->title, $row->master_employee_status_id, $row->employment_awards,
                 $row->last_salary_amount, $row->job_detail, $row->termination_reasons]
            );
        }
        foreach ($trainee->trainee_certifications ?? [] as $row) {
            $apprenticesConn->execute(
                "INSERT INTO apprentice_certifications (apprentice_id, title, institution_name, certification_date, detail)
                 VALUES (?, ?, ?, ?, ?)",
                [$apprenticeId, $row->title, $row->institution_name, $row->certification_date, $row->detail]
            );
        }
        foreach ($trainee->trainee_courses ?? [] as $row) {
            $apprenticesConn->execute(
                "INSERT INTO apprentice_courses (apprentice_id, vocational_training_institution_id, course_major_id, course_year, title, detail)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$apprenticeId, $row->vocational_training_institution_id, $row->course_major_id,
                 $row->course_year, $row->title, $row->detail]
            );
        }
        foreach ($trainee->trainee_families ?? [] as $row) {
            $apprenticesConn->execute(
                "INSERT INTO apprentice_families (apprentice_id, master_family_connection_id, name, age, master_occupation_id, detail)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$apprenticeId, $row->master_family_connection_id, $row->name, $row->age,
                 $row->master_occupation_id, $row->detail]
            );
        }
        foreach ($trainee->trainee_family_stories ?? [] as $row) {
            $apprenticesConn->execute(
                "INSERT INTO apprentice_family_stories (apprentice_id, title, date_occurrence, problem_classification, problem_contents, problem_inference, problem_solution, image_path)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$apprenticeId, $row->title, $row->date_occurrence, $row->problem_classification,
                 $row->problem_contents, $row->problem_inference, $row->problem_solution, $row->image_path]
            );
        }

        // Mark trainee as promoted
        $trainee->is_training_pass = 1;
        $this->Trainees->save($trainee);

        $this->Flash->success(__('"{0}" has been promoted to apprentice. All profile data copied successfully.', $trainee->name));
        return $this->redirect(['action' => 'promoteToApprentice']);

        } catch (\Exception $e) {
            $this->log('doPromoteToApprentice error: ' . $e->getMessage(), 'error');
            $this->Flash->error(__('Promotion failed for "{0}": {1}', $trainee->name, $e->getMessage()));
            return $this->redirect(['action' => 'promoteToApprentice']);
        }
    }

    /**
     * Promotion checklist: pass flags per trainee
     */
    public function promotionChecklist()
    {
        $this->paginate = ['order' => ['Trainees.id' => 'DESC']];
        $trainees = $this->paginate($this->Trainees);
        $this->set(compact('trainees'));
    }

    /**
     * Promotion history: trainees who have been promoted to apprentice
     */
    public function promotionHistory()
    {
        $apprenticesConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_apprentices');
        $histories = $apprenticesConn->execute(
            "SELECT a.id, a.trainee_id, a.candidate_id, a.name, a.tmm_code, a.applicant_code,
                    a.is_candidate_pass, a.is_training_pass, a.is_apprenticeship_pass
             FROM apprentices a
             ORDER BY a.id DESC
             LIMIT 200"
        )->fetchAll('assoc');

        $this->set(compact('histories'));
    }
}

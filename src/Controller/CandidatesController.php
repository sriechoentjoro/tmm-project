<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Candidates Controller
 *
 * @property \App\Model\Table\CandidatesTable $Candidates
 *
 * @method \App\Model\Entity\Candidate[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidatesController extends AppController
{
    use \App\Controller\ExportTrait;
    use \App\Controller\LpkDataFilterTrait;
    
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['wizard', 'getKabupaten', 'getKecamatan', 'getKelurahan']);
    }
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // Apply institution filter for LPK users
        $query = $this->getInstitutionFilteredQuery('Candidates', 'vocational_training_institution_id');
        
        $this->paginate = [
            'contain' => ['ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes', 'MasterCandidateInterviewResults', 'MasterRejectedReasons'],
        ];
        $candidates = $this->paginate($query);

        // Load dropdown data for filters
        $apprentice_orders = $this->Candidates->ApprenticeOrders->find('list')->limit(200)->toArray();
        $vocational_training_institutions = $this->Candidates->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();
        $acceptance_organizations = $this->Candidates->AcceptanceOrganizations->find('list')->limit(200)->toArray();
        $master_genders = $this->Candidates->MasterGenders->find('list')->limit(200)->toArray();
        $master_religions = $this->Candidates->MasterReligions->find('list')->limit(200)->toArray();
        $master_marriage_statuss = $this->Candidates->MasterMarriageStatuses->find('list')->limit(200)->toArray();
        $master_propinsis = $this->Candidates->MasterPropinsis->find('list')->limit(200)->toArray();
        $master_kabupatens = $this->Candidates->MasterKabupatens->find('list')->limit(200)->toArray();
        $master_kecamatans = $this->Candidates->MasterKecamatans->find('list')->limit(200)->toArray();
        $master_kelurahans = $this->Candidates->MasterKelurahans->find('list')->limit(200)->toArray();
        $master_blood_types = $this->Candidates->MasterBloodTypes->find('list')->limit(200)->toArray();
        $master_candidate_interview_results = $this->Candidates->MasterCandidateInterviewResults->find('list')->limit(200)->toArray();
        $master_rejected_reasons = $this->Candidates->MasterRejectedReasons->find('list')->limit(200)->toArray();
        $this->set(compact('candidates', 'apprentice_orders', 'vocational_training_institutions', 'acceptance_organizations', 'master_genders', 'master_religions', 'master_marriage_statuss', 'master_propinsis', 'master_kabupatens', 'master_kecamatans', 'master_kelurahans', 'master_blood_types', 'master_candidate_interview_results', 'master_rejected_reasons'));
    }

    /**
     * Dashboard method
     * 
     * Display statistics and summary information about candidates
     *
     * @return \Cake\Http\Response|null
     */
    public function dashboard()
    {
        // Apply institution filter for LPK users
        $baseQuery = $this->getInstitutionFilteredQuery('Candidates', 'vocational_training_institution_id');
        
        // Note: candidates table does NOT have created/modified columns
        // Display statistics by training status instead
        $byYear = $this->Candidates->find()
            ->select([
                'status' => 'CASE 
                    WHEN is_training_pass = 1 AND is_apprenticeship_pass = 1 THEN "Passed All"
                    WHEN is_training_pass = 1 THEN "Training Pass Only"
                    WHEN is_apprenticeship_pass = 1 THEN "Apprenticeship Pass Only"
                    ELSE "In Progress"
                END',
                'count' => $this->Candidates->find()->func()->count('*')
            ])
            ->group(['is_training_pass', 'is_apprenticeship_pass'])
            ->all();
        
        // Statistics by host company (acceptance organization)
        // First get the counts
        $byHostRaw = $this->Candidates->find()
            ->select([
                'acceptance_organization_id',
                'count' => $this->Candidates->find()->func()->count('*')
            ])
            ->where(['acceptance_organization_id IS NOT NULL'])
            ->group(['acceptance_organization_id'])
            ->order(['count' => 'DESC'])
            ->limit(10)
            ->toArray();
        
        // Then load full entities with associations
        $orgIds = array_column($byHostRaw, 'acceptance_organization_id');
        $byHost = [];
        if (!empty($orgIds)) {
            $byHost = $this->Candidates->find()
                ->select([
                    'Candidates.acceptance_organization_id',
                    'count' => $this->Candidates->find()->func()->count('*')
                ])
                ->contain(['AcceptanceOrganizations'])
                ->where(['Candidates.acceptance_organization_id IN' => $orgIds])
                ->group(['Candidates.acceptance_organization_id'])
                ->order(['count' => 'DESC'])
                ->all();
        }
        
        // Statistics by supervising company (currently not in schema, use placeholder)
        $bySuper = [];
        
        // Total candidates count
        $totalCount = $this->Candidates->find()->count();
        
        $this->set(compact('byYear', 'byHost', 'bySuper', 'totalCount'));
    }

                                                                                                
    /**
     * View method
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if (!ctype_digit((string)$id)) {
            $this->Flash->error(__('Invalid candidate ID.'));
            return $this->redirect(['action' => 'index']);
        }

        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
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
        $contain[] = 'MasterCandidateInterviewResults';
        $contain[] = 'MasterRejectedReasons';
        
        // Check row-level access for LPK users (load first to verify)
        
        // Add HasMany with nested BelongsTo for foreign key display
            // CandidateCertifications with its associations
        $candidateCertificationsAssociations = [];
        try {
            $candidateCertificationsTable = $this->Candidates->CandidateCertifications;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateCertificationsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateCertificationsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateCertificationsAssociations)) {
            $contain['CandidateCertifications'] = $candidateCertificationsAssociations;
        } else {
            $contain[] = 'CandidateCertifications';
        }
        
            // CandidateCourses with its associations
        $candidateCoursesAssociations = [];
        try {
            $candidateCoursesTable = $this->Candidates->CandidateCourses;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateCoursesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateCoursesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateCoursesAssociations)) {
            $contain['CandidateCourses'] = $candidateCoursesAssociations;
        } else {
            $contain[] = 'CandidateCourses';
        }
        
            // CandidateDocuments with its associations
        $candidateDocumentsAssociations = [];
        try {
            $candidateDocumentsTable = $this->Candidates->CandidateDocuments;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateDocumentsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateDocumentsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateDocumentsAssociations)) {
            $contain['CandidateDocuments'] = $candidateDocumentsAssociations;
        } else {
            $contain[] = 'CandidateDocuments';
        }
        
            // CandidateEducations with its associations
        $candidateEducationsAssociations = [];
        try {
            $candidateEducationsTable = $this->Candidates->CandidateEducations;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateEducationsTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateEducationsAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateEducationsAssociations)) {
            $contain['CandidateEducations'] = $candidateEducationsAssociations;
        } else {
            $contain[] = 'CandidateEducations';
        }
        
            // CandidateExperiences with its associations
        $candidateExperiencesAssociations = [];
        try {
            $candidateExperiencesTable = $this->Candidates->CandidateExperiences;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateExperiencesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateExperiencesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateExperiencesAssociations)) {
            $contain['CandidateExperiences'] = $candidateExperiencesAssociations;
        } else {
            $contain[] = 'CandidateExperiences';
        }
        
            // CandidateFamilies with its associations
        $candidateFamiliesAssociations = [];
        try {
            $candidateFamiliesTable = $this->Candidates->CandidateFamilies;
            // Get all BelongsTo associations for nested contain
            foreach ($candidateFamiliesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $candidateFamiliesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($candidateFamiliesAssociations)) {
            $contain['CandidateFamilies'] = $candidateFamiliesAssociations;
        } else {
            $contain[] = 'CandidateFamilies';
        }
        
        $candidate = $this->Candidates->get($id, [
            'contain' => $contain,
        ]);
        
        // Check if LPK user can access this record
        if (!$this->canAccessRecord($candidate, 'vocational_training_institution_id')) {
            $this->Flash->error(__('You are not authorized to view this candidate.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set('candidate', $candidate);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidate = $this->Candidates->newEntity();
        
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
                $imagePath = $this->uploadImage('Candidates', $fieldName, 'candidates');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('Candidates', $fieldName, 'candidates');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidate = $this->Candidates->patchEntity($candidate, $data);
            
            // Auto-assign institution for LPK users
            $candidate = $this->setInstitutionId($candidate, 'vocational_training_institution_id');
            
            if ($this->Candidates->save($candidate)) {
                $this->Flash->success(__('The candidate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate could not be saved. Please, try again.'));
        }
        $apprenticeOrders = $this->Candidates->ApprenticeOrders->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->Candidates->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->Candidates->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterGenders = $this->Candidates->MasterGenders->find('list', ['limit' => 200]);
        $masterReligions = $this->Candidates->MasterReligions->find('list', ['limit' => 200]);
        $masterMarriageStatuses = $this->Candidates->MasterMarriageStatuses->find('list', ['limit' => 200]);
        $masterPropinsis = $this->Candidates->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->Candidates->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->Candidates->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->Candidates->MasterKelurahans->find('list', ['limit' => 200]);
        $masterBloodTypes = $this->Candidates->MasterBloodTypes->find('list', ['limit' => 200]);
        $masterCandidateInterviewResults = $this->Candidates->MasterCandidateInterviewResults->find('list', ['limit' => 200]);
        $masterRejectedReasons = $this->Candidates->MasterRejectedReasons->find('list', ['limit' => 200]);
        $this->set(compact('candidate', 'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations', 'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes', 'masterCandidateInterviewResults', 'masterRejectedReasons'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidate = $this->Candidates->get($id, [
            'contain' => [],
        ]);
        
        // Check if LPK user can access this record
        if (!$this->canAccessRecord($candidate, 'vocational_training_institution_id')) {
            $this->Flash->error(__('You are not authorized to edit this candidate.'));
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            // Get POST data
            $data = $this->request->getData();
            
            // Convert date formats automatically
            $data = $this->convertDateFormats($data);
            
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
                $imagePath = $this->uploadImage('Candidates', $fieldName, 'candidates');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('Candidates', $fieldName, 'candidates');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidate = $this->Candidates->patchEntity($candidate, $data);
            
            if ($this->Candidates->save($candidate)) {
                $this->Flash->success(__('The candidate has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate could not be saved. Please, try again.'));
        }
        $apprenticeOrders = $this->Candidates->ApprenticeOrders->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->Candidates->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->Candidates->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterGenders = $this->Candidates->MasterGenders->find('list', ['limit' => 200]);
        $masterReligions = $this->Candidates->MasterReligions->find('list', ['limit' => 200]);
        $masterMarriageStatuses = $this->Candidates->MasterMarriageStatuses->find('list', ['limit' => 200]);
        $masterPropinsis = $this->Candidates->MasterPropinsis->find('list', ['limit' => 200]);
        $masterKabupatens = $this->Candidates->MasterKabupatens->find('list', ['limit' => 200]);
        $masterKecamatans = $this->Candidates->MasterKecamatans->find('list', ['limit' => 200]);
        $masterKelurahans = $this->Candidates->MasterKelurahans->find('list', ['limit' => 200]);
        $masterBloodTypes = $this->Candidates->MasterBloodTypes->find('list', ['limit' => 200]);
        $masterCandidateInterviewResults = $this->Candidates->MasterCandidateInterviewResults->find('list', ['limit' => 200]);
        $masterRejectedReasons = $this->Candidates->MasterRejectedReasons->find('list', ['limit' => 200]);
        $this->set(compact('candidate', 'apprenticeOrders', 'vocationalTrainingInstitutions', 'acceptanceOrganizations', 'masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans', 'masterBloodTypes', 'masterCandidateInterviewResults', 'masterRejectedReasons'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidate = $this->Candidates->get($id);
        
        // Check if LPK user can access this record
        if (!$this->canAccessRecord($candidate, 'vocational_training_institution_id')) {
            $this->Flash->error(__('You are not authorized to delete this candidate.'));
            return $this->redirect(['action' => 'index']);
        }
        
        if ($this->Candidates->delete($candidate)) {
            $this->Flash->success(__('The candidate has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Help method - LPK Candidate Registration Guide
     *
     * @return void
     */
    public function help()
    {
        // This is just a view, no data processing needed
    }

    /**
     * Wizard method - Comprehensive 9-step candidate registration
     * Step 1: Identity number check (duplicate prevention)
     * Step 2: Photo upload with cropping (square passport style)
     * Step 3: Complete candidate information (all main fields)
     * Step 4: Education history (hasMany - required, min 1)
     * Step 5: Work experience (hasMany - optional)
     * Step 6: Family information (hasMany - recommended min 2)
     * Step 7: Certifications (hasMany - optional)
     * Step 8: Courses/Training (hasMany - optional)
     * Step 9: Review and submit
     *
     * @return \Cake\Http\Response|null
     */
    public function wizard()
    {
        $step = $this->request->getQuery('step', 1);
        
        // Initialize session data if not exists
        if (!$this->request->getSession()->check('Wizard.Candidate')) {
            $this->request->getSession()->write('Wizard.Candidate', [
                'educations' => [],
                'experiences' => [],
                'families' => [],
                'certifications' => [],
                'courses' => []
            ]);
        }
        
        $wizardData = $this->request->getSession()->read('Wizard.Candidate');
        
        switch ($step) {
            case 1:
                // ========== STEP 1: Identity Number Check ==========
                if ($this->request->is(['post', 'put'])) {
                    $identityNumber = trim($this->request->getData('identity_number'));
                    
                    // Validation
                    if (empty($identityNumber)) {
                        $this->Flash->error(__('Identity number is required.'));
                    } elseif (strlen($identityNumber) != 16) {
                        $this->Flash->error(__('Identity number must be exactly 16 digits.'));
                    } else {
                        // Check if identity number already exists
                        $existing = $this->Candidates->find()
                            ->where(['identity_number' => $identityNumber])
                            ->first();
                        
                        if ($existing) {
                            $this->Flash->error(__('This identity number is already registered.'));
                            $this->set('existingCandidate', $existing);
                        } else {
                            // Save to session and proceed to step 2
                            $wizardData['identity_number'] = $identityNumber;
                            $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                            $this->Flash->success(__('Identity number verified. Proceed to photo upload.'));
                            return $this->redirect(['action' => 'wizard', '?' => ['step' => 2]]);
                        }
                    }
                }
                break;
                
            case 2:
                // ========== STEP 2: Photo Upload & Crop ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $croppedImage = $this->request->getData('image_photo_cropped');
                    
                    if (!empty($croppedImage)) {
                        // Store base64 cropped image in session
                        $wizardData['image_photo_cropped'] = $croppedImage;
                        $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                        $this->Flash->success(__('Photo uploaded successfully. Proceed to candidate information.'));
                        return $this->redirect(['action' => 'wizard', '?' => ['step' => 3]]);
                    } else {
                        $this->Flash->error(__('Please upload and crop a photo before continuing.'));
                    }
                }
                break;
                
            case 3:
                // ========== STEP 3: Complete Candidate Information ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $data = $this->request->getData();
                    
                    // Validation for required fields
                    $errors = [];
                    if (empty($data['name'])) $errors[] = 'Name is required';
                    if (empty($data['birth_place'])) $errors[] = 'Birth place is required';
                    if (empty($data['birth_date'])) $errors[] = 'Birth date is required';
                    if (empty($data['master_gender_id'])) $errors[] = 'Gender is required';
                    if (empty($data['master_religion_id'])) $errors[] = 'Religion is required';
                    if (empty($data['master_marriage_status_id'])) $errors[] = 'Marriage status is required';
                    if (empty($data['address'])) $errors[] = 'Address is required';
                    if (empty($data['telephone_emergency'])) $errors[] = 'Emergency telephone is required';
                    
                    if (!empty($errors)) {
                        $this->Flash->error(__('Please fill all required fields: ') . implode(', ', $errors));
                        $this->set('validationErrors', $errors);
                    } else {
                        // Merge with wizard data
                        $wizardData = array_merge($wizardData, $data);
                        $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                        $this->Flash->success(__('Candidate information saved. Proceed to education history.'));
                        return $this->redirect(['action' => 'wizard', '?' => ['step' => 4]]);
                    }
                }
                
                // Load master data for dropdowns
                $this->loadModel('MasterGenders');
                $this->loadModel('MasterReligions');
                $this->loadModel('MasterMarriageStatuses');
                $this->loadModel('MasterBloodTypes');
                $this->loadModel('MasterPropinsis');
                
                $masterGenders = $this->MasterGenders->find('list');
                $masterReligions = $this->MasterReligions->find('list');
                $masterMarriageStatuses = $this->MasterMarriageStatuses->find('list');
                $masterBloodTypes = $this->MasterBloodTypes->find('list');
                $masterPropinsis = $this->MasterPropinsis->find('list');
                
                $this->set(compact('masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterBloodTypes', 'masterPropinsis'));
                break;
                
            case 4:
                // ========== STEP 4: Education History (hasMany) ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $educations = $this->request->getData('educations');
                    
                    // Validation: At least 1 education entry required
                    if (empty($educations) || count($educations) < 1) {
                        $this->Flash->error(__('At least one education entry is required.'));
                    } else {
                        // Filter out empty entries
                        $educations = array_filter($educations, function($edu) {
                            return !empty($edu['institution_name']) || !empty($edu['level']);
                        });
                        
                        if (count($educations) < 1) {
                            $this->Flash->error(__('Please provide at least one complete education entry.'));
                        } else {
                            $wizardData['educations'] = array_values($educations);
                            $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                            $this->Flash->success(__('Education history saved. Proceed to work experience.'));
                            return $this->redirect(['action' => 'wizard', '?' => ['step' => 5]]);
                        }
                    }
                }
                break;
                
            case 5:
                // ========== STEP 5: Work Experience (hasMany - Optional) ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $experiences = $this->request->getData('experiences');
                    
                    // Filter out empty entries
                    if (!empty($experiences)) {
                        $experiences = array_filter($experiences, function($exp) {
                            return !empty($exp['company_name']) || !empty($exp['position']);
                        });
                        $wizardData['experiences'] = array_values($experiences);
                    } else {
                        $wizardData['experiences'] = [];
                    }
                    
                    $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                    $this->Flash->success(__('Work experience saved. Proceed to family information.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 6]]);
                }
                break;
                
            case 6:
                // ========== STEP 6: Family Information (hasMany) ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $families = $this->request->getData('families');
                    
                    // Filter out empty entries
                    if (!empty($families)) {
                        $families = array_filter($families, function($fam) {
                            return !empty($fam['full_name']) || !empty($fam['relationship']);
                        });
                        
                        if (count($families) < 2) {
                            $this->Flash->warning(__('It is recommended to add at least 2 family members (Father & Mother).'));
                        }
                        
                        $wizardData['families'] = array_values($families);
                    } else {
                        $wizardData['families'] = [];
                    }
                    
                    $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                    $this->Flash->success(__('Family information saved. Proceed to certifications.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 7]]);
                }
                break;
                
            case 7:
                // ========== STEP 7: Certifications (hasMany - Optional) ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $certifications = $this->request->getData('certifications');
                    
                    // Filter out empty entries
                    if (!empty($certifications)) {
                        $certifications = array_filter($certifications, function($cert) {
                            return !empty($cert['certification_name']) || !empty($cert['issuing_organization']);
                        });
                        $wizardData['certifications'] = array_values($certifications);
                    } else {
                        $wizardData['certifications'] = [];
                    }
                    
                    $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                    $this->Flash->success(__('Certifications saved. Proceed to courses/training.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 8]]);
                }
                break;
                
            case 8:
                // ========== STEP 8: Courses/Training (hasMany - Optional) ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    $courses = $this->request->getData('courses');
                    
                    // Filter out empty entries
                    if (!empty($courses)) {
                        $courses = array_filter($courses, function($course) {
                            return !empty($course['course_name']) || !empty($course['training_institution']);
                        });
                        $wizardData['courses'] = array_values($courses);
                    } else {
                        $wizardData['courses'] = [];
                    }
                    
                    $this->request->getSession()->write('Wizard.Candidate', $wizardData);
                    $this->Flash->success(__('Courses/training saved. Proceed to final review.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 9]]);
                }
                break;
                
            case 9:
                // ========== STEP 9: Review and Submit ==========
                if (empty($wizardData['identity_number'])) {
                    $this->Flash->error(__('Please start from step 1.'));
                    return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
                }
                
                if ($this->request->is(['post', 'put'])) {
                    // Final validation
                    if (empty($wizardData['educations']) || count($wizardData['educations']) < 1) {
                        $this->Flash->error(__('At least one education entry is required. Please go back to Step 4.'));
                        break;
                    }
                    
                    // Create candidate entity from wizard data
                    $candidateData = $wizardData;
                    
                    // Remove hasMany data temporarily
                    unset($candidateData['educations']);
                    unset($candidateData['experiences']);
                    unset($candidateData['families']);
                    unset($candidateData['certifications']);
                    unset($candidateData['courses']);
                    unset($candidateData['image_photo_cropped']);
                    
                    $candidate = $this->Candidates->newEntity($candidateData);
                    
                    // Get current user's institution info (for LPK users)
                    $user = $this->request->getSession()->read('Auth.User');
                    if (!empty($user['institution_id'])) {
                        $candidate->vocational_training_institution_id = $user['institution_id'];
                    }
                    
                    // Handle cropped photo
                    if (!empty($wizardData['image_photo_cropped'])) {
                        $photoPath = $this->saveCroppedImage($wizardData['image_photo_cropped'], $wizardData['identity_number']);
                        if ($photoPath) {
                            $candidate->image_photo = $photoPath;
                        }
                    }
                    
                    if ($this->Candidates->save($candidate)) {
                        $candidateId = $candidate->id;
                        
                        // Save hasMany relationships
                        $this->saveEducations($candidateId, $wizardData['educations']);
                        $this->saveExperiences($candidateId, $wizardData['experiences']);
                        $this->saveFamilies($candidateId, $wizardData['families']);
                        $this->saveCertifications($candidateId, $wizardData['certifications']);
                        $this->saveCourses($candidateId, $wizardData['courses']);
                        
                        // Clear wizard session data
                        $this->request->getSession()->delete('Wizard.Candidate');
                        $this->Flash->success(__('Candidate has been successfully registered with all related information!'));
                        return $this->redirect(['action' => 'view', $candidateId]);
                    } else {
                        $this->Flash->error(__('The candidate could not be saved. Please check the errors and try again.'));
                        $this->set('errors', $candidate->getErrors());
                    }
                }
                
                // Load master data for review display
                $this->loadModel('MasterGenders');
                $this->loadModel('MasterReligions');
                $this->loadModel('MasterMarriageStatuses');
                $this->loadModel('MasterBloodTypes');
                
                $masterGenders = $this->MasterGenders->find('list')->toArray();
                $masterReligions = $this->MasterReligions->find('list')->toArray();
                $masterMarriageStatuses = $this->MasterMarriageStatuses->find('list')->toArray();
                $masterBloodTypes = $this->MasterBloodTypes->find('list')->toArray();
                
                $this->set(compact('masterGenders', 'masterReligions', 'masterMarriageStatuses', 'masterBloodTypes'));
                break;
                
            default:
                return $this->redirect(['action' => 'wizard', '?' => ['step' => 1]]);
        }
        
        $this->set('step', $step);
        $this->set('wizardData', $wizardData);
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
            $candidate = $this->Candidates->get($data['id'], ['contain' => []]);
            $candidate = $this->Candidates->patchEntity($candidate, $data);
        } else {
            $candidate = $this->Candidates->newEntity($data);
        }

        // Get validation errors
        $validationErrors = $candidate->getErrors();
        
        // Manual check: Edit mode requires ID in data
        if ($isEditMode && empty($data['id'])) {
            $validationErrors['id'] = ['ID is required for updating existing records'];
        }
        
        // Get database schema metadata
        $schema = $this->Candidates->getSchema();
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
        if (!empty($candidate->id) && empty($data['id'])) {
            $data['id'] = $candidate->id;
        }
        
        // Load associations for display
        if (!empty($candidate->id)) {
            $candidate = $this->Candidates->get($candidate->id, [
                'contain' => [                    'ApprenticeOrders',                    'VocationalTrainingInstitutions',                    'AcceptanceOrganizations',                    'MasterGenders',                    'MasterReligions',                    'MasterMarriageStatuses',                    'MasterPropinsis',                    'MasterKabupatens',                    'MasterKecamatans',                    'MasterKelurahans',                    'MasterBloodTypes',                    'MasterCandidateInterviewResults',                    'MasterRejectedReasons',                    'CandidateCertifications',                    'CandidateCourses',                    'CandidateDocuments',                    'CandidateEducations',                    'CandidateExperiences',                    'CandidateFamilies',                ]
            ]);
        }

        $this->set(compact('candidate', 'validationErrors', 'fieldMetadata', 'data'));
    }
    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->Candidates->find('all')
            ->contain(['ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes', 'MasterCandidateInterviewResults', 'MasterRejectedReasons']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'Candidates', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->Candidates->find('all')
            ->contain(['ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes', 'MasterCandidateInterviewResults', 'MasterRejectedReasons']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'Candidates', $headers, $fields);
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
        
        $query = $this->Candidates->find('all')
            ->contain(['ApprenticeOrders', 'VocationalTrainingInstitutions', 'AcceptanceOrganizations', 'MasterGenders', 'MasterReligions', 'MasterMarriageStatuses', 'MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans', 'MasterBloodTypes', 'MasterCandidateInterviewResults', 'MasterRejectedReasons']);
        
        // Define report configuration
        $title = 'Candidates Report';
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
     * Save cropped image from base64 data
     *
     * @param string $base64Data Base64 encoded image data
     * @param string $identityNumber Identity number for filename
     * @return string|false File path or false on failure
     */
    public function saveCroppedImage($base64Data, $identityNumber)
    {
        try {
            // Extract base64 data
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('Invalid image type');
                }
                
                $base64Data = base64_decode($base64Data);
                
                if ($base64Data === false) {
                    throw new \Exception('Base64 decode failed');
                }
            } else {
                throw new \Exception('Invalid base64 string');
            }
            
            // Create upload directory if not exists
            $uploadDir = WWW_ROOT . 'files' . DS . 'uploads' . DS . 'candidates' . DS;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate filename
            $filename = 'photo_' . $identityNumber . '_' . time() . '.' . $type;
            $filepath = $uploadDir . $filename;
            
            // Save file
            if (file_put_contents($filepath, $base64Data)) {
                // Return relative path for database storage
                return 'files/uploads/candidates/' . $filename;
            }
            
            return false;
        } catch (\Exception $e) {
            $this->log('Photo save error: ' . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Save education records for a candidate
     *
     * @param int $candidateId Candidate ID
     * @param array $educations Array of education data
     * @return void
     */
    public function saveEducations($candidateId, $educations)
    {
        if (empty($educations)) {
            return;
        }
        
        $this->loadModel('CandidateEducations');
        
        foreach ($educations as $education) {
            $education['candidate_id'] = $candidateId;
            $entity = $this->CandidateEducations->newEntity($education);
            $this->CandidateEducations->save($entity);
        }
    }

    /**
     * Save work experience records for a candidate
     *
     * @param int $candidateId Candidate ID
     * @param array $experiences Array of experience data
     * @return void
     */
    public function saveExperiences($candidateId, $experiences)
    {
        if (empty($experiences)) {
            return;
        }
        
        $this->loadModel('CandidateExperiences');
        
        foreach ($experiences as $experience) {
            $experience['candidate_id'] = $candidateId;
            $entity = $this->CandidateExperiences->newEntity($experience);
            $this->CandidateExperiences->save($entity);
        }
    }

    /**
     * Save family records for a candidate
     *
     * @param int $candidateId Candidate ID
     * @param array $families Array of family data
     * @return void
     */
    public function saveFamilies($candidateId, $families)
    {
        if (empty($families)) {
            return;
        }
        
        $this->loadModel('CandidateFamilies');
        
        foreach ($families as $family) {
            $family['candidate_id'] = $candidateId;
            $entity = $this->CandidateFamilies->newEntity($family);
            $this->CandidateFamilies->save($entity);
        }
    }

    /**
     * Save certification records for a candidate
     *
     * @param int $candidateId Candidate ID
     * @param array $certifications Array of certification data
     * @return void
     */
    public function saveCertifications($candidateId, $certifications)
    {
        if (empty($certifications)) {
            return;
        }
        
        $this->loadModel('CandidateCertifications');
        
        foreach ($certifications as $certification) {
            $certification['candidate_id'] = $candidateId;
            $entity = $this->CandidateCertifications->newEntity($certification);
            $this->CandidateCertifications->save($entity);
        }
    }

    /**
     * Save course records for a candidate
     *
     * @param int $candidateId Candidate ID
     * @param array $courses Array of course data
     * @return void
     */
    public function saveCourses($candidateId, $courses)
    {
        if (empty($courses)) {
            return;
        }
        
        $this->loadModel('CandidateCourses');
        
        foreach ($courses as $course) {
            $course['candidate_id'] = $candidateId;
            $entity = $this->CandidateCourses->newEntity($course);
            $this->CandidateCourses->save($entity);
        }
    }

    /**
     * CV View - Display candidate CV in HTML format
     *
     * @param int|null $id Candidate ID
     * @return \Cake\Http\Response|null
     */
    public function cv($id = null)
    {
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'CandidateEducations',
                'CandidateExperiences',
                'CandidateFamilies',
                'CandidateCertifications',
                'CandidateCourses',
                'MasterGenders',
                'MasterReligions',
                'MasterMarriageStatuses',
                'MasterBloodTypes',
                'MasterPropinsis'
            ]
        ]);

        $this->set('candidate', $candidate);
    }

    /**
     * Export CV to Excel format
     *
     * @param int|null $id Candidate ID
     * @return \Cake\Http\Response
     */
    public function exportCvExcel($id = null)
    {
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'CandidateEducations',
                'CandidateExperiences',
                'CandidateFamilies',
                'CandidateCertifications',
                'CandidateCourses',
                'MasterGenders',
                'MasterReligions',
                'MasterMarriageStatuses',
                'MasterBloodTypes'
            ]
        ]);

        $fmtDate = function ($value) {
            if (empty($value)) {
                return '';
            }
            return is_object($value) ? $value->format('Y-m-d') : (string)$value;
        };

        // Same English -> Japanese label map used on the printed CV (cv.ctp),
        // so the Excel export mirrors what the Japanese interviewer sees on paper.
        $ja = [
            'Candidate Curriculum Vitae' => '候補者履歴書',
            'Basic Information'     => '基本情報',
            'Identity Number'       => '身分証明書番号',
            'Candidate Code'        => '候補者コード',
            'Full Name'             => '氏名',
            'Place / Date of Birth' => '出生地・生年月日',
            'years'                 => '歳',
            'Gender'                => '性別',
            'Religion'               => '宗教',
            'Marriage Status'       => '婚姻状況',
            'Blood Type'            => '血液型',
            'Height / Weight'       => '身長・体重',
            'Contact Information'   => '連絡先情報',
            'Mobile Phone'          => '携帯電話番号',
            'Emergency Contact'     => '緊急連絡先',
            'Email'                 => 'メールアドレス',
            'Address'                => '住所',
            'Education History'     => '学歴',
            'No.'                   => '番号',
            'School / College'      => '学校名',
            'Major'                  => '専攻',
            'Entry Date'             => '入学',
            'Graduate Date'          => '卒業',
            'Work Experience'       => '職歴',
            'Company'                => '会社名',
            'Position'               => '役職',
            'Start Year'             => '開始年',
            'End Year'               => '終了年',
            'Job Detail'             => '業務内容',
            'Certifications'        => '資格',
            'Certification'         => '資格名',
            'Institution'            => '取得機関',
            'Date'                   => '取得日',
            'Detail'                 => '詳細',
            'Courses & Training'    => '研修・コース',
            'Course'                 => 'コース名',
            'Year'                   => '年',
            'Family Information'    => '家族構成',
            'Name'                   => '氏名',
            'Age'                    => '年齢',
            'Additional Information' => 'その他の情報',
            'Strengths'              => '長所',
            'Weaknesses'             => '短所',
            'Hobby'                  => '趣味',
            'Application Reasons'   => '志望動機',
        ];
        $bl = function ($label) use ($ja) {
            return isset($ja[$label]) ? $label . ' / ' . $ja[$label] : $label;
        };

        $age = '';
        if (!empty($candidate->birth_date)) {
            try {
                $birth = new \DateTime($fmtDate($candidate->birth_date));
                $age = $birth->diff(new \DateTime())->y;
            } catch (\Exception $e) {
                $age = '';
            }
        }

        // Build a list of layout "blocks" (mirrors the sections on the printed
        // CV / cv.ctp) which the renderer below turns into a styled .xlsx —
        // colored section bars, bold table headers, borders — matching the PDF look.
        $blocks = [];

        // Photo path (mirrors cv.ctp's existence check — some records reference
        // photos that were never actually uploaded to this server)
        $photoPath = (!empty($candidate->image_photo) && file_exists(WWW_ROOT . $candidate->image_photo))
            ? WWW_ROOT . $candidate->image_photo
            : null;

        $blocks[] = [
            'type' => 'header',
            'subtitle' => $bl('Candidate Curriculum Vitae'),
            'name' => $candidate->name,
            'katakana' => $candidate->name_katakana,
            'photo' => $photoPath,
        ];

        $kv = [];
        $kv[] = [$bl('Identity Number'), $candidate->identity_number];
        if (!empty($candidate->candidate_code)) {
            $kv[] = [$bl('Candidate Code'), $candidate->candidate_code];
        }
        $kv[] = [$bl('Full Name'), $candidate->name];
        $kv[] = [$bl('Place / Date of Birth'), trim($candidate->birth_place . ', ' . $fmtDate($candidate->birth_date) . ($age !== '' ? ' (' . $age . ' ' . $ja['years'] . ')' : ''), ', ')];
        $kv[] = [$bl('Gender'), $candidate->master_gender ? $candidate->master_gender->title : ''];
        $kv[] = [$bl('Religion'), $candidate->master_religion ? $candidate->master_religion->title : ''];
        $kv[] = [$bl('Marriage Status'), $candidate->master_marriage_status ? $candidate->master_marriage_status->title : ''];
        if (!empty($candidate->master_blood_type)) {
            $kv[] = [$bl('Blood Type'), $candidate->master_blood_type->title];
        }
        if (!empty($candidate->body_height) || !empty($candidate->body_weight)) {
            $kv[] = [$bl('Height / Weight'), (!empty($candidate->body_height) ? $candidate->body_height . ' cm' : '-') . ' / ' . (!empty($candidate->body_weight) ? $candidate->body_weight . ' kg' : '-')];
        }
        $blocks[] = ['type' => 'section', 'text' => $bl('Basic Information')];
        $blocks[] = ['type' => 'kv', 'rows' => $kv];

        $kv = [];
        if (!empty($candidate->telephone_mobile)) {
            $kv[] = [$bl('Mobile Phone'), $candidate->telephone_mobile];
        }
        $kv[] = [$bl('Emergency Contact'), $candidate->telephone_emergency];
        if (!empty($candidate->email)) {
            $kv[] = [$bl('Email'), $candidate->email];
        }
        $kv[] = [$bl('Address'), trim($candidate->address . (!empty($candidate->post_code) ? ' (' . $candidate->post_code . ')' : ''))];
        $blocks[] = ['type' => 'section', 'text' => $bl('Contact Information')];
        $blocks[] = ['type' => 'kv', 'rows' => $kv];

        if (!empty($candidate->candidate_educations)) {
            $rows = [];
            foreach ($candidate->candidate_educations as $i => $edu) {
                $rows[] = [$i + 1, $edu->college_name, $edu->college_major, $fmtDate($edu->college_entry_date), $fmtDate($edu->college_graduate_date)];
            }
            $blocks[] = ['type' => 'section', 'text' => $bl('Education History')];
            $blocks[] = ['type' => 'table', 'headers' => [$bl('No.'), $bl('School / College'), $bl('Major'), $bl('Entry Date'), $bl('Graduate Date')], 'rows' => $rows, 'widths' => [6, 34, 24, 16, 16]];
        }

        if (!empty($candidate->candidate_experiences)) {
            $rows = [];
            foreach ($candidate->candidate_experiences as $i => $exp) {
                $rows[] = [$i + 1, $exp->company_name, $exp->title, $exp->employment_start_year, $exp->employment_end_year, $exp->job_detail];
            }
            $blocks[] = ['type' => 'section', 'text' => $bl('Work Experience')];
            $blocks[] = ['type' => 'table', 'headers' => [$bl('No.'), $bl('Company'), $bl('Position'), $bl('Start Year'), $bl('End Year'), $bl('Job Detail')], 'rows' => $rows, 'widths' => [6, 28, 20, 13, 13, 30]];
        }

        if (!empty($candidate->candidate_certifications)) {
            $rows = [];
            foreach ($candidate->candidate_certifications as $i => $cert) {
                $rows[] = [$i + 1, $cert->title, $cert->institution_name, $fmtDate($cert->certification_date), $cert->detail];
            }
            $blocks[] = ['type' => 'section', 'text' => $bl('Certifications')];
            $blocks[] = ['type' => 'table', 'headers' => [$bl('No.'), $bl('Certification'), $bl('Institution'), $bl('Date'), $bl('Detail')], 'rows' => $rows, 'widths' => [6, 30, 26, 16, 22]];
        }

        if (!empty($candidate->candidate_courses)) {
            $rows = [];
            foreach ($candidate->candidate_courses as $i => $course) {
                $rows[] = [$i + 1, $course->title, $course->course_year, $course->detail];
            }
            $blocks[] = ['type' => 'section', 'text' => $bl('Courses & Training')];
            $blocks[] = ['type' => 'table', 'headers' => [$bl('No.'), $bl('Course'), $bl('Year'), $bl('Detail')], 'rows' => $rows, 'widths' => [6, 36, 14, 44]];
        }

        if (!empty($candidate->candidate_families)) {
            $rows = [];
            foreach ($candidate->candidate_families as $i => $fam) {
                $rows[] = [$i + 1, $fam->name, $fam->age, $fam->detail];
            }
            $blocks[] = ['type' => 'section', 'text' => $bl('Family Information')];
            $blocks[] = ['type' => 'table', 'headers' => [$bl('No.'), $bl('Name'), $bl('Age'), $bl('Detail')], 'rows' => $rows, 'widths' => [6, 30, 10, 54]];
        }

        if (!empty($candidate->strengths) || !empty($candidate->weaknesses) || !empty($candidate->hobby) || !empty($candidate->application_reasons)) {
            $kv = [];
            if (!empty($candidate->strengths)) {
                $kv[] = [$bl('Strengths'), $candidate->strengths];
            }
            if (!empty($candidate->weaknesses)) {
                $kv[] = [$bl('Weaknesses'), $candidate->weaknesses];
            }
            if (!empty($candidate->hobby)) {
                $kv[] = [$bl('Hobby'), $candidate->hobby];
            }
            if (!empty($candidate->application_reasons)) {
                $kv[] = [$bl('Application Reasons'), $candidate->application_reasons];
            }
            $blocks[] = ['type' => 'section', 'text' => $bl('Additional Information')];
            $blocks[] = ['type' => 'kv', 'rows' => $kv];
        }

        $filename = 'CV_' . $candidate->identity_number . '_' . date('Ymd') . '.xlsx';

        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Fallback: flatten blocks to plain CSV if PhpSpreadsheet isn't installed
            $csv = [];
            foreach ($blocks as $block) {
                if ($block['type'] === 'header') {
                    $csv[] = [$block['subtitle']];
                    $csv[] = [$block['name'], $block['katakana'] ?? ''];
                } elseif ($block['type'] === 'section') {
                    $csv[] = [$block['text']];
                } elseif ($block['type'] === 'kv') {
                    foreach ($block['rows'] as $row) {
                        $csv[] = $row;
                    }
                } elseif ($block['type'] === 'table') {
                    $csv[] = $block['headers'];
                    foreach ($block['rows'] as $row) {
                        $csv[] = $row;
                    }
                }
                $csv[] = [];
            }
            $stream = fopen('php://temp', 'r+');
            foreach ($csv as $row) {
                fputcsv($stream, $row);
            }
            rewind($stream);
            $body = stream_get_contents($stream);
            fclose($stream);

            return $this->response
                ->withStringBody("\xEF\xBB\xBF" . $body)
                ->withType('csv')
                ->withDownload(str_replace('.xlsx', '.csv', $filename));
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CV');

        $maxCols = 6;
        $colLetters = ['A', 'B', 'C', 'D', 'E', 'F'];
        $sheet->getColumnDimension('A')->setWidth(46);
        foreach (range('B', 'F') as $col) {
            $sheet->getColumnDimension($col)->setWidth(20);
        }
        $sheet->getDefaultRowDimension()->setRowHeight(18);

        $sectionFill = '00BCD4';
        $tableHeaderFill = 'F1FAFB';
        $borderColor = 'DDE7EA';

        $row = 1;
        foreach ($blocks as $block) {
            if ($block['type'] === 'header') {
                // Photo column (A) spans 6 rows, ~150px tall — matches the PDF header photo
                $headerRows = 6;
                $photoCell = "A{$row}";
                $sheet->mergeCells($photoCell . ':A' . ($row + $headerRows - 1));
                for ($r = $row; $r < $row + $headerRows; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(20);
                }
                $sheet->getStyle($photoCell)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'CFD8DC']]],
                    'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                ]);

                if (!empty($block['photo'])) {
                    $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                    $drawing->setPath($block['photo']);
                    $drawing->setHeight(110);
                    $drawing->setOffsetX(8);
                    $drawing->setOffsetY(6);
                    $drawing->setCoordinates($photoCell);
                    $drawing->setWorksheet($sheet);
                } else {
                    $sheet->setCellValue($photoCell, '(no photo)');
                    $sheet->getStyle($photoCell)->applyFromArray(['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => 'B0BEC5']]]);
                }

                // Subtitle / Name / Katakana stacked in columns B:F
                $sheet->mergeCells("B{$row}:F{$row}");
                $sheet->setCellValue("B{$row}", $block['subtitle']);
                $sheet->getStyle("B{$row}")->applyFromArray([
                    'font' => ['size' => 9, 'color' => ['rgb' => '90A4AE'], 'bold' => true],
                ]);

                $nameRow = $row + 1;
                $sheet->mergeCells("B{$nameRow}:F" . ($nameRow + 1));
                $sheet->setCellValue("B{$nameRow}", $block['name']);
                $sheet->getStyle("B{$nameRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => '2C3E50']],
                    'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                ]);

                if (!empty($block['katakana'])) {
                    $kanaRow = $nameRow + 2;
                    $sheet->mergeCells("B{$kanaRow}:F{$kanaRow}");
                    $sheet->setCellValue("B{$kanaRow}", $block['katakana']);
                    $sheet->getStyle("B{$kanaRow}")->applyFromArray([
                        'font' => ['size' => 13, 'color' => ['rgb' => '607D8B']],
                    ]);
                }

                // Bottom border under the whole header band — matches the PDF's
                // 3px solid cyan rule separating header from body
                $sheet->getStyle('A' . ($row + $headerRows - 1) . ':F' . ($row + $headerRows - 1))->applyFromArray([
                    'borders' => ['bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK, 'color' => ['rgb' => $sectionFill]]],
                ]);

                $row += $headerRows + 1;
                continue;
            }

            if ($block['type'] === 'section') {
                $sheet->mergeCells("A{$row}:F{$row}");
                $sheet->setCellValue("A{$row}", $block['text']);
                $sheet->getStyle("A{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $sectionFill]],
                    'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'indent' => 1],
                ]);
                $sheet->getRowDimension($row)->setRowHeight(20);
                $row++;
                continue;
            }

            if ($block['type'] === 'kv') {
                foreach ($block['rows'] as $kvRow) {
                    $sheet->setCellValueExplicit("A{$row}", $kvRow[0], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->mergeCells("B{$row}:F{$row}");
                    // Force text type so long numeric strings (NIK, phone numbers) never
                    // get auto-converted to scientific notation by Excel.
                    $sheet->setCellValueExplicit("B{$row}", (string)$kvRow[1], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '455A64']],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $tableHeaderFill]],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                        'alignment' => ['wrapText' => false, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle("B{$row}:F{$row}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                        'alignment' => ['wrapText' => true, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                    ]);
                    $row++;
                }
                $row++;
                continue;
            }

            if ($block['type'] === 'table') {
                $colCount = count($block['headers']);
                if (!empty($block['widths'])) {
                    foreach ($block['widths'] as $i => $w) {
                        // Column A is shared with the photo box and the kv label column
                        // above/below — keep it fixed at the header-text width instead of
                        // letting each table shrink it down to its own "No." column width.
                        if ($i === 0) {
                            continue;
                        }
                        if (isset($colLetters[$i])) {
                            $sheet->getColumnDimension($colLetters[$i])->setWidth($w);
                        }
                    }
                }
                foreach ($block['headers'] as $i => $h) {
                    $cell = $colLetters[$i] . $row;
                    $sheet->setCellValue($cell, $h);
                }
                $sheet->getStyle($colLetters[0] . $row . ':' . $colLetters[$colCount - 1] . $row)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '455A64']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $tableHeaderFill]],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                    'alignment' => ['wrapText' => true],
                ]);
                $row++;

                foreach ($block['rows'] as $dataRow) {
                    foreach ($dataRow as $i => $val) {
                        $sheet->setCellValue($colLetters[$i] . $row, $val);
                    }
                    $sheet->getStyle($colLetters[0] . $row . ':' . $colLetters[$colCount - 1] . $row)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                        'alignment' => ['wrapText' => true, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP],
                    ]);
                    $row++;
                }
                $row++;
            }
        }

        $sheet->setSelectedCell('A1');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $tmpFile = tempnam(sys_get_temp_dir(), 'cv_xlsx_');
        $writer->save($tmpFile);
        $body = file_get_contents($tmpFile);
        unlink($tmpFile);

        return $this->response
            ->withStringBody($body)
            ->withType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withDownload($filename);
    }

    /**
     * Export CV to PDF format
     *
     * @param int|null $id Candidate ID
     * @return \Cake\Http\Response|null
     */
    public function exportCvPdf($id = null)
    {
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'CandidateEducations',
                'CandidateExperiences',
                'CandidateFamilies',
                'CandidateCertifications',
                'CandidateCourses',
                'MasterGenders',
                'MasterReligions',
                'MasterMarriageStatuses',
                'MasterBloodTypes'
            ]
        ]);

        $this->set('candidate', $candidate);
        $this->viewBuilder()->setLayout('print');
        $this->render('cv');
    }

    /**
     * AJAX: Get Kabupaten by Propinsi ID
     *
     * @return \Cake\Http\Response
     */
    public function getKabupaten()
    {
        $this->autoRender = false;
        $propinsiId = $this->request->getQuery('propinsi_id');
        
        $this->loadModel('MasterKabupatens');
        $kabupatens = $this->MasterKabupatens->find('list', [
            'keyField' => 'id',
            'valueField' => 'title'
        ])
            ->where(['propinsi_id' => $propinsiId])
            ->order(['title' => 'ASC'])
            ->toArray();
        
        return $this->response->withType('application/json')
            ->withStringBody(json_encode($kabupatens));
    }

    /**
     * AJAX: Get Kecamatan by Kabupaten ID
     *
     * @return \Cake\Http\Response
     */
    public function getKecamatan()
    {
        $this->autoRender = false;
        $kabupatenId = $this->request->getQuery('kabupaten_id');
        
        $this->loadModel('MasterKecamatans');
        $kecamatans = $this->MasterKecamatans->find('list', [
            'keyField' => 'id',
            'valueField' => 'title'
        ])
            ->where(['kabupaten_id' => $kabupatenId])
            ->order(['title' => 'ASC'])
            ->toArray();
        
        return $this->response->withType('application/json')
            ->withStringBody(json_encode($kecamatans));
    }

    /**
     * AJAX: Get Kelurahan by Kecamatan ID
     *
     * @return \Cake\Http\Response
     */
    public function getKelurahan()
    {
        $this->autoRender = false;
        $kecamatanId = $this->request->getQuery('kecamatan_id');
        
        $this->loadModel('MasterKelurahans');
        $kelurahans = $this->MasterKelurahans->find('list', [
            'keyField' => 'id',
            'valueField' => 'title'
        ])
            ->where(['kecamatan_id' => $kecamatanId])
            ->order(['title' => 'ASC'])
            ->toArray();
        
        return $this->response->withType('application/json')
            ->withStringBody(json_encode($kelurahans));
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
     * Recruitment promotion dashboard.
     * Shows all active candidates with physical score, interview score, and document completeness.
     * TMM Recruitment PIC uses this to review and promote candidates to trainee.
     */
    public function promoteToTrainee()
    {
        // Already-promoted candidate IDs
        $traineesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Trainees');
        $promotedIds = $traineesTable->find()
            ->where(['candidate_id IS NOT' => null])
            ->extract('candidate_id')
            ->toList();

        // Eligible: active, not yet promoted, candidate_pass=1
        $eligibleConditions = ['Candidates.status_flag' => 'active', 'Candidates.is_candidate_pass' => 1];
        if (!empty($promotedIds)) {
            $eligibleConditions['Candidates.id NOT IN'] = $promotedIds;
        }
        // Already promoted
        $promotedConditions = ['Candidates.is_candidate_pass' => 1];
        if (!empty($promotedIds)) {
            $promotedConditions['Candidates.id IN'] = $promotedIds;
        }

        $selectFields = [
            'Candidates.id', 'Candidates.candidate_code', 'Candidates.name',
            'Candidates.master_gender_id',
            'Candidates.vocational_training_institution_id',
            'Candidates.acceptance_organization_id',
            'Candidates.fitness_score', 'Candidates.fitness_pushups',
            'Candidates.fitness_situps', 'Candidates.fitness_running_minutes',
            'Candidates.fitness_running_seconds',
            'Candidates.interview_score', 'Candidates.interview_recommendation',
            'Candidates.mcu_score', 'Candidates.mcu_rank',
            'Candidates.is_candidate_pass', 'Candidates.status_flag',
        ];

        $candidates = $this->Candidates->find()
            ->select($selectFields)
            ->contain(['VocationalTrainingInstitutions', 'AcceptanceOrganizations'])
            ->where($eligibleConditions)
            ->order(['Candidates.name' => 'ASC'])
            ->all();

        $promotedCandidates = !empty($promotedIds)
            ? $this->Candidates->find()
                ->select($selectFields)
                ->contain(['VocationalTrainingInstitutions', 'AcceptanceOrganizations'])
                ->where($promotedConditions)
                ->order(['Candidates.name' => 'ASC'])
                ->all()
            : [];

        // Relation counts for ALL candidates shown
        $allCandidateIds = array_merge(
            array_column($candidates->toArray(), 'id'),
            array_column(is_array($promotedCandidates) ? $promotedCandidates : $promotedCandidates->toArray(), 'id')
        );
        $relCounts = [];
        if (!empty($allCandidateIds)) {
            $candConn = $this->Candidates->getConnection();
            $inList   = implode(',', array_fill(0, count($allCandidateIds), '?'));
            foreach ([
                'educations'     => 'candidate_educations',
                'experiences'    => 'candidate_experiences',
                'certifications' => 'candidate_certifications',
                'courses'        => 'candidate_courses',
                'families'       => 'candidate_families',
            ] as $key => $table) {
                $rows = $candConn->execute(
                    "SELECT candidate_id, COUNT(*) AS cnt FROM {$table} WHERE candidate_id IN ({$inList}) GROUP BY candidate_id",
                    $allCandidateIds
                )->fetchAll('assoc');
                foreach ($rows as $r) {
                    $relCounts[(int)$r['candidate_id']][$key] = (int)$r['cnt'];
                }
            }
        }

        // Document completeness not tracked (submission_documents uses legacy applicant_id)
        $totalRequired = 0;
        $docCompleteness = [];

        // Training batches with open seat counts
        $batchConn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_trainings');
        $trainingBatches = $batchConn->execute(
            "SELECT ttb.id, ttb.batch_name, ttb.origin_training_location,
                    ttb.origin_start_plan_date, ttb.origin_finish_plan_date,
                    ttb.training_term_of_months,
                    COUNT(t.id) AS enrolled
             FROM trainee_training_batches ttb
             LEFT JOIN cms_tmm_trainees.trainees t ON t.trainee_training_batch_id = ttb.id
             GROUP BY ttb.id, ttb.batch_name, ttb.origin_training_location,
                      ttb.origin_start_plan_date, ttb.origin_finish_plan_date,
                      ttb.training_term_of_months
             ORDER BY ttb.id DESC"
        )->fetchAll('assoc');

        $this->set(compact('candidates', 'promotedCandidates', 'docCompleteness', 'totalRequired', 'trainingBatches', 'relCounts'));
    }

    /**
     * POST: promote a single candidate to trainee status.
     * Marks is_candidate_pass = 1 and creates a trainee record in cms_tmm_trainees.
     */
    public function doPromote($candidateId = null)
    {
        $this->request->allowMethod(['post']);

        if (!$this->hasRole('administrator') && !$this->hasRole('tmm-recruitment')) {
            $this->Flash->error(__('Only TMM Recruitment role can promote candidates.'));
            return $this->redirect(['action' => 'promoteToTrainee']);
        }

        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $candidate = $this->Candidates->get($candidateId, [
            'contain' => [
                'CandidateEducations',
                'CandidateExperiences',
                'CandidateCertifications',
                'CandidateCourses',
                'CandidateFamilies',
            ]
        ]);

        $traineesTable = $locator->get('Trainees');

        // Guard: already promoted
        $existing = $traineesTable->find()->where(['candidate_id' => $candidateId])->first();
        if ($existing) {
            $this->Flash->warning(__('Candidate "{0}" is already a trainee.', $candidate->name));
            return $this->redirect(['action' => 'promoteToTrainee']);
        }

        // Collect remarks about missing scores
        $remarks = [];
        if (empty($candidate->fitness_score) || $candidate->fitness_score < 1) {
            $remarks[] = __('No physical test score');
        }
        if (empty($candidate->interview_score) || $candidate->interview_score < 1) {
            $remarks[] = __('No AO interview score');
        }
        $gradingRemarks = $this->request->getData('grading_remarks') ?: implode('; ', $remarks);

        // Create trainee core record
        $trainee = $traineesTable->newEntity([
            'candidate_id'                       => $candidate->id,
            'applicant_code'                     => $candidate->candidate_code,
            'tmm_code'                           => 'TMM-' . strtoupper(uniqid()),
            'vocational_training_institution_id' => $candidate->vocational_training_institution_id ?? 1,
            'acceptance_organization_id'         => $candidate->acceptance_organization_id,
            'identity_number'                    => $candidate->identity_number ?? '',
            'name'                               => $candidate->name,
            'name_katakana'                      => $candidate->name_katakana,
            'master_gender_id'                   => $candidate->master_gender_id ?? 1,
            'master_religion_id'                 => $candidate->master_religion_id ?? 1,
            'master_marriage_status_id'          => $candidate->master_marriage_status_id ?? 1,
            'birth_place'                        => $candidate->birth_place,
            'birth_place_katakana'               => $candidate->birth_place_katakana,
            'birth_date'                         => $candidate->birth_date,
            'telephone_mobile'                   => $candidate->telephone_mobile,
            'telephone_emergency'                => $candidate->telephone_emergency ?? '-',
            'email'                              => $candidate->email,
            'master_propinsi_id'                 => $candidate->master_propinsi_id,
            'master_kabupaten_id'                => $candidate->master_kabupaten_id,
            'master_kecamatan_id'                => $candidate->master_kecamatan_id,
            'master_kelurahan_id'                => $candidate->master_kelurahan_id,
            'post_code'                          => $candidate->post_code,
            'address'                            => $candidate->address ?? '-',
            'image_photo'                        => $candidate->image_photo,
            'strengths'                          => $candidate->strengths,
            'weaknesses'                         => $candidate->weaknesses,
            'hobby'                              => $candidate->hobby,
            'last_salary_amount'                 => $candidate->last_salary_amount,
            'application_reasons'                => $candidate->application_reasons,
            'is_ever_went_to_japan'              => $candidate->is_ever_went_to_japan,
            'will_go_to_japan_after_finished'    => $candidate->will_go_to_japan_after_finished,
            'expected_work_upon_returning_to_japan' => $candidate->expected_work_upon_returning_to_japan,
            'is_holding_passport'                => $candidate->is_holding_passport,
            'saving_goal_amount'                 => $candidate->saving_goal_amount,
            'blood_type_id'                      => $candidate->master_blood_type_id,
            'body_weight'                        => $candidate->body_weight,
            'body_height'                        => $candidate->body_height,
            'is_wear_eye_glasses'                => $candidate->is_wear_eye_glasses,
            'explain_eye_condition'              => $candidate->explain_eye_condition,
            'is_color_blind'                     => $candidate->is_color_blind,
            'explain_color_blind'                => $candidate->explain_color_blind,
            'is_right_handed'                    => $candidate->is_right_handed,
            'is_smoking'                         => $candidate->is_smoking,
            'is_drinking_alcohol'                => $candidate->is_drinking_alcohol,
            'is_tattooed'                        => $candidate->is_tattooed,
            'link_whatsapp'                      => $candidate->link_whatsapp,
            'link_line'                          => $candidate->link_line,
            'link_instagram'                     => $candidate->link_instagram,
            'link_facebook'                      => $candidate->link_facebook,
            'link_tiktok'                        => $candidate->link_tiktok,
            'is_candidate_pass'                  => 1,
            'is_training_pass'                   => 0,
            'grading_remarks'                    => $gradingRemarks,
            'trainee_training_batch_id'          => $this->request->getData('trainee_training_batch_id') ?: null,
        ]);

        if (!$traineesTable->save($trainee)) {
            $this->Flash->error(__('Could not create trainee record. Please check the data.'));
            return $this->redirect(['action' => 'promoteToTrainee']);
        }

        $traineeId = $trainee->id;

        // Copy relation tables: educations
        try {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainees');
        foreach ($candidate->candidate_educations ?? [] as $row) {
            $conn->execute(
                "INSERT INTO trainee_educations (trainee_id, master_strata_id, master_propinsi_id, master_kabupaten_id, college_entry_date, college_graduate_date, college_name, college_major)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$traineeId, $row->master_strata_id, $row->master_propinsi_id, $row->master_kabupaten_id,
                 $row->college_entry_date, $row->college_graduate_date, $row->college_name, $row->college_major]
            );
        }
        // Experiences
        foreach ($candidate->candidate_experiences ?? [] as $row) {
            $conn->execute(
                "INSERT INTO trainee_experiences (trainee_id, employment_start_year, employment_end_year, company_name, title, master_employee_status_id, employment_awards, last_salary_amount, job_detail, termination_reasons)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$traineeId, $row->employment_start_year, $row->employment_end_year, $row->company_name,
                 $row->title, $row->master_employee_status_id, $row->employment_awards, $row->last_salary_amount,
                 $row->job_detail, $row->termination_reasons]
            );
        }
        // Certifications
        foreach ($candidate->candidate_certifications ?? [] as $row) {
            $conn->execute(
                "INSERT INTO trainee_certifications (trainee_id, title, institution_name, certification_date, detail)
                 VALUES (?, ?, ?, ?, ?)",
                [$traineeId, $row->title, $row->institution_name, $row->certification_date, $row->detail]
            );
        }
        // Courses
        foreach ($candidate->candidate_courses ?? [] as $row) {
            $conn->execute(
                "INSERT INTO trainee_courses (trainee_id, vocational_training_institution_id, course_major_id, course_year, title, detail)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$traineeId, $row->vocational_training_institution_id, $row->master_course_major_id,
                 $row->course_year, $row->title, $row->detail]
            );
        }
        // Families
        foreach ($candidate->candidate_families ?? [] as $row) {
            $conn->execute(
                "INSERT INTO trainee_families (trainee_id, master_family_connection_id, name, age, master_occupation_id, detail)
                 VALUES (?, ?, ?, ?, ?, ?)",
                [$traineeId, $row->master_family_connection_id, $row->name, $row->age,
                 $row->master_occupation_id, $row->detail]
            );
        }

        // Mark candidate as promoted
        $candidate->is_candidate_pass = 1;
        $this->Candidates->save($candidate);

        $this->Flash->success(__('"{0}" has been promoted to trainee. All profile data copied successfully.', $candidate->name));
        return $this->redirect(['action' => 'promoteToTrainee']);

        } catch (\Exception $e) {
            $this->log('doPromote error: ' . $e->getMessage(), 'error');
            $this->Flash->error(__('Promotion failed for "{0}": {1}', $candidate->name, $e->getMessage()));
            return $this->redirect(['action' => 'promoteToTrainee']);
        }
    }

    /**
     * Promotion history for candidates
     */
    public function promotionHistory()
    {
        $traineesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Trainees');
        $histories = $traineesTable->find()
            ->contain(['Candidates'])
            ->order(['Trainees.id' => 'DESC'])
            ->limit(200)
            ->all();

        // Ringkasan pipeline pasca-promosi (dari tabel trainees terasosiasi)
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainees');
        $pstats = $conn->execute(
            'SELECT COUNT(*) AS total,
                    SUM(apprenticeship_order_id IS NOT NULL) AS with_order,
                    SUM(is_apprenticeship_pass = 1) AS passed
             FROM trainees'
        )->fetch('assoc');

        $this->set(compact('histories', 'pstats'));
    }
}

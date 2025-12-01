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

        // Create CSV content (Excel-compatible)
        $csv = [];
        
        // Header
        $csv[] = ['CANDIDATE CURRICULUM VITAE'];
        $csv[] = [];
        
        // Basic Information
        $csv[] = ['BASIC INFORMATION'];
        $csv[] = ['Identity Number', $candidate->identity_number];
        $csv[] = ['Full Name', $candidate->name];
        $csv[] = ['Birth Place', $candidate->birth_place];
        $csv[] = ['Birth Date', $candidate->birth_date];
        $csv[] = ['Gender', $candidate->master_gender ? $candidate->master_gender->name : ''];
        $csv[] = ['Religion', $candidate->master_religion ? $candidate->master_religion->name : ''];
        $csv[] = ['Marriage Status', $candidate->master_marriage_status ? $candidate->master_marriage_status->name : ''];
        $csv[] = ['Blood Type', $candidate->master_blood_type ? $candidate->master_blood_type->name : ''];
        $csv[] = [];
        
        // Contact Information
        $csv[] = ['CONTACT INFORMATION'];
        $csv[] = ['Mobile Phone', $candidate->telephone_mobile];
        $csv[] = ['Emergency Contact', $candidate->telephone_emergency];
        $csv[] = ['Email', $candidate->email];
        $csv[] = ['Address', $candidate->address];
        $csv[] = [];
        
        // Education
        if (!empty($candidate->candidate_educations)) {
            $csv[] = ['EDUCATION HISTORY'];
            $csv[] = ['No.', 'Institution', 'Level', 'Field of Study', 'Year Graduated'];
            foreach ($candidate->candidate_educations as $index => $edu) {
                $csv[] = [
                    $index + 1,
                    $edu->institution_name,
                    $edu->level,
                    $edu->field_of_study,
                    $edu->year_graduated
                ];
            }
            $csv[] = [];
        }
        
        // Work Experience
        if (!empty($candidate->candidate_experiences)) {
            $csv[] = ['WORK EXPERIENCE'];
            $csv[] = ['No.', 'Company', 'Position', 'Start Date', 'End Date'];
            foreach ($candidate->candidate_experiences as $index => $exp) {
                $csv[] = [
                    $index + 1,
                    $exp->company_name,
                    $exp->position,
                    $exp->start_date,
                    $exp->end_date
                ];
            }
            $csv[] = [];
        }
        
        // Family
        if (!empty($candidate->candidate_families)) {
            $csv[] = ['FAMILY INFORMATION'];
            $csv[] = ['No.', 'Name', 'Relationship', 'Age', 'Occupation'];
            foreach ($candidate->candidate_families as $index => $fam) {
                $csv[] = [
                    $index + 1,
                    $fam->full_name,
                    $fam->relationship,
                    $fam->age,
                    $fam->occupation
                ];
            }
            $csv[] = [];
        }
        
        // Certifications
        if (!empty($candidate->candidate_certifications)) {
            $csv[] = ['CERTIFICATIONS'];
            $csv[] = ['No.', 'Certification', 'Organization', 'Issue Date', 'Expiry Date'];
            foreach ($candidate->candidate_certifications as $index => $cert) {
                $csv[] = [
                    $index + 1,
                    $cert->certification_name,
                    $cert->issuing_organization,
                    $cert->issue_date,
                    $cert->expiry_date
                ];
            }
            $csv[] = [];
        }
        
        // Courses
        if (!empty($candidate->candidate_courses)) {
            $csv[] = ['COURSES & TRAINING'];
            $csv[] = ['No.', 'Course', 'Institution', 'Start Date', 'End Date', 'Duration (hrs)'];
            foreach ($candidate->candidate_courses as $index => $course) {
                $csv[] = [
                    $index + 1,
                    $course->course_name,
                    $course->training_institution,
                    $course->start_date,
                    $course->end_date,
                    $course->duration_hours
                ];
            }
        }
        
        // Generate CSV file
        $filename = 'CV_' . $candidate->identity_number . '_' . date('Ymd') . '.csv';
        
        $response = $this->response->withStringBody(function () use ($csv) {
            $output = fopen('php://output', 'w');
            foreach ($csv as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        });
        
        return $response
            ->withType('csv')
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
        $this->viewBuilder()->setLayout('process_flow');
    }
}
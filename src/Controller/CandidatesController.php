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
    use \App\Controller\CandidateWizardTrait;

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

        /**
         * Nothing here is public any more.
         *
         * Auth->allow() does not relax a permission, it removes the question:
         * an allowed action is reachable by anyone who can reach the site,
         * signed in or not. These four were on that list.
         *
         * The wizard is not a public form. The guide calls it one of the two
         * ways an operator enters a candidate - "the plain form is there when
         * you already know what you are doing" - and the action itself reads
         * Auth.User to stamp the candidate with the operator's institution.
         * Yet it saved a candidate, their educations, experiences, families,
         * certifications and courses, and wrote a photo file, for a caller who
         * had not signed in. Such a candidate got no institution, so it
         * belonged to nobody; and the last step redirects to view, which was
         * never public, so whoever submitted it was bounced to the login page
         * the moment it saved. It was not a working public flow, it was an
         * open door.
         *
         * The three region lookups were swept onto the list beside it. They
         * carry nothing about anybody, but they have an isAuthorized() of
         * their own now that grants them to any signed-in user - and while
         * they sat here that check was never reached, because an allowed
         * action is never authorized at all.
         */
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
        // Scoped by the filter already chosen rather than capped at two
        // hundred by id: every province, and below one only what belongs
        // to it. See AppController::regionListsForFilters().
        $regions = $this->regionListsForFilters();
        $master_propinsis = $regions['masterPropinsis'];
        $master_kabupatens = $regions['masterKabupatens'];
        $master_kecamatans = $regions['masterKecamatans'];
        $master_kelurahans = $regions['masterKelurahans'];
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
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($candidate);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $masterKecamatans = $regions['masterKecamatans'];
        $masterKelurahans = $regions['masterKelurahans'];
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
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($candidate);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $masterKecamatans = $regions['masterKecamatans'];
        $masterKelurahans = $regions['masterKelurahans'];
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
                // Every propinsi, rather than the first two hundred by id that
                // find('list', ['limit' => 200]) gave - see
                // AppController::regionLists(). Nothing is passed because this
                // step has no record yet, only session data, and the three
                // lists below the province are loaded by the wizard's own
                // script, which also puts back whatever was chosen before.
                $regions = $this->regionLists();
                $masterPropinsis = $regions['masterPropinsis'];
                
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

        // The candidate as they stand, for the CV beside the form. Rebuilt
        // from the session on every step, so the preview is never behind what
        // has been typed; candidate-cv-live.js keeps it in step between steps.
        $this->set('cvCandidate', $this->wizardCandidateEntity($wizardData));
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
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        //
        // The filters were only half of that. index() also narrows to the
        // user's own institution, and this did not, so Export and Print
        // answered with every institution's rows while the screen above them
        // showed one institution's - the line about giving what the screen
        // shows being the only thing that said otherwise.
        $query = $this->scopeToInstitution($query);
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->scopeToInstitution($query);
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->scopeToInstitution($query);
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
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
            
            // Generate filename.
            //
            // The identity number went in whole, and it is whatever was typed
            // on step one. A value carrying ../ walked the photo out of the
            // uploads directory and into anywhere the web user can write - the
            // extension is held to an image, so no code could be planted, but
            // an existing image could be written over. Only the characters an
            // identity number is made of are kept, and the name is held to a
            // sane length so a long one cannot push the rest off the end.
            $safeIdentity = substr(preg_replace('/[^A-Za-z0-9_-]/', '', (string)$identityNumber), 0, 40);
            if ($safeIdentity === '') {
                $safeIdentity = 'unknown';
            }
            $filename = 'photo_' . $safeIdentity . '_' . time() . '.' . $type;
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
        
        // Through the map: the form's own names are not columns, and an
        // entity drops what is not a column without a word. See
        // CandidateWizardTrait.
        foreach ($this->wizardRows('educations', $educations) as $row) {
            $row['candidate_id'] = $candidateId;
            $entity = $this->CandidateEducations->newEntity([], ['validate' => false]);
            foreach ($row as $column => $value) {
                $entity->set($column, $value);
            }
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
        
        // Through the map: the form's own names are not columns, and an
        // entity drops what is not a column without a word. See
        // CandidateWizardTrait.
        foreach ($this->wizardRows('experiences', $experiences) as $row) {
            $row['candidate_id'] = $candidateId;
            $entity = $this->CandidateExperiences->newEntity([], ['validate' => false]);
            foreach ($row as $column => $value) {
                $entity->set($column, $value);
            }
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
        
        // Through the map: the form's own names are not columns, and an
        // entity drops what is not a column without a word. See
        // CandidateWizardTrait.
        foreach ($this->wizardRows('families', $families) as $row) {
            $row['candidate_id'] = $candidateId;
            $entity = $this->CandidateFamilies->newEntity([], ['validate' => false]);
            foreach ($row as $column => $value) {
                $entity->set($column, $value);
            }
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
        
        // Through the map: the form's own names are not columns, and an
        // entity drops what is not a column without a word. See
        // CandidateWizardTrait.
        foreach ($this->wizardRows('certifications', $certifications) as $row) {
            $row['candidate_id'] = $candidateId;
            $entity = $this->CandidateCertifications->newEntity([], ['validate' => false]);
            foreach ($row as $column => $value) {
                $entity->set($column, $value);
            }
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
        
        // Through the map: the form's own names are not columns, and an
        // entity drops what is not a column without a word. See
        // CandidateWizardTrait.
        foreach ($this->wizardRows('courses', $courses) as $row) {
            $row['candidate_id'] = $candidateId;
            $entity = $this->CandidateCourses->newEntity([], ['validate' => false]);
            foreach ($row as $column => $value) {
                $entity->set($column, $value);
            }
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
        // Everything the printed form shows. The four region tables make up
        // the one address line it prints; the nested ones carry the Japanese
        // spelling of a school level, a family tie and an occupation, which
        // the form prints and a bare foreign key cannot.
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'CandidateEducations' => ['MasterStratas'],
                'CandidateExperiences',
                'CandidateFamilies' => ['MasterFamilyConnections', 'MasterOccupations'],
                'CandidateCertifications',
                'CandidateCourses',
                'MasterGenders',
                'MasterReligions',
                'MasterMarriageStatuses',
                'MasterBloodTypes',
                'MasterPropinsis',
                'MasterKabupatens',
                'MasterKecamatans',
                'MasterKelurahans',
                'AcceptanceOrganizations',
                // 企業名 falls back to the order's employer when the
                // candidate has not been put forward to one of their own.
                'ApprenticeOrders' => ['AcceptanceOrganizations'],
            ]
        ]);

        $this->set('candidate', $candidate);
    }

    /**
     * The CV as it stands, from what is on the form right now.
     *
     * The preview beside the registration wizard asks for this on a pause in
     * typing and swaps in what comes back. Rendering it on the server rather
     * than redrawing it in JavaScript means the preview is the same document
     * the candidate ends up with - the same element, through the same field
     * map as the save - instead of a second CV drawn to look like the first
     * and free to drift from it.
     *
     * Nothing is written. It builds an entity, renders it, and throws it away.
     *
     * @return void
     */
    public function cvPreview()
    {
        $this->request->allowMethod(['post']);
        $this->viewBuilder()->setLayout('ajax');

        $session = $this->request->getSession()->read('Wizard.Candidate');
        $wizardData = is_array($session) ? $session : [];

        // What the form holds now wins over what the session remembers: the
        // point of the preview is to show what has just been typed.
        foreach ($this->request->getData() as $field => $value) {
            if ($field === '_csrfToken' || $field === '_Token') {
                continue;
            }
            $wizardData[$field] = $value;
        }

        $this->set('cvCandidate', $this->wizardCandidateEntity($wizardData));
    }

    /**
     * The CV as a spreadsheet, laid out as the form.
     *
     * The same document Element/Candidates/cv_body.ctp prints: the letterhead,
     * 企業名, the head block with its photo, 現住所 and No.HP, 確認事項, 学歴,
     * 職歴, 家族構成 and the certificates after them. It used to be a workbook
     * of its own design - "Basic Information", "Contact Information",
     * "Additional Information" - which was the CV this application drew before
     * the LPK's own form replaced it, and it carried none of 確認事項 at all.
     * So Export Excel handed over a different document from the one on the
     * screen and on the paper.
     *
     * A spreadsheet cannot render a template, so this builds its own grid. What
     * keeps the two from drifting is tests/candidate_cv_excel.php, which reads
     * the labels and the values out of the rendered page and finds every one of
     * them in the workbook.
     *
     * @param int|null $id Candidate ID
     * @return \Cake\Http\Response
     */
    public function exportCvExcel($id = null)
    {
        // The same associations the CV element reads. A missing one here is a
        // blank cell in the download and nothing else, which is why it is the
        // same list and not a shorter one.
        $candidate = $this->Candidates->get($id, [
            'contain' => [
                'CandidateEducations' => ['MasterStratas'],
                'CandidateExperiences',
                'CandidateFamilies' => ['MasterFamilyConnections', 'MasterOccupations'],
                'CandidateCertifications',
                'CandidateCourses',
                'MasterGenders',
                'MasterReligions',
                'MasterMarriageStatuses',
                'MasterBloodTypes',
                'MasterPropinsis',
                'MasterKabupatens',
                'MasterKecamatans',
                'MasterKelurahans',
                'AcceptanceOrganizations',
                'ApprenticeOrders' => ['AcceptanceOrganizations'],
            ]
        ]);

        $form = new \App\View\CandidateCvForm($candidate);
        $filename = 'CV_' . ($candidate->identity_number ?: $candidate->id)
            . '_' . date('Ymd') . '.xlsx';

        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Without the library the form cannot be drawn, so what goes out is
            // the same content as a readable two-column list rather than a
            // spreadsheet named .xlsx that is not one.
            $stream = fopen('php://temp', 'r+');
            foreach ($form->flat() as $line) {
                fputcsv($stream, $line);
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

        // The same proportions the page uses: a narrower name block and a
        // narrower figure, and what that frees becomes the Catatan column.
        $widths = ['A' => 15, 'B' => 11, 'C' => 20, 'D' => 10, 'E' => 21,
            'F' => 7, 'G' => 5, 'H' => 28];
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $thin = [
            'borders' => ['allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000'],
            ]],
        ];
        $middle = ['alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ]];
        $centre = ['alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ]];
        $shade = function ($rgb) {
            return ['fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => $rgb],
            ]];
        };

        /**
         * Write one cell, merging it across a range when it spans more than one.
         */
        $put = function ($range, $text, array $style = []) use ($sheet) {
            $cell = strpos($range, ':') === false ? $range : substr($range, 0, strpos($range, ':'));
            if (strpos($range, ':') !== false) {
                $sheet->mergeCells($range);
            }
            $sheet->setCellValueExplicit($cell, (string)$text,
                \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            if ($style) {
                $sheet->getStyle($range)->applyFromArray($style);
            }
        };

        $label = array_merge($thin, $middle, $shade('EBEBEB'));
        $value = array_merge($thin, $middle, $centre);
        $right = array_merge($thin, $middle, ['alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        ]]);
        $left = array_merge($thin, $middle, ['alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
        ]]);
        $valueLeft = array_merge($thin, $middle);
        $section = array_merge($thin, $middle, $centre, $shade('D9D9D9'),
            ['font' => ['bold' => true]]);

        $row = 1;

        // The letterhead, as on the paper.
        $logo = WWW_ROOT . 'img' . DS . 'lpk-trimitra-logo.png';
        if (file_exists($logo)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($logo);
            $drawing->setHeight(62);
            $drawing->setCoordinates('A' . $row);
            $drawing->setOffsetX(6);
            $drawing->setWorksheet($sheet);
        }
        foreach ($form->letterhead() as $index => $line) {
            $put('B' . $row . ':H' . $row, $line, array_merge($centre,
                $index === 0 ? ['font' => ['bold' => true, 'size' => 14]] : ['font' => ['size' => 9]]));
            $row++;
        }
        $row++;

        foreach ($form->company() as $line) {
            $put('A' . $row . ':H' . $row, $line);
            $row++;
        }
        $row++;

        // The head block: photo down the left, nine rows beside it.
        $head = $form->head();
        $first = $row;
        $last = $row + count($head) - 1;
        $sheet->mergeCells('A' . $first . ':A' . $last);
        $sheet->getStyle('A' . $first . ':A' . $last)->applyFromArray($thin);
        $photo = $form->photoPath();
        if ($photo) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($photo);
            // A drawing floats over the grid rather than sitting in a cell,
            // so it is sized to stay inside column A's width (15 characters,
            // about 110px): 120 tall is about 90 wide at a portrait's ratio.
            $drawing->setHeight(120);
            $drawing->setCoordinates('A' . $first);
            $drawing->setOffsetX(8);
            $drawing->setOffsetY(6);
            $drawing->setWorksheet($sheet);
        }
        foreach ($head as $line) {
            $sheet->getRowDimension($row)->setRowHeight(17);
            // Left of the block: a label on its own line, or a value centred
            // under it.
            if ($line['left_kind'] === 'label') {
                $put('B' . $row . ':C' . $row, $line['left'], $label);
            } elseif ($line['left_kind'] === 'split') {
                $put('B' . $row, $line['left'], $label);
                $put('C' . $row, '', $value);
            } else {
                $put('B' . $row . ':C' . $row, $line['left'],
                    array_merge($value, ['font' => ['bold' => true]]));
            }
            $put('D' . $row, $line['ja'], $label);
            $put('E' . $row, $line['id'], $label);
            if ($line['unit'] === '') {
                $put('F' . $row . ':G' . $row, $line['value'],
                    array_merge($value, ['font' => ['bold' => true]]));
            } else {
                $put('F' . $row, $line['value'], array_merge($right, ['font' => ['bold' => true]]));
                $put('G' . $row, $line['unit'], $left);
            }
            $row++;
        }
        // No blank row here: on the page the address block carries straight on
        // from the head block inside the same table, which is what lets one
        // cell span both.

        // The Catatan column is the interviewer's, and it runs beside the head
        // block and the address under it, stopping above 確認事項 - which is
        // the first gap the blocks give us. Everything after that spans the
        // full width.
        $notesFrom = $first;
        $edge = 'G';
        foreach ($form->blocks() as $block) {
            if ($block['type'] === 'gap' && $edge === 'G') {
                $sheet->mergeCells('H' . $notesFrom . ':H' . ($row - 1));
                $put('H' . $notesFrom . ':H' . ($row - 1), $form->label('notes'), $left);
                $edge = 'H';
                $row++;
                continue;
            }
            if ($block['type'] === 'section') {
                $put('A' . $row . ':' . $edge . $row, $block['text'], $section);
                $row++;
                continue;
            }
            if ($block['type'] === 'wide') {
                $put('A' . $row . ':' . $edge . $row, $block['text'], $value);
                $row++;
                continue;
            }
            if ($block['type'] === 'pair') {
                $put('A' . $row . ':B' . $row, $block['label'], $label);
                $put('C' . $row . ':' . $edge . $row, $block['value'], $value);
                $row++;
                continue;
            }
            if ($block['type'] === 'four') {
                $put('A' . $row . ':B' . $row, $block['cells'][0], $label);
                $put('C' . $row . ':D' . $row, $block['cells'][1], $value);
                $put('E' . $row, $block['cells'][2], $label);
                $put('F' . $row . ':' . $edge . $row, $block['cells'][3], $value);
                $row++;
                continue;
            }
            if ($block['type'] === 'two') {
                $put('A' . $row . ':B' . $row, $block['cells'][0], $label);
                $put('C' . $row . ':' . $edge . $row, $block['cells'][1], $value);
                $row++;
                continue;
            }
            if ($block['type'] === 'three') {
                $style = !empty($block['head']) ? $label : $value;
                $put('A' . $row . ':B' . $row, $block['cells'][0], $style);
                $put('C' . $row . ':D' . $row, $block['cells'][1],
                    !empty($block['head']) ? $label : $valueLeft);
                $put('E' . $row . ':' . $edge . $row, $block['cells'][2],
                    !empty($block['head']) ? $label : $valueLeft);
                $row++;
                continue;
            }
            if ($block['type'] === 'family') {
                $style = !empty($block['head']) ? $label : $value;
                $put('A' . $row, $block['cells'][0], $style);
                $put('B' . $row . ':C' . $row, $block['cells'][1],
                    !empty($block['head']) ? $label : $valueLeft);
                $put('D' . $row, $block['cells'][2], $style);
                $put('E' . $row . ':' . $edge . $row, $block['cells'][3],
                    !empty($block['head']) ? $label : $valueLeft);
                $row++;
                continue;
            }
            if ($block['type'] === 'gap') {
                $row++;
            }
        }

        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.3)->setBottom(0.3)->setLeft(0.3)->setRight(0.3);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $body = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

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
    }



    /**
     * Roles that may declare a candidate through selection, or take that back.
     *
     * The institution owns the decision; an administrator supports it.
     *
     * @var array
     */
    const PROPOSER_ROLES = ['administrator', 'lpk-penyangga'];

    /**
     * Actions the menu permissions cannot be expected to know about.
     *
     * @var array
     */
    /**
     * The address cascade behind the wizard. Reference data only.
     */
    const REGION_LOOKUPS = ['getKabupaten', 'getKecamatan', 'getKelurahan'];

    const SELECTION_ACTIONS = ['propose', 'withdrawProposal'];

    /**
     * Let the proposing institution reach the two selection actions.
     *
     * Without this the feature would ship dead for the role it was built for.
     * AppController::isAuthorized() asks hasPermission(), which reads
     * role_menus.granted_actions - and a value of "*" there does NOT mean every
     * action: getMenuRolePermissions() expands it to the menu's own action plus
     * index and view. A brand-new action is in nobody's granted list, so
     * lpk-penyangga would have been refused on a button built for it, and the
     * refusal would have looked like a permissions bug rather than a missing
     * row of data.
     *
     * Everything else still goes through the normal DB-driven check.
     *
     * @param array $user The authenticated user.
     * @return bool
     */
    public function isAuthorized($user)
    {
        $this->currentUser = $user;

        $action = $this->request->getParam('action');

        // The wizard's address cascade. These three answer with the id and
        // name of Indonesian administrative regions - public reference data,
        // nothing about any candidate - and are asked for by a script while
        // somebody fills in an address on a form they already reached.
        // Without this they fall to getMenuRolePermissions(), which expands
        // granted_actions = '*' to [the menu's own action, index, view], so
        // they were refused to every role but administrator: the wizard got a
        // redirect where it expected JSON and the dropdowns stayed empty.
        if (in_array($action, self::REGION_LOOKUPS, true)) {
            return (bool)$this->Auth->user('id');
        }

        // The wizard enters a candidate; so does add. Whoever may do it on the
        // plain form may do it here, which is the only sensible reading and
        // the only one that does not need a row of data somewhere to be right.
        // Left to getMenuRolePermissions(), which expands granted_actions = '*'
        // to [the menu's own action, index, view], the wizard would be refused
        // to every role but administrator.
        //
        // Either grant opens it, and the second one is not redundant: a menu
        // pointing at /candidates/wizard expands to [wizard, index, view],
        // which does not contain add. Read against add alone, the wizard would
        // refuse itself to the one role whose menu is the wizard - which is
        // what database/migrations/add_candidate_wizard_menu.sql creates.
        if ($action === 'wizard' || $action === 'cvPreview') {
            return $this->hasPermission('Candidates', 'add')
                || $this->hasPermission('Candidates', 'wizard');
        }

        // The CV, and the form preview beside it, are this candidate's own
        // record read back. Whoever may open the candidate may read it; the
        // same expansion applies as above, so without this the buttons would
        // answer "your role does not have access" to the role the screens
        // were built for.
        if ($action === 'cv' || $action === 'preview'
            || $action === 'exportCvExcel' || $action === 'exportCvPdf') {
            return $this->hasPermission('Candidates', 'view');
        }

        if (!in_array($action, self::SELECTION_ACTIONS, true)) {
            return parent::isAuthorized($user);
        }

        foreach (self::PROPOSER_ROLES as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        $this->handleUnauthorizedAccess(
            $action,
            __('Only the proposing institution can declare a candidate through selection.')
        );

        return false;
    }

    /**
     * The institution declares a candidate through its own selection.
     *
     * This is the step that was missing. Recruitment's promotion list used to
     * ask for a flag that only promotion itself set, so nobody could ever reach
     * it. What puts a candidate in front of recruitment is their institution
     * saying the candidate has passed selection; whether that candidate then
     * becomes a trainee stays recruitment's decision, recorded by promoting.
     *
     * A candidate whose medical check-up says not fit is refused here rather
     * than further along, where the refusal would be harder to explain.
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null
     */
    public function propose($id = null)
    {
        $this->request->allowMethod(['post']);

        if (!$this->Candidates->getSchema()->hasColumn('lpk_proposed_at')) {
            $this->Flash->error(__('This installation cannot record a proposal yet. An administrator needs to run the selection-flow update first.'));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        $candidate = $this->Candidates->get($id);
        if (!$this->canAccessRecord($candidate)) {
            $this->Flash->error(__('That candidate belongs to another institution.'));

            return $this->redirect(['action' => 'index']);
        }
        if (!$this->hasRole('administrator') && !$this->hasRole('lpk-penyangga')) {
            $this->Flash->error(__('Only the proposing institution can declare a candidate through selection.'));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        if ($this->Candidates->getSchema()->hasColumn('mcu_result')
            && $candidate->get('mcu_result') === 'fail'
        ) {
            $this->Flash->error(__('"{0}" is marked not medically fit and cannot be put forward.', $candidate->name));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        $candidate->set('lpk_proposed_at', new \Cake\I18n\FrozenTime());
        if ($this->Candidates->getSchema()->hasColumn('lpk_proposed_by')) {
            $candidate->set('lpk_proposed_by', $this->Auth->user('id'));
        }

        // checkRules/validate off: these two fields are not user input, and a
        // candidate with some unrelated legacy validation problem would
        // otherwise be impossible to put forward for a reason nobody could see.
        if ($this->Candidates->save($candidate, ['checkRules' => false, 'validate' => false])) {
            $this->recordDecision('candidate.propose', [
                'type' => 'Candidate', 'id' => $candidate->id, 'label' => $candidate->name,
            ], ['mcu_result' => $candidate->get('mcu_result')]);
            $this->Flash->success(__('"{0}" has been put forward. Recruitment will see them on the promotion list.', $candidate->name));
        } else {
            $this->Flash->error(__('"{0}" could not be put forward. Please, try again.', $candidate->name));
        }

        return $this->redirect($this->referer(['action' => 'index']));
    }

    /**
     * Take back a proposal.
     *
     * The candidate leaves recruitment's list and returns to the institution's
     * own hands. Nothing else about them changes; a candidate who was already
     * promoted is not affected, because promotion has already copied them.
     *
     * @param string|null $id Candidate id.
     * @return \Cake\Http\Response|null
     */
    public function withdrawProposal($id = null)
    {
        $this->request->allowMethod(['post']);

        if (!$this->Candidates->getSchema()->hasColumn('lpk_proposed_at')) {
            $this->Flash->error(__('This installation cannot record a proposal yet. An administrator needs to run the selection-flow update first.'));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        $candidate = $this->Candidates->get($id);
        if (!$this->canAccessRecord($candidate)) {
            $this->Flash->error(__('That candidate belongs to another institution.'));

            return $this->redirect(['action' => 'index']);
        }
        if (!$this->hasRole('administrator') && !$this->hasRole('lpk-penyangga')) {
            $this->Flash->error(__('Only the proposing institution can take a candidate back.'));

            return $this->redirect($this->referer(['action' => 'index']));
        }

        $candidate->set('lpk_proposed_at', null);
        if ($this->Candidates->getSchema()->hasColumn('lpk_proposed_by')) {
            $candidate->set('lpk_proposed_by', null);
        }

        if ($this->Candidates->save($candidate, ['checkRules' => false, 'validate' => false])) {
            $this->recordDecision('candidate.withdrawProposal', [
                'type' => 'Candidate', 'id' => $candidate->id, 'label' => $candidate->name,
            ]);
            $this->Flash->success(__('"{0}" has been taken back off the promotion list.', $candidate->name));
        } else {
            $this->Flash->error(__('"{0}" could not be taken back. Please, try again.', $candidate->name));
        }

        return $this->redirect($this->referer(['action' => 'index']));
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

        // Eligible: active, put forward by the institution, not yet promoted.
        //
        // This used to require is_candidate_pass = 1, which only doPromote()
        // ever sets - and it sets it at the same moment it creates the trainee,
        // which then excludes the candidate by the NOT IN below. The list could
        // therefore only ever be empty. The flag is the outcome of recruitment
        // promoting somebody, not the ticket that gets them onto the list; what
        // gets them on the list is their own institution declaring them through
        // selection.
        $eligibleConditions = ['Candidates.status_flag' => 'active'];
        if ($this->Candidates->getSchema()->hasColumn('lpk_proposed_at')) {
            $eligibleConditions['Candidates.lpk_proposed_at IS NOT'] = null;
        }
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
        // Added only where the columns exist, so the screen keeps working on an
        // installation that has not yet run bin/cake add_selection_flow_columns.
        foreach (['mcu_result', 'lpk_proposed_at'] as $optional) {
            if ($this->Candidates->getSchema()->hasColumn($optional)) {
                $selectFields[] = 'Candidates.' . $optional;
            }
        }

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

        // A candidate marked not medically fit is not promoted, whatever else
        // their scores say. The check-up screen already warned the institution
        // when the result was entered; this is the place it actually holds.
        if ($this->Candidates->getSchema()->hasColumn('mcu_result')
            && $candidate->get('mcu_result') === 'fail'
        ) {
            $this->Flash->error(__('"{0}" is marked not medically fit and cannot be promoted.', $candidate->name));

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

        $this->recordDecision('candidate.promoteToTrainee', [
            'type' => 'Candidate', 'id' => $candidate->id, 'label' => $candidate->name,
        ], ['trainee_id' => $trainee->id]);

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

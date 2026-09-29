<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * VocationalTrainingInstitutions Controller
 *
 * @property \App\Model\Table\VocationalTrainingInstitutionsTable $VocationalTrainingInstitutions
 *
 * @method \App\Model\Entity\VocationalTrainingInstitution[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class VocationalTrainingInstitutionsController extends AppController
{
    use \App\Controller\ExportTrait;
    
    /**
     * Initialize method
     */
    public function initialize()
    {
        parent::initialize();
        
        // Load Email component with error handling
        try {
            $this->loadComponent('Email');
        } catch (\Exception $e) {
            // Email component failed to load, continue without it
            // This can happen if database tables aren't set up yet
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans'],
        ];
        $vocationalTrainingInstitutions = $this->paginate($this->VocationalTrainingInstitutions);

        // Load dropdown data for filters
        // Scoped by the filter already chosen rather than capped at two
        // hundred by id: every province, and below one only what belongs
        // to it. See AppController::regionListsForFilters().
        $regions = $this->regionListsForFilters();
        $masterpropinsis = $regions['masterPropinsis'];
        $masterkabupatens = $regions['masterKabupatens'];
        $masterkecamatans = $regions['masterKecamatans'];
        $masterkelurahans = $regions['masterKelurahans'];
        $master_propinsis = $masterpropinsis;
        $master_kabupatens = $masterkabupatens;
        $master_kecamatans = $masterkecamatans;
        $master_kelurahans = $masterkelurahans;
        // Headline counts for the summary cards. The registration workflow is
        // the point of this screen (create -> email link -> institution
        // registers), so how many are still pending is the number that matters.
        $table = $this->VocationalTrainingInstitutions;
        $total = $table->find()->count();
        $registered = $table->find()->where(['is_registered' => 1])->count();
        $stats = [
            'total' => $total,
            'registered' => $registered,
            'pending' => $total - $registered,
            'special_skill' => $table->find()
                ->where(['is_special_skill_support_institution' => 1])
                ->count(),
        ];

        // Used by the delete confirmation, which names how many candidates are
        // recorded against the institution before it goes.
        $candidateCounts = $this->_candidateCounts();

        $this->set(compact('vocationalTrainingInstitutions', 'masterpropinsis', 'masterkabupatens', 'masterkecamatans', 'masterkelurahans', 'master_propinsis', 'master_kabupatens', 'master_kecamatans', 'master_kelurahans', 'stats', 'candidateCounts'));
    }


                
    /**
     * View method
     *
     * @param string|null $id Vocational Training Institution id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'MasterPropinsis';
        $contain[] = 'MasterKabupatens';
        $contain[] = 'MasterKecamatans';
        $contain[] = 'MasterKelurahans';
        
        // Add HasMany with nested BelongsTo for foreign key display
            // VocationalTrainingInstitutionStories with its associations
        $vocationalTrainingInstitutionStoriesAssociations = [];
        try {
            $vocationalTrainingInstitutionStoriesTable = $this->VocationalTrainingInstitutions->VocationalTrainingInstitutionStories;
            // Get all BelongsTo associations for nested contain
            foreach ($vocationalTrainingInstitutionStoriesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $vocationalTrainingInstitutionStoriesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($vocationalTrainingInstitutionStoriesAssociations)) {
            $contain['VocationalTrainingInstitutionStories'] = $vocationalTrainingInstitutionStoriesAssociations;
        } else {
            $contain[] = 'VocationalTrainingInstitutionStories';
        }
        
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->get($id, [
            'contain' => $contain,
        ]);

        $this->set('vocationalTrainingInstitution', $vocationalTrainingInstitution);
        $this->set('credentialAudience', $this->_credentialAudience());
    }

    /**
     * Whose login name the signed-in user is allowed to see.
     *
     * The login name is a credential: half of what is needed to sign in as the
     * institution. It belongs to an administrator, who has to support the
     * account, and to the institution itself, which has to use it - and to
     * nobody else on the staff, however many other columns of the record they
     * can read.
     *
     *   true          an administrator: every institution's
     *   (int) id      an LPK account: its own, and only its own
     *   null          everyone else, including anyone not signed in
     *
     * An LPK is recognised by the institution its user account is attached to,
     * which setPassword() writes when the account is activated.
     *
     * @return true|int|null
     */
    protected function _credentialAudience()
    {
        $user = $this->Auth->user();
        if (empty($user)) {
            return null;
        }

        if (in_array('administrator', (array)$this->Auth->user('role_names'), true)) {
            return true;
        }

        if (!empty($user['institution_id'])
            && isset($user['institution_type'])
            && $user['institution_type'] === 'vocational_training'
        ) {
            return (int)$user['institution_id'];
        }

        return null;
    }

    /**
     * Create method
     *
     * Full registration form for a new institution. This is the canonical
     * create action; add() redirects here so existing links keep working.
     *
     * @return \Cake\Http\Response|null Redirects on successful create, renders view otherwise.
     */
    public function create()
    {
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->newEntity();
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
                $imagePath = $this->uploadImage('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->patchEntity($vocationalTrainingInstitution, $data);
            if ($this->VocationalTrainingInstitutions->save($vocationalTrainingInstitution)) {
                // Generate registration token
                $vocationalTrainingInstitution->generateRegistrationToken(48); // 48 hours expiry
                $this->VocationalTrainingInstitutions->save($vocationalTrainingInstitution);
                
                // Send registration email (if Email component is loaded)
                $emailSent = false;
                if (isset($this->Email)) {
                    $registrationUrl = \Cake\Routing\Router::url([
                        'controller' => 'InstitutionRegistration',
                        'action' => 'complete',
                        $vocationalTrainingInstitution->registration_token
                    ], true);
                    
                    $emailSent = $this->Email->sendRegistrationEmail($vocationalTrainingInstitution, $registrationUrl);
                }
                
                if ($emailSent) {
                    $this->Flash->success(__(
                        'Institution saved! Registration email sent to {0}',
                        $vocationalTrainingInstitution->email
                    ));
                } else {
                    $this->Flash->success(__(
                        'Institution saved! Registration token: {0}. Use this URL: {1}',
                        $vocationalTrainingInstitution->registration_token,
                        'http://localhost/tmm/institution-registration/complete/' . $vocationalTrainingInstitution->registration_token
                    ));
                }

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vocational training institution could not be saved. Please, try again.'));
        }
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($vocationalTrainingInstitution);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $masterKecamatans = $regions['masterKecamatans'];
        $masterKelurahans = $regions['masterKelurahans'];
        $this->set(compact('vocationalTrainingInstitution', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans'));
    }

    /**
     * Add method
     *
     * Kept so existing links, bookmarks and menu entries pointing at /add do
     * not break. The form itself lives at create().
     *
     * @return \Cake\Http\Response Always redirects to create().
     */
    public function add()
    {
        return $this->redirect(['action' => 'create']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Vocational Training Institution id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->get($id, [
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
                $imagePath = $this->uploadImage('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('VocationalTrainingInstitutions', $fieldName, 'vocationaltraininginstitutions');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->patchEntity($vocationalTrainingInstitution, $data);
            if ($this->VocationalTrainingInstitutions->save($vocationalTrainingInstitution)) {
                $this->Flash->success(__('The vocational training institution has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The vocational training institution could not be saved. Please, try again.'));
        }
        // Every propinsi, and below it only what belongs to what is already
        // chosen. These lists used to be find('list', ['limit' => 200]) - two
        // hundred of 84,305 kelurahan - and an edit form whose saved region
        // fell outside them posted an empty value and wiped it. See
        // AppController::regionLists().
        $regions = $this->regionLists($vocationalTrainingInstitution);
        $masterPropinsis = $regions['masterPropinsis'];
        $masterKabupatens = $regions['masterKabupatens'];
        $masterKecamatans = $regions['masterKecamatans'];
        $masterKelurahans = $regions['masterKelurahans'];
        $this->set(compact('vocationalTrainingInstitution', 'masterPropinsis', 'masterKabupatens', 'masterKecamatans', 'masterKelurahans'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Vocational Training Institution id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $vocationalTrainingInstitution = $this->VocationalTrainingInstitutions->get($id);
        if ($this->VocationalTrainingInstitutions->delete($vocationalTrainingInstitution)) {
            $this->Flash->success(__('The vocational training institution has been deleted.'));
        } else {
            $this->Flash->error(__('The vocational training institution could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Register method - Register a new LPK
     *
     * Redirects to the 3-step LPK registration wizard (Admin/LpkRegistration).
     *
     * @return \Cake\Http\Response
     */
    public function register()
    {
        return $this->redirect(['prefix' => 'admin', 'controller' => 'LpkRegistration', 'action' => 'create']);
    }

    /**
     * Verify method - LPK email verification status overview
     *
     * Lists all institutions with their registration/verification status.
     *
     * @return void
     */
    public function verify()
    {
        $this->paginate = [
            // username is selected for the login-name column, which the
            // template shows only to an administrator or to the institution
            // itself - see _credentialAudience().
            'fields' => ['id', 'name', 'status', 'is_registered', 'registered_at', 'email', 'username'],
            'order' => ['VocationalTrainingInstitutions.registered_at' => 'DESC'],
            'limit' => 20,
        ];
        $institutions = $this->paginate($this->VocationalTrainingInstitutions);

        // Ringkasan verifikasi + jumlah kandidat per LPK (tabel terasosiasi lintas-DB)
        $t = $this->VocationalTrainingInstitutions;
        $vstats = [
            'total'      => $t->find()->count(),
            'registered' => $t->find()->where(['is_registered' => 1])->count(),
            'active'     => $t->find()->where(['status' => 'active'])->count(),
        ];
        $vstats['pending'] = $vstats['total'] - $vstats['registered'];

        $candidateCounts = $this->_candidateCounts();

        $this->set(compact('institutions', 'vstats', 'candidateCounts'));
        $this->set('credentialAudience', $this->_credentialAudience());
    }

    /**
     * How many candidates each institution has, keyed by institution id.
     *
     * The candidates live in their own database connection with no foreign key
     * back to this table, so nothing here can join to them and nothing stops an
     * institution being deleted out from under them. Counting them is what lets
     * a screen say so before it happens.
     *
     * A missing or unreachable connection is not an error worth stopping a page
     * for: the caller gets an empty map and shows no counts.
     *
     * @return array<int, int>
     */
    protected function _candidateCounts()
    {
        $counts = [];
        try {
            foreach (\Cake\Datasource\ConnectionManager::get('cms_lpk_candidates')->execute(
                'SELECT vocational_training_institution_id AS lpk, COUNT(*) AS n
                 FROM candidates GROUP BY vocational_training_institution_id'
            )->fetchAll('assoc') as $r) {
                $counts[$r['lpk']] = $r['n'];
            }
        } catch (\Exception $e) {
        }

        return $counts;
    }

    /**
     * Help method - Stakeholder Management Guide
     *
     * @return void
     */
    public function help()
    {
        // This is just a view, no data processing needed
    }

    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->VocationalTrainingInstitutions->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans']);
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
        return $this->doExportCsv($query, 'VocationalTrainingInstitutions', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->VocationalTrainingInstitutions->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans']);
        
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
        $query = $this->applyIndexFilters($query);
        list($headers, $fields) = $this->exportColumns($query);
        
        return $this->doExportExcel($query, 'VocationalTrainingInstitutions', $headers, $fields);
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
        
        $query = $this->VocationalTrainingInstitutions->find('all')
            ->contain(['MasterPropinsis', 'MasterKabupatens', 'MasterKecamatans', 'MasterKelurahans']);
        
        // Define report configuration
        $title = 'VocationalTrainingInstitutions Report';
        // The four these used to ask for were bake's placeholder and were
        // never filled in: of the ninety-two tables here, four have all of
        // name, created and modified and fifty-four have none, and a column
        // that is not there exports as an empty cell. The table's own columns
        // are taken instead, with foreign keys resolved to the names they
        // point at. See AppController::exportColumns().
        //
        // And the same filters the list was narrowed by, so Export gives what
        // the screen shows rather than the whole table.
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
     * Resend registration email
     *
     * @param string|null $id Institution id
     * @return \Cake\Http\Response|null
     */
    public function resendRegistrationEmail($id = null)
    {
        $this->request->allowMethod(['post']);
        
        $institution = $this->VocationalTrainingInstitutions->get($id);
        
        // Regenerate token
        $institution->generateRegistrationToken(48);
        $this->VocationalTrainingInstitutions->save($institution);
        
        // Send email
        $registrationUrl = \Cake\Routing\Router::url([
            'controller' => 'InstitutionRegistration',
            'action' => 'complete',
            $institution->registration_token
        ], true);
        
        if ($this->Email->sendRegistrationEmail($institution, $registrationUrl)) {
            $this->Flash->success(__('Registration email resent successfully.'));
        } else {
            $this->Flash->error(__('Failed to send email. Please check email configuration.'));
        }
        
        return $this->redirect(['action' => 'index']);
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
}
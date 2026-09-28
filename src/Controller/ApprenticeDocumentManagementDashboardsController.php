<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeDocumentManagementDashboards Controller
 *
 * @property \App\Model\Table\ApprenticeDocumentManagementDashboardsTable $ApprenticeDocumentManagementDashboards
 *
 * @method \App\Model\Entity\ApprenticeDocumentManagementDashboard[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeDocumentManagementDashboardsController extends AppController
{
    use \App\Controller\ExportTrait;

    /**
     * Who may recount the figures.
     *
     * The documents themselves are tmm-documentation's work, so bringing the
     * summary in line with them is too.
     *
     * @var array
     */
    const RECOUNT_ROLES = ['administrator', 'tmm-documentation'];

    /**
     * Let those roles reach an action the menu permissions cannot know about.
     *
     * AppController::isAuthorized() reads role_menus.granted_actions, and "*"
     * there does not mean every action: it expands to the menu's own action
     * plus index and view. A brand-new action is in nobody's granted list, so
     * without this the button would refuse the roles it was built for, and the
     * refusal would look like a permissions bug rather than a missing row.
     *
     * @param array|null $user The authenticated user.
     * @return bool
     */
    public function isAuthorized($user = null)
    {
        if ($this->request->getParam('action') !== 'refresh') {
            return parent::isAuthorized($user);
        }

        foreach (self::RECOUNT_ROLES as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return parent::isAuthorized($user);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Candidates'],
        ];
        $apprenticeDocumentManagementDashboards = $this->paginate($this->ApprenticeDocumentManagementDashboards);

        // The four totals on each row were typed in once and never recomputed.
        // Counting the register here puts the real figure beside the stored one
        // so a row that has drifted says so, instead of reading as a count.
        $ids = [];
        foreach ($apprenticeDocumentManagementDashboards as $row) {
            $ids[] = (int)$row->candidate_id;
        }
        $standing = $this->ApprenticeDocumentManagementDashboards->standingFor($ids);

        $drifted = 0;
        foreach ($apprenticeDocumentManagementDashboards as $row) {
            $id = (int)$row->candidate_id;
            if (isset($standing[$id])
                && $this->ApprenticeDocumentManagementDashboards->disagreements($row, $standing[$id])) {
                $drifted++;
            }
        }

        // Load dropdown data for filters
        $candidates = $this->ApprenticeDocumentManagementDashboards->Candidates->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeDocumentManagementDashboards', 'candidates', 'standing', 'drifted'));
    }

    /**
     * Write the counted figures onto the rows on this page.
     *
     * A person can still type whatever they like into the form; this is for
     * when they would rather the row said what the register says.
     *
     * @return \Cake\Http\Response
     */
    public function refresh()
    {
        $this->request->allowMethod(['post']);
        $table = $this->ApprenticeDocumentManagementDashboards;

        $one = (int)$this->request->getData('candidate_id');
        $ids = $one ? [$one] : array_map('intval', array_filter(
            $table->find()->select(['candidate_id'])->distinct(['candidate_id'])
                ->enableHydration(false)->extract('candidate_id')->toList()));

        $changed = 0;
        foreach ($ids as $id) {
            if ($table->refresh($id) !== null) {
                $changed++;
            }
        }

        if ($changed) {
            $this->Flash->success(__('{0} row(s) now say what the register says.', $changed));
        } else {
            $this->Flash->error(__('Nothing could be recounted. The register or the master list could not be read.'));
        }

        return $this->redirect(['action' => 'index']);
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Document Management Dashboard id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeDocumentManagementDashboard = $this->ApprenticeDocumentManagementDashboards->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeDocumentManagementDashboard', $apprenticeDocumentManagementDashboard);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeDocumentManagementDashboard = $this->ApprenticeDocumentManagementDashboards->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeDocumentManagementDashboards', $fieldName, 'apprenticedocumentmanagementdashboards');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeDocumentManagementDashboards', $fieldName, 'apprenticedocumentmanagementdashboards');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeDocumentManagementDashboard = $this->ApprenticeDocumentManagementDashboards->patchEntity($apprenticeDocumentManagementDashboard, $data);
            if ($this->ApprenticeDocumentManagementDashboards->save($apprenticeDocumentManagementDashboard)) {
                $this->Flash->success(__('The apprentice document management dashboard has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice document management dashboard could not be saved. Please, try again.'));
        }
        $candidates = $this->ApprenticeDocumentManagementDashboards->Candidates->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeDocumentManagementDashboard', 'candidates'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Document Management Dashboard id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeDocumentManagementDashboard = $this->ApprenticeDocumentManagementDashboards->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeDocumentManagementDashboards', $fieldName, 'apprenticedocumentmanagementdashboards');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeDocumentManagementDashboards', $fieldName, 'apprenticedocumentmanagementdashboards');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeDocumentManagementDashboard = $this->ApprenticeDocumentManagementDashboards->patchEntity($apprenticeDocumentManagementDashboard, $data);
            if ($this->ApprenticeDocumentManagementDashboards->save($apprenticeDocumentManagementDashboard)) {
                $this->Flash->success(__('The apprentice document management dashboard has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice document management dashboard could not be saved. Please, try again.'));
        }
        $candidates = $this->ApprenticeDocumentManagementDashboards->Candidates->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeDocumentManagementDashboard', 'candidates'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Document Management Dashboard id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeDocumentManagementDashboard = $this->ApprenticeDocumentManagementDashboards->get($id);
        if ($this->ApprenticeDocumentManagementDashboards->delete($apprenticeDocumentManagementDashboard)) {
            $this->Flash->success(__('The apprentice document management dashboard has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice document management dashboard could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeDocumentManagementDashboards->find('all')
            ->contain(['Candidates']);
        
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
        
        return $this->doExportCsv($query, 'ApprenticeDocumentManagementDashboards', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeDocumentManagementDashboards->find('all')
            ->contain(['Candidates']);
        
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
        
        return $this->doExportExcel($query, 'ApprenticeDocumentManagementDashboards', $headers, $fields);
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
        
        $query = $this->ApprenticeDocumentManagementDashboards->find('all')
            ->contain(['Candidates']);
        
        // Define report configuration
        $title = 'ApprenticeDocumentManagementDashboards Report';
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
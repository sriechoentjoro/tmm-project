<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AcceptanceOrganizationStories Controller
 *
 * @property \App\Model\Table\AcceptanceOrganizationStoriesTable $AcceptanceOrganizationStories
 *
 * @method \App\Model\Entity\AcceptanceOrganizationStory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AcceptanceOrganizationStoriesController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // Optional filters
        $filterOrg = (int)$this->request->getQuery('organization_id');
        $filterClass = trim((string)$this->request->getQuery('classification'));
        $search = trim((string)$this->request->getQuery('q'));

        $query = $this->AcceptanceOrganizationStories->find()->contain(['AcceptanceOrganizations']);
        if ($filterOrg) {
            $query->where(['AcceptanceOrganizationStories.acceptance_organization_id' => $filterOrg]);
        }
        if ($filterClass !== '') {
            $query->where(['AcceptanceOrganizationStories.problem_classification' => $filterClass]);
        }
        if ($search !== '') {
            $query->where(['OR' => [
                'AcceptanceOrganizationStories.problem_contents LIKE' => '%' . $search . '%',
                'AcceptanceOrganizationStories.problem_solution LIKE' => '%' . $search . '%',
                'AcceptanceOrganizationStories.problem_inference LIKE' => '%' . $search . '%',
            ]]);
        }

        $this->paginate = ['order' => ['AcceptanceOrganizationStories.date_occurrence' => 'DESC']];
        $acceptanceOrganizationStories = $this->paginate($query);

        $acceptanceorganizations = $this->AcceptanceOrganizationStories->AcceptanceOrganizations->find('list')->limit(200)->toArray();

        // Summary + classification / recency stats
        $summary = ['total' => 0, 'resolved' => 0, 'orgs' => 0, 'recent' => 0];
        $classifications = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_stakeholders');
            $summary['total'] = (int)$conn->execute('SELECT COUNT(*) FROM acceptance_organization_stories')->fetch()[0];
            $summary['resolved'] = (int)$conn->execute(
                "SELECT COUNT(*) FROM acceptance_organization_stories WHERE problem_solution IS NOT NULL AND problem_solution <> ''"
            )->fetch()[0];
            $summary['orgs'] = (int)$conn->execute(
                'SELECT COUNT(DISTINCT acceptance_organization_id) FROM acceptance_organization_stories'
            )->fetch()[0];
            $summary['recent'] = (int)$conn->execute(
                'SELECT COUNT(*) FROM acceptance_organization_stories WHERE date_occurrence >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)'
            )->fetch()[0];
            foreach ($conn->execute(
                "SELECT problem_classification AS c, COUNT(*) AS total
                 FROM acceptance_organization_stories
                 WHERE problem_classification IS NOT NULL AND problem_classification <> ''
                 GROUP BY problem_classification ORDER BY total DESC"
            )->fetchAll('assoc') as $row) {
                $classifications[$row['c']] = (int)$row['total'];
            }
        } catch (\Exception $e) {
        }

        $this->set(compact('acceptanceOrganizationStories', 'acceptanceorganizations',
            'summary', 'classifications', 'filterOrg', 'filterClass', 'search'));
        $this->set('acceptance_organizations', $acceptanceorganizations);
    }



    /**
     * View method
     *
     * @param string|null $id Acceptance Organization Story id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'AcceptanceOrganizations';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $acceptanceOrganizationStory = $this->AcceptanceOrganizationStories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('acceptanceOrganizationStory', $acceptanceOrganizationStory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $acceptanceOrganizationStory = $this->AcceptanceOrganizationStories->newEntity();
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
                $imagePath = $this->uploadImage('AcceptanceOrganizationStories', $fieldName, 'acceptanceorganizationstories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('AcceptanceOrganizationStories', $fieldName, 'acceptanceorganizationstories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $acceptanceOrganizationStory = $this->AcceptanceOrganizationStories->patchEntity($acceptanceOrganizationStory, $data);
            if ($this->AcceptanceOrganizationStories->save($acceptanceOrganizationStory)) {
                $this->Flash->success(__('The acceptance organization story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The acceptance organization story could not be saved. Please, try again.'));
        }
        $acceptanceOrganizations = $this->AcceptanceOrganizationStories->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('acceptanceOrganizationStory', 'acceptanceOrganizations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Acceptance Organization Story id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $acceptanceOrganizationStory = $this->AcceptanceOrganizationStories->get($id, [
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
                $imagePath = $this->uploadImage('AcceptanceOrganizationStories', $fieldName, 'acceptanceorganizationstories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('AcceptanceOrganizationStories', $fieldName, 'acceptanceorganizationstories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $acceptanceOrganizationStory = $this->AcceptanceOrganizationStories->patchEntity($acceptanceOrganizationStory, $data);
            if ($this->AcceptanceOrganizationStories->save($acceptanceOrganizationStory)) {
                $this->Flash->success(__('The acceptance organization story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The acceptance organization story could not be saved. Please, try again.'));
        }
        $acceptanceOrganizations = $this->AcceptanceOrganizationStories->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $this->set(compact('acceptanceOrganizationStory', 'acceptanceOrganizations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Acceptance Organization Story id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $acceptanceOrganizationStory = $this->AcceptanceOrganizationStories->get($id);
        if ($this->AcceptanceOrganizationStories->delete($acceptanceOrganizationStory)) {
            $this->Flash->success(__('The acceptance organization story has been deleted.'));
        } else {
            $this->Flash->error(__('The acceptance organization story could not be deleted. Please, try again.'));
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
        $query = $this->AcceptanceOrganizationStories->find('all')
            ->contain(['AcceptanceOrganizations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'AcceptanceOrganizationStories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->AcceptanceOrganizationStories->find('all')
            ->contain(['AcceptanceOrganizations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'AcceptanceOrganizationStories', $headers, $fields);
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
        
        $query = $this->AcceptanceOrganizationStories->find('all')
            ->contain(['AcceptanceOrganizations']);
        
        // Define report configuration
        $title = 'AcceptanceOrganizationStories Report';
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
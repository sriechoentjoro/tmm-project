<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateDocumentManagementDashboardDetails Controller
 *
 * @property \App\Model\Table\CandidateDocumentManagementDashboardDetailsTable $CandidateDocumentManagementDashboardDetails
 *
 * @method \App\Model\Entity\CandidateDocumentManagementDashboardDetail[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateDocumentManagementDashboardDetailsController extends AppController
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
            'contain' => ['CandidateDocumentManagementDashboards'],
        ];
        $candidateDocumentManagementDashboardDetails = $this->paginate($this->CandidateDocumentManagementDashboardDetails);

        // Load dropdown data for filters
        $dashboards = $this->CandidateDocumentManagementDashboardDetails->Dashboards->find('list')->limit(200)->toArray();
        $this->set(compact('candidateDocumentManagementDashboardDetails', 'CandidateDocumentManagementDashboards'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Document Management Dashboard Detail id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'CandidateDocumentManagementDashboards';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateDocumentManagementDashboardDetail = $this->CandidateDocumentManagementDashboardDetails->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateDocumentManagementDashboardDetail', $candidateDocumentManagementDashboardDetail);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateDocumentManagementDashboardDetail = $this->CandidateDocumentManagementDashboardDetails->newEntity();
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
                $imagePath = $this->uploadImage('CandidateDocumentManagementDashboardDetails', $fieldName, 'candidatedocumentmanagementdashboarddetails');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateDocumentManagementDashboardDetails', $fieldName, 'candidatedocumentmanagementdashboarddetails');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateDocumentManagementDashboardDetail = $this->CandidateDocumentManagementDashboardDetails->patchEntity($candidateDocumentManagementDashboardDetail, $data);
            if ($this->CandidateDocumentManagementDashboardDetails->save($candidateDocumentManagementDashboardDetail)) {
                $this->Flash->success(__('The candidate document management dashboard detail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate document management dashboard detail could not be saved. Please, try again.'));
        }
        $dashboards = $this->CandidateDocumentManagementDashboardDetails->Dashboards->find('list', ['limit' => 200]);
        $this->set(compact('candidateDocumentManagementDashboardDetail', 'CandidateDocumentManagementDashboards'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Document Management Dashboard Detail id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateDocumentManagementDashboardDetail = $this->CandidateDocumentManagementDashboardDetails->get($id, [
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
                $imagePath = $this->uploadImage('CandidateDocumentManagementDashboardDetails', $fieldName, 'candidatedocumentmanagementdashboarddetails');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateDocumentManagementDashboardDetails', $fieldName, 'candidatedocumentmanagementdashboarddetails');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateDocumentManagementDashboardDetail = $this->CandidateDocumentManagementDashboardDetails->patchEntity($candidateDocumentManagementDashboardDetail, $data);
            if ($this->CandidateDocumentManagementDashboardDetails->save($candidateDocumentManagementDashboardDetail)) {
                $this->Flash->success(__('The candidate document management dashboard detail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate document management dashboard detail could not be saved. Please, try again.'));
        }
        $dashboards = $this->CandidateDocumentManagementDashboardDetails->Dashboards->find('list', ['limit' => 200]);
        $this->set(compact('candidateDocumentManagementDashboardDetail', 'CandidateDocumentManagementDashboards'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Document Management Dashboard Detail id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateDocumentManagementDashboardDetail = $this->CandidateDocumentManagementDashboardDetails->get($id);
        if ($this->CandidateDocumentManagementDashboardDetails->delete($candidateDocumentManagementDashboardDetail)) {
            $this->Flash->success(__('The candidate document management dashboard detail has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate document management dashboard detail could not be deleted. Please, try again.'));
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
        $query = $this->CandidateDocumentManagementDashboardDetails->find('all')
            ->contain(['CandidateDocumentManagementDashboards']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateDocumentManagementDashboardDetails', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateDocumentManagementDashboardDetails->find('all')
            ->contain(['CandidateDocumentManagementDashboards']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateDocumentManagementDashboardDetails', $headers, $fields);
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
        
        $query = $this->CandidateDocumentManagementDashboardDetails->find('all')
            ->contain(['CandidateDocumentManagementDashboards']);
        
        // Define report configuration
        $title = 'CandidateDocumentManagementDashboardDetails Report';
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
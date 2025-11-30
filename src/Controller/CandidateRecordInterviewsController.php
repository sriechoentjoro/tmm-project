<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateRecordInterviews Controller
 *
 * @property \App\Model\Table\CandidateRecordInterviewsTable $CandidateRecordInterviews
 *
 * @method \App\Model\Entity\CandidateRecordInterview[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateRecordInterviewsController extends AppController
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
            'contain' => ['Candidates', 'MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults'],
        ];
        $candidateRecordInterviews = $this->paginate($this->CandidateRecordInterviews);

        // Load dropdown data for filters
        $applicants = $this->CandidateRecordInterviews->Applicants->find('list')->limit(200)->toArray();
        $mastercandidateinterviewtypes = $this->CandidateRecordInterviews->MasterCandidateInterviewTypes->find('list')->limit(200)->toArray();
        $mastercandidateinterviewresults = $this->CandidateRecordInterviews->MasterCandidateInterviewResults->find('list')->limit(200)->toArray();
        $this->set(compact('candidateRecordInterviews', 'Candidates', 'mastercandidateinterviewtypes', 'mastercandidateinterviewresults'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Record Interview id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'MasterCandidateInterviewTypes';
        $contain[] = 'MasterCandidateInterviewResults';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateRecordInterview = $this->CandidateRecordInterviews->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateRecordInterview', $candidateRecordInterview);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateRecordInterview = $this->CandidateRecordInterviews->newEntity();
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
                $imagePath = $this->uploadImage('CandidateRecordInterviews', $fieldName, 'candidaterecordinterviews');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateRecordInterviews', $fieldName, 'candidaterecordinterviews');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateRecordInterview = $this->CandidateRecordInterviews->patchEntity($candidateRecordInterview, $data);
            if ($this->CandidateRecordInterviews->save($candidateRecordInterview)) {
                $this->Flash->success(__('The candidate record interview has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate record interview could not be saved. Please, try again.'));
        }
        $applicants = $this->CandidateRecordInterviews->Applicants->find('list', ['limit' => 200]);
        $masterCandidateInterviewTypes = $this->CandidateRecordInterviews->MasterCandidateInterviewTypes->find('list', ['limit' => 200]);
        $masterCandidateInterviewResults = $this->CandidateRecordInterviews->MasterCandidateInterviewResults->find('list', ['limit' => 200]);
        $this->set(compact('candidateRecordInterview', 'Candidates', 'masterCandidateInterviewTypes', 'masterCandidateInterviewResults'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Record Interview id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateRecordInterview = $this->CandidateRecordInterviews->get($id, [
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
                $imagePath = $this->uploadImage('CandidateRecordInterviews', $fieldName, 'candidaterecordinterviews');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateRecordInterviews', $fieldName, 'candidaterecordinterviews');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateRecordInterview = $this->CandidateRecordInterviews->patchEntity($candidateRecordInterview, $data);
            if ($this->CandidateRecordInterviews->save($candidateRecordInterview)) {
                $this->Flash->success(__('The candidate record interview has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate record interview could not be saved. Please, try again.'));
        }
        $applicants = $this->CandidateRecordInterviews->Applicants->find('list', ['limit' => 200]);
        $masterCandidateInterviewTypes = $this->CandidateRecordInterviews->MasterCandidateInterviewTypes->find('list', ['limit' => 200]);
        $masterCandidateInterviewResults = $this->CandidateRecordInterviews->MasterCandidateInterviewResults->find('list', ['limit' => 200]);
        $this->set(compact('candidateRecordInterview', 'Candidates', 'masterCandidateInterviewTypes', 'masterCandidateInterviewResults'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Record Interview id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateRecordInterview = $this->CandidateRecordInterviews->get($id);
        if ($this->CandidateRecordInterviews->delete($candidateRecordInterview)) {
            $this->Flash->success(__('The candidate record interview has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate record interview could not be deleted. Please, try again.'));
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
        $query = $this->CandidateRecordInterviews->find('all')
            ->contain(['Candidates', 'MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateRecordInterviews', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateRecordInterviews->find('all')
            ->contain(['Candidates', 'MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateRecordInterviews', $headers, $fields);
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
        
        $query = $this->CandidateRecordInterviews->find('all')
            ->contain(['Candidates', 'MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults']);
        
        // Define report configuration
        $title = 'CandidateRecordInterviews Report';
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
}


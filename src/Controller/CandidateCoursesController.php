<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateCourses Controller
 *
 * @property \App\Model\Table\CandidateCoursesTable $CandidateCourses
 *
 * @method \App\Model\Entity\CandidateCourse[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateCoursesController extends AppController
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
            'contain' => ['Candidates', 'VocationalTrainingInstitutions'],
        ];
        $candidateCourses = $this->paginate($this->CandidateCourses);

        // Load dropdown data for filters
        $candidates = $this->CandidateCourses->Candidates->find('list')->limit(200)->toArray();
        $vocationaltraininginstitutions = $this->CandidateCourses->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();
        $this->set(compact('candidateCourses', 'candidates', 'vocationaltraininginstitutions'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Course id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'VocationalTrainingInstitutions';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateCourse = $this->CandidateCourses->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateCourse', $candidateCourse);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateCourse = $this->CandidateCourses->newEntity();
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
                $imagePath = $this->uploadImage('CandidateCourses', $fieldName, 'candidatecourses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateCourses', $fieldName, 'candidatecourses');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateCourse = $this->CandidateCourses->patchEntity($candidateCourse, $data);
            if ($this->CandidateCourses->save($candidateCourse)) {
                $this->Flash->success(__('The candidate course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate course could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateCourses->Candidates->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->CandidateCourses->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $this->set(compact('candidateCourse', 'candidates', 'vocationalTrainingInstitutions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Course id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateCourse = $this->CandidateCourses->get($id, [
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
                $imagePath = $this->uploadImage('CandidateCourses', $fieldName, 'candidatecourses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateCourses', $fieldName, 'candidatecourses');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateCourse = $this->CandidateCourses->patchEntity($candidateCourse, $data);
            if ($this->CandidateCourses->save($candidateCourse)) {
                $this->Flash->success(__('The candidate course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate course could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateCourses->Candidates->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->CandidateCourses->VocationalTrainingInstitutions->find('list', ['limit' => 200]);
        $this->set(compact('candidateCourse', 'candidates', 'vocationalTrainingInstitutions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Course id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateCourse = $this->CandidateCourses->get($id);
        if ($this->CandidateCourses->delete($candidateCourse)) {
            $this->Flash->success(__('The candidate course has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate course could not be deleted. Please, try again.'));
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
        $query = $this->CandidateCourses->find('all')
            ->contain(['Candidates', 'VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateCourses', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateCourses->find('all')
            ->contain(['Candidates', 'VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateCourses', $headers, $fields);
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
        
        $query = $this->CandidateCourses->find('all')
            ->contain(['Candidates', 'VocationalTrainingInstitutions']);
        
        // Define report configuration
        $title = 'CandidateCourses Report';
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
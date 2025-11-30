<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateExperiences Controller
 *
 * @property \App\Model\Table\CandidateExperiencesTable $CandidateExperiences
 *
 * @method \App\Model\Entity\CandidateExperience[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateExperiencesController extends AppController
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
            'contain' => ['Candidates', 'MasterEmployeeStatuses'],
        ];
        $candidateExperiences = $this->paginate($this->CandidateExperiences);

        // Load dropdown data for filters
        $candidates = $this->CandidateExperiences->Candidates->find('list')->limit(200)->toArray();
        $masteremployeestatuses = $this->CandidateExperiences->MasterEmployeeStatuses->find('list')->limit(200)->toArray();
        $this->set(compact('candidateExperiences', 'candidates', 'masteremployeestatuses'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Experience id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'MasterEmployeeStatuses';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateExperience = $this->CandidateExperiences->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateExperience', $candidateExperience);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateExperience = $this->CandidateExperiences->newEntity();
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
                $imagePath = $this->uploadImage('CandidateExperiences', $fieldName, 'candidateexperiences');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateExperiences', $fieldName, 'candidateexperiences');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateExperience = $this->CandidateExperiences->patchEntity($candidateExperience, $data);
            if ($this->CandidateExperiences->save($candidateExperience)) {
                $this->Flash->success(__('The candidate experience has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate experience could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateExperiences->Candidates->find('list', ['limit' => 200]);
        $masterEmployeeStatuses = $this->CandidateExperiences->MasterEmployeeStatuses->find('list', ['limit' => 200]);
        $this->set(compact('candidateExperience', 'candidates', 'masterEmployeeStatuses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Experience id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateExperience = $this->CandidateExperiences->get($id, [
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
                $imagePath = $this->uploadImage('CandidateExperiences', $fieldName, 'candidateexperiences');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateExperiences', $fieldName, 'candidateexperiences');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateExperience = $this->CandidateExperiences->patchEntity($candidateExperience, $data);
            if ($this->CandidateExperiences->save($candidateExperience)) {
                $this->Flash->success(__('The candidate experience has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate experience could not be saved. Please, try again.'));
        }
        $candidates = $this->CandidateExperiences->Candidates->find('list', ['limit' => 200]);
        $masterEmployeeStatuses = $this->CandidateExperiences->MasterEmployeeStatuses->find('list', ['limit' => 200]);
        $this->set(compact('candidateExperience', 'candidates', 'masterEmployeeStatuses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Experience id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateExperience = $this->CandidateExperiences->get($id);
        if ($this->CandidateExperiences->delete($candidateExperience)) {
            $this->Flash->success(__('The candidate experience has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate experience could not be deleted. Please, try again.'));
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
        $query = $this->CandidateExperiences->find('all')
            ->contain(['Candidates', 'MasterEmployeeStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateExperiences', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateExperiences->find('all')
            ->contain(['Candidates', 'MasterEmployeeStatuses']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateExperiences', $headers, $fields);
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
        
        $query = $this->CandidateExperiences->find('all')
            ->contain(['Candidates', 'MasterEmployeeStatuses']);
        
        // Define report configuration
        $title = 'CandidateExperiences Report';
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


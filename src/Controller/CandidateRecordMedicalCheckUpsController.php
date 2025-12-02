<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CandidateRecordMedicalCheckUps Controller
 *
 * @property \App\Model\Table\CandidateRecordMedicalCheckUpsTable $CandidateRecordMedicalCheckUps
 *
 * @method \App\Model\Entity\CandidateRecordMedicalCheckUp[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CandidateRecordMedicalCheckUpsController extends AppController
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
            'contain' => ['Candidates', 'MasterMedicalCheckUpResults'],
        ];
        $candidateRecordMedicalCheckUps = $this->paginate($this->CandidateRecordMedicalCheckUps);

        // Load dropdown data for filters
        $applicants = $this->CandidateRecordMedicalCheckUps->Applicants->find('list')->limit(200)->toArray();
        $medicalcheckupresults = $this->CandidateRecordMedicalCheckUps->MedicalCheckUpResults->find('list')->limit(200)->toArray();
        $this->set(compact('candidateRecordMedicalCheckUps', 'Candidates', 'MasterMedicalCheckUpResults'));
    }



    /**
     * View method
     *
     * @param string|null $id Candidate Record Medical Check Up id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Candidates';
        $contain[] = 'MasterMedicalCheckUpResults';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $candidateRecordMedicalCheckUp = $this->CandidateRecordMedicalCheckUps->get($id, [
            'contain' => $contain,
        ]);

        $this->set('candidateRecordMedicalCheckUp', $candidateRecordMedicalCheckUp);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $candidateRecordMedicalCheckUp = $this->CandidateRecordMedicalCheckUps->newEntity();
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
                $imagePath = $this->uploadImage('CandidateRecordMedicalCheckUps', $fieldName, 'candidaterecordmedicalcheckups');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CandidateRecordMedicalCheckUps', $fieldName, 'candidaterecordmedicalcheckups');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $candidateRecordMedicalCheckUp = $this->CandidateRecordMedicalCheckUps->patchEntity($candidateRecordMedicalCheckUp, $data);
            if ($this->CandidateRecordMedicalCheckUps->save($candidateRecordMedicalCheckUp)) {
                $this->Flash->success(__('The candidate record medical check up has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate record medical check up could not be saved. Please, try again.'));
        }
        $applicants = $this->CandidateRecordMedicalCheckUps->Applicants->find('list', ['limit' => 200]);
        $medicalCheckUpResults = $this->CandidateRecordMedicalCheckUps->MedicalCheckUpResults->find('list', ['limit' => 200]);
        $this->set(compact('candidateRecordMedicalCheckUp', 'Candidates', 'MasterMedicalCheckUpResults'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Candidate Record Medical Check Up id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $candidateRecordMedicalCheckUp = $this->CandidateRecordMedicalCheckUps->get($id, [
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
                $imagePath = $this->uploadImage('CandidateRecordMedicalCheckUps', $fieldName, 'candidaterecordmedicalcheckups');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CandidateRecordMedicalCheckUps', $fieldName, 'candidaterecordmedicalcheckups');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $candidateRecordMedicalCheckUp = $this->CandidateRecordMedicalCheckUps->patchEntity($candidateRecordMedicalCheckUp, $data);
            if ($this->CandidateRecordMedicalCheckUps->save($candidateRecordMedicalCheckUp)) {
                $this->Flash->success(__('The candidate record medical check up has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The candidate record medical check up could not be saved. Please, try again.'));
        }
        $applicants = $this->CandidateRecordMedicalCheckUps->Applicants->find('list', ['limit' => 200]);
        $medicalCheckUpResults = $this->CandidateRecordMedicalCheckUps->MedicalCheckUpResults->find('list', ['limit' => 200]);
        $this->set(compact('candidateRecordMedicalCheckUp', 'Candidates', 'MasterMedicalCheckUpResults'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Candidate Record Medical Check Up id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $candidateRecordMedicalCheckUp = $this->CandidateRecordMedicalCheckUps->get($id);
        if ($this->CandidateRecordMedicalCheckUps->delete($candidateRecordMedicalCheckUp)) {
            $this->Flash->success(__('The candidate record medical check up has been deleted.'));
        } else {
            $this->Flash->error(__('The candidate record medical check up could not be deleted. Please, try again.'));
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
        $query = $this->CandidateRecordMedicalCheckUps->find('all')
            ->contain(['Candidates', 'MasterMedicalCheckUpResults']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CandidateRecordMedicalCheckUps', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CandidateRecordMedicalCheckUps->find('all')
            ->contain(['Candidates', 'MasterMedicalCheckUpResults']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CandidateRecordMedicalCheckUps', $headers, $fields);
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
        
        $query = $this->CandidateRecordMedicalCheckUps->find('all')
            ->contain(['Candidates', 'MasterMedicalCheckUpResults']);
        
        // Define report configuration
        $title = 'CandidateRecordMedicalCheckUps Report';
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
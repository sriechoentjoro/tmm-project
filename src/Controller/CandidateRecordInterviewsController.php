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
    use \App\Controller\LpkDataFilterTrait;

    /**
     * Record AO interview scoring for a candidate (LPK role entry point)
     */
    public function aoScore($candidateId = null)
    {
        $candidatesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Candidates');
        $candidate = $candidatesTable->get($candidateId, ['contain' => ['VocationalTrainingInstitutions', 'AcceptanceOrganizations']]);

        // LPK access guard
        if ($this->hasRole('lpk-penyangga')) {
            $myLpkId = $this->getUserInstitutionId();
            if ($candidate->vocational_training_institution_id != $myLpkId) {
                $this->Flash->error(__('Access denied: this candidate does not belong to your institution.'));
                return $this->redirect(['action' => 'index']);
            }
        }

        // tmm-recruitment is view-only
        $viewOnly = $this->hasRole('tmm-recruitment');
        if ($viewOnly && $this->request->is(['post', 'put'])) {
            $this->Flash->error(__('Your role does not have permission to change interview scores.'));
            return $this->redirect(['action' => 'aoScore', $candidateId]);
        }

        // Most recent interview record for this candidate (type 3 or 4 = with AO)
        $existingInterview = $this->CandidateRecordInterviews->find()
            ->where(['applicant_id' => $candidateId])
            ->order(['id' => 'DESC'])
            ->first();

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            // Compute weighted overall score (each dimension 0–20, total 0–100)
            $dims = ['politeness_score', 'communication_score', 'motivation_score', 'adaptability_score', 'technical_score'];
            $sum = 0;
            foreach ($dims as $d) {
                $sum += (int)($data[$d] ?? 0);
            }
            $overall = round($sum, 2);
            $data['overall_score'] = $overall;

            // Auto-fill title and type (type 3 = Offline - TMM / AO Interview)
            if (empty($data['title'])) {
                $data['title'] = 'Interview dengan Acceptance Organization';
            }
            if (empty($data['master_candidate_interview_type_id'])) {
                $data['master_candidate_interview_type_id'] = 4; // Offline - TMM
            }
            $data['applicant_id'] = $candidateId;
            if (empty($data['date_interview'])) {
                $data['date_interview'] = date('Y-m-d');
            }
            if (empty($data['date_interview_result'])) {
                $data['date_interview_result'] = date('Y-m-d');
            }

            // Set result based on recommendation
            $recMap = ['pass' => 1, 'reserved' => 3, 'fail' => 2];
            $rec = $data['recommendation'] ?? 'pass';
            $data['master_candidate_interview_result_id'] = $recMap[$rec] ?? 1;

            $interview = $this->CandidateRecordInterviews->newEntity($data);
            if ($this->CandidateRecordInterviews->save($interview)) {
                // Mirror key fields to candidates table for quick access
                $candidate->interview_politeness  = (int)($data['politeness_score'] ?? 0);
                $candidate->interview_communication= (int)($data['communication_score'] ?? 0);
                $candidate->interview_motivation   = (int)($data['motivation_score'] ?? 0);
                $candidate->interview_adaptability = (int)($data['adaptability_score'] ?? 0);
                $candidate->interview_technical    = (int)($data['technical_score'] ?? 0);
                $candidate->interview_score        = $overall;
                $candidate->interview_notes        = $data['comments'] ?? null;
                $candidate->interview_notes_japanese = $data['interview_notes_japanese'] ?? null;
                $candidate->interview_recommendation = $rec;
                $candidatesTable->save($candidate);

                $this->Flash->success(__('Interview record saved. Overall score: {0}/100', $overall));
                return $this->redirect(['action' => 'aoScore', $candidateId]);
            }
            $this->Flash->error(__('Could not save interview. Please try again.'));
        }

        // Load all interview records for this candidate
        $interviewHistory = $this->CandidateRecordInterviews->find()
            ->where(['applicant_id' => $candidateId])
            ->contain(['MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults'])
            ->order(['id' => 'DESC'])
            ->all();

        $this->set(compact('candidate', 'existingInterview', 'interviewHistory', 'viewOnly'));
    }

    /**
     * Index method (list all interview records)
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
        $applicants = $this->CandidateRecordInterviews->Candidates->find('list')->limit(200)->toArray();
        $master_candidate_interview_types = $this->CandidateRecordInterviews->MasterCandidateInterviewTypes->find('list')->limit(200)->toArray();
        $master_candidate_interview_results = $this->CandidateRecordInterviews->MasterCandidateInterviewResults->find('list')->limit(200)->toArray();
        $this->set(compact('candidateRecordInterviews', 'applicants', 'master_candidate_interview_types', 'master_candidate_interview_results'));
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
        $candidateRecordInterview = $this->CandidateRecordInterviews->get($id, [
            'contain' => ['Candidates', 'MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults'],
        ]);

        // Other interview records for the same candidate (history)
        $otherInterviews = $this->CandidateRecordInterviews->find()
            ->where(['applicant_id' => $candidateRecordInterview->applicant_id, 'id !=' => $candidateRecordInterview->id])
            ->contain(['MasterCandidateInterviewTypes', 'MasterCandidateInterviewResults'])
            ->order(['id' => 'DESC'])
            ->all();

        // tmm-recruitment is view-only on AO interview records; LPK roles can edit/delete
        $viewOnly = $this->hasRole('tmm-recruitment');

        $this->set(compact('candidateRecordInterview', 'otherInterviews', 'viewOnly'));
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
        $applicants = $this->CandidateRecordInterviews->Candidates->find('list', ['limit' => 200]);
        $masterCandidateInterviewTypes = $this->CandidateRecordInterviews->MasterCandidateInterviewTypes->find('list', ['limit' => 200]);
        $masterCandidateInterviewResults = $this->CandidateRecordInterviews->MasterCandidateInterviewResults->find('list', ['limit' => 200]);
        $this->set(compact('candidateRecordInterview', 'applicants', 'masterCandidateInterviewTypes', 'masterCandidateInterviewResults'));
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
        if ($this->hasRole('tmm-recruitment')) {
            $this->Flash->error(__('Your role does not have permission to edit interview records.'));
            return $this->redirect(['action' => 'view', $id]);
        }

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
        $applicants = $this->CandidateRecordInterviews->Candidates->find('list', ['limit' => 200]);
        $masterCandidateInterviewTypes = $this->CandidateRecordInterviews->MasterCandidateInterviewTypes->find('list', ['limit' => 200]);
        $masterCandidateInterviewResults = $this->CandidateRecordInterviews->MasterCandidateInterviewResults->find('list', ['limit' => 200]);
        $this->set(compact('candidateRecordInterview', 'applicants', 'masterCandidateInterviewTypes', 'masterCandidateInterviewResults'));
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

        if ($this->hasRole('tmm-recruitment')) {
            $this->Flash->error(__('Your role does not have permission to delete interview records.'));
            return $this->redirect(['action' => 'view', $id]);
        }

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
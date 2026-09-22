<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MasterMedicalCheckUpResults Controller
 *
 * @property \App\Model\Table\MasterMedicalCheckUpResultsTable $MasterMedicalCheckUpResults
 *
 * @method \App\Model\Entity\MasterMedicalCheckUpResult[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class MasterMedicalCheckUpResultsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $masterMedicalCheckUpResults = $this->paginate($this->MasterMedicalCheckUpResults);

        $this->set(compact('masterMedicalCheckUpResults'));
    }



    /**
     * View method
     *
     * @param string|null $id Master Medical Check Up Result id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        
        // Add HasMany with nested BelongsTo for foreign key display
        $masterMedicalCheckUpResult = $this->MasterMedicalCheckUpResults->get($id, [
            'contain' => $contain,
        ]);

        $this->set('masterMedicalCheckUpResult', $masterMedicalCheckUpResult);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $masterMedicalCheckUpResult = $this->MasterMedicalCheckUpResults->newEntity();
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
                $imagePath = $this->uploadImage('MasterMedicalCheckUpResults', $fieldName, 'mastermedicalcheckupresults');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('MasterMedicalCheckUpResults', $fieldName, 'mastermedicalcheckupresults');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $masterMedicalCheckUpResult = $this->MasterMedicalCheckUpResults->patchEntity($masterMedicalCheckUpResult, $data);
            if ($this->MasterMedicalCheckUpResults->save($masterMedicalCheckUpResult)) {
                $this->Flash->success(__('The master medical check up result has been saved.'));
                $this->_restandAfterMarking($masterMedicalCheckUpResult->id);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master medical check up result could not be saved. Please, try again.'));
        }
        $this->set(compact('masterMedicalCheckUpResult'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Master Medical Check Up Result id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $masterMedicalCheckUpResult = $this->MasterMedicalCheckUpResults->get($id, [
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
                $imagePath = $this->uploadImage('MasterMedicalCheckUpResults', $fieldName, 'mastermedicalcheckupresults');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('MasterMedicalCheckUpResults', $fieldName, 'mastermedicalcheckupresults');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $masterMedicalCheckUpResult = $this->MasterMedicalCheckUpResults->patchEntity($masterMedicalCheckUpResult, $data);
            if ($this->MasterMedicalCheckUpResults->save($masterMedicalCheckUpResult)) {
                $this->Flash->success(__('The master medical check up result has been saved.'));
                $this->_restandAfterMarking($masterMedicalCheckUpResult->id);

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The master medical check up result could not be saved. Please, try again.'));
        }
        $this->set(compact('masterMedicalCheckUpResult'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Master Medical Check Up Result id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $masterMedicalCheckUpResult = $this->MasterMedicalCheckUpResults->get($id);
        // Read before the delete: afterwards the entity is still in memory but
        // the row is gone, and every check-up that named it has to be counted
        // again without it.
        $resultId = $masterMedicalCheckUpResult->id;
        if ($this->MasterMedicalCheckUpResults->delete($masterMedicalCheckUpResult)) {
            $this->Flash->success(__('The master medical check up result has been deleted.'));
            $this->_restandAfterMarking($resultId);
        } else {
            $this->Flash->error(__('The master medical check up result could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Recalculate everybody whose medical standing this result type decides.
     *
     * Marking a result fit or not fit is a statement about every check-up ever
     * recorded against it, not just the next one. Without this, changing the
     * marking here changed nothing for anybody already on file: their stored
     * standing was worked out under the old marking and would sit there until
     * somebody happened to re-save their check-up. A result switched to "not
     * fit" would quietly leave people passing who should now be stopped, which
     * is the failure this column exists to prevent.
     *
     * Both sides are covered because both read this one master list: the
     * candidates, whose standing decides whether they can be put forward for
     * promotion, and the apprentices, whose standing decides whether a
     * departure can be recorded.
     *
     * @param int|null $resultId The result type that was added, edited or deleted.
     * @return void
     */
    protected function _restandAfterMarking($resultId)
    {
        $resultId = (int)$resultId;
        if (!$resultId) {
            return;
        }

        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $moved = ['pass' => 0, 'fail' => 0, 'unknown' => 0];

        foreach ([
            ['CandidateRecordMedicalCheckUps', 'medical_check_up_result_id', 'applicant_id', 'Candidates'],
            ['ApprenticeRecordMedicalCheckUps', 'master_medical_check_up_result_id', 'apprentice_id', 'Apprentices'],
        ] as list($recordAlias, $resultField, $ownerField, $ownerAlias)) {
            try {
                $ids = $locator->get($recordAlias)->find()
                    ->select([$ownerField])
                    ->where([$resultField => $resultId])
                    ->enableHydration(false)
                    ->extract($ownerField)
                    ->toList();
                $owner = $locator->get($ownerAlias);
            } catch (\Throwable $e) {
                // An older installation without one of these tables should not
                // lose the save it just made.
                $this->log('restand after marking failed for ' . $recordAlias . ': ' . $e->getMessage(), 'error');
                continue;
            }

            foreach (array_unique(array_filter($ids)) as $id) {
                try {
                    $standing = $owner->refreshMcuStanding($id);
                } catch (\Throwable $e) {
                    continue;
                }
                $moved[$standing === null ? 'unknown' : $standing]++;
            }
        }

        $total = $moved['pass'] + $moved['fail'] + $moved['unknown'];
        if (!$total) {
            return;
        }

        if ($moved['fail']) {
            $this->Flash->warning(__(
                '{0} record(s) were re-checked against this result. {1} are now marked not medically fit: a candidate cannot be put forward and an apprentice cannot be recorded as departed until that changes.',
                $total,
                $moved['fail']
            ));

            return;
        }

        $this->Flash->success(__('{0} record(s) were re-checked against this result.', $total));
    }

    /**
     * Export to CSV
     *
     * @return \Cake\Http\Response
     */
    public function exportCsv()
    {
        $query = $this->MasterMedicalCheckUpResults->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'MasterMedicalCheckUpResults', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->MasterMedicalCheckUpResults->find('all');
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'MasterMedicalCheckUpResults', $headers, $fields);
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
        
        $query = $this->MasterMedicalCheckUpResults->find('all');
        
        // Define report configuration
        $title = 'MasterMedicalCheckUpResults Report';
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
    }
}
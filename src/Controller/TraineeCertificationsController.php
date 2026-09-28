<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeCertifications Controller
 *
 * @property \App\Model\Table\TraineeCertificationsTable $TraineeCertifications
 *
 * @method \App\Model\Entity\TraineeCertification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeCertificationsController extends AppController
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
            'contain' => ['Trainees'],
        ];
        $traineeCertifications = $this->paginate($this->TraineeCertifications);

        // Load dropdown data for filters
        $trainees = $this->TraineeCertifications->Trainees->find('list')->limit(200)->toArray();
        $this->set(compact('traineeCertifications', 'trainees'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Certification id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeCertification = $this->TraineeCertifications->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeCertification', $traineeCertification);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeCertification = $this->TraineeCertifications->newEntity();
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
                $imagePath = $this->uploadImage('TraineeCertifications', $fieldName, 'traineecertifications');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeCertifications', $fieldName, 'traineecertifications');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeCertification = $this->TraineeCertifications->patchEntity($traineeCertification, $data);
            if ($this->TraineeCertifications->save($traineeCertification)) {
                $this->Flash->success(__('The trainee certification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee certification could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeCertifications->Trainees->find('list', ['limit' => 200]);
        $this->set(compact('traineeCertification', 'trainees'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Certification id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeCertification = $this->TraineeCertifications->get($id, [
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
                $imagePath = $this->uploadImage('TraineeCertifications', $fieldName, 'traineecertifications');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeCertifications', $fieldName, 'traineecertifications');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeCertification = $this->TraineeCertifications->patchEntity($traineeCertification, $data);
            if ($this->TraineeCertifications->save($traineeCertification)) {
                $this->Flash->success(__('The trainee certification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee certification could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeCertifications->Trainees->find('list', ['limit' => 200]);
        $this->set(compact('traineeCertification', 'trainees'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Certification id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeCertification = $this->TraineeCertifications->get($id);
        if ($this->TraineeCertifications->delete($traineeCertification)) {
            $this->Flash->success(__('The trainee certification has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee certification could not be deleted. Please, try again.'));
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
        $query = $this->TraineeCertifications->find('all')
            ->contain(['Trainees']);
        
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
        
        return $this->doExportCsv($query, 'TraineeCertifications', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeCertifications->find('all')
            ->contain(['Trainees']);
        
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
        
        return $this->doExportExcel($query, 'TraineeCertifications', $headers, $fields);
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
        
        $query = $this->TraineeCertifications->find('all')
            ->contain(['Trainees']);
        
        // Define report configuration
        $title = 'TraineeCertifications Report';
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
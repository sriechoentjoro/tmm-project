<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeCourses Controller
 *
 * @property \App\Model\Table\TraineeCoursesTable $TraineeCourses
 *
 * @method \App\Model\Entity\TraineeCourse[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeCoursesController extends AppController
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
            'contain' => ['Trainees', 'VocationalTrainingInstitutions'],
        ];
        $traineeCourses = $this->paginate($this->TraineeCourses);

        // Load dropdown data for filters
        $trainees = $this->TraineeCourses->Trainees->find('list')->limit(200)->toArray();
        $vocationaltraininginstitutions = $this->TraineeCourses->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();        $this->set(compact('traineeCourses', 'trainees', 'vocationaltraininginstitutions'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Course id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'VocationalTrainingInstitutions';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeCourse = $this->TraineeCourses->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeCourse', $traineeCourse);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeCourse = $this->TraineeCourses->newEntity();
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
                $imagePath = $this->uploadImage('TraineeCourses', $fieldName, 'traineecourses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeCourses', $fieldName, 'traineecourses');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $traineeCourse = $this->TraineeCourses->patchEntity($traineeCourse, $data);
            if ($this->TraineeCourses->save($traineeCourse)) {
                $this->Flash->success(__('The trainee course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee course could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeCourses->Trainees->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->TraineeCourses->VocationalTrainingInstitutions->find('list', ['limit' => 200]);        $this->set(compact('traineeCourse', 'trainees', 'vocationalTrainingInstitutions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Course id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeCourse = $this->TraineeCourses->get($id, [
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
                $imagePath = $this->uploadImage('TraineeCourses', $fieldName, 'traineecourses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeCourses', $fieldName, 'traineecourses');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeCourse = $this->TraineeCourses->patchEntity($traineeCourse, $data);
            if ($this->TraineeCourses->save($traineeCourse)) {
                $this->Flash->success(__('The trainee course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee course could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeCourses->Trainees->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->TraineeCourses->VocationalTrainingInstitutions->find('list', ['limit' => 200]);        $this->set(compact('traineeCourse', 'trainees', 'vocationalTrainingInstitutions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Course id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeCourse = $this->TraineeCourses->get($id);
        if ($this->TraineeCourses->delete($traineeCourse)) {
            $this->Flash->success(__('The trainee course has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee course could not be deleted. Please, try again.'));
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
        $query = $this->TraineeCourses->find('all')
            ->contain(['Trainees', 'VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeCourses', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeCourses->find('all')
            ->contain(['Trainees', 'VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeCourses', $headers, $fields);
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
        
        $query = $this->TraineeCourses->find('all')
            ->contain(['Trainees', 'VocationalTrainingInstitutions']);
        
        // Define report configuration
        $title = 'TraineeCourses Report';
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
        $this->viewBuilder()->setLayout('process_flow');
    }
}
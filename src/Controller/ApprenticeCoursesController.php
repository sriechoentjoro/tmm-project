<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeCourses Controller
 *
 * @property \App\Model\Table\ApprenticeCoursesTable $ApprenticeCourses
 *
 * @method \App\Model\Entity\ApprenticeCourse[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeCoursesController extends AppController
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
            'contain' => ['Apprentices', 'VocationalTrainingInstitutions'],
        ];
        $apprenticeCourses = $this->paginate($this->ApprenticeCourses);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeCourses->Apprentices->find('list')->limit(200)->toArray();
        $vocationaltraininginstitutions = $this->ApprenticeCourses->VocationalTrainingInstitutions->find('list')->limit(200)->toArray();        $this->set(compact('apprenticeCourses', 'apprentices', 'vocationaltraininginstitutions'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Course id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        $contain[] = 'VocationalTrainingInstitutions';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeCourse = $this->ApprenticeCourses->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeCourse', $apprenticeCourse);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeCourse = $this->ApprenticeCourses->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeCourses', $fieldName, 'apprenticecourses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeCourses', $fieldName, 'apprenticecourses');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeCourse = $this->ApprenticeCourses->patchEntity($apprenticeCourse, $data);
            if ($this->ApprenticeCourses->save($apprenticeCourse)) {
                $this->Flash->success(__('The apprentice course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice course could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeCourses->Apprentices->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->ApprenticeCourses->VocationalTrainingInstitutions->find('list', ['limit' => 200]);        $this->set(compact('apprenticeCourse', 'apprentices', 'vocationalTrainingInstitutions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Course id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeCourse = $this->ApprenticeCourses->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeCourses', $fieldName, 'apprenticecourses');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeCourses', $fieldName, 'apprenticecourses');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeCourse = $this->ApprenticeCourses->patchEntity($apprenticeCourse, $data);
            if ($this->ApprenticeCourses->save($apprenticeCourse)) {
                $this->Flash->success(__('The apprentice course has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice course could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeCourses->Apprentices->find('list', ['limit' => 200]);
        $vocationalTrainingInstitutions = $this->ApprenticeCourses->VocationalTrainingInstitutions->find('list', ['limit' => 200]);        $this->set(compact('apprenticeCourse', 'apprentices', 'vocationalTrainingInstitutions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Course id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeCourse = $this->ApprenticeCourses->get($id);
        if ($this->ApprenticeCourses->delete($apprenticeCourse)) {
            $this->Flash->success(__('The apprentice course has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice course could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeCourses->find('all')
            ->contain(['Apprentices', 'VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeCourses', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeCourses->find('all')
            ->contain(['Apprentices', 'VocationalTrainingInstitutions']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeCourses', $headers, $fields);
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
        
        $query = $this->ApprenticeCourses->find('all')
            ->contain(['Apprentices', 'VocationalTrainingInstitutions']);
        
        // Define report configuration
        $title = 'ApprenticeCourses Report';
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


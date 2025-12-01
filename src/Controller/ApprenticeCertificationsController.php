<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeCertifications Controller
 *
 * @property \App\Model\Table\ApprenticeCertificationsTable $ApprenticeCertifications
 *
 * @method \App\Model\Entity\ApprenticeCertification[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeCertificationsController extends AppController
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
            'contain' => ['Apprentices'],
        ];
        $apprenticeCertifications = $this->paginate($this->ApprenticeCertifications);

        // Load dropdown data for filters
        $apprentices = $this->ApprenticeCertifications->Apprentices->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeCertifications', 'apprentices'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Certification id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Apprentices';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeCertification = $this->ApprenticeCertifications->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeCertification', $apprenticeCertification);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeCertification = $this->ApprenticeCertifications->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeCertifications', $fieldName, 'apprenticecertifications');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeCertifications', $fieldName, 'apprenticecertifications');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeCertification = $this->ApprenticeCertifications->patchEntity($apprenticeCertification, $data);
            if ($this->ApprenticeCertifications->save($apprenticeCertification)) {
                $this->Flash->success(__('The apprentice certification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice certification could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeCertifications->Apprentices->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeCertification', 'apprentices'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Certification id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeCertification = $this->ApprenticeCertifications->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeCertifications', $fieldName, 'apprenticecertifications');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeCertifications', $fieldName, 'apprenticecertifications');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeCertification = $this->ApprenticeCertifications->patchEntity($apprenticeCertification, $data);
            if ($this->ApprenticeCertifications->save($apprenticeCertification)) {
                $this->Flash->success(__('The apprentice certification has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice certification could not be saved. Please, try again.'));
        }
        $apprentices = $this->ApprenticeCertifications->Apprentices->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeCertification', 'apprentices'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Certification id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeCertification = $this->ApprenticeCertifications->get($id);
        if ($this->ApprenticeCertifications->delete($apprenticeCertification)) {
            $this->Flash->success(__('The apprentice certification has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice certification could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeCertifications->find('all')
            ->contain(['Apprentices']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeCertifications', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeCertifications->find('all')
            ->contain(['Apprentices']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeCertifications', $headers, $fields);
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
        
        $query = $this->ApprenticeCertifications->find('all')
            ->contain(['Apprentices']);
        
        // Define report configuration
        $title = 'ApprenticeCertifications Report';
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
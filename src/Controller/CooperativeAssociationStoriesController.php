<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CooperativeAssociationStories Controller
 *
 * @property \App\Model\Table\CooperativeAssociationStoriesTable $CooperativeAssociationStories
 *
 * @method \App\Model\Entity\CooperativeAssociationStory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CooperativeAssociationStoriesController extends AppController
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
            'contain' => ['CooperativeAssociations'],
        ];
        $cooperativeAssociationStories = $this->paginate($this->CooperativeAssociationStories);

        // Load dropdown data for filters
        $cooperativeassociations = $this->CooperativeAssociationStories->CooperativeAssociations->find('list')->limit(200)->toArray();
        $this->set(compact('cooperativeAssociationStories', 'cooperativeassociations'));
    }



    /**
     * View method
     *
     * @param string|null $id Cooperative Association Story id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'CooperativeAssociations';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $cooperativeAssociationStory = $this->CooperativeAssociationStories->get($id, [
            'contain' => $contain,
        ]);

        $this->set('cooperativeAssociationStory', $cooperativeAssociationStory);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $cooperativeAssociationStory = $this->CooperativeAssociationStories->newEntity();
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
                $imagePath = $this->uploadImage('CooperativeAssociationStories', $fieldName, 'cooperativeassociationstories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('CooperativeAssociationStories', $fieldName, 'cooperativeassociationstories');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $cooperativeAssociationStory = $this->CooperativeAssociationStories->patchEntity($cooperativeAssociationStory, $data);
            if ($this->CooperativeAssociationStories->save($cooperativeAssociationStory)) {
                $this->Flash->success(__('The cooperative association story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cooperative association story could not be saved. Please, try again.'));
        }
        $cooperativeAssociations = $this->CooperativeAssociationStories->CooperativeAssociations->find('list', ['limit' => 200]);
        $this->set(compact('cooperativeAssociationStory', 'cooperativeAssociations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Cooperative Association Story id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $cooperativeAssociationStory = $this->CooperativeAssociationStories->get($id, [
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
                $imagePath = $this->uploadImage('CooperativeAssociationStories', $fieldName, 'cooperativeassociationstories');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('CooperativeAssociationStories', $fieldName, 'cooperativeassociationstories');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $cooperativeAssociationStory = $this->CooperativeAssociationStories->patchEntity($cooperativeAssociationStory, $data);
            if ($this->CooperativeAssociationStories->save($cooperativeAssociationStory)) {
                $this->Flash->success(__('The cooperative association story has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The cooperative association story could not be saved. Please, try again.'));
        }
        $cooperativeAssociations = $this->CooperativeAssociationStories->CooperativeAssociations->find('list', ['limit' => 200]);
        $this->set(compact('cooperativeAssociationStory', 'cooperativeAssociations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Cooperative Association Story id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $cooperativeAssociationStory = $this->CooperativeAssociationStories->get($id);
        if ($this->CooperativeAssociationStories->delete($cooperativeAssociationStory)) {
            $this->Flash->success(__('The cooperative association story has been deleted.'));
        } else {
            $this->Flash->error(__('The cooperative association story could not be deleted. Please, try again.'));
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
        $query = $this->CooperativeAssociationStories->find('all')
            ->contain(['CooperativeAssociations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'CooperativeAssociationStories', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->CooperativeAssociationStories->find('all')
            ->contain(['CooperativeAssociations']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'CooperativeAssociationStories', $headers, $fields);
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
        
        $query = $this->CooperativeAssociationStories->find('all')
            ->contain(['CooperativeAssociations']);
        
        // Define report configuration
        $title = 'CooperativeAssociationStories Report';
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


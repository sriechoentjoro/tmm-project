<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeFlights Controller
 *
 * @property \App\Model\Table\ApprenticeFlightsTable $ApprenticeFlights
 *
 * @method \App\Model\Entity\ApprenticeFlight[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeFlightsController extends AppController
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
            'contain' => ['ApprenticeTickets', 'MasterAirlines', 'DepartureAirports', 'ArrivalAirports'],
        ];
        $apprenticeFlights = $this->paginate($this->ApprenticeFlights);

        // Load dropdown data for filters
        $apprenticetickets = $this->ApprenticeFlights->ApprenticeTickets->find('list')->limit(200)->toArray();
        $masterairlines = $this->ApprenticeFlights->MasterAirlines->find('list')->limit(200)->toArray();
        $departureairports = $this->ApprenticeFlights->DepartureAirports->find('list')->limit(200)->toArray();
        $arrivalairports = $this->ApprenticeFlights->ArrivalAirports->find('list')->limit(200)->toArray();
        $this->set(compact('apprenticeFlights', 'apprenticetickets', 'masterairlines', 'departureairports', 'arrivalairports'));
    }



    /**
     * View method
     *
     * @param string|null $id Apprentice Flight id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'ApprenticeTickets';
        $contain[] = 'MasterAirlines';
        $contain[] = 'DepartureAirports';
        $contain[] = 'ArrivalAirports';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $apprenticeFlight = $this->ApprenticeFlights->get($id, [
            'contain' => $contain,
        ]);

        $this->set('apprenticeFlight', $apprenticeFlight);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeFlight = $this->ApprenticeFlights->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeFlights', $fieldName, 'apprenticeflights');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeFlights', $fieldName, 'apprenticeflights');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeFlight = $this->ApprenticeFlights->patchEntity($apprenticeFlight, $data);
            if ($this->ApprenticeFlights->save($apprenticeFlight)) {
                $this->Flash->success(__('The apprentice flight has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice flight could not be saved. Please, try again.'));
        }
        $apprenticeTickets = $this->ApprenticeFlights->ApprenticeTickets->find('list', ['limit' => 200]);
        $masterAirlines = $this->ApprenticeFlights->MasterAirlines->find('list', ['limit' => 200]);
        $departureAirports = $this->ApprenticeFlights->DepartureAirports->find('list', ['limit' => 200]);
        $arrivalAirports = $this->ApprenticeFlights->ArrivalAirports->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeFlight', 'apprenticeTickets', 'masterAirlines', 'departureAirports', 'arrivalAirports'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Flight id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeFlight = $this->ApprenticeFlights->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeFlights', $fieldName, 'apprenticeflights');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeFlights', $fieldName, 'apprenticeflights');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeFlight = $this->ApprenticeFlights->patchEntity($apprenticeFlight, $data);
            if ($this->ApprenticeFlights->save($apprenticeFlight)) {
                $this->Flash->success(__('The apprentice flight has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice flight could not be saved. Please, try again.'));
        }
        $apprenticeTickets = $this->ApprenticeFlights->ApprenticeTickets->find('list', ['limit' => 200]);
        $masterAirlines = $this->ApprenticeFlights->MasterAirlines->find('list', ['limit' => 200]);
        $departureAirports = $this->ApprenticeFlights->DepartureAirports->find('list', ['limit' => 200]);
        $arrivalAirports = $this->ApprenticeFlights->ArrivalAirports->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeFlight', 'apprenticeTickets', 'masterAirlines', 'departureAirports', 'arrivalAirports'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Flight id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeFlight = $this->ApprenticeFlights->get($id);
        if ($this->ApprenticeFlights->delete($apprenticeFlight)) {
            $this->Flash->success(__('The apprentice flight has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice flight could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeFlights->find('all')
            ->contain(['ApprenticeTickets', 'MasterAirlines', 'DepartureAirports', 'ArrivalAirports']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeFlights', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeFlights->find('all')
            ->contain(['ApprenticeTickets', 'MasterAirlines', 'DepartureAirports', 'ArrivalAirports']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeFlights', $headers, $fields);
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
        
        $query = $this->ApprenticeFlights->find('all')
            ->contain(['ApprenticeTickets', 'MasterAirlines', 'DepartureAirports', 'ArrivalAirports']);
        
        // Define report configuration
        $title = 'ApprenticeFlights Report';
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


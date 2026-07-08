<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeDocuments Controller
 *
 * @property \App\Model\Table\ApprenticeDocumentsTable $ApprenticeDocuments
 */
class ApprenticeDocumentsController extends AppController
{
    public function index()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_apprentice_documents');

        // Optional filters
        $filterApprentice = (int)$this->request->getQuery('apprentice_id');
        $filterDocument = (int)$this->request->getQuery('document_id');
        $filterStatus = (int)$this->request->getQuery('status_id');

        $query = $this->ApprenticeDocuments->find();
        if ($filterApprentice) {
            $query->where(['apprentice_id' => $filterApprentice]);
        }
        if ($filterDocument) {
            $query->where(['apprenticeship_submission_document_id' => $filterDocument]);
        }
        if ($filterStatus) {
            $query->where(['master_document_submission_status_id' => $filterStatus]);
        }

        $this->paginate = ['order' => ['ApprenticeDocuments.id' => 'DESC']];
        $apprenticeDocuments = $this->paginate($query);

        // Lookup maps to display names instead of raw ids
        $apprenticeNames = [];
        try {
            foreach ($locator->get('Apprentices')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $apprentice) {
                $apprenticeNames[$apprentice->id] = $apprentice->name . ($apprentice->tmm_code ? ' (' . $apprentice->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }

        $documentTitles = [];
        try {
            foreach ($conn->execute('SELECT id, title FROM master_apprentice_submission_documents ORDER BY id')->fetchAll('assoc') as $row) {
                $documentTitles[$row['id']] = $row['title'];
            }
        } catch (\Exception $e) {
        }

        $statusNames = [];
        try {
            $statusNames = $locator->get('MasterDocumentSubmissionStatuses')->find('list')->toArray();
        } catch (\Exception $e) {
        }

        $userNames = [];
        try {
            $userNames = $locator->get('Users')->find()->enableHydration(false)
                ->all()->combine('id', 'username')->toArray();
        } catch (\Exception $e) {
        }

        // Summary counts (unfiltered)
        $summaryQuery = $this->ApprenticeDocuments->find();
        $byStatus = $summaryQuery
            ->select([
                'status_id' => 'master_document_submission_status_id',
                'total' => $summaryQuery->func()->count('*'),
            ])
            ->group(['master_document_submission_status_id'])
            ->enableHydration(false)
            ->toArray();
        $summary = ['total' => 0];
        foreach ($byStatus as $row) {
            $name = isset($statusNames[$row['status_id']]) ? $statusNames[$row['status_id']] : 'Unknown';
            $summary[$name] = (int)$row['total'];
            $summary['total'] += (int)$row['total'];
        }

        $this->set(compact('apprenticeDocuments', 'apprenticeNames', 'documentTitles',
            'statusNames', 'userNames', 'summary', 'filterApprentice', 'filterDocument', 'filterStatus'));
    }

    public function view($id = null)
    {
        $apprenticeDocument = $this->ApprenticeDocuments->get($id);
        $this->set('apprenticeDocument', $apprenticeDocument);
    }

    public function add()
    {
        $apprenticeDocument = $this->ApprenticeDocuments->newEntity();
        if ($this->request->is('post')) {
            $apprenticeDocument = $this->ApprenticeDocuments->patchEntity($apprenticeDocument, $this->request->getData());
            if ($this->ApprenticeDocuments->save($apprenticeDocument)) {
                $this->Flash->success(__('The Apprentice Document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Apprentice Document could not be saved. Please, try again.'));
        }
        $this->set('apprenticeDocument', $apprenticeDocument);
    }

    public function edit($id = null)
    {
        $apprenticeDocument = $this->ApprenticeDocuments->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $apprenticeDocument = $this->ApprenticeDocuments->patchEntity($apprenticeDocument, $this->request->getData());
            if ($this->ApprenticeDocuments->save($apprenticeDocument)) {
                $this->Flash->success(__('The Apprentice Document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Apprentice Document could not be saved. Please, try again.'));
        }
        $this->set('apprenticeDocument', $apprenticeDocument);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeDocument = $this->ApprenticeDocuments->get($id);
        if ($this->ApprenticeDocuments->delete($apprenticeDocument)) {
            $this->Flash->success(__('The Apprentice Document has been deleted.'));
        } else {
            $this->Flash->error(__('The Apprentice Document could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

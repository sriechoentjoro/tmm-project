<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeSubmissionDocuments Controller
 *
 * @property \App\Model\Table\TraineeSubmissionDocumentsTable $TraineeSubmissionDocuments
 */
class TraineeSubmissionDocumentsController extends AppController
{
    public function index()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_documents');

        // Optional filters
        $filterTrainee = (int)$this->request->getQuery('trainee_id');
        $filterDocument = (int)$this->request->getQuery('document_id');
        $filterStatus = (int)$this->request->getQuery('status_id');

        $query = $this->TraineeSubmissionDocuments->find();
        if ($filterTrainee) {
            $query->where(['trainee_id' => $filterTrainee]);
        }
        if ($filterDocument) {
            $query->where(['apprenticeship_submission_document_id' => $filterDocument]);
        }
        if ($filterStatus) {
            $query->where(['master_document_submission_status_id' => $filterStatus]);
        }

        $this->paginate = ['order' => ['TraineeSubmissionDocuments.id' => 'DESC']];
        $traineeSubmissionDocuments = $this->paginate($query);

        // Lookup maps to display names instead of raw ids
        $traineeNames = [];
        foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
            $traineeNames[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
        }

        $documentTitles = $locator->get('MasterTraineeSubmissionDocuments', ['connection' => $conn])
            ->find()->enableHydration(false)->all()->combine('id', 'title')->toArray();

        $statusNames = $locator->get('MasterDocumentSubmissionStatuses')->find('list')->toArray();

        $userNames = [];
        try {
            $userNames = $locator->get('Users')->find()->enableHydration(false)
                ->all()->combine('id', 'username')->toArray();
        } catch (\Exception $e) {
            // uploader names are cosmetic - ignore if the users table is unavailable
        }

        // Summary counts (unfiltered)
        $summaryQuery = $this->TraineeSubmissionDocuments->find();
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

        $this->set(compact('traineeSubmissionDocuments', 'traineeNames', 'documentTitles',
            'statusNames', 'userNames', 'summary', 'filterTrainee', 'filterDocument', 'filterStatus'));
    }

    public function view($id = null)
    {
        $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->get($id);
        $this->set('traineeSubmissionDocument', $traineeSubmissionDocument);
    }

    public function add()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_documents');

        $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $traineeId = (int)($data['trainee_id'] ?? 0);
            $documentId = (int)($data['apprenticeship_submission_document_id'] ?? 0);
            $file = $data['document_file'] ?? null;
            unset($data['document_file']);

            $uploadOk = true;
            if (is_array($file) && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
                if (!in_array($extension, $allowed)) {
                    $this->Flash->error(__('File type .{0} is not allowed.', $extension));
                    $uploadOk = false;
                } else {
                    $dir = WWW_ROOT . 'files' . DS . 'uploads' . DS . 'trainee_documents';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $fileName = 'TRAINEE' . $traineeId . '_DOC' . $documentId . '_' . time() . '.' . $extension;
                    if (move_uploaded_file($file['tmp_name'], $dir . DS . $fileName)) {
                        $data['file_path'] = 'files/uploads/trainee_documents/' . $fileName;
                    } else {
                        $this->Flash->error(__('The file could not be stored. Please, try again.'));
                        $uploadOk = false;
                    }
                }
            } elseif (empty($data['file_path'])) {
                $this->Flash->error(__('Please choose a file to upload.'));
                $uploadOk = false;
            }

            if ($uploadOk) {
                $data['uploaded_by'] = $this->Auth->user('id');
                $data['uploaded_at'] = date('Y-m-d H:i:s');

                // Same semantics as the checklist: re-uploading a document a trainee
                // already submitted replaces the existing record instead of duplicating it.
                $existing = null;
                if ($traineeId && $documentId) {
                    $existing = $this->TraineeSubmissionDocuments->find()
                        ->where([
                            'trainee_id' => $traineeId,
                            'apprenticeship_submission_document_id' => $documentId,
                        ])
                        ->first();
                }
                if ($existing) {
                    $traineeSubmissionDocument = $existing;
                }

                $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->patchEntity($traineeSubmissionDocument, $data);
                if ($this->TraineeSubmissionDocuments->save($traineeSubmissionDocument)) {
                    $this->Flash->success($existing
                        ? __('The existing submission has been replaced with the new file.')
                        : __('The Trainee Submission Document has been saved.'));

                    return $this->redirect(['action' => 'checklist', '?' => ['trainee_id' => $traineeId]]);
                }
                $this->Flash->error(__('The Trainee Submission Document could not be saved. Please, try again.'));
            } else {
                $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->patchEntity(
                    $traineeSubmissionDocument, $data, ['validate' => false]);
            }
        }

        // Trainees with TMM code for the dropdown
        $trainees = [];
        foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
            $trainees[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
        }

        // Master documents grouped by category (optgroups) + metadata for the info panel
        $categories = $locator->get('MasterTraineeSubmissionDocumentCategories', ['connection' => $conn])
            ->find()->enableHydration(false)->all()->combine('id', 'name')->toArray();

        $documents = [];
        $documentMeta = [];
        foreach ($locator->get('MasterTraineeSubmissionDocuments', ['connection' => $conn])
            ->find()->order(['master_trainee_submission_document_category_id' => 'ASC', 'id' => 'ASC']) as $doc) {
            $categoryName = isset($categories[$doc->master_trainee_submission_document_category_id])
                ? $categories[$doc->master_trainee_submission_document_category_id]
                : __('Other');
            $documents[$categoryName][$doc->id] = $doc->title . ($doc->is_required ? ' *' : '');
            $documentMeta[$doc->id] = [
                'title' => $doc->title,
                'title_jp' => $doc->title_jp,
                'format' => $doc->format,
                'category' => $categoryName,
                'is_required' => (bool)$doc->is_required,
                'is_translation_required' => (bool)$doc->is_translation_required,
            ];
        }

        $statuses = $locator->get('MasterDocumentSubmissionStatuses')->find('list')->toArray();

        // Existing submissions per trainee (for duplicate warning + progress panel)
        $submissionsByTrainee = [];
        foreach ($this->TraineeSubmissionDocuments->find()
            ->select(['trainee_id', 'apprenticeship_submission_document_id'])
            ->enableHydration(false) as $row) {
            $submissionsByTrainee[$row['trainee_id']][] = (int)$row['apprenticeship_submission_document_id'];
        }

        $requiredTotal = $locator->get('MasterTraineeSubmissionDocuments', ['connection' => $conn])
            ->find()->where(['is_required' => true])->count();

        $this->set(compact('traineeSubmissionDocument', 'trainees', 'documents', 'documentMeta',
            'statuses', 'submissionsByTrainee', 'requiredTotal'));
    }

    public function edit($id = null)
    {
        $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->patchEntity($traineeSubmissionDocument, $this->request->getData());
            if ($this->TraineeSubmissionDocuments->save($traineeSubmissionDocument)) {
                $this->Flash->success(__('The Trainee Submission Document has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Trainee Submission Document could not be saved. Please, try again.'));
        }
        $this->set('traineeSubmissionDocument', $traineeSubmissionDocument);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeSubmissionDocument = $this->TraineeSubmissionDocuments->get($id);
        if ($this->TraineeSubmissionDocuments->delete($traineeSubmissionDocument)) {
            $this->Flash->success(__('The Trainee Submission Document has been deleted.'));
        } else {
            $this->Flash->error(__('The Trainee Submission Document could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Document checklist - one-page matrix per trainee.
     *
     * Every document from master_trainee_submission_documents is listed with
     * its uploaded/not-uploaded status from trainee_submission_documents.
     * POST with a file uploads/replaces the document for the trainee.
     */
    public function checklist()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_documents');

        $trainees = $locator->get('Trainees')->find('list')->order(['name' => 'ASC'])->toArray();
        $traineeId = (int)($this->request->getData('trainee_id') ?: $this->request->getQuery('trainee_id'));
        if ($traineeId && !isset($trainees[$traineeId])) {
            $traineeId = 0;
        }

        // Handle per-row file upload
        if ($this->request->is('post') && $traineeId) {
            $documentId = (int)$this->request->getData('document_id');
            $file = $this->request->getData('document_file');

            if ($documentId && is_array($file) && !empty($file['tmp_name']) && $file['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'zip'];
                if (!in_array($extension, $allowed)) {
                    $this->Flash->error(__('File type .{0} is not allowed.', $extension));
                } else {
                    $dir = WWW_ROOT . 'files' . DS . 'uploads' . DS . 'trainee_documents';
                    if (!is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    $fileName = 'TRAINEE' . $traineeId . '_DOC' . $documentId . '_' . time() . '.' . $extension;
                    if (move_uploaded_file($file['tmp_name'], $dir . DS . $fileName)) {
                        $record = $this->TraineeSubmissionDocuments->find()
                            ->where(['trainee_id' => $traineeId, 'apprenticeship_submission_document_id' => $documentId])
                            ->first();
                        if (!$record) {
                            $record = $this->TraineeSubmissionDocuments->newEntity();
                            $record->trainee_id = $traineeId;
                            $record->apprenticeship_submission_document_id = $documentId;
                        }
                        $record->file_path = 'files/uploads/trainee_documents/' . $fileName;
                        $record->uploaded_by = $this->Auth->user('id');
                        $record->uploaded_at = date('Y-m-d H:i:s');

                        if ($this->TraineeSubmissionDocuments->save($record)) {
                            $this->Flash->success(__('The document has been uploaded.'));
                        } else {
                            $this->Flash->error(__('The document record could not be saved. Please, try again.'));
                        }
                    } else {
                        $this->Flash->error(__('The file could not be stored. Please, try again.'));
                    }
                }
            } else {
                $this->Flash->error(__('Please choose a file to upload.'));
            }

            return $this->redirect(['action' => 'checklist', '?' => ['trainee_id' => $traineeId]]);
        }

        $masterDocuments = $locator->get('MasterTraineeSubmissionDocuments', ['connection' => $conn])
            ->find()
            ->order(['master_trainee_submission_document_category_id' => 'ASC', 'id' => 'ASC'])
            ->all();

        $categories = $locator->get('MasterTraineeSubmissionDocumentCategories', ['connection' => $conn])
            ->find()
            ->enableHydration(false)
            ->all()
            ->combine('id', 'name')
            ->toArray();

        // submissions keyed by master document id for the selected trainee
        $submissions = [];
        if ($traineeId) {
            $rows = $this->TraineeSubmissionDocuments->find()
                ->where(['trainee_id' => $traineeId])
                ->all();
            foreach ($rows as $row) {
                $submissions[$row->apprenticeship_submission_document_id] = $row;
            }
        }

        $this->set(compact('trainees', 'traineeId', 'masterDocuments', 'categories', 'submissions'));
    }

    /**
     * Remove an uploaded checklist document record for a trainee.
     */
    public function removeSubmission()
    {
        $this->request->allowMethod(['post']);
        $traineeId = (int)$this->request->getData('trainee_id');
        $documentId = (int)$this->request->getData('document_id');

        $record = $this->TraineeSubmissionDocuments->find()
            ->where(['trainee_id' => $traineeId, 'apprenticeship_submission_document_id' => $documentId])
            ->first();
        if ($record && $this->TraineeSubmissionDocuments->delete($record)) {
            $this->Flash->success(__('The document record has been removed.'));
        } else {
            $this->Flash->error(__('The document record could not be removed.'));
        }

        return $this->redirect(['action' => 'checklist', '?' => ['trainee_id' => $traineeId]]);
    }

    /**
     * Progress tracking - answers: how far along are we, which trainees are
     * ready to depart, what is missing for the rest, and which document is
     * the bottleneck across all trainees.
     */
    public function progress()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_documents');

        // Trainees
        $trainees = [];
        foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
            $trainees[$trainee->id] = [
                'name' => $trainee->name,
                'tmm_code' => $trainee->tmm_code,
            ];
        }

        // Required master documents with their categories
        $requiredDocs = $conn->execute(
            'SELECT m.id, m.title, c.name AS category
             FROM master_trainee_submission_documents m
             LEFT JOIN master_trainee_submission_document_categories c
               ON c.id = m.master_trainee_submission_document_category_id
             WHERE m.is_required = 1
             ORDER BY c.id ASC, m.id ASC'
        )->fetchAll('assoc');

        // Statuses + all submissions (trainee x document x status)
        $statusNames = $locator->get('MasterDocumentSubmissionStatuses')->find('list')->toArray();
        $submissions = $conn->execute(
            'SELECT trainee_id, apprenticeship_submission_document_id AS document_id,
                    master_document_submission_status_id AS status_id
             FROM trainee_submission_documents'
        )->fetchAll('assoc');

        $submittedMap = [];   // [traineeId][documentId] => status_id
        $statusCounts = [];   // [statusName] => count
        foreach ($submissions as $row) {
            $submittedMap[$row['trainee_id']][$row['document_id']] = $row['status_id'];
            $name = isset($statusNames[$row['status_id']]) ? $statusNames[$row['status_id']] : 'Unknown';
            $statusCounts[$name] = (isset($statusCounts[$name]) ? $statusCounts[$name] : 0) + 1;
        }

        // Per-trainee progress with the exact missing documents
        $traineeProgress = [];
        $readyCount = 0;
        $notStartedCount = 0;
        foreach ($trainees as $traineeId => $info) {
            $missing = [];
            $submitted = 0;
            foreach ($requiredDocs as $doc) {
                if (isset($submittedMap[$traineeId][$doc['id']])) {
                    $submitted++;
                } else {
                    $missing[] = $doc['title'];
                }
            }
            $requiredTotal = count($requiredDocs);
            $percent = $requiredTotal > 0 ? (int)round($submitted / $requiredTotal * 100) : 0;
            if ($percent >= 100) {
                $readyCount++;
            } elseif ($submitted === 0) {
                $notStartedCount++;
            }
            $traineeProgress[] = [
                'id' => $traineeId,
                'name' => $info['name'],
                'tmm_code' => $info['tmm_code'],
                'submitted' => $submitted,
                'required' => $requiredTotal,
                'percent' => $percent,
                'missing' => $missing,
            ];
        }
        // Least complete first - those need attention
        usort($traineeProgress, function ($a, $b) {
            return $a['percent'] - $b['percent'];
        });

        // Per-document coverage across trainees (bottleneck view)
        $documentProgress = [];
        $traineeTotal = count($trainees);
        foreach ($requiredDocs as $doc) {
            $covered = 0;
            foreach (array_keys($trainees) as $traineeId) {
                if (isset($submittedMap[$traineeId][$doc['id']])) {
                    $covered++;
                }
            }
            $documentProgress[] = [
                'id' => $doc['id'],
                'title' => $doc['title'],
                'category' => $doc['category'],
                'covered' => $covered,
                'total' => $traineeTotal,
                'percent' => $traineeTotal > 0 ? (int)round($covered / $traineeTotal * 100) : 0,
            ];
        }
        // Most missing first - the bottlenecks
        usort($documentProgress, function ($a, $b) {
            return $a['percent'] - $b['percent'];
        });

        $expected = count($requiredDocs) * $traineeTotal;
        $collected = 0;
        foreach ($traineeProgress as $row) {
            $collected += $row['submitted'];
        }
        $summary = [
            'traineeTotal' => $traineeTotal,
            'requiredDocs' => count($requiredDocs),
            'expected' => $expected,
            'collected' => $collected,
            'overallPercent' => $expected > 0 ? (int)round($collected / $expected * 100) : 0,
            'ready' => $readyCount,
            'notStarted' => $notStartedCount,
            'inProgress' => $traineeTotal - $readyCount - $notStartedCount,
            'submitted' => isset($statusCounts['Submitted']) ? $statusCounts['Submitted'] : 0,
            'pending' => isset($statusCounts['Pending']) ? $statusCounts['Pending'] : 0,
        ];

        $this->set(compact('summary', 'traineeProgress', 'documentProgress'));
    }

    /**
     * Cost management - document cost records
     */
    public function costs()
    {
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_accountings');
        $costs = $conn->execute(
            'SELECT id, trainee_id, document_type, description, amount, currency_code,
                    payment_status, payment_date, paid_amount
             FROM trainee_document_costs ORDER BY id DESC LIMIT 500'
        )->fetchAll('assoc');

        // Nama trainee dari DB terasosiasi
        $traineeMap = [];
        foreach (\Cake\Datasource\ConnectionManager::get('cms_tmm_trainees')->execute(
            'SELECT id, name, tmm_code FROM trainees'
        )->fetchAll('assoc') as $r) {
            $traineeMap[$r['id']] = $r;
        }

        // Ringkasan biaya
        $summary = ['total' => 0, 'paid' => 0, 'outstanding' => 0, 'count' => count($costs)];
        $byType = [];
        foreach ($costs as $c) {
            $summary['total'] += (float)$c['amount'];
            $summary['paid'] += (float)$c['paid_amount'];
            $byType[$c['document_type']] = ($byType[$c['document_type']] ?? 0) + (float)$c['amount'];
        }
        $summary['outstanding'] = $summary['total'] - $summary['paid'];
        arsort($byType);

        $this->set(compact('costs', 'traineeMap', 'summary', 'byType'));
    }
}

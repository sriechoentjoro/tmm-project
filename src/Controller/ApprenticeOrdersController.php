<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ApprenticeOrders Controller
 *
 * @property \App\Model\Table\ApprenticeOrdersTable $ApprenticeOrders
 *
 * @method \App\Model\Entity\ApprenticeOrder[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ApprenticeOrdersController extends AppController
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
            'contain' => ['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories'],
        ];
        $apprenticeOrders = $this->paginate($this->ApprenticeOrders);

        // Load dropdown data for filters
        $cooperativeassociations = $this->ApprenticeOrders->CooperativeAssociations->find('list')->limit(200)->toArray();
        $acceptanceorganizations = $this->ApprenticeOrders->AcceptanceOrganizations->find('list')->limit(200)->toArray();
        $masterjobcategories = $this->ApprenticeOrders->MasterJobCategories->find('list')->limit(200)->toArray();
        $cooperative_associations = $cooperativeassociations;
        $acceptance_organizations = $acceptanceorganizations;
        $job_categorys = $masterjobcategories;
        $this->set(compact('apprenticeOrders', 'cooperativeassociations', 'acceptanceorganizations', 'masterjobcategories', 'cooperative_associations', 'acceptance_organizations', 'job_categorys'));
    }

    /**
     * Statistics method - order counts and requested trainees per departure year
     *
     * @return void
     */
    public function statistics()
    {
        $query = $this->ApprenticeOrders->find();
        $byYear = $query
            ->select([
                'departure_year',
                'order_count' => $query->func()->count('*'),
                'total_male' => $query->func()->sum('male_trainee_number'),
                'total_female' => $query->func()->sum('female_trainee_number'),
            ])
            ->group(['departure_year'])
            ->order(['departure_year' => 'DESC'])
            ->enableHydration(false)
            ->toArray();

        $totals = [
            'orders' => array_sum(array_column($byYear, 'order_count')),
            'trainees' => array_sum(array_map(function ($r) {
                return (int)$r['total_male'] + (int)$r['total_female'];
            }, $byYear)),
        ];

        // ---- Pengayaan: breakdown & fulfillment dari tabel terasosiasi ----
        $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainees');

        // Per kumiai (cooperative association) — join lintas-database
        $byKumiai = $conn->execute(
            'SELECT ca.name AS label, COUNT(*) AS orders,
                    SUM(ao.male_trainee_number + ao.female_trainee_number) AS requested
             FROM apprentice_orders ao
             LEFT JOIN cms_tmm_stakeholders.cooperative_associations ca ON ca.id = ao.cooperative_association_id
             GROUP BY ao.cooperative_association_id, ca.name
             ORDER BY requested DESC LIMIT 10'
        )->fetchAll('assoc');

        // Per organisasi penerima
        $byOrg = $conn->execute(
            'SELECT ao2.title AS label, COUNT(*) AS orders,
                    SUM(ao.male_trainee_number + ao.female_trainee_number) AS requested
             FROM apprentice_orders ao
             LEFT JOIN cms_tmm_stakeholders.acceptance_organizations ao2 ON ao2.id = ao.acceptance_organization_id
             GROUP BY ao.acceptance_organization_id, ao2.title
             ORDER BY requested DESC LIMIT 10'
        )->fetchAll('assoc');

        // Per kategori pekerjaan
        $byJob = $conn->execute(
            'SELECT mjc.title AS label, COUNT(*) AS orders,
                    SUM(ao.male_trainee_number + ao.female_trainee_number) AS requested
             FROM apprentice_orders ao
             LEFT JOIN cms_masters.master_job_categories mjc ON mjc.id = ao.master_job_category_id
             GROUP BY ao.master_job_category_id, mjc.title
             ORDER BY requested DESC LIMIT 10'
        )->fetchAll('assoc');

        // Fulfillment per order: diminta vs trainee ter-assign vs lulus apprenticeship
        $fulfillment = $conn->execute(
            'SELECT ao.id, ao.title, ao.departure_year, ao.departure_month,
                    ca.name AS kumiai, org.title AS organization,
                    (ao.male_trainee_number + ao.female_trainee_number) AS requested,
                    COALESCE(t.assigned, 0) AS assigned,
                    COALESCE(t.passed, 0)   AS passed
             FROM apprentice_orders ao
             LEFT JOIN cms_tmm_stakeholders.cooperative_associations ca ON ca.id = ao.cooperative_association_id
             LEFT JOIN cms_tmm_stakeholders.acceptance_organizations org ON org.id = ao.acceptance_organization_id
             LEFT JOIN (
                 SELECT apprenticeship_order_id, COUNT(*) AS assigned,
                        SUM(is_apprenticeship_pass = 1) AS passed
                 FROM trainees GROUP BY apprenticeship_order_id
             ) t ON t.apprenticeship_order_id = ao.id
             ORDER BY ao.departure_year DESC, ao.id DESC'
        )->fetchAll('assoc');

        $totals['assigned'] = array_sum(array_column($fulfillment, 'assigned'));
        $totals['passed']   = array_sum(array_column($fulfillment, 'passed'));

        $this->set(compact('byYear', 'totals', 'byKumiai', 'byOrg', 'byJob', 'fulfillment'));
    }

    /**
     * View method
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'CooperativeAssociations';
        $contain[] = 'AcceptanceOrganizations';
        $contain[] = 'MasterJobCategories';
        
        // Add HasMany with nested BelongsTo for foreign key display
            // Apprentices with its associations
        $apprenticesAssociations = [];
        try {
            $apprenticesTable = $this->ApprenticeOrders->Apprentices;
            // Get all BelongsTo associations for nested contain
            foreach ($apprenticesTable->associations() as $association) {
                if ($association->type() === 'manyToOne') {
                    $apprenticesAssociations[] = $association->getName();
                }
            }
        } catch (\Exception $e) {
            // If association doesn't exist, just use empty array
        }
        
        if (!empty($apprenticesAssociations)) {
            $contain['Apprentices'] = $apprenticesAssociations;
        } else {
            $contain[] = 'Apprentices';
        }
        
        $apprenticeOrder = $this->ApprenticeOrders->get($id, [
            'contain' => $contain,
        ]);

        // Debug: Check if apprentices are loaded
        $apprenticesCount = !empty($apprenticeOrder->apprentices) ? count($apprenticeOrder->apprentices) : 0;
        $this->log('ApprenticeOrder ID: ' . $id . ' - Apprentices loaded: ' . $apprenticesCount, 'debug');
        
        // If no apprentices loaded via contain, try direct query with pagination
        $apprenticesPage = $this->request->getQuery('apprentices_page', 1);
        $apprenticesLimit = 50;
        
        if (empty($apprenticeOrder->apprentices)) {
            $apprenticesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Apprentices');
            
            // Get total count
            $totalApprentices = $apprenticesTable->find()
                ->where(['apprentice_order_id' => $id])
                ->count();
            
            // Get paginated data
            $apprenticeOrder->apprentices = $apprenticesTable->find()
                ->where(['apprentice_order_id' => $id])
                ->limit($apprenticesLimit)
                ->offset(($apprenticesPage - 1) * $apprenticesLimit)
                ->all()
                ->toArray();
                
            $this->log('Direct query found: ' . count($apprenticeOrder->apprentices) . ' apprentices (page ' . $apprenticesPage . ' of ' . ceil($totalApprentices / $apprenticesLimit) . ')', 'debug');
            
            // Pass pagination info to view
            $this->set('apprenticesPage', $apprenticesPage);
            $this->set('apprenticesTotal', $totalApprentices);
        } else {
            // If loaded via contain, calculate pagination info
            $this->set('apprenticesPage', 1);
            $this->set('apprenticesTotal', count($apprenticeOrder->apprentices));
        }

        $this->set('apprenticeOrder', $apprenticeOrder);
        $this->_setSharingData($apprenticeOrder->id);
    }

    /**
     * Offer this order to one or more vocational training institutions.
     *
     * Each institution gets the apprentice_order_shared email. That template
     * has been sitting in email_templates, active, with nothing in the
     * application asking for its key - this is the code it was written for.
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null Always a redirect back to the order.
     */
    public function share($id = null)
    {
        $this->request->allowMethod(['post']);

        try {
            $order = $this->ApprenticeOrders->get($id, [
                'contain' => ['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories'],
            ]);
        } catch (\Throwable $e) {
            $this->Flash->error(__('Apprentice order not found.'));

            return $this->redirect(['action' => 'index']);
        }

        $chosen = array_filter((array)$this->request->getData('institution_ids'));
        if (!$chosen) {
            $this->Flash->error(__('Choose at least one institution to share this order with.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        $shares = $this->_shares();
        $institutions = $this->_institutionsById($chosen);

        $sent = 0;
        $failed = [];
        foreach ($chosen as $institutionId) {
            $institutionId = (int)$institutionId;
            if (!isset($institutions[$institutionId])) {
                continue;
            }
            $institution = $institutions[$institutionId];

            // One row per order and institution: re-sharing something that was
            // cancelled reopens that row rather than adding a second, so the
            // history of an offer stays in one place.
            $share = $shares->find()
                ->where([
                    'apprentice_order_id' => $order->id,
                    'vocational_training_institution_id' => $institutionId,
                ])
                ->first();

            if (!$share) {
                $share = $shares->newEntity(['apprentice_order_id' => $order->id,
                    'vocational_training_institution_id' => $institutionId]);
            }

            $share->lpk_name = $institution->name;
            $share->lpk_email = $institution->email;
            $share->status = 'shared';
            $share->cancelled_at = null;
            $share->shared_by_user_id = $this->Auth->user('id');
            $share->shared_by_name = $this->Auth->user('fullname') ?: $this->Auth->user('username');
            $share->created = new \Cake\I18n\FrozenTime();

            // 0 and 1, not false and true. The integer marshaller rejects a
            // boolean - is_numeric(false) is false - and returns null for it,
            // so a value set through newEntity() reaches a NOT NULL column as
            // NULL and the insert fails. Integers survive whether the column
            // reflects as boolean or as integer.
            $share->notified = 0;

            if (!$shares->save($share)) {
                $failed[] = $institution->name;
                continue;
            }

            if ($this->_notify('apprentice_order_shared', $order, $share)) {
                $share->notified = 1;
                $shares->save($share);
                $sent++;
            } else {
                $failed[] = $institution->name;
            }
        }

        if ($sent) {
            $this->Flash->success(__('Order shared with {0} institution(s).', $sent));
        }
        if ($failed) {
            // Saved but not delivered is a different state from not saved, and
            // the share row records which - notified says whether the mail went.
            $this->Flash->warning(__('Could not notify: {0}', implode(', ', $failed)));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Withdraw an order from an institution it was offered to.
     *
     * Sends the apprentice_order_cancelled email, the other template that had
     * no code behind it.
     *
     * @param string|null $id Apprentice Order id.
     * @param string|null $shareId The share to withdraw.
     * @return \Cake\Http\Response|null Always a redirect back to the order.
     */
    public function cancelShare($id = null, $shareId = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        $shares = $this->_shares();

        try {
            $order = $this->ApprenticeOrders->get($id, [
                'contain' => ['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories'],
            ]);
            $share = $shares->get($shareId);
        } catch (\Throwable $e) {
            $this->Flash->error(__('That share could not be found.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        if ((int)$share->apprentice_order_id !== (int)$order->id) {
            $this->Flash->error(__('That share belongs to a different order.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        if ($share->isCancelled()) {
            $this->Flash->info(__('That share was already withdrawn.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        $share->status = 'cancelled';
        $share->cancelled_at = new \Cake\I18n\FrozenTime();

        if (!$shares->save($share)) {
            $this->Flash->error(__('The share could not be withdrawn. Please, try again.'));

            return $this->redirect(['action' => 'view', $id]);
        }

        if ($this->_notify('apprentice_order_cancelled', $order, $share)) {
            $this->Flash->success(__('Order withdrawn from {0}, and they have been told.', $share->lpk_name));
        } else {
            // The withdrawal stands either way: the institution must not keep
            // seeing the order as open because an email failed.
            $this->Flash->warning(__('Order withdrawn from {0}, but the email could not be sent.', $share->lpk_name));
        }

        return $this->redirect(['action' => 'view', $id]);
    }

    /**
     * Send one of the two apprentice-order templates.
     *
     * The variable names are the ones the templates in email_templates actually
     * use - read off the rows with bin/cake list_email_templates. A name a
     * template uses and this does not supply arrives at the reader as
     * {{name}}, so that shell is the check on this list.
     *
     * organization_name and organization are both supplied because the shared
     * template uses both: the acceptance organization is the one placing the
     * order, and the cooperative association is the one it comes through.
     *
     * @param string $templateKey Which template.
     * @param \App\Model\Entity\ApprenticeOrder $order The order.
     * @param \App\Model\Entity\ApprenticeOrderShare $share The share.
     * @return bool Whether the email went.
     */
    protected function _notify($templateKey, $order, $share)
    {
        if (empty($share->lpk_email)) {
            return false;
        }

        $this->loadComponent('Email');

        $quantity = (int)$order->male_trainee_number + (int)$order->female_trainee_number;

        return $this->Email->sendTemplate($templateKey, $share->lpk_email, [
            'title' => (string)$order->title,
            'lpk_name' => (string)$share->lpk_name,
            'job_title' => $order->has('master_job_category') ? (string)$order->master_job_category->title : '',
            'organization_name' => $order->has('acceptance_organization') ? (string)$order->acceptance_organization->name : '',
            'organization' => $order->has('cooperative_association') ? (string)$order->cooperative_association->name : '',
            'requirement' => (string)$order->other_requirements,
            'user_name' => (string)$share->shared_by_name,
            'quantity' => (string)$quantity,
            'departure' => trim($order->departure_month . ' ' . $order->departure_year),
        ]);
    }

    /**
     * The shares table, which has no association on ApprenticeOrders.
     *
     * @return \App\Model\Table\ApprenticeOrderSharesTable
     */
    protected function _shares()
    {
        return \Cake\ORM\TableRegistry::getTableLocator()->get('ApprenticeOrderShares');
    }

    /**
     * Institutions by id, read from their own connection.
     *
     * vocational_training_institutions is on cms_tmm_stakeholders and the order
     * is on cms_tmm_trainees, so this cannot be a contain() - it is a second
     * query, the way the rest of the application crosses the same boundary.
     *
     * @param array $ids Institution ids, or empty for all of them.
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    protected function _institutionsById(array $ids = [])
    {
        $table = \Cake\ORM\TableRegistry::getTableLocator()->get('VocationalTrainingInstitutions');
        $query = $table->find()->order(['name' => 'ASC']);
        if ($ids) {
            $query->where(['id IN' => array_map('intval', $ids)]);
        }

        $out = [];
        foreach ($query as $row) {
            $out[(int)$row->id] = $row;
        }

        return $out;
    }

    /**
     * What the order page needs to offer the order and to list who has it.
     *
     * A missing apprentice_order_shares table is not an error worth taking the
     * page down for - the feature is simply not installed yet, and the view
     * says so instead of showing a panel that cannot work.
     *
     * @param int $orderId The order.
     * @return void
     */
    protected function _setSharingData($orderId)
    {
        try {
            $shares = $this->_shares()->forOrder($orderId)->toArray();
            $installed = true;
        } catch (\Exception $e) {
            $shares = [];
            $installed = false;
        }

        $already = [];
        foreach ($shares as $share) {
            if (!$share->isCancelled()) {
                $already[(int)$share->vocational_training_institution_id] = true;
            }
        }

        $this->set('orderShares', $shares);
        $this->set('sharingInstalled', $installed);
        $this->set('shareableInstitutions', $installed ? $this->_institutionsById() : []);
        $this->set('alreadySharedWith', $already);
    }

    /**
     * AJAX Search for related apprentices with server-side filtering
     *
     * @return \Cake\Http\Response|null JSON response
     */
    public function searchApprentices()
    {
        $this->request->allowMethod(['get', 'post']);
        $this->autoRender = false;
        
        try {
            $apprenticeOrderId = $this->request->getQuery('apprentice_order_id');
            $page = (int) $this->request->getQuery('page', 1);
            $limit = (int) $this->request->getQuery('limit', 50);
            $filtersJson = $this->request->getQuery('filters', '{}');
            
            // Decode filters JSON
            $filters = [];
            if (is_string($filtersJson)) {
                $filters = json_decode($filtersJson, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $filters = [];
                }
            } elseif (is_array($filtersJson)) {
                $filters = $filtersJson;
            }
            
            if (empty($apprenticeOrderId)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'error' => 'Missing apprentice_order_id']));
            }
            
            $apprenticesTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Apprentices');
            $query = $apprenticesTable->find()
                ->where(['apprentice_order_id' => $apprenticeOrderId]);
            
            // Apply filters with OR logic (match ANY filter, not ALL)
            if (!empty($filters) && is_array($filters)) {
                $orConditions = [];
                
                foreach ($filters as $field => $filterData) {
                    if (!is_array($filterData)) continue;
                    
                    $value = isset($filterData['value']) ? trim($filterData['value']) : '';
                    $operator = isset($filterData['operator']) ? $filterData['operator'] : 'contains';
                    
                    // Skip if value is empty and operator requires a value
                    if ($value === '' && !in_array($operator, ['file_exists', 'file_not_exists'])) {
                        continue;
                    }
                    
                    switch ($operator) {
                        case 'contains':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => '%' . $value . '%'];
                            }
                            break;
                        case 'equals':
                            $orConditions[] = [$field => $value];
                            break;
                        case 'not_equals':
                            $orConditions[] = [$field . ' !=' => $value];
                            break;
                        case 'starts_with':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => $value . '%'];
                            }
                            break;
                        case 'ends_with':
                            if (!empty($value)) {
                                $orConditions[] = [$field . ' LIKE' => '%' . $value];
                            }
                            break;
                        case 'greater_than':
                            $orConditions[] = [$field . ' >' => $value];
                            break;
                        case 'less_than':
                            $orConditions[] = [$field . ' <' => $value];
                            break;
                        case 'greater_equal':
                            $orConditions[] = [$field . ' >=' => $value];
                            break;
                        case 'less_equal':
                            $orConditions[] = [$field . ' <=' => $value];
                            break;
                        case 'file_exists':
                            // Records where field is NOT NULL and NOT empty
                            $orConditions[] = function ($exp, $q) use ($field) {
                                return $exp->and_([
                                    $exp->isNotNull($field),
                                    $exp->notEq($field, '')
                                ]);
                            };
                            break;
                        case 'file_not_exists':
                            // Records where field IS NULL or empty
                            $orConditions[] = function ($exp, $q) use ($field) {
                                return $exp->or_([
                                    $exp->isNull($field),
                                    $exp->eq($field, '')
                                ]);
                            };
                            break;
                    }
                }
                
                // Apply OR conditions if any filters were added
                if (!empty($orConditions)) {
                    $query->where(['OR' => $orConditions]);
                }
            }
            
            // Log query before execution
            $sql = $query->sql();
            $this->log('=== AJAX Search Debug ===', 'debug');
            $this->log('Filters: ' . json_encode($filters), 'debug');
            $this->log('SQL: ' . $sql, 'debug');
            
            // Get total count with filters
            $total = $query->count();
            $this->log('Total found: ' . $total, 'debug');
            
            // Get paginated results
            $records = $query
                ->limit($limit)
                ->offset(($page - 1) * $limit)
                ->all()
                ->toArray();
            
            // Format records for JSON
            $formattedRecords = [];
            foreach ($records as $record) {
                $formattedRecords[] = [
                    'id' => $record->id,
                    'tmm_code' => $record->tmm_code,
                    'name' => $record->name,
                    'identity_number' => $record->identity_number,
                    'birth_date' => $record->birth_date ? $record->birth_date->format('Y-m-d') : null,
                    'image_photo' => $record->image_photo
                ];
            }
            
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => true,
                    'records' => $formattedRecords,
                    'total' => $total,
                    'page' => $page,
                    'pages' => ceil($total / $limit)
                ]));
                
        } catch (\Exception $e) {
            $this->log('SearchApprentices Error: ' . $e->getMessage(), 'error');
            return $this->response->withType('application/json')
                ->withStringBody(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $apprenticeOrder = $this->ApprenticeOrders->newEntity();
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
                $imagePath = $this->uploadImage('ApprenticeOrders', $fieldName, 'apprenticeorders');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('ApprenticeOrders', $fieldName, 'apprenticeorders');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            $apprenticeOrder = $this->ApprenticeOrders->patchEntity($apprenticeOrder, $data);
            if ($this->ApprenticeOrders->save($apprenticeOrder)) {
                $this->Flash->success(__('The apprentice order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice order could not be saved. Please, try again.'));
        }
        $cooperativeAssociations = $this->ApprenticeOrders->CooperativeAssociations->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->ApprenticeOrders->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterJobCategories = $this->ApprenticeOrders->MasterJobCategories->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeOrder', 'cooperativeAssociations', 'acceptanceOrganizations', 'masterJobCategories'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $apprenticeOrder = $this->ApprenticeOrders->get($id, [
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
                $imagePath = $this->uploadImage('ApprenticeOrders', $fieldName, 'apprenticeorders');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('ApprenticeOrders', $fieldName, 'apprenticeorders');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $apprenticeOrder = $this->ApprenticeOrders->patchEntity($apprenticeOrder, $data);
            if ($this->ApprenticeOrders->save($apprenticeOrder)) {
                $this->Flash->success(__('The apprentice order has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The apprentice order could not be saved. Please, try again.'));
        }
        $cooperativeAssociations = $this->ApprenticeOrders->CooperativeAssociations->find('list', ['limit' => 200]);
        $acceptanceOrganizations = $this->ApprenticeOrders->AcceptanceOrganizations->find('list', ['limit' => 200]);
        $masterJobCategories = $this->ApprenticeOrders->MasterJobCategories->find('list', ['limit' => 200]);
        $this->set(compact('apprenticeOrder', 'cooperativeAssociations', 'acceptanceOrganizations', 'masterJobCategories'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Apprentice Order id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $apprenticeOrder = $this->ApprenticeOrders->get($id);
        if ($this->ApprenticeOrders->delete($apprenticeOrder)) {
            $this->Flash->success(__('The apprentice order has been deleted.'));
        } else {
            $this->Flash->error(__('The apprentice order could not be deleted. Please, try again.'));
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
        $query = $this->ApprenticeOrders->find('all')
            ->contain(['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'ApprenticeOrders', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->ApprenticeOrders->find('all')
            ->contain(['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'ApprenticeOrders', $headers, $fields);
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
        
        $query = $this->ApprenticeOrders->find('all')
            ->contain(['CooperativeAssociations', 'AcceptanceOrganizations', 'MasterJobCategories']);
        
        // Define report configuration
        $title = 'ApprenticeOrders Report';
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
     * AJAX method to get related records with filtering and pagination
     * Used by related_records_table element for lazy loading
     */
    public function getRelated()
    {
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json')
            ->withCharset('UTF-8');
        
        try {
            // Get filter parameters
            $filterField = $this->request->getQuery('filterField');
            $filterValue = $this->request->getQuery('filterValue');
            $association = $this->request->getQuery('association', 'Apprentices');
            $page = (int)$this->request->getQuery('page', 1);
            $limit = min((int)$this->request->getQuery('limit', 50), 100);
            $filtersJson = $this->request->getQuery('filters');
            
            // Parse column filters
            $columnFilters = $filtersJson ? json_decode($filtersJson, true) : [];
            
            // Load the association table directly using TableRegistry for cross-database support
            $associationTable = \Cake\ORM\TableRegistry::getTableLocator()->get($association);
            
            // Build query with primary filter from association table
            $query = $associationTable->find()
                ->where([$filterField => $filterValue])
                ->limit($limit)
                ->offset(($page - 1) * $limit);
            
            // Apply column filters
            if ($columnFilters && is_array($columnFilters)) {
                foreach ($columnFilters as $column => $filter) {
                    if (empty($filter['value'])) continue;
                    
                    $operator = isset($filter['operator']) ? $filter['operator'] : 'contains';
                    $value = $filter['value'];
                    
                    switch ($operator) {
                        case 'equals':
                            $query->where([$column => $value]);
                            break;
                        case 'contains':
                            $query->where([$column . ' LIKE' => '%' . $value . '%']);
                            break;
                        case 'starts_with':
                            $query->where([$column . ' LIKE' => $value . '%']);
                            break;
                        case 'ends_with':
                            $query->where([$column . ' LIKE' => '%' . $value]);
                            break;
                        case 'greater_than':
                            $query->where([$column . ' >' => $value]);
                            break;
                        case 'less_than':
                            $query->where([$column . ' <' => $value]);
                            break;
                        case 'not_empty':
                            $query->where([$column . ' IS NOT' => null]);
                            break;
                    }
                }
            }
            
            // Get total count for pagination
            $total = $query->count();
            
            // Execute query
            $results = $query->toArray();
            
            // Check file existence for image/photo/file fields
            foreach ($results as $result) {
                foreach ($result->toArray() as $field => $value) {
                    if (preg_match('/(image|photo|file|document)/i', $field) && !empty($value)) {
                        $fullPath = WWW_ROOT . $value;
                        $result->{$field . '_exists'} = file_exists($fullPath);
                    }
                }
            }
            
            // Build response
            $response = [
                'success' => true,
                'data' => $results,
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ];
            
            // Debug log
            $this->log('getRelated Response: ' . json_encode([
                'success' => true,
                'dataCount' => count($results),
                'total' => $total,
                'filterField' => $filterField,
                'filterValue' => $filterValue,
                'association' => $association
            ]), 'debug');
            
            return $this->response->withStringBody(json_encode($response));
            
        } catch (\Exception $e) {
            $this->log('getRelated Error: ' . $e->getMessage(), 'error');
            $this->log('Stack trace: ' . $e->getTraceAsString(), 'error');
            
            $response = [
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
            return $this->response->withStringBody(json_encode($response));
        }
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
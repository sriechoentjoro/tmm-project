<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * TraineeInstallments Controller
 *
 * @property \App\Model\Table\TraineeInstallmentsTable $TraineeInstallments
 *
 * @method \App\Model\Entity\TraineeInstallment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TraineeInstallmentsController extends AppController
{
    use \App\Controller\ExportTrait;
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        // Optional filters
        $filterTrainee = (int)$this->request->getQuery('trainee_id');
        $filterCategory = (int)$this->request->getQuery('category_id');
        $filterPaid = $this->request->getQuery('paid'); // '' | '1' | '0'

        $query = $this->TraineeInstallments->find();
        if ($filterTrainee) {
            $query->where(['TraineeInstallments.trainee_id' => $filterTrainee]);
        }
        if ($filterCategory) {
            $query->where(['TraineeInstallments.master_transaction_category_id' => $filterCategory]);
        }
        if ($filterPaid === '1' || $filterPaid === '0') {
            $query->where(['TraineeInstallments.is_paid_off' => (int)$filterPaid]);
        } else {
            $filterPaid = '';
        }

        $this->paginate = [
            'contain' => ['Trainees', 'MasterTransactionCategories', 'MasterCurrencies'],
            'order' => ['TraineeInstallments.id' => 'DESC'],
        ];
        $traineeInstallments = $this->paginate($query);

        // Filter dropdowns (trainee names with TMM code)
        $trainees = [];
        try {
            foreach (\Cake\ORM\TableRegistry::getTableLocator()->get('Trainees')
                ->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
                $trainees[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }
        $mastertransactioncategories = $this->TraineeInstallments->MasterTransactionCategories->find('list')->limit(200)->toArray();

        // Summary: payment count, total received, settled trainees (unfiltered)
        $summary = ['payments' => 0, 'received' => 0, 'settled' => 0, 'open' => 0];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_accountings');
            $row = $conn->execute(
                'SELECT COUNT(*) AS payments, COALESCE(SUM(payment_amount), 0) AS received
                 FROM trainee_installments'
            )->fetch('assoc');
            $summary['payments'] = (int)$row['payments'];
            $summary['received'] = (int)$row['received'];
            foreach ($conn->execute(
                'SELECT t.is_paid_off, COUNT(*) AS total
                 FROM trainee_installments t
                 JOIN (SELECT trainee_id, MAX(id) AS max_id FROM trainee_installments GROUP BY trainee_id) latest
                   ON latest.max_id = t.id
                 GROUP BY t.is_paid_off'
            )->fetchAll('assoc') as $statusRow) {
                if ($statusRow['is_paid_off']) {
                    $summary['settled'] = (int)$statusRow['total'];
                } else {
                    $summary['open'] = (int)$statusRow['total'];
                }
            }
        } catch (\Exception $e) {
        }

        $this->set(compact('traineeInstallments', 'trainees', 'mastertransactioncategories',
            'summary', 'filterTrainee', 'filterCategory', 'filterPaid'));
    }



    /**
     * View method
     *
     * @param string|null $id Trainee Installment id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        // Load with nested associations to display foreign key names
        $contain = [];
        
        // Add simple associations
        $contain[] = 'Trainees';
        $contain[] = 'MasterTransactionCategories';
        $contain[] = 'MasterCurrencies';
        
        // Add HasMany with nested BelongsTo for foreign key display
        $traineeInstallment = $this->TraineeInstallments->get($id, [
            'contain' => $contain,
        ]);

        $this->set('traineeInstallment', $traineeInstallment);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $traineeInstallment = $this->TraineeInstallments->newEntity();
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
                $imagePath = $this->uploadImage('TraineeInstallments', $fieldName, 'traineeinstallments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $this->uploadFile('TraineeInstallments', $fieldName, 'traineeinstallments');
                $data = $this->request->getData(); // Get updated data after upload
            }
            
            // Enforced flow:
            //   1. the trainee's owing cost must be set first (Set Owing Cost),
            //   2. every payment accumulates automatically
            //      (mode 'full' pays the entire remaining balance, like a
            //       credit card full payment; 'partial' is a free amount),
            //   3. the difference (owing - accumulated) is calculated,
            //   4. when the difference reaches 0 the system issues the receipt
            //      (is_paid_off = 1 -> the row appears on the Receipts page).
            $traineeId = (int)(isset($data['trainee_id']) ? $data['trainee_id'] : 0);
            $previous = $traineeId
                ? $this->TraineeInstallments->find()
                    ->where(['trainee_id' => $traineeId])
                    ->order(['id' => 'DESC'])
                    ->first()
                : null;

            $mode = (isset($data['payment_mode']) && $data['payment_mode'] === 'full') ? 'full' : 'partial';
            $paymentAmount = ($mode === 'full' && $previous)
                ? (int)$previous->unpaid_amount
                : (int)(isset($data['payment_amount']) ? $data['payment_amount'] : 0);
            $data['payment_amount'] = $paymentAmount;
            unset($data['payment_mode']);

            $fullAmount = $previous ? (int)$previous->full_payment_amount : 0;

            if (!$previous) {
                $this->Flash->error(__('No owing cost set for this trainee yet - use "Set Owing Cost" on the Payment Tracking page first.'));
            } elseif ($previous->is_paid_off) {
                $this->Flash->error(__('This trainee is already paid off - the receipt has been issued and no further installments are needed.'));
            } elseif ($paymentAmount <= 0) {
                $this->Flash->error(__('The payment amount must be greater than zero.'));
            } else {
                $accumulated = ($previous ? (int)$previous->payment_accummulated : 0) + $paymentAmount;
                $unpaid = max(0, $fullAmount - $accumulated);

                $data['full_payment_amount'] = $fullAmount;
                $data['payment_accummulated'] = $accumulated;
                $data['unpaid_amount'] = $unpaid;
                $data['is_paid_off'] = ($unpaid === 0) ? 1 : 0;

                $traineeInstallment = $this->TraineeInstallments->patchEntity($traineeInstallment, $data);
                if ($this->TraineeInstallments->save($traineeInstallment)) {
                    if ($data['is_paid_off']) {
                        $this->Flash->success(__('Balance settled - the receipt has been issued.'));

                        // Difference reached 0: hand the user the receipt straight away
                        return $this->redirect(['action' => 'receipts']);
                    }
                    $this->Flash->success(__('Payment saved. Remaining balance: Rp {0}', number_format($unpaid, 0, ',', '.')));

                    return $this->redirect(['action' => 'tracking']);
                }
                $this->Flash->error(__('The trainee installment could not be saved. Please, try again.'));
            }
        }

        // Trainee options with TMM code + running status per trainee for the live preview
        $trainees = [];
        $traineeStatus = [];
        try {
            foreach (\Cake\ORM\TableRegistry::getTableLocator()->get('Trainees')
                ->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
                $trainees[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_accountings');
            foreach ($conn->execute(
                'SELECT t.trainee_id, t.payment_accummulated, t.full_payment_amount, t.unpaid_amount, t.is_paid_off
                 FROM trainee_installments t
                 JOIN (SELECT trainee_id, MAX(id) AS max_id FROM trainee_installments GROUP BY trainee_id) latest
                   ON latest.max_id = t.id'
            )->fetchAll('assoc') as $row) {
                $traineeStatus[$row['trainee_id']] = [
                    'accumulated' => (int)$row['payment_accummulated'],
                    'full' => (int)$row['full_payment_amount'],
                    'unpaid' => (int)$row['unpaid_amount'],
                    'is_paid_off' => (bool)$row['is_paid_off'],
                ];
            }
        } catch (\Exception $e) {
        }

        $masterTransactionCategories = $this->TraineeInstallments->MasterTransactionCategories->find('list', ['limit' => 200]);
        $masterCurrencies = $this->TraineeInstallments->MasterCurrencies->find('list', ['limit' => 200]);
        $this->set(compact('traineeInstallment', 'trainees', 'traineeStatus', 'masterTransactionCategories', 'masterCurrencies'));
    }

    /**
     * Set Owing Cost - step 1 of the payment flow. Creates the opening
     * installment record (zero payment) that fixes the trainee's total to
     * pay, so payments can be recorded afterwards.
     */
    public function setOwingCost()
    {
        $this->request->allowMethod(['post']);
        $traineeId = (int)$this->request->getData('trainee_id');
        $amount = (int)$this->request->getData('full_payment_amount');
        $existing = $traineeId
            ? $this->TraineeInstallments->find()->where(['trainee_id' => $traineeId])->count()
            : 0;

        if (!$traineeId || $amount <= 0) {
            $this->Flash->error(__('Please choose a trainee and enter an owing cost greater than zero.'));
        } elseif ($existing) {
            $this->Flash->error(__('This trainee already has an owing cost set.'));
        } else {
            $opening = $this->TraineeInstallments->newEntity([
                'trainee_id' => $traineeId,
                'master_transaction_category_id' => 1, // Debit (receivable opening)
                'payment_amount' => 0,
                'payment_date' => date('Y-m-d'),
                'full_payment_amount' => $amount,
                'payment_accummulated' => 0,
                'unpaid_amount' => $amount,
                'master_currency_id' => 66,
                'is_paid_off' => 0,
            ]);
            if ($this->TraineeInstallments->save($opening)) {
                $this->Flash->success(__('Owing cost set: Rp {0}. Payments can now be recorded.',
                    number_format($amount, 0, ',', '.')));
            } else {
                $this->Flash->error(__('The owing cost could not be saved. Please, try again.'));
            }
        }

        return $this->redirect(['action' => 'tracking']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Trainee Installment id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $traineeInstallment = $this->TraineeInstallments->get($id, [
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
                $imagePath = $this->uploadImage('TraineeInstallments', $fieldName, 'traineeinstallments');
                if ($imagePath) {
                    $data[$fieldName] = $imagePath;
                } else {
                    // Keep existing value if upload failed
                    unset($data[$fieldName]);
                }
            }
            
            // Upload files (documents, etc)
            foreach ($fileFields as $fieldName) {
                $success = $this->uploadFile('TraineeInstallments', $fieldName, 'traineeinstallments');
                if ($success) {
                    $data = $this->request->getData(); // Get updated data after upload
                } else {
                    unset($data[$fieldName]); // Keep existing value
                }
            }
            
            $traineeInstallment = $this->TraineeInstallments->patchEntity($traineeInstallment, $data);
            if ($this->TraineeInstallments->save($traineeInstallment)) {
                $this->Flash->success(__('The trainee installment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The trainee installment could not be saved. Please, try again.'));
        }
        $trainees = $this->TraineeInstallments->Trainees->find('list', ['limit' => 200]);
        $masterTransactionCategories = $this->TraineeInstallments->MasterTransactionCategories->find('list', ['limit' => 200]);
        $masterCurrencies = $this->TraineeInstallments->MasterCurrencies->find('list', ['limit' => 200]);
        $this->set(compact('traineeInstallment', 'trainees', 'masterTransactionCategories', 'masterCurrencies'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Trainee Installment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $traineeInstallment = $this->TraineeInstallments->get($id);
        if ($this->TraineeInstallments->delete($traineeInstallment)) {
            $this->Flash->success(__('The trainee installment has been deleted.'));
        } else {
            $this->Flash->error(__('The trainee installment could not be deleted. Please, try again.'));
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
        $query = $this->TraineeInstallments->find('all')
            ->contain(['Trainees', 'MasterTransactionCategories', 'MasterCurrencies']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportCsv($query, 'TraineeInstallments', $headers, $fields);
    }
    /**
     * Export to Excel
     *
     * @return \Cake\Http\Response
     */
    public function exportExcel()
    {
        $query = $this->TraineeInstallments->find('all')
            ->contain(['Trainees', 'MasterTransactionCategories', 'MasterCurrencies']);
        
        // Define headers and fields for export
        $headers = ['ID', 'Name', 'Created', 'Modified'];
        $fields = ['id', 'name', 'created', 'modified'];
        
        return $this->doExportExcel($query, 'TraineeInstallments', $headers, $fields);
    }
    /**
     * Print Report
     *
     * @return \Cake\Http\Response|null
     */
    public function printReport()
    {
        $this->layout = 'print';
        $this->viewBuilder()->setLayout('print');

        // A specific installment id -> print a single receipt / payment voucher.
        $id = (int)($this->request->getQuery('id') ?: $this->request->getParam('pass.0'));
        if ($id) {
            $installment = $this->TraineeInstallments->get($id, [
                'contain' => ['Trainees', 'MasterTransactionCategories', 'MasterCurrencies'],
            ]);

            // Running figures for this trainee (in case a later payment changed them)
            $trainee = $installment->has('trainee') ? $installment->trainee : null;
            $this->set(compact('installment', 'trainee'));
            $this->viewBuilder()->setTemplate('receipt');

            return $this->render();
        }

        // No id -> a proper full installment list (corrected columns).
        $query = $this->TraineeInstallments->find('all')
            ->contain(['Trainees', 'MasterTransactionCategories', 'MasterCurrencies'])
            ->order(['TraineeInstallments.id' => 'DESC']);

        $title = 'Trainee Installments Report';
        $headers = ['ID', 'Trainee', 'Payment', 'Date', 'Accumulated', 'Remaining', 'Status'];
        $fields = ['id', 'trainee.name', 'payment_amount', 'payment_date',
            'payment_accummulated', 'unpaid_amount', 'is_paid_off'];

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
        
        $this->viewBuilder()->setLayout('process_flow');
    }

    /**
     * Payment tracking - all installments with paid status
     */
    public function tracking()
    {
        $locator = \Cake\ORM\TableRegistry::getTableLocator();

        // Optional filters
        $filterTrainee = (int)$this->request->getQuery('trainee_id');
        $filterPaid = $this->request->getQuery('paid'); // '' | '1' | '0'

        $query = $this->TraineeInstallments->find();
        if ($filterTrainee) {
            $query->where(['trainee_id' => $filterTrainee]);
        }
        if ($filterPaid === '1' || $filterPaid === '0') {
            $query->where(['is_paid_off' => (int)$filterPaid]);
        }

        $this->paginate = ['order' => ['TraineeInstallments.id' => 'DESC']];
        $installments = $this->paginate($query);

        // Lookup maps
        $traineeNames = [];
        try {
            foreach ($locator->get('Trainees')->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
                $traineeNames[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }
        $categoryNames = [];
        try {
            $categoryNames = $locator->get('MasterTransactionCategories')->find('list')->toArray();
        } catch (\Exception $e) {
        }
        $currencyNames = [];
        try {
            $currencyNames = $locator->get('MasterCurrencies')->find()
                ->enableHydration(false)->all()->combine('id', 'title')->toArray();
        } catch (\Exception $e) {
        }

        // Per-trainee payment progress: latest installment row per trainee carries
        // the running accumulated / unpaid figures.
        $paymentProgress = [];
        $summary = ['payingTrainees' => 0, 'paidOff' => 0, 'collected' => 0, 'outstanding' => 0];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_accountings');
            $rows = $conn->execute(
                'SELECT t.trainee_id, t.payment_accummulated, t.full_payment_amount,
                        t.unpaid_amount, t.is_paid_off, t.payment_date, t.master_currency_id
                 FROM trainee_installments t
                 JOIN (SELECT trainee_id, MAX(id) AS max_id
                       FROM trainee_installments GROUP BY trainee_id) latest
                   ON latest.max_id = t.id
                 ORDER BY t.is_paid_off ASC, t.unpaid_amount DESC'
            )->fetchAll('assoc');
            foreach ($rows as $row) {
                $full = (int)$row['full_payment_amount'];
                $accumulated = (int)$row['payment_accummulated'];
                $paymentProgress[] = [
                    'trainee_id' => $row['trainee_id'],
                    'name' => isset($traineeNames[$row['trainee_id']]) ? $traineeNames[$row['trainee_id']] : ('#' . $row['trainee_id']),
                    'accumulated' => $accumulated,
                    'full' => $full,
                    'unpaid' => (int)$row['unpaid_amount'],
                    'is_paid_off' => (bool)$row['is_paid_off'],
                    'last_payment' => $row['payment_date'],
                    'currency' => isset($currencyNames[$row['master_currency_id']]) ? $currencyNames[$row['master_currency_id']] : 'IDR',
                    'percent' => $full > 0 ? min(100, (int)round($accumulated / $full * 100)) : 0,
                ];
                $summary['payingTrainees']++;
                $summary['collected'] += $accumulated;
                $summary['outstanding'] += (int)$row['unpaid_amount'];
                if ($row['is_paid_off']) {
                    $summary['paidOff']++;
                }
            }
        } catch (\Exception $e) {
        }

        // Trainees who have no owing cost set yet (step 1 of the flow)
        $traineesWithoutCost = array_diff_key($traineeNames,
            array_flip(array_column($paymentProgress, 'trainee_id')));

        $this->set(compact('installments', 'traineeNames', 'categoryNames', 'currencyNames',
            'paymentProgress', 'summary', 'filterTrainee', 'filterPaid', 'traineesWithoutCost'));
    }

    /**
     * Receipts - fully paid installments
     */
    public function receipts()
    {
        $this->paginate = ['order' => ['TraineeInstallments.id' => 'DESC']];
        $installments = $this->paginate($this->TraineeInstallments->find()->where(['TraineeInstallments.is_paid_off' => 1]));

        // Names + summary of settled amounts
        $traineeNames = [];
        try {
            foreach (\Cake\ORM\TableRegistry::getTableLocator()->get('Trainees')
                ->find()->select(['id', 'name', 'tmm_code'])->order(['name' => 'ASC']) as $trainee) {
                $traineeNames[$trainee->id] = $trainee->name . ($trainee->tmm_code ? ' (' . $trainee->tmm_code . ')' : '');
            }
        } catch (\Exception $e) {
        }
        $categoryNames = [];
        $currencyNames = [];
        $summary = ['receipts' => 0, 'traineesSettled' => 0, 'totalSettled' => 0];
        try {
            $categoryNames = $this->TraineeInstallments->MasterTransactionCategories->find('list')->toArray();
            $currencyNames = \Cake\ORM\TableRegistry::getTableLocator()->get('MasterCurrencies')
                ->find()->enableHydration(false)->all()->combine('id', 'title')->toArray();
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_accountings');
            $row = $conn->execute(
                'SELECT COUNT(*) AS receipts, COUNT(DISTINCT trainee_id) AS trainees,
                        COALESCE(SUM(CASE WHEN unpaid_amount = 0 THEN 0 ELSE 0 END), 0) AS zero_col
                 FROM trainee_installments WHERE is_paid_off = 1'
            )->fetch('assoc');
            $summary['receipts'] = (int)$row['receipts'];
            $summary['traineesSettled'] = (int)$row['trainees'];
            $settled = $conn->execute(
                'SELECT COALESCE(SUM(t.full_payment_amount), 0) AS total
                 FROM trainee_installments t
                 JOIN (SELECT trainee_id, MAX(id) AS max_id FROM trainee_installments
                       WHERE is_paid_off = 1 GROUP BY trainee_id) latest ON latest.max_id = t.id'
            )->fetch('assoc');
            $summary['totalSettled'] = (int)$settled['total'];
        } catch (\Exception $e) {
        }

        $this->set(compact('installments', 'traineeNames', 'categoryNames', 'currencyNames', 'summary'));
    }
}

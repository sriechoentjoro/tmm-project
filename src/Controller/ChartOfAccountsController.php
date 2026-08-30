<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ChartOfAccounts Controller
 *
 * @property \App\Model\Table\ChartOfAccountsTable $ChartOfAccounts
 */
class ChartOfAccountsController extends AppController
{
    public function index()
    {
        // Optional type filter
        $filterType = (string)$this->request->getQuery('type');
        $validTypes = ['Asset', 'Liability', 'Equity', 'Revenue', 'Expense'];
        if (!in_array($filterType, $validTypes, true)) {
            $filterType = '';
        }

        $query = $this->ChartOfAccounts->find();
        if ($filterType) {
            $query->where(['type' => $filterType]);
        }

        $this->paginate = ['order' => ['ChartOfAccounts.code' => 'ASC']];
        $chartOfAccounts = $this->paginate($query);

        // Counts by type + usage (how many journal lines reference each account)
        $countsByType = [];
        $usageByAccount = [];
        try {
            $conn = \Cake\Datasource\ConnectionManager::get('cms_tmm_trainee_accountings');
            foreach ($conn->execute(
                'SELECT type, COUNT(*) AS total FROM chart_of_accounts GROUP BY type'
            )->fetchAll('assoc') as $row) {
                $countsByType[$row['type']] = (int)$row['total'];
            }
            foreach ($conn->execute(
                'SELECT chart_of_account_id, COUNT(*) AS total FROM journal_details GROUP BY chart_of_account_id'
            )->fetchAll('assoc') as $row) {
                $usageByAccount[$row['chart_of_account_id']] = (int)$row['total'];
            }
        } catch (\Exception $e) {
        }

        $this->set(compact('chartOfAccounts', 'countsByType', 'usageByAccount', 'filterType'));
    }

    public function view($id = null)
    {
        $chartOfAccount = $this->ChartOfAccounts->get($id);
        $this->set('chartOfAccount', $chartOfAccount);
    }

    public function add()
    {
        $chartOfAccount = $this->ChartOfAccounts->newEntity();
        if ($this->request->is('post')) {
            $chartOfAccount = $this->ChartOfAccounts->patchEntity($chartOfAccount, $this->request->getData());
            if ($this->ChartOfAccounts->save($chartOfAccount)) {
                $this->Flash->success(__('The Chart Of Account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Chart Of Account could not be saved. Please, try again.'));
        }
        $this->set('chartOfAccount', $chartOfAccount);
    }

    public function edit($id = null)
    {
        $chartOfAccount = $this->ChartOfAccounts->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $chartOfAccount = $this->ChartOfAccounts->patchEntity($chartOfAccount, $this->request->getData());
            if ($this->ChartOfAccounts->save($chartOfAccount)) {
                $this->Flash->success(__('The Chart Of Account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Chart Of Account could not be saved. Please, try again.'));
        }
        $this->set('chartOfAccount', $chartOfAccount);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $chartOfAccount = $this->ChartOfAccounts->get($id);
        if ($this->ChartOfAccounts->delete($chartOfAccount)) {
            $this->Flash->success(__('The Chart Of Account has been deleted.'));
        } else {
            $this->Flash->error(__('The Chart Of Account could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Account hierarchy grouped by account type
     */
    public function hierarchy()
    {
        $accounts = $this->ChartOfAccounts->find()
            ->order(['type' => 'ASC', 'code' => 'ASC'])
            ->enableHydration(false)
            ->toArray();

        // Saldo dari tabel terasosiasi journal_details (jumlah debit/kredit per akun)
        $balances = [];
        foreach ($this->ChartOfAccounts->getConnection()->execute(
            'SELECT chart_of_account_id AS aid, SUM(debit) AS debit, SUM(credit) AS credit, COUNT(*) AS line_count
             FROM journal_details GROUP BY chart_of_account_id'
        )->fetchAll('assoc') as $r) {
            $balances[$r['aid']] = $r;
        }

        $grouped = [];
        $typeTotals = [];
        foreach ($accounts as $account) {
            $type = $account['type'] ?: '(untyped)';
            $bal = $balances[$account['id']] ?? ['debit' => 0, 'credit' => 0, 'line_count' => 0];
            $account['_debit'] = (float)$bal['debit'];
            $account['_credit'] = (float)$bal['credit'];
            $account['_lines'] = (int)$bal['line_count'];
            // Saldo normal: Asset/Expense = debit - credit; lainnya = credit - debit
            $account['_balance'] = in_array($type, ['Asset', 'Expense'], true)
                ? $account['_debit'] - $account['_credit']
                : $account['_credit'] - $account['_debit'];
            $grouped[$type][] = $account;
            $typeTotals[$type] = ($typeTotals[$type] ?? 0) + $account['_balance'];
        }

        $this->set(compact('grouped', 'typeTotals'));
    }
}

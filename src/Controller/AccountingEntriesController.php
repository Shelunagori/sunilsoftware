<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AccountingEntries Controller
 *
 * @property \App\Model\Table\AccountingEntriesTable $AccountingEntries
 *
 * @method \App\Model\Entity\AccountingEntry[] paginate($object = null, array $settings = [])
 */
class AccountingEntriesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Ledgers', 'Companies', 'PurchaseVouchers', 'SalesInvoices', 'SaleReturns', 'SalesVouchers', 'JournalVouchers']
        ];
        $accountingEntries = $this->paginate($this->AccountingEntries);

        $this->set(compact('accountingEntries'));
        $this->set('_serialize', ['accountingEntries']);
    }

	public function ProfitLossStatement()
    {
		$this->viewBuilder()->layout('index_layout');
        $company_id=$this->Auth->User('session_company_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$from_date = date("Y-m-d",strtotime($from_date));
		$to_date= date("Y-m-d",strtotime($to_date));
			$this->set(compact('from_date','to_date'));
		
		
	if($from_date){
		$query=$this->AccountingEntries->find();
		 
		$Ledgers_Expense=$query->select(['total_debit' => $query->func()->sum('debit'),'total_credit' => $query->func()->sum('credit')])
			->matching('Ledgers.AccountingGroups', function ($q) {
				return $q->where(['AccountingGroups.nature_of_group_id' => 4]);
			})
			->where(['AccountingEntries.company_id'=>$company_id])
			->contain(['Ledgers'])
			->group(['AccountingEntries.ledger_id'])
			->autoFields(true);
		//pr($Ledgers_Expense->toArray());
		//exit;
		$Expense_groups=[];
			foreach($Ledgers_Expense as $Ledgers_Expense){
				$Expense_groups[$Ledgers_Expense->_matchingData['AccountingGroups']->id]['group_id']
					=$Ledgers_Expense->_matchingData['AccountingGroups']->id;
				$Expense_groups[$Ledgers_Expense->_matchingData['AccountingGroups']->id]['debit']
					=@$Expense_groups[$Ledgers_Expense->_matchingData['AccountingGroups']->id]['debit']+($Ledgers_Expense->total_debit);
				$Expense_groups[$Ledgers_Expense->_matchingData['AccountingGroups']->id]['credit']
					=@$Expense_groups[$Ledgers_Expense->_matchingData['AccountingGroups']->id]['credit']+($Ledgers_Expense->total_credit);
				$Expense_groups[$Ledgers_Expense->_matchingData['AccountingGroups']->id]['name']
					=$Ledgers_Expense->_matchingData['AccountingGroups']->name;
				}
		pr($Expense_groups);
		echo '</br>';
		$query2=$this->AccountingEntries->find();
			$Ledgers_Income=$query2->select(['total_debit' => $query2->func()->sum('debit'),'total_credit' => $query2->func()->sum('credit')])
			->matching('Ledgers.AccountingGroups', function ($q) {
				return $q->where(['AccountingGroups.nature_of_group_id' => 3]);
			})
			->where(['AccountingEntries.company_id'=>$company_id])
			->contain(['Ledgers'])
			->group(['AccountingEntries.ledger_id'])
			->autoFields(true);
			
			$Income_groups=[];
			foreach($Ledgers_Income as $Ledgers_Income){ //pr($Ledgers_Liablitie->total_credit);
				$Income_groups[$Ledgers_Income->_matchingData['AccountingGroups']->id]['group_id']
					=$Ledgers_Income->_matchingData['AccountingGroups']->id;
				$Income_groups[$Ledgers_Income->_matchingData['AccountingGroups']->id]['debit']
					=@$Income_groups[$Ledgers_Income->_matchingData['AccountingGroups']->id]['debit']+($Ledgers_Income->total_debit);
				$Income_groups[$Ledgers_Income->_matchingData['AccountingGroups']->id]['credit']
					=@$Income_groups[$Ledgers_Income->_matchingData['AccountingGroups']->id]['credit']+($Ledgers_Income->total_credit);
				$Income_groups[$Ledgers_Income->_matchingData['AccountingGroups']->id]['name']
					=$Ledgers_Income->_matchingData['AccountingGroups']->name;
			}
			pr($Income_groups);
		echo '</br>';
		//exit;
	}	
		
		
        $this->set(compact('accountingEntries','Income_groups','Expense_groups'));
        $this->set('_serialize', ['accountingEntries']);
    }
    /**
     * View method
     *
     * @param string|null $id Accounting Entry id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $accountingEntry = $this->AccountingEntries->get($id, [
            'contain' => ['Ledgers', 'Companies', 'PurchaseVouchers', 'SalesInvoices', 'SaleReturns', 'SalesVouchers', 'JournalVouchers']
        ]);

        $this->set('accountingEntry', $accountingEntry);
        $this->set('_serialize', ['accountingEntry']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $accountingEntry = $this->AccountingEntries->newEntity();
        if ($this->request->is('post')) {
            $accountingEntry = $this->AccountingEntries->patchEntity($accountingEntry, $this->request->getData());
            if ($this->AccountingEntries->save($accountingEntry)) {
                $this->Flash->success(__('The accounting entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accounting entry could not be saved. Please, try again.'));
        }
        $ledgers = $this->AccountingEntries->Ledgers->find('list', ['limit' => 200]);
        $companies = $this->AccountingEntries->Companies->find('list', ['limit' => 200]);
        $purchaseVouchers = $this->AccountingEntries->PurchaseVouchers->find('list', ['limit' => 200]);
        $salesInvoices = $this->AccountingEntries->SalesInvoices->find('list', ['limit' => 200]);
        $saleReturns = $this->AccountingEntries->SaleReturns->find('list', ['limit' => 200]);
        $salesVouchers = $this->AccountingEntries->SalesVouchers->find('list', ['limit' => 200]);
        $journalVouchers = $this->AccountingEntries->JournalVouchers->find('list', ['limit' => 200]);
        $this->set(compact('accountingEntry', 'ledgers', 'companies', 'purchaseVouchers', 'salesInvoices', 'saleReturns', 'salesVouchers', 'journalVouchers'));
        $this->set('_serialize', ['accountingEntry']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Accounting Entry id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $accountingEntry = $this->AccountingEntries->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accountingEntry = $this->AccountingEntries->patchEntity($accountingEntry, $this->request->getData());
            if ($this->AccountingEntries->save($accountingEntry)) {
                $this->Flash->success(__('The accounting entry has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accounting entry could not be saved. Please, try again.'));
        }
        $ledgers = $this->AccountingEntries->Ledgers->find('list', ['limit' => 200]);
        $companies = $this->AccountingEntries->Companies->find('list', ['limit' => 200]);
        $purchaseVouchers = $this->AccountingEntries->PurchaseVouchers->find('list', ['limit' => 200]);
        $salesInvoices = $this->AccountingEntries->SalesInvoices->find('list', ['limit' => 200]);
        $saleReturns = $this->AccountingEntries->SaleReturns->find('list', ['limit' => 200]);
        $salesVouchers = $this->AccountingEntries->SalesVouchers->find('list', ['limit' => 200]);
        $journalVouchers = $this->AccountingEntries->JournalVouchers->find('list', ['limit' => 200]);
        $this->set(compact('accountingEntry', 'ledgers', 'companies', 'purchaseVouchers', 'salesInvoices', 'saleReturns', 'salesVouchers', 'journalVouchers'));
        $this->set('_serialize', ['accountingEntry']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Accounting Entry id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accountingEntry = $this->AccountingEntries->get($id);
        if ($this->AccountingEntries->delete($accountingEntry)) {
            $this->Flash->success(__('The accounting entry has been deleted.'));
        } else {
            $this->Flash->error(__('The accounting entry could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Ledgers Controller
 *
 * @property \App\Model\Table\LedgersTable $Ledgers
 *
 * @method \App\Model\Entity\Ledger[] paginate($object = null, array $settings = [])
 */
class LedgersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
        $this->paginate = [
            'contain' => ['AccountingGroups', 'Companies']
        ];
        $ledgers = $this->paginate($this->Ledgers->find()->where(['Ledgers.company_id'=>$company_id]));
        //pr($ledgers->toArray());exit;
        $this->set(compact('ledgers'));
        $this->set('_serialize', ['ledgers']);
    }

    /**
     * View method
     *
     * @param string|null $id Ledger id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $ledger = $this->Ledgers->get($id, [
            'contain' => ['AccountingGroups', 'Suppliers', 'Customers', 'AccountingEntries']
        ]);

        $this->set('ledger', $ledger);
        $this->set('_serialize', ['ledger']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
        $ledger = $this->Ledgers->newEntity();
		$company_id=$this->Auth->User('session_company_id');
        if ($this->request->is('post')) 
		{
            $ledger = $this->Ledgers->patchEntity($ledger, $this->request->getData());
			$ledger->company_id = $company_id;
            if ($this->Ledgers->save($ledger)) 
			{
				//Create Accounting Entry//
				$transaction_date=$this->Auth->User('session_company')->books_beginning_from;
				$AccountingEntry = $this->Ledgers->AccountingEntries->newEntity();
				$AccountingEntry->ledger_id = $ledger->id;
				if($ledger->debit_credit=="Dr")
				{
					$AccountingEntry->debit = $ledger->opening_balance_value;
				}
				if($ledger->debit_credit=="Cr")
				{
					$AccountingEntry->credit = $ledger->opening_balance_value;
				}
				$AccountingEntry->transaction_date      = date("Y-m-d",strtotime($transaction_date));
				$AccountingEntry->company_id            = $company_id;
				$AccountingEntry->is_opening_balance    = 'yes';
				$this->Ledgers->AccountingEntries->save($AccountingEntry);
				
                $this->Flash->success(__('The ledger has been saved.'));

                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('The ledger could not be saved. Please, try again.'));
        }
		$SundryDebtor = $this->Ledgers->AccountingGroups->find('all')->where(['customer'=>1,'company_id'=>$company_id])->first();
		$accountingGroupdebitors = $this->Ledgers->AccountingGroups
							->find('children', ['for' => $SundryDebtor->id])
							->find('all');
		$debtorArray=[];
		foreach($accountingGroupdebitors as $accountingGroupdebitor)
		{ 
			$debtorArray[]= $accountingGroupdebitor->id;
		}
		$datadebtor[]=$SundryDebtor->id;
		$SundryCredior = $this->Ledgers->AccountingGroups->find('all')->where(['supplier'=>1,'company_id'=>$company_id])->first();
		$accountingGroupcreditors = $this->Ledgers->AccountingGroups
							->find('children', ['for' => $SundryCredior->id])
							->find('all');
		$creditorArray=[];
		foreach($accountingGroupcreditors as $accountingGroupcreditor)
		{ 
			$creditorArray[]= $accountingGroupcreditor->id;
		}
		$datacreditor[]=$SundryCredior->id;
		$alldebtors=array_merge($datadebtor,$debtorArray,$datacreditor,$creditorArray);
		$accountingGroups = $this->Ledgers->AccountingGroups->find('list')->where(['id NOT IN'=>$alldebtors,'company_id'=>$company_id]);
		
			
        $suppliers = $this->Ledgers->Suppliers->find('list')->where(['company_id'=>$company_id]);
        $customers = $this->Ledgers->Customers->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('ledger', 'accountingGroups',  'suppliers', 'customers'));
        $this->set('_serialize', ['ledger']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Ledger id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $ledger = $this->Ledgers->get($id, [
            'contain' => ['AccountingEntries'=>function($q){
				return $q->where(['AccountingEntries.is_opening_balance'=>'yes']);
			}],
        ]);
		
		$company_id=$this->Auth->User('session_company_id');
        if ($this->request->is(['patch', 'post', 'put'])) 
		{
            $ledger = $this->Ledgers->patchEntity($ledger, $this->request->getData());
            if ($this->Ledgers->save($ledger)) 
			{
				//Accounting Entry
				$query_delete = $this->Ledgers->AccountingEntries->query();
				$query_delete->delete()
				->where(['ledger_id' => $ledger->id,'company_id'=>$company_id,'is_opening_balance'=>'yes'])
				->execute();
				
				$transaction_date=$this->Auth->User('session_company')->books_beginning_from;
				$AccountingEntry = $this->Ledgers->AccountingEntries->newEntity();
				$AccountingEntry->ledger_id = $ledger->id;
				if($ledger->debit_credit=="Dr")
				{
					$AccountingEntry->debit = $ledger->opening_balance_value;
				}
				if($ledger->debit_credit=="Cr")
				{
					$AccountingEntry->credit = $ledger->opening_balance_value;
				}
				$AccountingEntry->transaction_date      = date("Y-m-d",strtotime($transaction_date));
				$AccountingEntry->company_id            = $company_id;
				$AccountingEntry->is_opening_balance    = 'yes';
				$this->Ledgers->AccountingEntries->save($AccountingEntry);
				
                $this->Flash->success(__('The ledger has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The ledger could not be saved. Please, try again.'));
        }
        $SundryDebtor = $this->Ledgers->AccountingGroups->find('all')->where(['customer'=>1,'company_id'=>$company_id])->first();
		$accountingGroupdebitors = $this->Ledgers->AccountingGroups
							->find('children', ['for' => $SundryDebtor->id])
							->find('all');
		$debtorArray=[];
		foreach($accountingGroupdebitors as $accountingGroupdebitor)
		{ 
			$debtorArray[]= $accountingGroupdebitor->id;
		}
		$datadebtor[]=$SundryDebtor->id;
		$SundryCredior = $this->Ledgers->AccountingGroups->find('all')->where(['supplier'=>1,'company_id'=>$company_id])->first();
		$accountingGroupcreditors = $this->Ledgers->AccountingGroups
							->find('children', ['for' => $SundryCredior->id])
							->find('all');
		$creditorArray=[];
		foreach($accountingGroupcreditors as $accountingGroupcreditor)
		{ 
			$creditorArray[]= $accountingGroupcreditor->id;
		}
		$datacreditor[]=$SundryCredior->id;
		$alldebtors=array_merge($datadebtor,$debtorArray,$datacreditor,$creditorArray);
		$accountingGroups = $this->Ledgers->AccountingGroups->find('list')->where(['id NOT IN'=>$alldebtors,'company_id'=>$company_id]);
		$suppliers = $this->Ledgers->Suppliers->find('list')->where(['company_id'=>$company_id]);
        $customers = $this->Ledgers->Customers->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('ledger', 'accountingGroups', 'suppliers', 'customers'));
        $this->set('_serialize', ['ledger']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Ledger id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
       
		$this->request->allowMethod(['post', 'delete']);
        $ledger = $this->Ledgers->get($id);
        if ($this->Ledgers->delete($ledger)) 
		{
            $this->Flash->success(__('The ledger has been deleted.'));
        } 
		else
		{
            $this->Flash->error(__('The ledger could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function trialBalance($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$ledger    = $this->Ledgers->newEntity();
		$company_id=$this->Auth->User('session_company_id');
		
		$from_date = $this->request->query('from_date');
		 $to_date   = $this->request->query('to_date');
		//exit;
		if(!empty($from_date) || !empty($to_date))
		{
			$from_date = date("Y-m-d",strtotime($from_date));
			$to_date   = date("Y-m-d",strtotime($to_date));
		}
		else
		{ 
			 $from_date = date("Y-m-d",strtotime($this->coreVariable['fyValidFrom']));
			 $toDate    = $this->Ledgers->AccountingEntries->find()->order(['AccountingEntries.transaction_date'=>'DESC'])->First();
			@$to_date   = date("Y-m-d",strtotime($toDate->transaction_date));
		}
		//if($toDate){
			$query = $this->Ledgers->AccountingEntries->find();
				$CaseDebitOpeningBalance = $query->newExpr()
					->addCase(
						$query->newExpr()->add(['transaction_date <'=>$from_date]),
						$query->newExpr()->add(['debit']),
						'decimal'
					);
				$CaseCreditOpeningBalance = $query->newExpr()
					->addCase(
						$query->newExpr()->add(['transaction_date <'=>$from_date]),
						$query->newExpr()->add(['credit']),
						'decimal'
					);
				$CaseDebitTransaction = $query->newExpr()
					->addCase(
						$query->newExpr()->add(['transaction_date >='=>$from_date,'transaction_date <='=>$to_date]),
						$query->newExpr()->add(['debit']),
						'decimal'
					);
				$CaseCreditTransaction = $query->newExpr()
					->addCase(
						$query->newExpr()->add(['transaction_date >='=>$from_date,'transaction_date <='=>$to_date]),
						$query->newExpr()->add(['credit']),
						'decimal'
					);
				$query->select([
					'debit_opening_balance' => $query->func()->sum($CaseDebitOpeningBalance),
					'credit_opening_balance' => $query->func()->sum($CaseCreditOpeningBalance),
					'debit_transaction' => $query->func()->sum($CaseDebitTransaction),
					'credit_transaction' => $query->func()->sum($CaseCreditTransaction),'id','ledger_id'
				])
				->where(['AccountingEntries.company_id'=>$company_id])
				->group('ledger_id')
				->autoFields(true)
				->contain(['Ledgers']);
				$TrialBalances = ($query);
			
				
				$debitAmount = $this->Ledgers->Companies->ItemLedgers->find();
				$debitAmount->select(['total_debit' => $debitAmount->func()->sum('ItemLedgers.amount')])
							->where(['ItemLedgers.is_opening_balance'=> 'yes','company_id' => $company_id]);
				
				$totalDebit  = $debitAmount->first()->total_debit;
			//pr($query->toArray()); exit;
		
		
		
		$this->set(compact('ledger','from_date','to_date','TrialBalances','totalDebit'));
        $this->set('_serialize', ['ledger']);
    }
	
	public function trialBalance1($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$ledger    = $this->Ledgers->newEntity();
		$company_id=$this->Auth->User('session_company_id');
		
		$from_date = $this->request->query('from_date');
		$to_date   = $this->request->query('to_date');
		if(!empty($from_date) || !empty($to_date))
		{
			$from_date = date("Y-m-d",strtotime($from_date));
			$to_date   = date("Y-m-d",strtotime($to_date));
		}
		
		$query = $this->Ledgers->AccountingEntries->find();
			$CaseDebitOpeningBalance = $query->newExpr()
				->addCase(
					$query->newExpr()->add(['transaction_date <'=>$from_date]),
					$query->newExpr()->add(['debit']),
					'decimal'
				);
			$CaseCreditOpeningBalance = $query->newExpr()
				->addCase(
					$query->newExpr()->add(['transaction_date <'=>$from_date]),
					$query->newExpr()->add(['credit']),
					'decimal'
				);
			$CaseDebitTransaction = $query->newExpr()
				->addCase(
					$query->newExpr()->add(['transaction_date >='=>$from_date,'transaction_date <='=>$to_date]),
					$query->newExpr()->add(['debit']),
					'decimal'
				);
			$CaseCreditTransaction = $query->newExpr()
				->addCase(
					$query->newExpr()->add(['transaction_date >='=>$from_date,'transaction_date <='=>$to_date]),
					$query->newExpr()->add(['credit']),
					'decimal'
				);
			$query->select([
				'debit_opening_balance' => $query->func()->sum($CaseDebitOpeningBalance),
				'credit_opening_balance' => $query->func()->sum($CaseCreditOpeningBalance),
				'debit_transaction' => $query->func()->sum($CaseDebitTransaction),
				'credit_transaction' => $query->func()->sum($CaseCreditTransaction),'id','ledger_id'
			])
			->where(['AccountingEntries.company_id'=>$company_id])
			->group('ParentAccountingGroups.id')
			->autoFields(true)
			->contain(['Ledgers'=>['AccountingGroups'=> function($q){
								return $q->find('children', ['for' => 1])->find('threaded')->contain(['ParentAccountingGroups']);
							}]]);
			$TrialBalances = ($query);
			
		$this->set(compact('ledger','from_date','to_date','TrialBalances'));
        $this->set('_serialize', ['ledger']);
	}
	
	public function accountLedger($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$accountLedger     = $this->Ledgers->newEntity();
		$company_id        = $this->Auth->User('session_company_id');
		
		$ledger_id         = $this->request->query('ledger_id');
		$from_date         = $this->request->query('from_date');
		$to_date           = $this->request->query('to_date');
		$where =[];
		if(!empty($from_date))
		{
			$from_date = date("Y-m-d",strtotime($from_date));
			$where['AccountingEntries.transaction_date >=']=$from_date;
		}
		if(!empty($to_date))
		{
			$to_date   = date("Y-m-d",strtotime($to_date));
			$where['AccountingEntries.transaction_date <=']=$to_date;
		}
		if(!empty($ledger_id))
		{
			$where['AccountingEntries.ledger_id']=$ledger_id;
		}

		if(!empty($ledger_id)){
		$query = $this->Ledgers->AccountingEntries->find();
		$CaseCreditOpeningBalance = $query->newExpr()
					->addCase(
						$query->newExpr()->add(['ledger_id']),
						$query->newExpr()->add(['credit']),
						'decimal'
					);
		$CaseDebitOpeningBalance = $query->newExpr()
					->addCase(
						$query->newExpr()->add(['ledger_id']),
						$query->newExpr()->add(['debit']),
						'decimal'
					);
		$query->select([
				'debit_opening_balance' => $query->func()->sum($CaseDebitOpeningBalance),
				'credit_opening_balance' => $query->func()->sum($CaseCreditOpeningBalance),
				'id','ledger_id'
			])
			->where(['AccountingEntries.company_id'=>$company_id,'AccountingEntries.transaction_date <'=>$from_date,'AccountingEntries.ledger_id'=>$ledger_id])
			->group('ledger_id')
			->autoFields(true);
		$AccountLedgersOpeningBalance=($query);
		$total_debit=0;
		$total_credit=0;
		foreach($AccountLedgersOpeningBalance as $AccountLedgersOpeningBalance){
			$total_debit=$AccountLedgersOpeningBalance->debit_opening_balance;
			$total_credit=$AccountLedgersOpeningBalance->credit_opening_balance;
		}
		@$opening_balance=$total_debit-$total_credit;
			if($opening_balance>0){
			@$opening_balance_type='Dr';	
			}
			else if($opening_balance<0){
			$opening_balance=abs($opening_balance);
			@$opening_balance_type='Cr';	
			}
			else{
			@$opening_balance_type='';	
			}
		$opening_balance=round($opening_balance,2);
		$AccountingLedgers=$this->Ledgers->AccountingEntries->find()->where(['AccountingEntries.company_id'=>$company_id])->contain(['Ledgers','PurchaseVouchers','SalesInvoices','SaleReturns','Payments','SalesVouchers','Receipts','JournalVouchers','ContraVouchers','CreditNotes','DebitNotes','JournalVouchers','PurchaseInvoices','PurchaseReturns'])->where($where)
		->autoFields(true);
		}
		//pr($AccountingLedgers->toArray());exit;
		$ledgers = $this->Ledgers->find('list')->where(['company_id'=>$company_id]);
		$this->set(compact('accountLedger','ledgers','opening_balance_type','opening_balance','openingBalance_credit1','closingBalance_credit1','AccountingLedgers','from_date','to_date','voucher_type','voucher_no','ledger_id'));
        $this->set('_serialize', ['ledger']);
    }
	public function dayBook($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$currentDate=date('Y-m-d');
		@$salesLedgers=$this->Ledgers->AccountingEntries->SalesInvoices->find()
		->where(['SalesInvoices.transaction_date'=>$currentDate])
		->contain(['AccountingEntries'])
		->order(['id'=>'DESC']);
		foreach($salesLedgers->toArray() as $data)
		{
		$data->voucher_type='Purchase Vouchers';
		}
		
		$this->set(compact('salesLedgers'));
        $this->set('_serialize', ['salesLedgers']);
    }
	
	
	public function overDueReport()
	{
		$company_id=$this->Auth->User('session_company_id');
		$this->viewBuilder()->layout('index_layout');
		$run_time_date = $this->request->query('run_time_date');
		
		if(!empty($run_time_date)) {
		$run_time_date = date("Y-m-d",strtotime($run_time_date)); }
	
		$parentSundryDebtors = $this->Ledgers->AccountingGroups->find()
				->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.customer'=>'1']);
		
		$childSundryDebtors=[];
		
		foreach($parentSundryDebtors as $parentSundryDebtor)
		{
			$accountingGroups = $this->Ledgers->AccountingGroups->find('children', ['for' => $parentSundryDebtor->id]);
			$childSundryDebtors[]=$parentSundryDebtor->id;
			foreach($accountingGroups as $accountingGroup){
				$childSundryDebtors[]=$accountingGroup->id;
			}			
		}
		
		$ledgerAccounts = $this->Ledgers->find()->where(['accounting_group_id IN'=>$childSundryDebtors]);
		
		$ledgerAccountids = [];

		foreach($ledgerAccounts as $ledgerAccount)
		{
			$ledgerAccountids[]=$ledgerAccount->id;
		}
		$reference_details = $this->Ledgers->ReferenceDetails->find()->contain(['Ledgers'])->where(['ReferenceDetails.company_id'=>$company_id]);
		$reference_details->select(['total_debit' => $reference_details->func()->sum('ReferenceDetails.debit'),'total_credit' => $reference_details->func()->sum('ReferenceDetails.credit')])
		->where(['ReferenceDetails.ledger_id IN '=> $ledgerAccountids,'ReferenceDetails.transaction_date <=' => $run_time_date])
		->group(['ReferenceDetails.ref_name','ReferenceDetails.ledger_id'])
		->autoFields(true);		 
		
		//pr($reference_details->toArray()); exit;
		$this->set(compact('reference_details','run_time_date'));
        $this->set('_serialize', ['reference_details']);
		
	/* 	$reference_details = $this->Ledgers->ReferenceDetails->find()
		->contain(['SalesInvoices'])
		->where(['ReferenceDetails.ledger_id IN'=>$ledgerAccountids]); */
		
		/*
		$reference_details=$reference_details->leftJoinWith('SalesInvoices', function ($q) use($run_time_date){
		return $q->orWhere(['SalesInvoices.transaction_date <=' => $run_time_date]);
		});
		
		$reference_details->leftJoinWith('Receipts', function ($q) use($run_time_date){
		return $q->orWhere(['Receipts.transaction_date <=' => $run_time_date]);
		});
		
		$reference_details->leftJoinWith('Payments', function ($q) use($run_time_date){
		return $q->orWhere(['Payments.transaction_date <=' => $run_time_date]);
		});
		*/
		
		
	}
	
	
	public function overDueReportPayable()
	{
		$company_id=$this->Auth->User('session_company_id');
		$this->viewBuilder()->layout('index_layout');
		$run_time_date = $this->request->query('run_time_date');
		
		if(!empty($run_time_date)) {
		$run_time_date = date("Y-m-d",strtotime($run_time_date)); }
	
		$parentSundryCreditors = $this->Ledgers->AccountingGroups->find()
				->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.supplier'=>'1']);
	
		$childSundryCreditors=[];
		
		foreach($parentSundryCreditors as $parentSundryCreditor)
		{
			$accountingGroups = $this->Ledgers->AccountingGroups->find('children', ['for' => $parentSundryCreditor->id]);
			$childSundryCreditors[]=$parentSundryCreditor->id;
			foreach($accountingGroups as $accountingGroup){
				$childSundryCreditors[]=$accountingGroup->id;
			}			
		}
		
		$ledgerAccounts = $this->Ledgers->find()->where(['accounting_group_id IN'=>$childSundryCreditors]);
		
		$ledgerAccountids = [];

		foreach($ledgerAccounts as $ledgerAccount)
		{
			$ledgerAccountids[]=$ledgerAccount->id;
		}
		$reference_details = $this->Ledgers->ReferenceDetails->find()->contain(['Ledgers'])->where(['ReferenceDetails.company_id'=>$company_id]);
		$reference_details->select(['total_debit' => $reference_details->func()->sum('ReferenceDetails.debit'),'total_credit' => $reference_details->func()->sum('ReferenceDetails.credit')])
		->where(['ReferenceDetails.ledger_id IN '=> $ledgerAccountids,'ReferenceDetails.transaction_date <=' => $run_time_date])
		->group(['ReferenceDetails.ref_name','ReferenceDetails.ledger_id'])
		->autoFields(true);		 
		
		//pr($reference_details->toArray()); exit;
		$this->set(compact('reference_details','run_time_date'));
        $this->set('_serialize', ['reference_details']);
		
	}
	
	
	
	
	
}

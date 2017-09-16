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
            'contain' => ['AccountingEntries'],'where'=>['AccountingEntries.is_opening_balance'=>'yes']
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
		
		$where['AccountingEntries.company_id']  = $company_id;
		$where1['AccountingEntries.company_id'] = $company_id;
		
		if(!empty($ledger_id)){
			$where['AccountingEntries.ledger_id']=$ledger_id;
			$where1['AccountingEntries.ledger_id']=$ledger_id;
		} 
		if(!empty($from_date)){
		    $From=date("Y-m-d",strtotime($from_date));
            $where['AccountingEntries.transaction_date >=']=$From;
			$where1['AccountingEntries.transaction_date <=']=$From;
        }
		if(!empty($to_date)){
			$To=date("Y-m-d",strtotime($to_date));
            $where['AccountingEntries.transaction_date <=']=$To;
			
		}
		if(!empty($ledger_id) || !empty($from_date) || !empty($to_date))
		$AccountingLedgers = $this->Ledgers->AccountingEntries->find()->where($where)->contain(['Ledgers','SalesInvoices'])->order(['AccountingEntries.transaction_date'=>'ASC']);
		if(!empty($AccountingLedgers))
		{ 
	
			$credit=0;$debit=0;$opening_balance_yes_credit_total=0;$opening_balance_yes_debit_total=0;
			$openingBalance_debit=0;$openingBalance_credit=0;
			foreach($AccountingLedgers as $AccountingLedgers1)
			{    
				if(!empty($AccountingLedgers1->purchase_voucher_id)){
					@$voucher_type[$AccountingLedgers1->id]='Purchase Vouchers';
					@$url_link=$this->Ledgers->AccountingEntries->PurchaseVouchers->find()->where(['PurchaseVouchers.id'=>$AccountingLedgers1->purchase_voucher_id])->first();
					$voucher_no[$AccountingLedgers1->id]=$url_link->voucher_no;
				}
				if(!empty($AccountingLedgers1->sales_invoice_id)){
					@$voucher_type[$AccountingLedgers1->id]='Sales Invoices';
					@$url_link=$this->Ledgers->AccountingEntries->SalesInvoices->find()->where(['SalesInvoices.id'=>$AccountingLedgers1->sales_invoice_id])->first();
					$voucher_no[$AccountingLedgers1->id]=$url_link->voucher_no;
					
				}
				if($AccountingLedgers1->is_opening_balance!='yes')
				{
					$credit += $AccountingLedgers1->credit; 
					$debit  += $AccountingLedgers1->debit; 
				}
			}
			
			$total_credit = $credit+$opening_balance_yes_credit_total;  
			$total_debit  = $debit+$opening_balance_yes_debit_total; 
			
			if($total_credit>$total_debit)
			{
				$openingBalance_debit = $total_credit-$total_debit;
			}
			
			if($total_credit<$total_debit)
			{
				$openingBalance_credit = $total_debit-$total_credit;
			}
			
		}
		
		$AccountingLedgersBeforeFromDate = $this->Ledgers->AccountingEntries->find()
											->where($where1)
											->contain(['Ledgers'])
											->order(['ledger_id'=>'ASC']);
		//pr($AccountingLedgersBeforeFromDate->toArray());
		$closingBalance_credit1 = 0;
		$closingBalance_debit1  = 0;
		if(!empty($AccountingLedgersBeforeFromDate))
		{
			$credit1=0;$debit1=0;$opening_balance_yes_credit_total1=0;$opening_balance_yes_debit_total1=0;
			foreach($AccountingLedgersBeforeFromDate as $AccountingLedgersBeforeFromDate1)
			{
				if($AccountingLedgersBeforeFromDate1->is_opening_balance!='yes')
				{
					$credit1 += $AccountingLedgersBeforeFromDate1->credit; 
					$debit1  += $AccountingLedgersBeforeFromDate1->debit; 
				}
				else
				{
					$opening_balance_yes_credit_total1 += $AccountingLedgersBeforeFromDate1->credit; 
					$opening_balance_yes_debit_total1  += $AccountingLedgersBeforeFromDate1->debit; 
				}
			}
			$total_credit1 = $credit1+$opening_balance_yes_credit_total1;  
			$total_debit1  = $debit1+$opening_balance_yes_debit_total1; 
			if($total_credit1 > $total_debit1)
			{ 
				$openingBalance_credit1 = $total_credit1-$total_debit1; 
				 
			}
			
			if($total_credit1 < $total_debit1)
			{ 
				$openingBalance_debit1 = $total_debit1-$total_credit1; 
				
			}
			$closingBalance_credit1 = @$openingBalance_credit1+@$openingBalance_credit;
			$closingBalance_debit1  = @$openingBalance_debit1+@$openingBalance_debit;
			//exit;
		}
		//pr($AccountingLedgers->toArray());exit;
		$ledgers = $this->Ledgers->find('list')->where(['company_id'=>$company_id]);
		$this->set(compact('accountLedger','ledgers','openingBalance_debit1','closingBalance_debit1','openingBalance_credit1','closingBalance_credit1','AccountingLedgers','from_date','to_date','voucher_type','voucher_no'));
        $this->set('_serialize', ['ledger']);
    }
}

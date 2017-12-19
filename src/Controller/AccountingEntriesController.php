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
	
	public function RatioReport()
    {
		$this->viewBuilder()->layout('index_layout');
        $company_id=$this->Auth->User('session_company_id');
		$to_date=$this->request->query('to_date');
		if($to_date){
		$to_date= date("Y-m-d",strtotime($to_date));
		}else{
		$to_date= date("Y-m-d");
		}
		$AccountingGroups=$this->AccountingEntries->Ledgers->AccountingGroups->find()
		->where(['AccountingGroups.nature_of_group_id IN'=>[3,4],'AccountingGroups.company_id'=>$company_id]);
		$Groups=[];
		foreach($AccountingGroups as $AccountingGroup){
			$Groups[$AccountingGroup->id]['ids'][]=$AccountingGroup->id;
			$Groups[$AccountingGroup->id]['name']=$AccountingGroup->name;
			$Groups[$AccountingGroup->id]['nature']=$AccountingGroup->nature_of_group_id;
			$accountingChildGroups = $this->AccountingEntries->Ledgers->AccountingGroups->find('children', ['for' => $AccountingGroup->id]);
			foreach($accountingChildGroups as $accountingChildGroup){
				$Groups[$AccountingGroup->id]['ids'][]=$accountingChildGroup->id;
			}
		}
		$AllGroups=[];
		foreach($Groups as $mainGroups){
			foreach($mainGroups['ids'] as $subGroup){
				$AllGroups[]=$subGroup;
			}
		}
		
		$query=$this->AccountingEntries->find();
		$query->select(['ledger_id','totalDebit' => $query->func()->sum('AccountingEntries.debit'),'totalCredit' => $query->func()->sum('AccountingEntries.credit')])
				->group('AccountingEntries.ledger_id')
				->where(['AccountingEntries.company_id'=>$company_id, 'AccountingEntries.transaction_date <='=>$to_date])
				->contain(['Ledgers'=>function($q){
					return $q->select(['Ledgers.accounting_group_id','Ledgers.id']);
				}]);
		$query->matching('Ledgers', function ($q) use($AllGroups){
			return $q->where(['Ledgers.accounting_group_id IN' => $AllGroups]);
		});
		$balanceOfLedgers=$query;
		$groupForPrint=[];
		foreach($balanceOfLedgers as $balanceOfLedger){
			foreach($Groups as $primaryGroup=>$Group){
				if(in_array($balanceOfLedger->ledger->accounting_group_id,$Group['ids'])){
					@$groupForPrint[$primaryGroup]['balance']+=$balanceOfLedger->totalDebit-$balanceOfLedger->totalCredit;
				}else{
					@$groupForPrint[$primaryGroup]['balance']+=0;
				}
				@$groupForPrint[$primaryGroup]['name']=$Group['name'];
				@$groupForPrint[$primaryGroup]['nature']=$Group['nature'];
			}
		}
		$openingValue= $this->StockValuationWithDate($to_date);
		$closingValue= $this->StockValuationWithDate2($to_date);
		$this->set(compact('from_date','to_date', 'groupForPrint', 'closingValue', 'openingValue'));
		
    }

	public function ProfitLossStatement()
    {
		$this->viewBuilder()->layout('index_layout');
        $company_id=$this->Auth->User('session_company_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$from_date = date("Y-m-d",strtotime($from_date));
		$to_date= date("Y-m-d",strtotime($to_date));
		
		$AccountingGroups=$this->AccountingEntries->Ledgers->AccountingGroups->find()
		->where(['AccountingGroups.nature_of_group_id IN'=>[3,4],'AccountingGroups.company_id'=>$company_id]);
		$Groups=[];
		foreach($AccountingGroups as $AccountingGroup){
			$Groups[$AccountingGroup->id]['ids'][]=$AccountingGroup->id;
			$Groups[$AccountingGroup->id]['name']=$AccountingGroup->name;
			$Groups[$AccountingGroup->id]['nature']=$AccountingGroup->nature_of_group_id;
			$accountingChildGroups = $this->AccountingEntries->Ledgers->AccountingGroups->find('children', ['for' => $AccountingGroup->id]);
			foreach($accountingChildGroups as $accountingChildGroup){
				$Groups[$AccountingGroup->id]['ids'][]=$accountingChildGroup->id;
			}
		}
		$AllGroups=[];
		foreach($Groups as $mainGroups){
			foreach($mainGroups['ids'] as $subGroup){
				$AllGroups[]=$subGroup;
			}
		}
		
		$query=$this->AccountingEntries->find();
		$query->select(['ledger_id','totalDebit' => $query->func()->sum('AccountingEntries.debit'),'totalCredit' => $query->func()->sum('AccountingEntries.credit')])
				->group('AccountingEntries.ledger_id')
				->where(['AccountingEntries.company_id'=>$company_id,'AccountingEntries.transaction_date >='=>$from_date, 'AccountingEntries.transaction_date <='=>$to_date])
				->contain(['Ledgers'=>function($q){
					return $q->select(['Ledgers.accounting_group_id','Ledgers.id']);
				}]);
		$query->matching('Ledgers', function ($q) use($AllGroups){
			return $q->where(['Ledgers.accounting_group_id IN' => $AllGroups]);
		});
		$balanceOfLedgers=$query;
		$groupForPrint=[];
		foreach($balanceOfLedgers as $balanceOfLedger){
			foreach($Groups as $primaryGroup=>$Group){
				if(in_array($balanceOfLedger->ledger->accounting_group_id,$Group['ids'])){
					@$groupForPrint[$primaryGroup]['balance']+=$balanceOfLedger->totalDebit-$balanceOfLedger->totalCredit;
				}else{
					@$groupForPrint[$primaryGroup]['balance']+=0;
				}
				@$groupForPrint[$primaryGroup]['name']=$Group['name'];
				@$groupForPrint[$primaryGroup]['nature']=$Group['nature'];
			}
		}
		$openingValue= $this->StockValuationWithDate($from_date);
		$closingValue= $this->StockValuationWithDate2($to_date);
		$this->set(compact('from_date','to_date', 'groupForPrint', 'closingValue', 'openingValue'));
		
    }
	
	public function BalanceSheet()
    {
		$this->viewBuilder()->layout('index_layout');
        $company_id=$this->Auth->User('session_company_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$from_date = date("Y-m-d",strtotime($from_date));
		$to_date= date("Y-m-d",strtotime($to_date));
		
		$AccountingGroups=$this->AccountingEntries->Ledgers->AccountingGroups->find()->where(['AccountingGroups.nature_of_group_id IN'=>[1,2],'AccountingGroups.company_id'=>$company_id]);
		$Groups=[];
		foreach($AccountingGroups as $AccountingGroup){
			$Groups[$AccountingGroup->id]['ids'][]=$AccountingGroup->id;
			$Groups[$AccountingGroup->id]['name']=$AccountingGroup->name;
			$Groups[$AccountingGroup->id]['nature']=$AccountingGroup->nature_of_group_id;
			$accountingChildGroups = $this->AccountingEntries->Ledgers->AccountingGroups->find('children', ['for' => $AccountingGroup->id]);
			foreach($accountingChildGroups as $accountingChildGroup){
				$Groups[$AccountingGroup->id]['ids'][]=$accountingChildGroup->id;
			}
		}
		$AllGroups=[];
		foreach($Groups as $mainGroups){
			foreach($mainGroups['ids'] as $subGroup){
				$AllGroups[]=$subGroup;
			}
		}
		
		$query=$this->AccountingEntries->find();
		$query->select(['ledger_id','totalDebit' => $query->func()->sum('AccountingEntries.debit'),'totalCredit' => $query->func()->sum('AccountingEntries.credit')])
				->group('AccountingEntries.ledger_id')
				->where(['AccountingEntries.company_id'=>$company_id])
				->contain(['Ledgers'=>function($q){
					return $q->select(['Ledgers.accounting_group_id','Ledgers.id']);
				}]);
		$query->matching('Ledgers', function ($q) use($AllGroups){
			return $q->where(['Ledgers.accounting_group_id IN' => $AllGroups]);
		});
		$balanceOfLedgers=$query;
		
		$groupForPrint=[];
		foreach($balanceOfLedgers as $balanceOfLedger){
			foreach($Groups as $primaryGroup=>$Group){
				if(in_array($balanceOfLedger->ledger->accounting_group_id,$Group['ids'])){
					@$groupForPrint[$primaryGroup]['balance']+=$balanceOfLedger->totalDebit-$balanceOfLedger->totalCredit;
				}else{
					@$groupForPrint[$primaryGroup]['balance']+=0;
				}
				@$groupForPrint[$primaryGroup]['name']=$Group['name'];
				@$groupForPrint[$primaryGroup]['nature']=$Group['nature'];
			}
		}
		$GrossProfit= $this->GrossProfit($from_date,$to_date);
		$closingValue= $this->StockValuationWithDate2($to_date);
		$differenceInOpeningBalance= $this->differenceInOpeningBalance();
		$this->set(compact('from_date','to_date', 'groupForPrint', 'GrossProfit', 'closingValue', 'differenceInOpeningBalance'));
		
    }
	
	public function bankReconciliation()
    {
		$this->viewBuilder()->layout('index_layout');
        $company_id=$this->Auth->User('session_company_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$ledger_id=$this->request->query('ledger_id');
		if($from_date){
			$from_date = date("Y-m-d",strtotime($from_date));
		}else{
			$from_date="";
		}
		
		if($to_date){
			$to_date= date("Y-m-d",strtotime($to_date));
		}else{
			$to_date="";
		}
		
		if($ledger_id){
			$AccountingEntries=$this->AccountingEntries->find()->contain(['PurchaseVouchers'=>['PurchaseVoucherRows'=>['Ledgers']],'Payments'=>['PaymentRows'=>['Ledgers']],'SalesVouchers'=>['SalesVoucherRows'=>['Ledgers']],'Receipts'=>['ReceiptRows'=>['Ledgers']],'ContraVouchers'=>['ContraVoucherRows'=>['Ledgers']],'CreditNotes'=>['CreditNoteRows'=>['Ledgers']],'DebitNotes'=>['DebitNoteRows'=>['Ledgers']]])->where(['AccountingEntries.transaction_date <='=>$to_date,'AccountingEntries.ledger_id'=>$ledger_id,'AccountingEntries.reconciliation_date'=>'0000-00-00','AccountingEntries.company_id'=>$company_id]);
		
			$query=$this->AccountingEntries->find();
			$query->select(['ledger_id','totalDebit' => $query->func()->sum('AccountingEntries.debit'),'totalCredit' => $query->func()->sum('AccountingEntries.credit')])
				->group('AccountingEntries.ledger_id')
				->where(['AccountingEntries.company_id'=>$company_id,'AccountingEntries.transaction_date <='=>$to_date,'AccountingEntries.reconciliation_date !='=>'0000-00-00','AccountingEntries.ledger_id'=>$ledger_id])->orWhere(['AccountingEntries.company_id'=>$company_id,'AccountingEntries.transaction_date <='=>$to_date,'AccountingEntries.reconciliation_date'=>'0000-00-00','AccountingEntries.ledger_id'=>$ledger_id,'AccountingEntries.is_opening_balance'=>'yes']);
				$BankEnteries=$query->first();
				$bank_credit=0; $bank_debit=0;
				@$bank_remaining=$BankEnteries->totalDebit-$BankEnteries->totalCredit;
					if($BankEnteries->totalDebit > $BankEnteries->totalCredit){
						@$bank_debit=$BankEnteries->totalDebit-$BankEnteries->totalCredit;}
						if($BankEnteries->totalDebit < $BankEnteries->totalCredit){
						@$bank_credit=$BankEnteries->totalCredit-$BankEnteries->totalDebit;
						}
						else if($BankEnteries->totalDebit == $BankEnteries->totalCredit){
						@$bank_credit='';
						@$bank_debit='';
						}
					
		foreach($AccountingEntries as $data){
			if(!empty($data->payment_id)){
				$data->hlink='Payment';
				$payment_rows1=$this->AccountingEntries->Payments->PaymentRows->find()->contain(['Ledgers'])->where(['PaymentRows.payment_id'=>$data->payment_id,'PaymentRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$payment_rows1->ledger->name;
				$payment_rows2=$this->AccountingEntries->Payments->PaymentRows->find()->contain(['Ledgers'])->where(['PaymentRows.payment_id'=>$data->payment_id,'PaymentRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$payment_rows2->mode_of_payment;
				$data->cheque_no=$payment_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($payment_rows2->cheque_date));
				
			}
			else if(!empty($data->receipt_id)){
				$data->hlink='Receipts';
				$receipt_rows1=$this->AccountingEntries->Receipts->ReceiptRows->find()->contain(['Ledgers'])->where(['ReceiptRows.receipt_id'=>$data->receipt_id,'ReceiptRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$receipt_rows1->ledger->name;
				$receipt_rows2=$this->AccountingEntries->Receipts->ReceiptRows->find()->contain(['Ledgers'])->where(['ReceiptRows.receipt_id'=>$data->receipt_id,'ReceiptRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$receipt_rows2->mode_of_payment;
				$data->cheque_no=$receipt_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($receipt_rows2->cheque_date));
			}
			else if(!empty($data->credit_note_id)){
				$data->hlink='Credit Notes';
				$credit_note_rows1=$this->AccountingEntries->CreditNotes->CreditNoteRows->find()->contain(['Ledgers'])->where(['CreditNoteRows.credit_note_id'=>$data->credit_note_id,'CreditNoteRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$credit_note_rows1->ledger->name;
				$credit_note_rows2=$this->AccountingEntries->CreditNotes->CreditNoteRows->find()->contain(['Ledgers'])->where(['CreditNoteRows.credit_note_id'=>$data->credit_note_id,'CreditNoteRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$credit_note_rows2->mode_of_payment;
				$data->cheque_no=$credit_note_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($credit_note_rows2->cheque_date));
			}
			else if(!empty($data->debit_note_id)){
				$data->hlink='Debit Notes';
				$debit_note_rows1=$this->AccountingEntries->DebitNotes->DebitNoteRows->find()->contain(['Ledgers'])->where(['DebitNoteRows.debit_note_id'=>$data->debit_note_id,'DebitNoteRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$debit_note_rows1->ledger->name;
				$debit_note_rows2=$this->AccountingEntries->DebitNotes->DebitNoteRows->find()->contain(['Ledgers'])->where(['DebitNoteRows.debit_note_id'=>$data->debit_note_id,'DebitNoteRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$debit_note_rows2->mode_of_payment;
				$data->cheque_no=$debit_note_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($debit_note_rows2->cheque_date));
			}
			else if(!empty($data->contra_voucher_id)){
				$data->hlink='Contra Voucher';
				$contra_rows1=$this->AccountingEntries->ContraVouchers->ContraVoucherRows->find()->contain(['Ledgers'])->where(['ContraVoucherRows.contra_voucher_id'=>$data->contra_voucher_id,'ContraVoucherRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$contra_rows1->ledger->name;
				$contra_rows2=$this->AccountingEntries->ContraVouchers->ContraVoucherRows->find()->contain(['Ledgers'])->where(['ContraVoucherRows.contra_voucher_id'=>$data->contra_voucher_id,'ContraVoucherRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$contra_rows2->mode_of_payment;
				$data->cheque_no=$contra_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($contra_rows2->cheque_date));
			}
			else if(!empty($data->purchase_voucher_id)){
				$data->hlink='Purchase Voucher';
				$purchase_voucher_rows1=$this->AccountingEntries->PurchaseVouchers->PurchaseVoucherRows->find()->contain(['Ledgers'])->where(['PurchaseVoucherRows.purchase_voucher_id'=>$data->purchase_voucher_id,'PurchaseVoucherRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$purchase_voucher_rows1->ledger->name;
				$purchase_voucher_rows2=$this->AccountingEntries->PurchaseVouchers->PurchaseVoucherRows->find()->contain(['Ledgers'])->where(['PurchaseVoucherRows.purchase_voucher_id'=>$data->purchase_voucher_id,'PurchaseVoucherRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$purchase_voucher_rows2->mode_of_payment;
				$data->cheque_no=$purchase_voucher_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($purchase_voucher_rows2->cheque_date));
			}
			else if(!empty($data->sales_voucher_id)){
				$data->hlink='Sales Voucher';
				$sales_voucher_rows1=$this->AccountingEntries->SalesVouchers->SalesVoucherRows->find()->contain(['Ledgers'])->where(['SalesVoucherRows.sales_voucher_id'=>$data->sales_voucher_id,'SalesVoucherRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$sales_voucher_rows1->ledger->name;
				$sales_voucher_rows2=$this->AccountingEntries->SalesVouchers->SalesVoucherRows->find()->contain(['Ledgers'])->where(['SalesVoucherRows.sales_voucher_id'=>$data->sales_voucher_id,'SalesVoucherRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$sales_voucher_rows2->mode_of_payment;
				$data->cheque_no=$sales_voucher_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($sales_voucher_rows2->cheque_date));
			}
		}
		}
		//pr($AccountingEntries->toArray());
		//exit;
	
		$bankParentGroups = $this->AccountingEntries->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.bank'=>'1']);
						
		$bankGroups=[];
		
		foreach($bankParentGroups as $bankParentGroup)
		{
			$accountingGroups = $this->AccountingEntries->Ledgers->AccountingGroups
			->find('children', ['for' => $bankParentGroup->id])->toArray();
			$bankGroups[]=$bankParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$bankGroups[]=$accountingGroup->id;
			}
		}
		if($bankGroups)
		{  
			$Bankledgers = $this->AccountingEntries->Ledgers->find()
							->where(['Ledgers.accounting_group_id IN' =>$bankGroups,'Ledgers.company_id'=>$company_id]);
        }
		$bankOptions=[];
		foreach($Bankledgers as $Bankledger){
		$bankOptions[]=['text' =>@$Bankledger->name, 'value' => $Bankledger->id];
		}
		$this->set(compact('from_date','to_date','ledger_id','bankOptions','AccountingEntries','bank_debit','bank_credit','bank_remaining'));
	}
	
	public function bankReconciliationView()
    {
		$this->viewBuilder()->layout('index_layout');
		$status=$this->request->query('status'); 
		if(!empty($status)){ 
			$this->viewBuilder()->layout('');	
		}else{ 
			$this->viewBuilder()->layout('index_layout');
		}
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
        $company_id=$this->Auth->User('session_company_id');
		$from_date=$this->request->query('from_date');
		$to_date=$this->request->query('to_date');
		$ledger_id=$this->request->query('ledger_id');
		if($from_date){
			$from_date = date("Y-m-d",strtotime($from_date));
		}else{
			$from_date="";
		}
		
		if($to_date){
			$to_date= date("Y-m-d",strtotime($to_date));
		}else{
			$to_date="";
		}
		if($ledger_id){
			$AccountingEntries=$this->AccountingEntries->find()->contain(['PurchaseVouchers'=>['PurchaseVoucherRows'=>['Ledgers']],'Payments'=>['PaymentRows'=>['Ledgers']],'SalesVouchers'=>['SalesVoucherRows'=>['Ledgers']],'Receipts'=>['ReceiptRows'=>['Ledgers']],'ContraVouchers'=>['ContraVoucherRows'=>['Ledgers']],'CreditNotes'=>['CreditNoteRows'=>['Ledgers']],'DebitNotes'=>['DebitNoteRows'=>['Ledgers']]])->where(['AccountingEntries.transaction_date >='=>$from_date,'AccountingEntries.transaction_date <='=>$to_date,'AccountingEntries.ledger_id'=>$ledger_id,'AccountingEntries.reconciliation_date !=' =>'0000-00-00','AccountingEntries.company_id'=>$company_id]);
		
			
		foreach($AccountingEntries as $data){
			if(!empty($data->payment_id)){
				$data->hlink='Payment';
				$payment_rows1=$this->AccountingEntries->Payments->PaymentRows->find()->contain(['Ledgers'])->where(['PaymentRows.payment_id'=>$data->payment_id,'PaymentRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$payment_rows1->ledger->name;
				$payment_rows2=$this->AccountingEntries->Payments->PaymentRows->find()->contain(['Ledgers'])->where(['PaymentRows.payment_id'=>$data->payment_id,'PaymentRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$payment_rows2->mode_of_payment;
				$data->cheque_no=$payment_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($payment_rows2->cheque_date));
				
			}
			else if(!empty($data->receipt_id)){
				$data->hlink='Receipts';
				$receipt_rows1=$this->AccountingEntries->Receipts->ReceiptRows->find()->contain(['Ledgers'])->where(['ReceiptRows.receipt_id'=>$data->receipt_id,'ReceiptRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$receipt_rows1->ledger->name;
				$receipt_rows2=$this->AccountingEntries->Receipts->ReceiptRows->find()->contain(['Ledgers'])->where(['ReceiptRows.receipt_id'=>$data->receipt_id,'ReceiptRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$receipt_rows2->mode_of_payment;
				$data->cheque_no=$receipt_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($receipt_rows2->cheque_date));
			}
			else if(!empty($data->credit_note_id)){
				$data->hlink='Credit Notes';
				$credit_note_rows1=$this->AccountingEntries->CreditNotes->CreditNoteRows->find()->contain(['Ledgers'])->where(['CreditNoteRows.credit_note_id'=>$data->credit_note_id,'CreditNoteRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$credit_note_rows1->ledger->name;
				$credit_note_rows2=$this->AccountingEntries->CreditNotes->CreditNoteRows->find()->contain(['Ledgers'])->where(['CreditNoteRows.credit_note_id'=>$data->credit_note_id,'CreditNoteRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$credit_note_rows2->mode_of_payment;
				$data->cheque_no=$credit_note_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($credit_note_rows2->cheque_date));
			}
			else if(!empty($data->debit_note_id)){
				$data->hlink='Debit Notes';
				$debit_note_rows1=$this->AccountingEntries->DebitNotes->DebitNoteRows->find()->contain(['Ledgers'])->where(['DebitNoteRows.debit_note_id'=>$data->debit_note_id,'DebitNoteRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$debit_note_rows1->ledger->name;
				$debit_note_rows2=$this->AccountingEntries->DebitNotes->DebitNoteRows->find()->contain(['Ledgers'])->where(['DebitNoteRows.debit_note_id'=>$data->debit_note_id,'DebitNoteRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$debit_note_rows2->mode_of_payment;
				$data->cheque_no=$debit_note_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($debit_note_rows2->cheque_date));
			}
			else if(!empty($data->contra_voucher_id)){
				$data->hlink='Contra Voucher';
				$contra_rows1=$this->AccountingEntries->ContraVouchers->ContraVoucherRows->find()->contain(['Ledgers'])->where(['ContraVoucherRows.contra_voucher_id'=>$data->contra_voucher_id,'ContraVoucherRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$contra_rows1->ledger->name;
				$contra_rows2=$this->AccountingEntries->ContraVouchers->ContraVoucherRows->find()->contain(['Ledgers'])->where(['ContraVoucherRows.contra_voucher_id'=>$data->contra_voucher_id,'ContraVoucherRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$contra_rows2->mode_of_payment;
				$data->cheque_no=$contra_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($contra_rows2->cheque_date));
			}
			else if(!empty($data->purchase_voucher_id)){
				$data->hlink='Purchase Voucher';
				$purchase_voucher_rows1=$this->AccountingEntries->PurchaseVouchers->PurchaseVoucherRows->find()->contain(['Ledgers'])->where(['PurchaseVoucherRows.purchase_voucher_id'=>$data->purchase_voucher_id,'PurchaseVoucherRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$purchase_voucher_rows1->ledger->name;
				$purchase_voucher_rows2=$this->AccountingEntries->PurchaseVouchers->PurchaseVoucherRows->find()->contain(['Ledgers'])->where(['PurchaseVoucherRows.purchase_voucher_id'=>$data->purchase_voucher_id,'PurchaseVoucherRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$purchase_voucher_rows2->mode_of_payment;
				$data->cheque_no=$purchase_voucher_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($purchase_voucher_rows2->cheque_date));
			}
			else if(!empty($data->sales_voucher_id)){
				$data->hlink='Sales Voucher';
				$sales_voucher_rows1=$this->AccountingEntries->SalesVouchers->SalesVoucherRows->find()->contain(['Ledgers'])->where(['SalesVoucherRows.sales_voucher_id'=>$data->sales_voucher_id,'SalesVoucherRows.ledger_id !='=>$ledger_id])->first();
				$data->ledger_name=$sales_voucher_rows1->ledger->name;
				$sales_voucher_rows2=$this->AccountingEntries->SalesVouchers->SalesVoucherRows->find()->contain(['Ledgers'])->where(['SalesVoucherRows.sales_voucher_id'=>$data->sales_voucher_id,'SalesVoucherRows.ledger_id'=>$ledger_id])->first();
				$data->transaction_type=$sales_voucher_rows2->mode_of_payment;
				$data->cheque_no=$sales_voucher_rows2->cheque_no;
				$data->cheque_date=date("d-m-Y",strtotime($sales_voucher_rows2->cheque_date));
			}
		}
		//pr($AccountingEntries->toArray());
		//exit;
	}
		$bankParentGroups = $this->AccountingEntries->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.bank'=>'1']);
						
		$bankGroups=[];
		
		foreach($bankParentGroups as $bankParentGroup)
		{
			$accountingGroups = $this->AccountingEntries->Ledgers->AccountingGroups
			->find('children', ['for' => $bankParentGroup->id])->toArray();
			$bankGroups[]=$bankParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$bankGroups[]=$accountingGroup->id;
			}
		}
		if($bankGroups)
		{  
			$Bankledgers = $this->AccountingEntries->Ledgers->find()
							->where(['Ledgers.accounting_group_id IN' =>$bankGroups,'Ledgers.company_id'=>$company_id]);
        }
		$bankOptions=[];
		foreach($Bankledgers as $Bankledger){
		$bankOptions[]=['text' =>@$Bankledger->name, 'value' => $Bankledger->id];
		}
		$this->set(compact('from_date','to_date','ledger_id','bankOptions','AccountingEntries','url','status'));
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
	public function reconciliationDateUpdate($acc_entry_id=null,$reconciliation_date=null)
    {
		$this->viewBuilder()->layout('');
		//$ledger = $this->Ledgers->get($id);
		if($reconciliation_date=="yes"){
		$reconciliation_date="0000-00-00";
		}else{
		$reconciliation_date=date("Y-m-d",strtotime($reconciliation_date));
		}
		$query = $this->AccountingEntries->query();
		$query->update()
		->set(['reconciliation_date' => $reconciliation_date])
		->where(['id' => $acc_entry_id])
		->execute();
		exit;
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

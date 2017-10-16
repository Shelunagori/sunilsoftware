<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PurchaseInvoices Controller
 *
 * @property \App\Model\Table\PurchaseInvoicesTable $PurchaseInvoices
 *
 * @method \App\Model\Entity\PurchaseInvoice[] paginate($object = null, array $settings = [])
 */
class PurchaseInvoicesController extends AppController
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
            'contain' => ['Companies', 'SupplierLedgers']
        ];
        $purchaseInvoices = $this->paginate($this->PurchaseInvoices->find()->where(['PurchaseInvoices.company_id'=>$company_id]));
		//pr($purchaseInvoices); exit;
        $this->set(compact('purchaseInvoices'));
        $this->set('_serialize', ['purchaseInvoices']);
    }

    /**
     * View method
     *
     * @param string|null $id Purchase Invoice id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $purchaseInvoice = $this->PurchaseInvoices->get($id, [
            'contain' => ['Companies', 'SupplierLedgers', 'PurchaseInvoiceRows']
        ]);

        $this->set('purchaseInvoice', $purchaseInvoice);
        $this->set('_serialize', ['purchaseInvoice']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id=null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$stateDetails=$this->Auth->User('session_company');
		$state_id=$stateDetails->state_id;
        $Grns = $this->PurchaseInvoices->Grns->get($id, [
            'contain' => (['GrnRows'=>['Items'=>['FirstGstFigures']],'SupplierLedgers'])
        ]);
		
		
			$Voucher_no_last = $this->PurchaseInvoices->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		//pr($Voucher_no_last->voucher_no); exit;
        $purchaseInvoice = $this->PurchaseInvoices->newEntity();
        if ($this->request->is('post')) {
            $purchaseInvoice = $this->PurchaseInvoices->patchEntity($purchaseInvoice, $this->request->getData());
			$purchaseInvoice->transaction_date = date("Y-m-d",strtotime($this->request->getData()['transaction_date']));
			$Voucher_no = $this->PurchaseInvoices->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
			if($Voucher_no)
			{
				$purchaseInvoice->voucher_no = $Voucher_no->voucher_no+1;
			}
			else
			{
				$purchaseInvoice->voucher_no = 1;
			} 
			$purchaseInvoice->company_id = $company_id;
			$purchaseInvoice->grn_id = $Grns->id;
                        $purchaseInvoice->purchase_ledger_id=$purchaseInvoice->purchase_ledger_id;
                        $purchaseInvoice->supplier_ledger_id=$Grns->supplier_ledger_id;
			//pr($purchaseInvoice); exit;
            if ($this->PurchaseInvoices->save($purchaseInvoice)) { 
				
				$query = $this->PurchaseInvoices->Grns->query();
				$query->update()
					->set(['status'=>'Invoice Booked'])
					->where(['id' => $Grns->id])
					->execute();
				
				//Accounting Entries for Purchase account//
				$AccountingEntrie = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
				$AccountingEntrie->ledger_id=$purchaseInvoice->purchase_ledger_id;
				$AccountingEntrie->debit=$purchaseInvoice->total_taxable_value;
				$AccountingEntrie->credit=0;
				$AccountingEntrie->transaction_date=$purchaseInvoice->transaction_date;
				$AccountingEntrie->company_id=$company_id;
				$AccountingEntrie->purchase_invoice_id=$purchaseInvoice->id;
				$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrie);
			  
			  
			  //Accounting Entries for Supplier account//
				$AccountingEntrie = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
				$AccountingEntrie->ledger_id=$purchaseInvoice->supplier_ledger_id;
				$AccountingEntrie->credit=$purchaseInvoice->total_amount;
				$AccountingEntrie->debit=0;
				$AccountingEntrie->transaction_date=$purchaseInvoice->transaction_date;
				$AccountingEntrie->company_id=$company_id;
				$AccountingEntrie->purchase_invoice_id=$purchaseInvoice->id;
				$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrie);
				
			//Accounting Entries for Round of Amount//
				$AccountingEntrie = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
				$RoundofLedgers = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()->where(['Ledgers.round_off'=>1,'Ledgers.company_id'=>$company_id])->first(); 
				$AccountingEntrie->ledger_id=$RoundofLedgers->id;
				$AccountingEntrie->transaction_date=$purchaseInvoice->transaction_date;
				$AccountingEntrie->company_id=$company_id;
				$AccountingEntrie->purchase_invoice_id=$purchaseInvoice->id;
				
				if($purchaseInvoice->total_round_amount > 0){
					$AccountingEntrie->debit=abs($purchaseInvoice->total_round_amount);
					$AccountingEntrie->credit=0;
				}else{
					$AccountingEntrie->credit=$purchaseInvoice->total_round_amount;
					$AccountingEntrie->debit=0;
				}
				if($purchaseInvoice->total_round_amount != 0){
					$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrie);
				}
				
				if($purchaseInvoice->is_interstate=='0'){
					foreach($purchaseInvoice->purchase_invoice_rows as $purchase_invoice_row)
					   { 
					   $gstAmtdata=$purchase_invoice_row->gst_value/2;
					   $gstAmtInsert=round($gstAmtdata,2);
					   
					   //Accounting Entries for GST//
					  $AccountingEntrieCGST = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
						$gstLedgerCGST = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$purchase_invoice_row->item_gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'input', 'Ledgers.gst_type'=>'CGST'])->first();
							
						$AccountingEntrieCGST->ledger_id=$gstLedgerCGST->id;
						$AccountingEntrieCGST->debit=$gstAmtInsert;
						$AccountingEntrieCGST->credit=0;
						$AccountingEntrieCGST->transaction_date=$purchaseInvoice->transaction_date;
						$AccountingEntrieCGST->company_id=$company_id;
						$AccountingEntrieCGST->purchase_invoice_id=$purchaseInvoice->id;
						$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrieCGST);
						
						$AccountingEntrieSGST = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
						$gstLedgerSGST = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$purchase_invoice_row->item_gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'input', 'Ledgers.gst_type'=>'SGST'])->first();
						$AccountingEntrieSGST->ledger_id=$gstLedgerSGST->id;
						$AccountingEntrieSGST->debit=$gstAmtInsert;
						$AccountingEntrieSGST->credit=0;
						$AccountingEntrieSGST->transaction_date=$purchaseInvoice->transaction_date;
						$AccountingEntrieSGST->company_id=$company_id;
						$AccountingEntrieSGST->purchase_invoice_id=$purchaseInvoice->id;
						$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrieSGST);
					   }
				}else{
					foreach($purchaseInvoice->purchase_invoice_rows as $purchase_invoice_row)
					   {
						   //Accounting Entries for IGST//
						$AccountingEntrieIGST = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
						$gstLedgerSGST = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$purchase_invoice_row->item_gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'input', 'Ledgers.gst_type'=>'IGST'])->first();
						$AccountingEntrieIGST->ledger_id=$gstLedgerSGST->id;
						$AccountingEntrieIGST->debit=$purchase_invoice_row->gst_value;
						$AccountingEntrieIGST->credit=0;
						$AccountingEntrieIGST->transaction_date=$purchaseInvoice->transaction_date;
						$AccountingEntrieIGST->company_id=$company_id;
						$AccountingEntrieIGST->purchase_invoice_id=$purchaseInvoice->id;
						$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrieIGST);
					   }
				}
				
				//Refrence Details For Party//
				$ReferenceDetail = $this->PurchaseInvoices->ReferenceDetails->newEntity(); 
				$ReferenceDetail->ledger_id=$purchaseInvoice->supplier_ledger_id;
				$ReferenceDetail->credit=$purchaseInvoice->total_amount;
				$ReferenceDetail->debit=0;
				$ReferenceDetail->transaction_date=$purchaseInvoice->transaction_date;
				$ReferenceDetail->company_id=$company_id;
				$ReferenceDetail->type='New Ref';
				$ReferenceDetail->ref_name='PI'.$purchaseInvoice->voucher_no;
				$ReferenceDetail->purchase_invoice_id=$purchaseInvoice->id;
				$this->PurchaseInvoices->ReferenceDetails->save($ReferenceDetail);
				  
				
                $this->Flash->success(__('The purchase invoice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase invoice could not be saved. Please, try again.'));
        }
		
		$partyParentGroups = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.
						purchase_invoice_party'=>'1']);
		$partyGroups=[];
		

		foreach($partyParentGroups as $partyParentGroup)
		{
			$accountingGroups = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups
			->find('children', ['for' => $partyParentGroup->id])->toArray();
			$partyGroups[]=$partyParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$partyGroups[]=$accountingGroup->id;
			}
		}
		if($partyGroups)
		{  
			$Partyledgers = $this->PurchaseInvoices->Grns->SupplierLedgers->find()
							->where(['SupplierLedgers.accounting_group_id IN' =>$partyGroups,'SupplierLedgers.company_id'=>$company_id])
							->contain(['Suppliers']);
        }

		$SupplierLedgersDetails = $this->PurchaseInvoices->Grns->SupplierLedgers->find()->where(['SupplierLedgers.id'=>$Grns->supplier_ledger->id])->contain(['Suppliers'])->first();
		$supplier_state_id=$SupplierLedgersDetails->supplier->state_id;
		
		//$supplier_state_id=$supplier_ledger_detail->supplier->state_id;
		
		$partyOptions=[];
		foreach($Partyledgers as $Partyledger){ 
			//pr($Partyledger->supplier->state_id);
			$partyOptions[]=['text' =>$Partyledger->name, 'value' => $Partyledger->id,'state_id'=>$Partyledger->supplier->state_id];
		}
		
		$accountLedgers = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups->find()->where(['AccountingGroups.purchase_invoice_purchase_account'=>1,'AccountingGroups.company_id'=>$company_id])->first();

		$accountingGroups2 = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups
		->find('children', ['for' => $accountLedgers->id])
		->find('List')->toArray();
		$accountingGroups2[$accountLedgers->id]=$accountLedgers->name;
		ksort($accountingGroups2);
		if($accountingGroups2)
		{   
			$account_ids="";
			foreach($accountingGroups2 as $key=>$accountingGroup)
			{
				$account_ids .=$key.',';
			}
			$account_ids = explode(",",trim($account_ids,','));
			$Accountledgers = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
		//pr($Accountledgers->toArray());
		//exit;
        $companies = $this->PurchaseInvoices->Companies->find('list', ['limit' => 200]);
        $supplierLedgers = $this->PurchaseInvoices->SupplierLedgers->find('list', ['limit' => 200]);
        $this->set(compact('purchaseInvoice', 'companies', 'supplierLedgers','Grns','partyOptions','state_id','Accountledgers','supplier_state_id','Voucher_no_last'));
        $this->set('_serialize', ['purchaseInvoice']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Purchase Invoice id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$stateDetails=$this->Auth->User('session_company');
		$state_id=$stateDetails->state_id;
        $purchaseInvoice = $this->PurchaseInvoices->get($id, [
            'contain' => ['PurchaseInvoiceRows'=>['Items'=>['FirstGstFigures']]]
        ]);
		
        if ($this->request->is(['patch', 'post', 'put'])) {
            $purchaseInvoice = $this->PurchaseInvoices->patchEntity($purchaseInvoice, $this->request->getData());
			$purchaseInvoice->transaction_date = date("Y-m-d",strtotime($this->request->getData()['transaction_date']));
			$purchaseInvoice->company_id = $company_id;
			
            if ($this->PurchaseInvoices->save($purchaseInvoice)) {
				
				//$this->PurchaseInvoices->ItemLedgers->deleteAll(['ItemLedgers.purchase_invoice_id' => $purchaseInvoice->id]);
				$this->PurchaseInvoices->AccountingEntries->deleteAll(['AccountingEntries.purchase_invoice_id' => $purchaseInvoice->id]);
				$this->PurchaseInvoices->ReferenceDetails->deleteAll(['ReferenceDetails.purchase_invoice_id' => $purchaseInvoice->id]);
				
				
				//Accounting Entries for Purchase account//
				$AccountingEntrie = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
				$AccountingEntrie->ledger_id=$purchaseInvoice->purchase_ledger_id;
				$AccountingEntrie->debit=$purchaseInvoice->total_taxable_value;
				$AccountingEntrie->credit=0;
				$AccountingEntrie->transaction_date=$purchaseInvoice->transaction_date;
				$AccountingEntrie->company_id=$company_id;
				$AccountingEntrie->purchase_invoice_id=$purchaseInvoice->id;
				$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrie);
			  
			  
			  //Accounting Entries for Supplier account//
				$AccountingEntrie = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
				$AccountingEntrie->ledger_id=$purchaseInvoice->supplier_ledger_id;
				$AccountingEntrie->credit=$purchaseInvoice->total_amount;
				$AccountingEntrie->debit=0;
				$AccountingEntrie->transaction_date=$purchaseInvoice->transaction_date;
				$AccountingEntrie->company_id=$company_id;
				$AccountingEntrie->purchase_invoice_id=$purchaseInvoice->id;
				$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrie);
				
			//Accounting Entries for Round of Amount//
				$AccountingEntrie = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
				$RoundofLedgers = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()->where(['Ledgers.round_off'=>1,'Ledgers.company_id'=>$company_id])->first(); 
				$AccountingEntrie->ledger_id=$RoundofLedgers->id;
				$AccountingEntrie->transaction_date=$purchaseInvoice->transaction_date;
				$AccountingEntrie->company_id=$company_id;
				$AccountingEntrie->purchase_invoice_id=$purchaseInvoice->id;
				
				if($purchaseInvoice->total_round_amount > 0){
					$AccountingEntrie->debit=abs($purchaseInvoice->total_round_amount);
					$AccountingEntrie->credit=0;
				}else{
					$AccountingEntrie->credit=$purchaseInvoice->total_round_amount;
					$AccountingEntrie->debit=0;
				}
				if($purchaseInvoice->total_round_amount != 0){
					$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrie);
				}
				
				if($purchaseInvoice->is_interstate=='0'){
					foreach($purchaseInvoice->purchase_invoice_rows as $purchase_invoice_row)
					   { 
					   $gstAmtdata=$purchase_invoice_row->gst_value/2;
					   $gstAmtInsert=round($gstAmtdata,2);
					   
					   //Accounting Entries for GST//
					  $AccountingEntrieCGST = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
						$gstLedgerCGST = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$purchase_invoice_row->item_gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'input', 'Ledgers.gst_type'=>'CGST'])->first();
							
						$AccountingEntrieCGST->ledger_id=$gstLedgerCGST->id;
						$AccountingEntrieCGST->debit=$gstAmtInsert;
						$AccountingEntrieCGST->credit=0;
						$AccountingEntrieCGST->transaction_date=$purchaseInvoice->transaction_date;
						$AccountingEntrieCGST->company_id=$company_id;
						$AccountingEntrieCGST->purchase_invoice_id=$purchaseInvoice->id;
						$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrieCGST);
						
						$AccountingEntrieSGST = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
						$gstLedgerSGST = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$purchase_invoice_row->item_gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'input', 'Ledgers.gst_type'=>'SGST'])->first();
						$AccountingEntrieSGST->ledger_id=$gstLedgerSGST->id;
						$AccountingEntrieSGST->debit=$gstAmtInsert;
						$AccountingEntrieSGST->credit=0;
						$AccountingEntrieSGST->transaction_date=$purchaseInvoice->transaction_date;
						$AccountingEntrieSGST->company_id=$company_id;
						$AccountingEntrieSGST->purchase_invoice_id=$purchaseInvoice->id;
						$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrieSGST);
					   }
				}else{
					foreach($purchaseInvoice->purchase_invoice_rows as $purchase_invoice_row)
					   {
						   //Accounting Entries for IGST//
						$AccountingEntrieIGST = $this->PurchaseInvoices->AccountingEntries->newEntity(); 
						$gstLedgerSGST = $this->PurchaseInvoices->PurchaseInvoiceRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$purchase_invoice_row->item_gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'input', 'Ledgers.gst_type'=>'IGST'])->first();
						$AccountingEntrieIGST->ledger_id=$gstLedgerSGST->id;
						$AccountingEntrieIGST->debit=$purchase_invoice_row->gst_value;
						$AccountingEntrieIGST->credit=0;
						$AccountingEntrieIGST->transaction_date=$purchaseInvoice->transaction_date;
						$AccountingEntrieIGST->company_id=$company_id;
						$AccountingEntrieIGST->purchase_invoice_id=$purchaseInvoice->id;
						$this->PurchaseInvoices->AccountingEntries->save($AccountingEntrieIGST);
					   }
				}
				
				//Refrence Details For Party/Supplier  //
				$ReferenceDetail = $this->PurchaseInvoices->ReferenceDetails->newEntity(); 
				$ReferenceDetail->ledger_id=$purchaseInvoice->supplier_ledger_id;
				$ReferenceDetail->credit=$purchaseInvoice->total_amount;
				$ReferenceDetail->debit=0;
				$ReferenceDetail->transaction_date=$purchaseInvoice->transaction_date;
				$ReferenceDetail->company_id=$company_id;
				$ReferenceDetail->type='New Ref';
				$ReferenceDetail->ref_name='PI'.$purchaseInvoice->voucher_no;
				$ReferenceDetail->purchase_invoice_id=$purchaseInvoice->id;
				$this->PurchaseInvoices->ReferenceDetails->save($ReferenceDetail);
				  
                $this->Flash->success(__('The purchase invoice has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase invoice could not be saved. Please, try again.'));
        }
		
		$partyParentGroups = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.
						purchase_invoice_party'=>'1']);
						//pr($partyParentGroups->toArray()); exit;
		$partyGroups=[];
		foreach($partyParentGroups as $partyParentGroup)
		{
			$accountingGroups = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups
			->find('children', ['for' => $partyParentGroup->id])->toArray();
			$partyGroups[]=$partyParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$partyGroups[]=$accountingGroup->id;
			}
		}
		
		if($partyGroups)
		{  
			$Partyledgers = $this->PurchaseInvoices->Grns->SupplierLedgers->find()
							->where(['SupplierLedgers.accounting_group_id IN' =>$partyGroups,'SupplierLedgers.company_id'=>$company_id])
							->contain(['Suppliers']);
        }
		
		
		$partyOptions=[];
		foreach($Partyledgers as $Partyledger){ 
			//pr($Partyledger->supplier->state_id);
			$partyOptions[]=['text' =>$Partyledger->name, 'value' => $Partyledger->id,'state_id'=>$Partyledger->supplier->state_id];
		}
		
		$accountLedgers = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups->find()->where(['AccountingGroups.purchase_invoice_purchase_account'=>1,'AccountingGroups.company_id'=>$company_id])->first();
		
		$supplier_ledger_detail = $this->PurchaseInvoices->SupplierLedgers->find()
							->where(['SupplierLedgers.id'=>$purchaseInvoice->supplier_ledger_id])
							->contain(['Suppliers'])
							->first();
						//pr($supplier_ledger_detail); exit;
		$supplier_state_id=$supplier_ledger_detail->supplier->state_id;
		$accountingGroups2 = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->AccountingGroups
		->find('children', ['for' => $accountLedgers->id])
		->find('List')->toArray();
		$accountingGroups2[$accountLedgers->id]=$accountLedgers->name;
		ksort($accountingGroups2);
		if($accountingGroups2)
		{   
			$account_ids="";
			foreach($accountingGroups2 as $key=>$accountingGroup)
			{
				$account_ids .=$key.',';
			}
			$account_ids = explode(",",trim($account_ids,','));
			$Accountledgers = $this->PurchaseInvoices->Grns->GrnRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
        $companies = $this->PurchaseInvoices->Companies->find('list', ['limit' => 200]);
        $supplierLedgers = $this->PurchaseInvoices->SupplierLedgers->find('list', ['limit' => 200]);
        $this->set(compact('purchaseInvoice', 'companies', 'supplierLedgers', 'companies', 'partyOptions','state_id','Accountledgers','supplier_state_id'));
        $this->set('_serialize', ['purchaseInvoice']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Purchase Invoice id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purchaseInvoice = $this->PurchaseInvoices->get($id);
        if ($this->PurchaseInvoices->delete($purchaseInvoice)) {
            $this->Flash->success(__('The purchase invoice has been deleted.'));
        } else {
            $this->Flash->error(__('The purchase invoice could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function PurchaseInvoiceReturn()
	{ 
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$stateDetails=$this->Auth->User('session_company');
		@$purchase_invoice_no=$this->request->query('purchase_invoice_no');
		$PurchaseInvoiceStatus="No";
		if(!empty(@$purchase_invoice_no)){ 
		$PurchaseInvoice = $this->PurchaseInvoices->find()
						->where(['PurchaseInvoices.voucher_no' =>$purchase_invoice_no,'PurchaseInvoices.company_id'=>$company_id])
						->contain(['Companies', 'SupplierLedgers'])
						->first();
		//pr($PurchaseInvoices);  exit;
		
		$PurchaseInvoiceStatus="Yes";
		}	

		$this->set(compact('PurchaseInvoiceStatus','PurchaseInvoice'));
		
	}
}

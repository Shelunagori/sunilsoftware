<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CreditNotes Controller
 *
 * @property \App\Model\Table\CreditNotesTable $CreditNotes
 *
 * @method \App\Model\Entity\CreditNote[] paginate($object = null, array $settings = [])
 */
class CreditNotesController extends AppController
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
            'contain' => ['Companies','SalesLedgers','PartyLedgers','CreditNoteRows']
        ];
		
		$creditNotes = $this->paginate($this->CreditNotes->find()->where(['CreditNotes.company_id'=>$company_id]));
        $this->set(compact('creditNotes'));
        $this->set('_serialize', ['creditNotes']);
    }

    /**
     * View method
     *
     * @param string|null $id Credit Note id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $creditNote = $this->CreditNotes->get($id, [
            'contain' => ['Companies', 'PartyLedgers', 'SalesLedgers', 'CreditNoteRows']
        ]);

        $this->set('creditNote', $creditNote);
        $this->set('_serialize', ['creditNote']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
        $creditNote = $this->CreditNotes->newEntity();
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$stateDetails=$this->Auth->User('session_company');
		$state_id=$stateDetails->state_id;
		
		// roundoff id find in ledger table start
		$roundOffId = $this->CreditNotes->CreditNoteRows->Ledgers->find()
		->where(['Ledgers.company_id'=>$company_id, 'Ledgers.round_off'=>1])->first();
		// roundoff id find in ledger table end
		
		//auto increament voucher no start
		$Voucher_no = $this->CreditNotes->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher_no)
		{
			$voucher_no=$Voucher_no->voucher_no+1;
		}
		else
		{
			$voucher_no=1;
		} 		
		//auto increament voucher no end
		
		if ($this->request->is('post')) {
			$transaction_date=date('Y-m-d', strtotime($this->request->data['transaction_date']));
            $creditNote = $this->CreditNotes->patchEntity($creditNote, $this->request->getData());
			$creditNote->transaction_date=$transaction_date;
			
			if($creditNote->cash_or_credit=='credit')
			{
				 $creditNote->party_ledger_id=$creditNote->party_ledger_id;
			}
			else{
				 $creditNote->party_ledger_id=$creditNote->cash_party_ledger_id;
			}
		
		
		
            if ($this->CreditNotes->save($creditNote)) {
				
				//item ledger entry start
				foreach($creditNote->credit_note_rows as $credit_note_row)
				{
					$stockData = $this->CreditNotes->ItemLedgers->query();
					$stockData->insert(['item_id', 'transaction_date','quantity', 'rate', 'amount', 'status', 'company_id', 'credit_note_id', 'credit_note_row_id', 'location_id'])
						->values([
						'item_id' => $credit_note_row->item_id,
						'transaction_date' => $creditNote->transaction_date,
						'quantity' => $credit_note_row->quantity,
						'rate' => $credit_note_row->rate,
						'amount' => $credit_note_row->net_amount,
						'status' => 'in',
						'company_id' => $creditNote->company_id,
						'credit_note_id' => $creditNote->id,
						'credit_note_row_id' => $credit_note_row->id,
						'location_id'=>$location_id
						])
					->execute();
				}
				$partyData = $this->CreditNotes->AccountingEntries->query();
						$partyData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $creditNote->party_ledger_id,
								'debit' => '',
								'credit' => $creditNote->amount_after_tax,
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
						$accountData = $this->CreditNotes->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $creditNote->sales_ledger_id,
								'debit' => $creditNote->amount_before_tax,
								'credit' => '',
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
						if(str_replace('-',' ',$creditNote->round_off)>0)
						{
						 $roundData = $this->CreditNotes->AccountingEntries->query();
						if($creditNote->isRoundofType=='0')
						{
						 $debit=str_replace('-',' ',$creditNote->round_off);
						 $credit=0;
						}
						else if($creditNote->isRoundofType=='1')
						{
						 $credit=str_replace('-',' ',$creditNote->round_off);
						 $debit=0;
						}
						 $roundData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $roundOffId->id,
								'debit' => $debit,
								'credit' => $credit,
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
						}
						
           if($creditNote->is_interstate=='0'){
		   for(@$i=0; $i<2; $i++){
			   foreach($creditNote->credit_note_rows as $credit_note_row)
			   {
			    $gstVal=$credit_note_row->gst_value/2;
			   if($i==0){
			    $gstLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$credit_note_row->gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'CGST'])->first();
			    $ledgerId=$gstLedgers->id;
			   }
			   if($i==1){ 
			    $gstLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$credit_note_row->gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'SGST'])->first();
			    $ledgerId=$gstLedgers->id;
			   }
			    $accountData = $this->CreditNotes->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $ledgerId,
								'debit' => $gstVal,
								'credit' => '',
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
			   }
			 }
		   }
		   else if($creditNote->is_interstate=='1'){
		   foreach($creditNote->credit_note_rows as $credit_note_row)
			   {
			   $gstLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$credit_note_row->gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'IGST'])->first();
			   $ledgerId=$gstLedgers->id;
			   $accountData = $this->CreditNotes->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $ledgerId,
								'debit' => $credit_note_row->gst_value,
								'credit' => '',
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
			   }
			
		   }
           $this->Flash->success(__('The credit note has been saved.'));

                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('The credit note could not be saved. Please, try again.'));
        }
		$customers = $this->CreditNotes->Customers->find()
					->where(['company_id'=>$company_id]);
		$customerOptions=[];
		foreach($customers as $customer){
			$customerOptions[]=['text' =>$customer->name, 'value' => $customer->id ,'customer_state_id'=>$customer->state_id];
		}
		
		$items = $this->CreditNotes->CreditNoteRows->Items->find()
					->where(['Items.company_id'=>$company_id])
					->contain(['FirstGstFigures', 'SecondGstFigures', 'Units']);
		$itemOptions=[];
		foreach($items as $item){
			$itemOptions[]=['text'=>$item->item_code.' '.$item->name, 'value'=>$item->id, 'first_gst_figure_id'=>$item->first_gst_figure_id, 'gst_amount'=>$item->gst_amount, 'second_gst_figure_id'=>$item->second_gst_figure_id, 'FirstGstFigure'=>$item->FirstGstFigures->tax_percentage, 'SecondGstFigure'=>$item->SecondGstFigures->tax_percentage];
		}
	
        $partyParentGroups = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.credit_note_party'=>'1']);
		$partyGroups=[];
		foreach($partyParentGroups as $partyParentGroup)
		{
			$accountingGroups = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups
			->find('children', ['for' => $partyParentGroup->id])->toArray();
			$partyGroups[]=$partyParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$partyGroups[]=$accountingGroup->id;
			}
		}
		if($partyGroups)
		{  
			$Partyledgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.accounting_group_id IN' =>$partyGroups,'Ledgers.company_id'=>$company_id])
							->contain(['Customers']);
        }
		$partyOptions=[];
		foreach($Partyledgers as $Partyledger){
			$partyOptions[]=['text' =>$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id];
		}
		
		$accountLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups->find()->where(['AccountingGroups.credit_note_sales_account'=>1,'AccountingGroups.company_id'=>$company_id])->first();

		$accountingGroups2 = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups
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
			$Accountledgers = $this->CreditNotes->CreditNoteRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
		$gstFigures = $this->CreditNotes->CreditNoteRows->GstFigures->find('list')
						->where(['company_id'=>$company_id]);
		$CashPartyLedgers = $this->CreditNotes->Ledgers->find('list')
							->where(['Ledgers.cash ' =>1,'Ledgers.company_id'=>$company_id]);
		
        $this->set(compact('creditNote','companies', 'customerOptions', 'gstFigures', 'voucher_no','company_id','itemOptions','state_id', 'partyOptions', 'Accountledgers','CashPartyLedgers'));
        $this->set('_serialize', ['creditNote']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Credit Note id.
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
		
		 $creditNote = $this->CreditNotes->get($id, [
            'contain' => (['CreditNoteRows'=>['Items', 'GstFigures']])
        ]);
		// roundoff id find in ledger table start
		$roundOffId = $this->CreditNotes->CreditNoteRows->Ledgers->find()
		->where(['Ledgers.company_id'=>$company_id, 'Ledgers.round_off'=>1])->first();
		// roundoff id find in ledger table end
		//auto increament voucher no start
		$Voucher_no = $this->CreditNotes->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher_no)
		{
			$voucher_no=$Voucher_no->voucher_no+1;
		}
		else
		{
			$voucher_no=1;
		} 		
		//auto increament voucher no end
		if ($this->request->is('put','post', 'patch')) {
			$transaction_date=date('Y-m-d', strtotime($this->request->data['transaction_date']));
            $creditNote = $this->CreditNotes->patchEntity($creditNote, $this->request->getData());
			$creditNote->transaction_date=$transaction_date;
			
			if($creditNote->cash_or_credit=='credit')
			{
				 $creditNote->party_ledger_id=$creditNote->party_ledger_id;
			}
			else{
				 $creditNote->party_ledger_id=$creditNote->cash_party_ledger_id;
			}
	
            if ($this->CreditNotes->save($creditNote)) {
				$deleteItemLedger = $this->CreditNotes->ItemLedgers->query();
				$deleteResult = $deleteItemLedger->delete()
					->where(['credit_note_id' => $creditNote->id])
					->execute();
					$deleteAccountEntries = $this->CreditNotes->AccountingEntries->query();
					$result = $deleteAccountEntries->delete()
						->where(['AccountingEntries.credit_note_id' => $id])
						->execute();
				//item ledger entry start
				foreach($creditNote->credit_note_rows as $credit_note_row)
				{
				$exactRate=$credit_note_row->taxable_value/$credit_note_row->quantity;
					$stockData = $this->CreditNotes->ItemLedgers->query();
					$stockData->insert(['item_id', 'transaction_date','quantity', 'rate', 'amount', 'status', 'company_id', 'credit_note_id', 'credit_note_row_id', 'location_id'])
						->values([
						'item_id' => $credit_note_row->item_id,
						'transaction_date' => $creditNote->transaction_date,
						'quantity' => $credit_note_row->quantity,
						'rate' => $exactRate,
						'amount' => $credit_note_row->taxable_value,
						'status' => 'in',
						'company_id' => $creditNote->company_id,
						'credit_note_id' => $creditNote->id,
						'credit_note_row_id' => $credit_note_row->id,
						'location_id'=> $location_id
						])
					->execute();
					/* 
					$stockData = $this->CreditNotes->ItemLedgers->query();
				    $result = $stockData->update()
                    ->set(['item_id' => $credit_note_row->item_id, 'transaction_date' => $creditNote->transaction_date, 'quantity' => $credit_note_row->quantity,
					'rate' => $exactRate,
						'amount' => $credit_note_row->taxable_value,
						'status' => 'in',
						'company_id' => $creditNote->company_id,
						'credit_note_id' => $creditNote->id,
						'credit_note_row_id' => $credit_note_row->id,
						'location_id'=> $location_id])
                    ->where(['ItemLedgers.credit_note_row_id' => $credit_note_row->id])
                    ->execute(); */
					
				}
				$partyData = $this->CreditNotes->AccountingEntries->query();
						$partyData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $creditNote->party_ledger_id,
								'debit' => '',
								'credit' => $creditNote->amount_after_tax,
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
						$accountData = $this->CreditNotes->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $creditNote->sales_ledger_id,
								'debit' => $creditNote->amount_before_tax,
								'credit' => '',
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
						if(str_replace('-',' ',$creditNote->round_off)>0)
						{
						 $roundData = $this->CreditNotes->AccountingEntries->query();
						if($creditNote->isRoundofType=='0')
						{
						 $debit=str_replace('-',' ',$creditNote->round_off);
						 $credit=0;
						}
						else if($creditNote->isRoundofType=='1')
						{
						 $credit=str_replace('-',' ',$creditNote->round_off);
						 $debit=0;
						}
						$roundData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $roundOffId->id,
								'debit' => $debit,
								'credit' => $credit,
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
						}
           if($creditNote->is_interstate=='0'){
		   for(@$i=0; $i<2; $i++){
			   foreach($creditNote->credit_note_rows as $credit_note_row)
			   {
			    $gstVal=$credit_note_row->gst_value/2;
			   if($i==0){
			    $gstLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$credit_note_row->gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'CGST'])->first();
			    $ledgerId=$gstLedgers->id;
			   }
			   if($i==1){ 
			    $gstLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$credit_note_row->gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'SGST'])->first();
			    $ledgerId=$gstLedgers->id;
			   }
			    $accountData = $this->CreditNotes->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $ledgerId,
								'debit' => $gstVal,
								'credit' => '',
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
			   }
			 }
		   }
		   else if($creditNote->is_interstate=='1'){
		   foreach($creditNote->credit_note_rows as $credit_note_row)
			   {
			   $gstLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.gst_figure_id' =>$credit_note_row->gst_figure_id,'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'IGST'])->first();
			   $ledgerId=$gstLedgers->id;
			   $accountData = $this->CreditNotes->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'credit_note_id'])
								->values([
								'ledger_id' => $ledgerId,
								'debit' => $credit_note_row->gst_value,
								'credit' => '',
								'transaction_date' => $creditNote->transaction_date,
								'company_id' => $creditNote->company_id,
								'credit_note_id' => $creditNote->id
								])
						->execute();
			   }
			
		   }
			
                $this->Flash->success(__('The credit note has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The credit note could not be saved. Please, try again.'));
        }
	     $customers = $this->CreditNotes->Customers->find()
					->where(['company_id'=>$company_id]);
		 $customerOptions=[];
		foreach($customers as $customer){
			$customerOptions[]=['text' =>$customer->name, 'value' => $customer->id ,'customer_state_id'=>$customer->state_id];
		}
		
		$items = $this->CreditNotes->CreditNoteRows->Items->find()
					->where(['Items.company_id'=>$company_id])
					->contain(['FirstGstFigures', 'SecondGstFigures', 'Units']);
		$itemOptions=[];
		foreach($items as $item){
			
			$itemOptions[]=['text'=>$item->item_code.' '.$item->name, 'value'=>$item->id, 'first_gst_figure_id'=>$item->first_gst_figure_id, 'gst_amount'=>$item->gst_amount, 'second_gst_figure_id'=>$item->second_gst_figure_id, 'FirstGstFigure'=>$item->FirstGstFigures->tax_percentage, 'SecondGstFigure'=>$item->SecondGstFigures->tax_percentage];
		}
        $partyParentGroups = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.credit_note_party'=>'1']);
		$partyGroups=[];
		foreach($partyParentGroups as $partyParentGroup)
		{
			$accountingGroups = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups
			->find('children', ['for' => $partyParentGroup->id])->toArray();
			$partyGroups[]=$partyParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$partyGroups[]=$accountingGroup->id;
			}
		}
		if($partyGroups)
		{  
			$Partyledgers = $this->CreditNotes->CreditNoteRows->Ledgers->find()
							->where(['Ledgers.accounting_group_id IN' =>$partyGroups,'Ledgers.company_id'=>$company_id])
							->contain(['Customers']);
        }
		$partyOptions=[];
		foreach($Partyledgers as $Partyledger){
			$partyOptions[]=['text' =>$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id];
		}
		
		$accountLedgers = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups->find()->where(['AccountingGroups.credit_note_sales_account'=>1,'AccountingGroups.company_id'=>$company_id])->first();

		$accountingGroups2 = $this->CreditNotes->CreditNoteRows->Ledgers->AccountingGroups
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
			$Accountledgers = $this->CreditNotes->CreditNoteRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
						
		$gstFigures = $this->CreditNotes->CreditNoteRows->GstFigures->find('list')
						->where(['company_id'=>$company_id]);
		$CashPartyLedgers = $this->CreditNotes->Ledgers->find('list')
							->where(['Ledgers.cash ' =>1,'Ledgers.company_id'=>$company_id]);
		
        $this->set(compact('creditNote','companies', 'customerOptions', 'gstFigures', 'voucher_no','company_id','itemOptions','state_id', 'partyOptions', 'Accountledgers','CashPartyLedgers'));
        $this->set('_serialize', ['creditNote']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Credit Note id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $creditNote = $this->CreditNotes->get($id);
        if ($this->CreditNotes->delete($creditNote)) {
            $this->Flash->success(__('The credit note has been deleted.'));
        } else {
            $this->Flash->error(__('The credit note could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function creditNoteBill($id=null)
    {
	    $this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$stateDetails=$this->Auth->User('session_company');
		$state_id=$stateDetails->state_id;
		$invoiceBills= $this->CreditNotes->find()
		->where(['CreditNotes.id'=>$id])
		->contain(['Companies'=>['States'],'CreditNoteRows'=>['Items'=>['Sizes'], 'GstFigures']]);
	
	    foreach($invoiceBills->toArray() as $data){
		foreach($data->credit_note_rows as $credit_note_row){
		$item_id=$credit_note_row->item_id;
		$accountingEntries= $this->CreditNotes->AccountingEntries->find()
		->where(['AccountingEntries.credit_note_id'=>$data->id]);
		$credit_note_row->accountEntries=$accountingEntries->toArray();
		
			$partyDetail= $this->CreditNotes->CreditNoterows->Ledgers->find()
			->where(['id'=>$data->party_ledger_id])->first();
		    $partyCustomerid=$partyDetail->customer_id;
			if($partyCustomerid>0)
			{
				$partyDetails= $this->CreditNotes->Customers->find()
				->where(['Customers.id'=>$partyCustomerid])
				->contain(['States'])->first();
				$data->partyDetails=$partyDetails;
			}
			else
			{
				$partyDetails=(object)['name'=>'Cash Customer', 'state_id'=>$state_id];
				$data->partyDetails=$partyDetails;
			}
		}
		}
		$this->set(compact('invoiceBills'));
        $this->set('_serialize', ['invoiceBills']);
    }
		
}

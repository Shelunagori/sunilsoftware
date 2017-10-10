<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * DebitNotes Controller
 *
 * @property \App\Model\Table\DebitNotesTable $DebitNotes
 *
 * @method \App\Model\Entity\DebitNote[] paginate($object = null, array $settings = [])
 */
class DebitNotesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
	$this->viewBuilder()->layout('index_layout');
        $this->paginate = [
            'contain' => ['Companies']
        ];
        $debitNotes = $this->paginate($this->DebitNotes);
        $this->set(compact('debitNotes'));
        $this->set('_serialize', ['debitNotes']);
    }

    /**
     * View method
     *
     * @param string|null $id Debit Note id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
	    $company_id=$this->Auth->User('session_company_id');
        $debitNotes = $this->DebitNotes->find()->where(['DebitNotes.company_id'=>$company_id, 'DebitNotes.id'=>$id])
		->contain(['Companies', 'DebitNoteRows'=>['ReferenceDetails', 'Ledgers']]);
	
        $this->set(compact('debitNotes'));
        $this->set('_serialize', ['debitNotes']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {   
		$this->viewBuilder()->layout('index_layout');
        $debitNote = $this->DebitNotes->newEntity();
		$company_id=$this->Auth->User('session_company_id');
        if ($this->request->is('post')) {
		 $debitNote = $this->DebitNotes->patchEntity($debitNote, $this->request->getData(),['associated' => ['DebitNoteRows','DebitNoteRows.ReferenceDetails']]);
		 $tdate=$this->request->data('transaction_date');
		 $debitNote->transaction_date=date('Y-m-d',strtotime($tdate));
		 
            if ($this->DebitNotes->save($debitNote)) {
			
			foreach($debitNote->debit_note_rows as $debit_note_row)
				{
					$accountEntry = $this->DebitNotes->AccountingEntries->newEntity();
					$accountEntry->ledger_id                  = $debit_note_row->ledger_id;
					$accountEntry->debit                      = @$debit_note_row->debit;
					$accountEntry->credit                     = @$debit_note_row->credit;
					$accountEntry->transaction_date           = $debitNote->transaction_date;
					$accountEntry->company_id                 = $company_id;
					$accountEntry->debit_note_id                 = $debitNote->id;
					$accountEntry->debit_note_row_id             = $debit_note_row->id;
					$this->DebitNotes->AccountingEntries->save($accountEntry);
				}
			
                $this->Flash->success(__('The Debit Note Note has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Debit Note Note could not be saved. Please, try again.'));
        }
		$Voucher_no = $this->DebitNotes->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher_no)
		{
			$voucher_no=$Voucher_no->voucher_no+1;
		}
		else
		{
			$voucher_no=1;
		}
		
		//bank group
		$bankParentGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.bank'=>'1']);
						
		$bankGroups=[];
		
		foreach($bankParentGroups as $bankParentGroup)
		{
			$accountingGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups
			->find('children', ['for' => $bankParentGroup->id])->toArray();
			$bankGroups[]=$bankParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$bankGroups[]=$accountingGroup->id;
			}
		}
		
		//cash-in-hand group
		$cashParentGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.cash'=>'1']);
						
		$cashGroups=[];
		
		foreach($cashParentGroups as $cashParentGroup)
		{
			$cashChildGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups
			->find('children', ['for' => $cashParentGroup->id])->toArray();
			$cashGroups[]=$cashParentGroup->id;
			foreach($cashChildGroups as $cashChildGroup){
				$cashGroups[]=$cashChildGroup->id;
			}
		}
		
		$partyParentGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find()
							->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.debit_note_ledger'=>1]);

		$partyGroups=[];
		
		foreach($partyParentGroups as $partyParentGroup)
		{
			
			$partyChildGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find('children', ['for' => $partyParentGroup->id]);
			$partyGroups[]=$partyParentGroup->id;
			foreach($partyChildGroups as $partyChildGroup){
				$partyGroups[]=$partyChildGroup->id;
			}
		}
	
		$partyLedgers = $this->DebitNotes->DebitNoteRows->Ledgers->find()
		->where(['Ledgers.accounting_group_id IN' =>$partyGroups,'Ledgers.company_id'=>$company_id]);
		
		//$ledgers = $this->Payments->PaymentRows->Ledgers->find()->where(['company_id'=>$company_id]);
		foreach($partyLedgers as $ledger){
			if(in_array($ledger->accounting_group_id,$bankGroups)){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id ,'open_window' => 'bank','bank_and_cash' => 'yes'];
			}
			else if($ledger->bill_to_bill_accounting == 'yes'){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'party','bank_and_cash' => 'no'];
			}
			else if(in_array($ledger->accounting_group_id,$cashGroups)){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id ,'open_window' => 'no','bank_and_cash' => 'yes'];
			}
			else{
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'no','bank_and_cash' => 'no' ];
			}
		}
		$referenceDetails=$this->DebitNotes->DebitNoteRows->ReferenceDetails->find('list');
        $companies = $this->DebitNotes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('debitNote', 'companies','voucher_no','ledgerOptions','company_id','referenceDetails'));
        $this->set('_serialize', ['debitNote']);
    }

    /**
     * Edit method
     *
     * @param string|null $id debitNote id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    
    /**
     * Edit method
     *
     * @param string|null $id debitNote id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
     public function edit($id = null)
    {
	
        $this->viewBuilder()->layout('index_layout');
        $debitNote = $this->DebitNotes->get($id, [
            'contain' => ['DebitNoteRows'=>['ReferenceDetails']]
        ]);
		$company_id=$this->Auth->User('session_company_id');
		
		$refDropDown =[];
		foreach($debitNote->debit_note_rows as $debit_note_row)
		{
			if(!empty($debit_note_row->reference_details))
			{
				$query = $this->DebitNotes->DebitNoteRows->ReferenceDetails->find();
				$query->select(['total_debit' => $query->func()->sum('ReferenceDetails.debit'),'total_credit' => $query->func()->sum('ReferenceDetails.credit')])
				->where(['ReferenceDetails.ledger_id'=>$debit_note_row->ledger_id,'ReferenceDetails.type !='=>'On Account'])
				->group(['ReferenceDetails.ref_name'])
				->autoFields(true);
				$referenceDetails=$query;
				$option=[];
				foreach($referenceDetails as $referenceDetail){
					$remider=$referenceDetail->total_debit-$referenceDetail->total_credit;
					if($remider>0){
						$bal=abs($remider).' Dr';
					}else if($remider<0){
						$bal=abs($remider).' Cr';
					}
					if($referenceDetail->total_debit!=$referenceDetail->total_credit){
						$option[$referenceDetail->ref_name]=$referenceDetail->ref_name;
					}
				}
				$refDropDown[$debit_note_row->id] = $option;
			}
		}
		
		$originaldebitNote=$debitNote;
        if ($this->request->is('put','patch','post')) {
		
	
		//GET ORIGINAL DATA AND DELETE REFERENCE DATA//
			$orignaldebit_note_ids=[];
			foreach($originaldebitNote->debit_note_rows as $originaldebitNote_rows){
				$orignaldebit_note_ids[]=$originaldebitNote_rows->id;
			}
			$this->DebitNotes->DebitNoteRows->ReferenceDetails->deleteAll(['ReferenceDetails.debit_note_row_id IN'=>$orignaldebit_note_ids]);
			
			$query_update = $this->DebitNotes->DebitNoteRows->query();
					$query_update->update()
					->set(['mode_of_payment' => '', 'cheque_no' => '', 'cheque_date' => ''])
					->where(['debit_note_id' => $debitNote->id])
					->execute();
			//GET ORIGINAL DATA AND DELETE REFERENCE DATA//
			
		
		
		 $debitNote = $this->DebitNotes->patchEntity($debitNote, $this->request->getData(),['associated' => ['DebitNoteRows','DebitNoteRows.ReferenceDetails']]);
		 $tdate=$this->request->data('transaction_date');
		 $debitNote->transaction_date=date('Y-m-d',strtotime($tdate));
		 
	
		
            if ($this->DebitNotes->save($debitNote)) {
			$query_delete = $this->DebitNotes->AccountingEntries->query();
					$query_delete->delete()
					->where(['credit_note_id' => $debitNote->id,'company_id'=>$company_id])
					->execute();
			
			foreach($debitNote->debit_note_rows as $debit_note_row)
				{
					$accountEntry = $this->DebitNotes->AccountingEntries->newEntity();
					$accountEntry->ledger_id                  = $debit_note_row->ledger_id;
					$accountEntry->debit                      = @$debit_note_row->debit;
					$accountEntry->credit                     = @$debit_note_row->credit;
					$accountEntry->transaction_date           = $debitNote->transaction_date;
					$accountEntry->company_id                 = $company_id;
					$accountEntry->debit_note_id                 = $debitNote->id;
					$accountEntry->debit_note_row_id             = $debit_note_row->id;
					$this->DebitNotes->AccountingEntries->save($accountEntry);
				}

                $this->Flash->success(__('The Debit Note has been Update.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The Debit Note could not be saved. Please, try again.'));
        }
		
		
		//bank group
		$bankParentGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.bank'=>'1']);
						
		$bankGroups=[];
		
		foreach($bankParentGroups as $bankParentGroup)
		{
			$accountingGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups
			->find('children', ['for' => $bankParentGroup->id])->toArray();
			$bankGroups[]=$bankParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$bankGroups[]=$accountingGroup->id;
			}
		}
		
		//cash-in-hand group
		$cashParentGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.cash'=>'1']);
						
		$cashGroups=[];
		
		foreach($cashParentGroups as $cashParentGroup)
		{
			$cashChildGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups
			->find('children', ['for' => $cashParentGroup->id])->toArray();
			$cashGroups[]=$cashParentGroup->id;
			foreach($cashChildGroups as $cashChildGroup){
				$cashGroups[]=$cashChildGroup->id;
			}
		}
		
		$partyParentGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find()
							->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.debit_note_ledger'=>1]);

		$partyGroups=[];
		
		foreach($partyParentGroups as $partyParentGroup)
		{
			
			$partyChildGroups = $this->DebitNotes->DebitNoteRows->Ledgers->AccountingGroups->find('children', ['for' => $partyParentGroup->id]);
			$partyGroups[]=$partyParentGroup->id;
			foreach($partyChildGroups as $partyChildGroup){
				$partyGroups[]=$partyChildGroup->id;
			}
		}
	
		$partyLedgers = $this->DebitNotes->DebitNoteRows->Ledgers->find()
		->where(['Ledgers.accounting_group_id IN' =>$partyGroups,'Ledgers.company_id'=>$company_id]);
		
		//$ledgers = $this->Payments->PaymentRows->Ledgers->find()->where(['company_id'=>$company_id]);
		foreach($partyLedgers as $ledger){
			if(in_array($ledger->accounting_group_id,$bankGroups)){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id ,'open_window' => 'bank','bank_and_cash' => 'yes'];
			}
			else if($ledger->bill_to_bill_accounting == 'yes'){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'party','bank_and_cash' => 'no'];
			}
			else if(in_array($ledger->accounting_group_id,$cashGroups)){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id ,'open_window' => 'no','bank_and_cash' => 'yes'];
			}
			else{
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'no','bank_and_cash' => 'no' ];
			}
		}
		
		
		$referenceDetails=$this->DebitNotes->DebitNoteRows->ReferenceDetails->find('list');
        $companies = $this->DebitNotes->Companies->find('list', ['limit' => 200]);
        $this->set(compact('debitNote', 'companies','voucher_no','ledgerOptions','company_id','referenceDetails','refDropDown'));
        $this->set('_serialize', ['debitNote']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Debit Note id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $debitNote = $this->DebitNotes->get($id);
        if ($this->DebitNotes->delete($debitNote)) {
            $this->Flash->success(__('The debit note has been deleted.'));
        } else {
            $this->Flash->error(__('The debit note could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Receipts Controller
 *
 * @property \App\Model\Table\ReceiptsTable $Receipts
 *
 * @method \App\Model\Entity\Receipt[] paginate($object = null, array $settings = [])
 */
class ReceiptsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Companies']
        ];
        $receipts = $this->paginate($this->Receipts);

        $this->set(compact('receipts'));
        $this->set('_serialize', ['receipts']);
    }

    /**
     * View method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {   
        $receipt = $this->Receipts->get($id, [
            'contain' => ['Companies', 'ReceiptRows', 'ReferenceDetails']
        ]);

        $this->set('receipt', $receipt);
        $this->set('_serialize', ['receipt']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    { $this->viewBuilder()->layout('index_layout');
        $receipt = $this->Receipts->newEntity();
		$company_id=$this->Auth->User('session_company_id');
        if ($this->request->is('post')) {
		 $receipt = $this->Receipts->patchEntity($receipt, $this->request->getData());
            if ($this->Receipts->save($receipt)) {
                $this->Flash->success(__('The receipt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The receipt could not be saved. Please, try again.'));
        }
		$Voucher_no = $this->Receipts->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher_no)
		{
			$voucher_no=$Voucher_no->voucher_no+1;
		}
		else
		{
			$voucher_no=1;
		}
        $partyParentGroups = $this->Receipts->ReceiptRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id]);
		$partyGroups=[];
		foreach($partyParentGroups as $partyParentGroup)
		{
			$accountingGroups = $this->Receipts->ReceiptRows->Ledgers->AccountingGroups
			->find('children', ['for' => $partyParentGroup->id])->toArray();
			$partyGroups[]=$partyParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$partyGroups[]=$accountingGroup->id;
			}
		}
		
		if($partyGroups)
		{  
			$Partyledgers = $this->Receipts->ReceiptRows->Ledgers->find()
							->where(['Ledgers.accounting_group_id IN' =>$partyGroups,'Ledgers.company_id'=>$company_id])
							->contain(['Customers']);
        }
		$partyOptions=[];
		
		foreach($Partyledgers as $Partyledger){
		$prty = $this->Receipts->ReceiptRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.id'=>$Partyledger->accounting_group_id])->first();
            
			
			$partyOptions[]=['text' =>str_pad(@$Partyledger->customer->customer_id, 4, '0', STR_PAD_LEFT).' - '.$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id, 'bill_to_bill_accounting'=>@$Partyledger->bill_to_bill_accounting, 'account_type'=>@$prty->bank];
		}
		
        $companies = $this->Receipts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('receipt', 'companies','voucher_no','partyOptions'));
        $this->set('_serialize', ['receipt']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $receipt = $this->Receipts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $receipt = $this->Receipts->patchEntity($receipt, $this->request->getData());
            if ($this->Receipts->save($receipt)) {
                $this->Flash->success(__('The receipt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The receipt could not be saved. Please, try again.'));
        }
        $companies = $this->Receipts->Companies->find('list', ['limit' => 200]);
        $this->set(compact('receipt', 'companies'));
        $this->set('_serialize', ['receipt']);
    }
	public function ajaxReferenceDetails($itemValue=null)
    {
	echo $itemValue;
	exit;
	    $this->viewBuilder()->layout('');
		$company_id=$this->Auth->User('session_company_id');
		$stateDetails=$this->Auth->User('session_company');
		$location_id=$this->Auth->User('session_location_id');
		$state_id=$stateDetails->state_id;
		$items = $this->SalesInvoices->SalesInvoiceRows->Items->find()
					->where(['Items.company_id'=>$company_id, 'Items.id'=>$itemId])
					->contain(['Units'])->first();
					$itemUnit=$items->unit->name;
		
		$query = $this->SalesInvoices->SalesInvoiceRows->Items->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$company_id]);
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'In']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['status' => 'out']),
				$query->newExpr()->add(['quantity']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','item_id'
		])
		->where(['ItemLedgers.item_id' => $itemId, 'ItemLedgers.company_id' => $company_id, 'ItemLedgers.location_id' => $location_id])
		->group('item_id')
		->autoFields(true)
		->contain(['Items']);
        $itemLedgers = ($query);
		if($itemLedgers->toArray())
		{
			  foreach($itemLedgers as $itemLedger){
				   $available_stock=$itemLedger->total_in;
				   $stock_issue=$itemLedger->total_out;
				 @$remaining=number_format($available_stock-$stock_issue, 2);
				 $mainstock=str_replace(',','',$remaining);
				 $stock='current stock is '. $remaining. ' ' .$itemUnit;
				 if($remaining>0)
				 {
				 $stockType='false';
				 }
				 else{
				 $stockType='true';
				 }
				 $h=array('text'=>$stock, 'type'=>$stockType, 'mainStock'=>$mainstock);
				 echo  $f=json_encode($h);
			  }
		  }
		  else{
		 
				 @$remaining=0;
				 $stock='current stock is '. $remaining. ' ' .$itemUnit;
				 if($remaining>0)
				 {
				 $stockType='false';
				 }
				 else{
				 $stockType='true';
				 }
				 $h=array('text'=>$stock, 'type'=>$stockType);
				 echo  $f=json_encode($h);
		  }
		  exit;
}	

    /**
     * Delete method
     *
     * @param string|null $id Receipt id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $receipt = $this->Receipts->get($id);
        if ($this->Receipts->delete($receipt)) {
            $this->Flash->success(__('The receipt has been deleted.'));
        } else {
            $this->Flash->error(__('The receipt could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

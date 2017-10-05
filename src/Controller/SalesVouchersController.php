<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * SalesVouchers Controller
 *
 * @property \App\Model\Table\SalesVouchersTable $SalesVouchers
 *
 * @method \App\Model\Entity\SalesVoucher[] paginate($object = null, array $settings = [])
 */
class SalesVouchersController extends AppController
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
        $salesVouchers = $this->paginate($this->SalesVouchers);

        $this->set(compact('salesVouchers'));
        $this->set('_serialize', ['salesVouchers']);
    }

    /**
     * View method
     *
     * @param string|null $id Sales Voucher id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $salesVoucher = $this->SalesVouchers->get($id, [
            'contain' => ['Companies', 'SalesVoucherRows']
        ]);

        $this->set('salesVoucher', $salesVoucher);
        $this->set('_serialize', ['salesVoucher']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
        $this->request->data['company_id'] =$company_id;
        $salesVoucher = $this->SalesVouchers->newEntity();
		
        if ($this->request->is('post')) {
			//pr($this->request->getData()); exit;
			$this->request->data['transaction_date'] = date("Y-m-d",strtotime($this->request->getData()['transaction_date']));
            $salesVoucher = $this->SalesVouchers->patchEntity($salesVoucher, $this->request->getData());
			$Voucher = $this->SalesVouchers->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
			if($Voucher)
			{
				$salesVoucher->voucher_no = $Voucher->voucher_no+1;
			}
			else
			{
				$salesVoucher->voucher_no = 1;
			} 
			$salesVoucher = $this->SalesVouchers->patchEntity($salesVoucher, $this->request->getData(), [
							'associated' => ['SalesVoucherRows','SalesVoucherRows.ReferenceDetails']
						]);
			//pr($salesVoucher->sales_voucher_rows); exit;
            if ($this->SalesVouchers->save($salesVoucher)) {
                $this->Flash->success(__('The sales voucher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sales voucher could not be saved. Please, try again.'));
        }
		$Voucher = $this->SalesVouchers->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher)
		{
			$voucher_no=$Voucher->voucher_no+1;
		}
		else
		{ 
			$voucher_no=1;
		} 
		$bankParentGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.bank'=>'1']);
						
		$bankGroups=[];
		
		foreach($bankParentGroups as $bankParentGroup)
		{
			$accountingGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups
			->find('children', ['for' => $bankParentGroup->id])->toArray();
			$bankGroups[]=$bankParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$bankGroups[]=$accountingGroup->id;
			}
		}
		
		$ParentGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find()->where(['sales_voucher_party'=>1,'company_id'=>$company_id]); 
		$Groups=[];
		foreach($ParentGroups as $ParentGroup)
		{
			$ChildGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find('children', ['for' =>$ParentGroup->id])->toArray();
			$Groups[]=$ParentGroup->id;
			foreach($ChildGroups as $ChildGroup)
			{
				$Groups[]=$ChildGroup->id;
			}
		}
		$ParentLedgers = $this->SalesVouchers->SalesVoucherRows->Ledgers->find()->where(['Ledgers.accounting_group_id IN' =>$Groups]);
		
		$ledgerDroption =[];
		foreach($ParentLedgers as $ParentLedger){
		if(in_array($ParentLedger->accounting_group_id,$bankGroups)){
				$ledgerDroption[]=['text' =>$ParentLedger->name, 'value' => $ParentLedger->id ,'open_window' => 'bank'];
			}
			else if($ParentLedger->bill_to_bill_accounting == 'yes'){
				$ledgerDroption[]=['text' =>$ParentLedger->name, 'value' => $ParentLedger->id,'open_window' => 'party' ];
			}
			else{
				$ledgerDroption[]=['text' =>$ParentLedger->name, 'value' => $ParentLedger->id,'open_window' => 'no' ];
			}
		}
		
		$ParentSalesAccountGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find()->where(['sales_voucher_sales_account'=>1,'company_id'=>$company_id]); 
		
		$Groupcrs=[];
		
		foreach($ParentSalesAccountGroups as $ParentSalesAccountGroup)
		{
			$ChildSalesAccountGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find('children', ['for' =>$ParentSalesAccountGroup->id])->toArray();
			$Groupcrs[]=$ParentSalesAccountGroup->id;
			foreach($ChildSalesAccountGroups as $ChildSalesAccountGroup)
			{
				$Groupcrs[]=$ChildSalesAccountGroup->id;
			}
		}
		
		$ledgers = $this->SalesVouchers->SalesVoucherRows->Ledgers->find()->where(['Ledgers.accounting_group_id IN' =>$Groupcrs]);
		$ledgerOptions =[];
		foreach($ledgers as $ledger){
		if(in_array($ledger->accounting_group_id,$bankGroups)){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id ,'open_window' => 'bank'];
			}
			else if($ledger->bill_to_bill_accounting == 'yes'){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'party' ];
			}
			else{
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'no' ];
			}
		}
		$referenceDetails=$this->SalesVouchers->SalesVoucherRows->ReferenceDetails->find('list');
        $this->set(compact('salesVoucher','voucher_no','ledgerOptions','company_id','referenceDetails','ledgerDroption'));
        $this->set('_serialize', ['salesVoucher']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Sales Voucher id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
        $this->request->data['company_id'] =$company_id;
        $salesVoucher = $this->SalesVouchers->get($id, [
            'contain' => ['SalesVoucherRows'=>['ReferenceDetails']]
        ]);
		//pr($salesVoucher);exit;
        if ($this->request->is(['patch', 'post', 'put'])) {
			$this->request->data['transaction_date'] = date("Y-m-d",strtotime($this->request->getData()['transaction_date']));
            $salesVoucher = $this->SalesVouchers->patchEntity($salesVoucher, $this->request->getData());
	 
			$salesVoucher = $this->SalesVouchers->patchEntity($salesVoucher, $this->request->getData(), [
							'associated' => ['SalesVoucherRows','SalesVoucherRows.ReferenceDetails']
						]);
			//pr($salesVoucher);exit;
			if ($this->SalesVouchers->save($salesVoucher)) {
                $this->Flash->success(__('The sales voucher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sales voucher could not be saved. Please, try again.'));
        }
		
		$bankParentGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.bank'=>'1']);
						
		$bankGroups=[];
		
		foreach($bankParentGroups as $bankParentGroup)
		{
			$accountingGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups
			->find('children', ['for' => $bankParentGroup->id])->toArray();
			$bankGroups[]=$bankParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$bankGroups[]=$accountingGroup->id;
			}
		}
		$ParentGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find()->where(['sales_voucher_party'=>1,'company_id'=>$company_id]); 
		$Groups=[];
		foreach($ParentGroups as $ParentGroup)
		{
			$ChildGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find('children', ['for' =>$ParentGroup->id])->toArray();
			$Groups[]=$ParentGroup->id;
			foreach($ChildGroups as $ChildGroup)
			{
				$Groups[]=$ChildGroup->id;
			}
		}
		$ParentLedgers = $this->SalesVouchers->SalesVoucherRows->Ledgers->find()->where(['Ledgers.accounting_group_id IN' =>$Groups]);
		
		$ledgerDroption =[];
		foreach($ParentLedgers as $ParentLedger){
		if(in_array($ParentLedger->accounting_group_id,$bankGroups)){
				$ledgerDroption[]=['text' =>$ParentLedger->name, 'value' => $ParentLedger->id ,'open_window' => 'bank'];
			}
			else if($ParentLedger->bill_to_bill_accounting == 'yes'){
				$ledgerDroption[]=['text' =>$ParentLedger->name, 'value' => $ParentLedger->id,'open_window' => 'party' ];
			}
			else{
				$ledgerDroption[]=['text' =>$ParentLedger->name, 'value' => $ParentLedger->id,'open_window' => 'no' ];
			}
		}
		
		$ParentSalesAccountGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find()->where(['sales_voucher_sales_account'=>1,'company_id'=>$company_id]); 
		
		$Groupcrs=[];
		
		foreach($ParentSalesAccountGroups as $ParentSalesAccountGroup)
		{
			$ChildSalesAccountGroups = $this->SalesVouchers->SalesVoucherRows->Ledgers->AccountingGroups->find('children', ['for' =>$ParentSalesAccountGroup->id])->toArray();
			$Groupcrs[]=$ParentSalesAccountGroup->id;
			foreach($ChildSalesAccountGroups as $ChildSalesAccountGroup)
			{
				$Groupcrs[]=$ChildSalesAccountGroup->id;
			}
		}
		
		$ledgers = $this->SalesVouchers->SalesVoucherRows->Ledgers->find()->where(['Ledgers.accounting_group_id IN' =>$Groupcrs]);
		$ledgerOptions =[];
		foreach($ledgers as $ledger){
		if(in_array($ledger->accounting_group_id,$bankGroups)){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id ,'open_window' => 'bank'];
			}
			else if($ledger->bill_to_bill_accounting == 'yes'){
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'party' ];
			}
			else{
				$ledgerOptions[]=['text' =>$ledger->name, 'value' => $ledger->id,'open_window' => 'no' ];
			}
		}
		$referenceDetails=$this->SalesVouchers->SalesVoucherRows->ReferenceDetails->find('list');
		foreach($salesVoucher->sales_voucher_rows)
		{
			if(!empty($sales_voucher_rows->reference_details))
			{
				foreach($sales_voucher_rows->reference_details as $reference_detail)
				{
					$ledgerId = $reference_detail->ledger_id
				}
			}
		}
			
		$query = $this->SalesVouchers->SalesVoucherRows->ReferenceDetails->find();
		$query->select(['total_debit' => $query->func()->sum('ReferenceDetails.debit'),'total_credit' => $query->func()->sum('ReferenceDetails.credit')])
		->where(['ReferenceDetails.ledger_id'=>$ledgerId,'ReferenceDetails.type !='=>'On Account'])
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
				$option[]=['text' =>$referenceDetail->ref_name.' ['.$bal.']', 'value' => $referenceDetail->ref_name,];
			}
		}
		pr($option);exit;
        $this->set(compact('salesVoucher', 'company_id','ledgerDroption','ledgerOptions','referenceDetails'));
        $this->set('_serialize', ['salesVoucher']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Sales Voucher id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $salesVoucher = $this->SalesVouchers->get($id);
        if ($this->SalesVouchers->delete($salesVoucher)) {
            $this->Flash->success(__('The sales voucher has been deleted.'));
        } else {
            $this->Flash->error(__('The sales voucher could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

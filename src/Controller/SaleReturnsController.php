<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * SaleReturns Controller
 *
 * @property \App\Model\Table\SaleReturnsTable $SaleReturns
 *
 * @method \App\Model\Entity\SaleReturn[] paginate($object = null, array $settings = [])
 */
class SaleReturnsController extends AppController
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
       
        $saleReturns = $this->paginate($this->SaleReturns->find()->contain(['Companies',  'SalesLedgers', 'PartyLedgers', 'Locations', 'SalesInvoices'])->where(['SaleReturns.company_id'=>$company_id]));
		//pr($saleReturns); exit;
        $this->set(compact('saleReturns'));
        $this->set('_serialize', ['saleReturns']);
    }

    /**
     * View method
     *
     * @param string|null $id Sale Return id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $saleReturn = $this->SaleReturns->get($id, [
            'contain' => ['Companies', 'SalesLedgers', 'PartyLedgers', 'Locations', 'SalesInvoices', 'SaleReturnRows'=>['Items']]
        ]);

        $this->set('saleReturn', $saleReturn);
        $this->set('_serialize', ['saleReturn']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id=null)
    {
		//pr($id); exit;
		$this->viewBuilder()->layout('index_layout');
        $salesInvoice = $this->SaleReturns->SalesInvoices->get($id, [
            'contain' => (['PartyLedgers','SaleReturns'=>['SaleReturnRows' => function($q) {
				return $q->select(['sale_return_id','item_id','total' => $q->func()->sum('SaleReturnRows.return_quantity')])->group('SaleReturnRows.item_id');
			}],'SalesInvoiceRows'=>['Items', 'GstFigures']])
        ]);
		
		$sales_return_qty=[];
		foreach($salesInvoice->sale_returns as $sale_returns){
			foreach($sale_returns->sale_return_rows as $sale_return_row){
				$sales_return_qty[@$sale_return_row->item_id]=@$sales_return_qty[$sale_return_row->item_id]+$sale_return_row->total;
			}
		}
		$company_id=$this->Auth->User('session_company_id');
		$stateDetails=$this->Auth->User('session_company');
		$location_id=$this->Auth->User('session_location_id');
		$state_id=$stateDetails->state_id;
		$roundOffId = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->find()
		->where(['Ledgers.company_id'=>$company_id, 'Ledgers.round_off'=>1])->first();

        $saleReturn = $this->SaleReturns->newEntity();
        if ($this->request->is(['patch', 'post', 'put'])) {
			$transaction_date=date('Y-m-d', strtotime($this->request->data['transaction_date']));
			$saleReturn = $this->SaleReturns->patchEntity($saleReturn, $this->request->getData());
			$saleReturn->transaction_date=$transaction_date;
			$saleReturn->sales_invoice_id=$id;
			$Voucher_no = $this->SaleReturns->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
			
			if($Voucher_no){
				$voucher_no=$Voucher_no->voucher_no+1;
			}else{
				$voucher_no=1;
			} 
			$saleReturn->voucher_no=$voucher_no;
			$saleReturn->sales_ledger_id=$salesInvoice->sales_ledger_id;
			$saleReturn->party_ledger_id=$salesInvoice->party_ledger_id;
            if($this->SaleReturns->save($saleReturn)){
				$gstVal=0;
				$gVal=0;
				foreach($saleReturn->sale_return_rows as $sale_return_row){ 
					$exactRate=$sale_return_row['taxable_value']/$sale_return_row['return_quantity'];
					$stockData = $this->SaleReturns->SalesInvoices->ItemLedgers->query();
					$stockData->insert(['item_id', 'transaction_date','quantity', 'rate', 'amount', 'status', 'company_id', 'sale_return_id', 'sale_return_row_id', 'location_id'])
							->values([
							'item_id' => $sale_return_row['item_id'],
							'transaction_date' => $saleReturn->transaction_date,
							'quantity' => $sale_return_row['return_quantity'],
							'rate' => $exactRate,
							'amount' => $sale_return_row['taxable_value'],
							'status' => 'in',
							'company_id' => $saleReturn->company_id,
							'sale_return_id' => $saleReturn->id,
							'sale_return_row_id' => $sale_return_row->id,
							'location_id'=>$saleReturn->location_id
							])
					->execute();
				} 
					$partyData = $this->SaleReturns->SalesInvoices->AccountingEntries->query();
					$partyData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'sale_return_id'])
							->values([
							'ledger_id' => $saleReturn->party_ledger_id,
							'debit' => '',
							'credit' => $saleReturn->amount_after_tax,
							'transaction_date' => $saleReturn->transaction_date,
							'company_id' => $saleReturn->company_id,
							'sale_return_id' => $saleReturn->id
							])
					->execute();
					$accountData = $this->SaleReturns->SalesInvoices->AccountingEntries->query();
					$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'sale_return_id'])
							->values([
							'ledger_id' => $saleReturn->sales_ledger_id,
							'debit' => $saleReturn->amount_before_tax,
							'credit' =>  '',
							'transaction_date' => $saleReturn->transaction_date,
							'company_id' => $saleReturn->company_id,
							'sale_return_id' => $saleReturn->id
							])
					->execute();
					if(str_replace('-',' ',$saleReturn->round_off)>0){
						$roundData = $this->SaleReturns->SalesInvoices->AccountingEntries->query();
						if($saleReturn->isRoundofType=='0'){
							$debit=0;
							$credit=str_replace('-',' ',$saleReturn->round_off);
						}else if($saleReturn->isRoundofType=='1'){
							$credit=0;
							$debit=str_replace('-',' ',$saleReturn->round_off);
						}
						$roundData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'sale_return_id'])
								->values([
								'ledger_id' => $roundOffId->id,
								'debit' => $debit,
								'credit' => $credit,
								'transaction_date' => $saleReturn->transaction_date,
								'company_id' => $saleReturn->company_id,
								'sale_return_id' => $saleReturn->id
								])
						->execute();
					}
				if($saleReturn->is_interstate=='0')
				{
					for(@$i=0; $i<2; $i++)
					{
						foreach($saleReturn->sale_return_rows as $sale_return_row)
						{
							$gstVal=$sale_return_row['gst_value']/2;
							if($i==0){
								$gstLedgers = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->find()
								->where(['Ledgers.gst_figure_id' =>$sale_return_row['gst_figure_id'],'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'CGST'])->first();
								$ledgerId=$gstLedgers->id;
							}
							if($i==1){ 
								$gstLedgers = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->find()
								->where(['Ledgers.gst_figure_id' =>$sale_return_row['gst_figure_id'],'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'SGST'])->first();
								$ledgerId=$gstLedgers->id;
							}
							$accountData = $this->SaleReturns->SalesInvoices->AccountingEntries->query();
							$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'sale_return_id'])
							->values([
							'ledger_id' => $ledgerId,
							'debit' => $gstVal,
							'credit' => '',
							'transaction_date' => $saleReturn->transaction_date,
							'company_id' => $saleReturn->company_id,
							'sale_return_id' => $saleReturn->id
							])
							->execute();
						}
					}
				}
			   else if($saleReturn->is_interstate=='1')
			   {
					foreach($saleReturn->sale_return_rows as $sale_return_row)
					{
						@$gstVal=$sale_return_row['gst_value'];
						$gstLedgers = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->find()
						->where(['Ledgers.gst_figure_id' =>$sale_return_row['gst_figure_id'],'Ledgers.company_id'=>$company_id, 'Ledgers.input_output'=>'output', 'Ledgers.gst_type'=>'IGST'])->first();
						$ledgerId=$gstLedgers->id;
						$accountData = $this->SaleReturns->SalesInvoices->AccountingEntries->query();
						$accountData->insert(['ledger_id', 'debit','credit', 'transaction_date', 'company_id', 'sale_return_id'])
						->values([
						'ledger_id' => $ledgerId,
						'debit' => $gstVal,
						'credit' => '',
						'transaction_date' => $saleReturn->transaction_date,
						'company_id' => $saleReturn->company_id,
						'sale_return_id' => $saleReturn->id
						])
						->execute();
					}
			   }
				$this->Flash->success(__('The sale return has been saved.'));
				return $this->redirect(['action' => 'index']);
            } else{
				 $this->Flash->error(__('The sale return could not be saved. Please, try again.'));
			}
           
        }
       
		$customers = $this->SaleReturns->SalesInvoices->Customers->find()
					->where(['company_id'=>$company_id]);
						$customerOptions=[];
		foreach($customers as $customer){
			$customerOptions[]=['text' =>$customer->name, 'value' => $customer->id ,'customer_state_id'=>$customer->state_id];
		}
		
		$items = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Items->find()
					->where(['Items.company_id'=>$company_id])
					->contain(['FirstGstFigures', 'SecondGstFigures', 'Units']);
		$itemOptions=[];
		foreach($items as $item){
			$itemOptions[]=['text'=>$item->item_code.' '.$item->name, 'value'=>$item->id, 'first_gst_figure_id'=>$item->first_gst_figure_id, 'gst_amount'=>floatval($item->gst_amount), 'sales_rate'=>$item->sales_rate, 'second_gst_figure_id'=>$item->second_gst_figure_id, 'FirstGstFigure'=>$item->FirstGstFigures->tax_percentage, 'SecondGstFigure'=>$item->SecondGstFigures->tax_percentage];
		}
	
        $partyParentGroups = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->AccountingGroups->find()
						->where(['AccountingGroups.company_id'=>$company_id, 'AccountingGroups.sale_invoice_party'=>'1']);
		$partyGroups=[];
		foreach($partyParentGroups as $partyParentGroup)
		{
			$accountingGroups = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->AccountingGroups
			->find('children', ['for' => $partyParentGroup->id])->toArray();
			$partyGroups[]=$partyParentGroup->id;
			foreach($accountingGroups as $accountingGroup){
				$partyGroups[]=$accountingGroup->id;
			}
		}
		if($partyGroups)
		{  
			$Partyledgers = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->find()
							->where(['Ledgers.accounting_group_id IN' =>$partyGroups,'Ledgers.company_id'=>$company_id])
							->contain(['Customers']);
        }
		$partyOptions=[];
		foreach($Partyledgers as $Partyledger){
			$partyOptions[]=['text' =>$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id];
		}
		
		$accountLedgers = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->AccountingGroups->find()->where(['AccountingGroups.sale_invoice_sales_account'=>1,'AccountingGroups.company_id'=>$company_id])->first();

		$accountingGroups2 = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->AccountingGroups
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
			$Accountledgers = $this->SaleReturns->SalesInvoices->SalesInvoiceRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }

//pr($partyOptions); exit;
        $this->set(compact('saleReturn', 'companies', 'customers', 'salesLedgers', 'partyLedgers', 'locations', 'salesInvoices','sales_return_qty'));
		$this->set(compact('salesInvoice', 'companies', 'customerOptions', 'gstFigures', 'voucher_no','company_id','itemOptions','state_id', 'Accountledgers', 'partyOptions', 'location_id'));
        $this->set('_serialize', ['saleReturn']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Sale Return id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $saleReturn = $this->SaleReturns->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $saleReturn = $this->SaleReturns->patchEntity($saleReturn, $this->request->getData());
            if ($this->SaleReturns->save($saleReturn)) {
                $this->Flash->success(__('The sale return has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The sale return could not be saved. Please, try again.'));
        }
        $companies = $this->SaleReturns->Companies->find('list', ['limit' => 200]);
        $customers = $this->SaleReturns->Customers->find('list', ['limit' => 200]);
        $salesLedgers = $this->SaleReturns->SalesLedgers->find('list', ['limit' => 200]);
        $partyLedgers = $this->SaleReturns->PartyLedgers->find('list', ['limit' => 200]);
        $locations = $this->SaleReturns->Locations->find('list', ['limit' => 200]);
        $salesInvoices = $this->SaleReturns->SalesInvoices->find('list', ['limit' => 200]);
        $this->set(compact('saleReturn', 'companies', 'customers', 'salesLedgers', 'partyLedgers', 'locations', 'salesInvoices'));
        $this->set('_serialize', ['saleReturn']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Sale Return id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $saleReturn = $this->SaleReturns->get($id);
        if ($this->SaleReturns->delete($saleReturn)) {
            $this->Flash->success(__('The sale return has been deleted.'));
        } else {
            $this->Flash->error(__('The sale return could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

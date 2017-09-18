<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * IntraLocationStockTransferVouchers Controller
 *
 * @property \App\Model\Table\IntraLocationStockTransferVouchersTable $IntraLocationStockTransferVouchers
 *
 * @method \App\Model\Entity\IntraLocationStockTransferVoucher[] paginate($object = null, array $settings = [])
 */
class IntraLocationStockTransferVouchersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index($status = Null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$this->viewBuilder()->layout('index_layout');
		if(!empty($status))
		{
			$where = $status;
		}
		else
		{
			$where = 'pending';
		}
		
        $this->paginate = [
            'contain' => ['TransferFromLocations','TransferToLocations']
        ];
        $intraLocationStockTransferVouchers = $this->paginate($this->IntraLocationStockTransferVouchers->find()->where(['IntraLocationStockTransferVouchers.company_id'=>$company_id,'IntraLocationStockTransferVouchers.status'=>@$where]));
        $this->set(compact('intraLocationStockTransferVouchers','status'));
        $this->set('_serialize', ['intraLocationStockTransferVouchers']);
    }

    /**
     * View method
     *
     * @param string|null $id Intra Location Stock Transfer Voucher id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->get($id, [
            'contain' => ['TransferFromLocations','TransferToLocations', 'IntraLocationStockTransferVoucherRows'=>['Items']]
        ]);

        $this->set('intraLocationStockTransferVoucher', $intraLocationStockTransferVoucher);
        $this->set('_serialize', ['intraLocationStockTransferVoucher']);
    }

	public function viewApproved($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->get($id, [
            'contain' => ['TransferFromLocations','TransferToLocations', 'IntraLocationStockTransferVoucherRows'=>['Items']]
        ]);

        $this->set('intraLocationStockTransferVoucher', $intraLocationStockTransferVoucher);
        $this->set('_serialize', ['intraLocationStockTransferVoucher']);
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
		
		$user_id=$this->Auth->User('id');
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->newEntity();
        if ($this->request->is('post')) {
            $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->patchEntity($intraLocationStockTransferVoucher, $this->request->getData());
			$Voucher_no = $this->IntraLocationStockTransferVouchers->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
			if($Voucher_no)
			{
				$intraLocationStockTransferVoucher->voucher_no = $Voucher_no->voucher_no+1;
			}
			else
			{
				$intraLocationStockTransferVoucher->voucher_no = 1;
			} 
			$intraLocationStockTransferVoucher->transaction_date = date("Y-m-d",strtotime($this->request->data['transaction_date']));
			$intraLocationStockTransferVoucher->company_id = $company_id;
			$intraLocationStockTransferVoucher->location_id = $location_id;
			$intraLocationStockTransferVoucher->user_id = $user_id;
			$intraLocationStockTransferVoucher->transfer_by = $user_id;
            if ($this->IntraLocationStockTransferVouchers->save($intraLocationStockTransferVoucher)) 
			{
				foreach($intraLocationStockTransferVoucher->intra_location_stock_transfer_voucher_rows as $intra_location_stock_transfer_voucher_row)
				{
					$itemLedger = $this->IntraLocationStockTransferVouchers->ItemLedgers->newEntity();
					$itemLedger->item_id            = $intra_location_stock_transfer_voucher_row->item_id;
					$itemLedger->transaction_date   = date("Y-m-d",strtotime($intraLocationStockTransferVoucher->transaction_date));
					$itemLedger->quantity           = $intra_location_stock_transfer_voucher_row->quantity;
					$itemLedger->status             = 'out';
					$itemLedger->location_id   		= $intraLocationStockTransferVoucher->transfer_from_location_id;
					$itemLedger->intra_location_stock_transfer_voucher_id          = $intraLocationStockTransferVoucher->id;
					$itemLedger->intra_location_stock_transfer_voucher_row_id		= $intra_location_stock_transfer_voucher_row->id;
					$itemLedger->intra_location_transfer		= 'Yes';
					$itemLedger->company_id          = $company_id;
					$this->IntraLocationStockTransferVouchers->ItemLedgers->save($itemLedger);
				}
					$this->Flash->success(__('The intra location stock transfer voucher has been saved.'));

					return $this->redirect(['action' => 'add']);
					
				
            }
            $this->Flash->error(__('The intra location stock transfer voucher could not be saved. Please, try again.'));
        }
		$Voucher_no = $this->IntraLocationStockTransferVouchers->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher_no){
			$voucher_no=$Voucher_no->voucher_no+1;
		}else{
			$voucher_no=1;
		} 
        $companies = $this->IntraLocationStockTransferVouchers->Companies->find('list');
        $TransferFromLocations = $this->IntraLocationStockTransferVouchers->TransferFromLocations->find('list')->where(['company_id'=>$company_id]);
		$TransferToLocations = $this->IntraLocationStockTransferVouchers->TransferToLocations->find('list')->where(['company_id'=>$company_id]);
		
		$items = $this->IntraLocationStockTransferVouchers->IntraLocationStockTransferVoucherRows->Items->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('intraLocationStockTransferVoucher', 'companies', 'TransferFromLocations','TransferToLocations','items','voucher_no'));
        $this->set('_serialize', ['intraLocationStockTransferVoucher']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Intra Location Stock Transfer Voucher id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$user_id=$this->Auth->User('id');
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->get($id, [
            'contain' => ['IntraLocationStockTransferVoucherRows'=>['Items']]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->patchEntity($intraLocationStockTransferVoucher, $this->request->getData());
			$intraLocationStockTransferVoucher->transaction_date = date("Y-m-d",strtotime($this->request->data['transaction_date']));
			$intraLocationStockTransferVoucher->company_id = $company_id;
			$intraLocationStockTransferVoucher->location_id = $location_id;
			$intraLocationStockTransferVoucher->user_id = $user_id;
            if ($this->IntraLocationStockTransferVouchers->save($intraLocationStockTransferVoucher)) {
				$query_delete = $this->IntraLocationStockTransferVouchers->ItemLedgers->query();
				$query_delete->delete()
				->where(['intra_location_stock_transfer_voucher_id' => $intraLocationStockTransferVoucher->id,'company_id'=>$company_id])
				->execute();
				
				foreach($intraLocationStockTransferVoucher->intra_location_stock_transfer_voucher_rows as $intra_location_stock_transfer_voucher_row)
				{
					$itemLedger = $this->IntraLocationStockTransferVouchers->ItemLedgers->newEntity();
					$itemLedger->item_id            = $intra_location_stock_transfer_voucher_row->item_id;
					$itemLedger->transaction_date   = date("Y-m-d",strtotime($intraLocationStockTransferVoucher->transaction_date));
					$itemLedger->quantity           = $intra_location_stock_transfer_voucher_row->quantity;
					$itemLedger->status             = 'out';
					$itemLedger->location_id   		= $intraLocationStockTransferVoucher->transfer_from_location_id;
					$itemLedger->intra_location_stock_transfer_voucher_id          = $intraLocationStockTransferVoucher->id;
					$itemLedger->intra_location_stock_transfer_voucher_row_id		= $intra_location_stock_transfer_voucher_row->id;
					$itemLedger->intra_location_transfer		= 'Yes';
					$itemLedger->company_id          = $company_id;
					$this->IntraLocationStockTransferVouchers->ItemLedgers->save($itemLedger);
				}
                $this->Flash->success(__('The intra location stock transfer voucher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The intra location stock transfer voucher could not be saved. Please, try again.'));
        }
        $companies = $this->IntraLocationStockTransferVouchers->Companies->find('list');
        $TransferFromLocations = $this->IntraLocationStockTransferVouchers->TransferFromLocations->find('list')->where(['company_id'=>$company_id]);
		$TransferToLocations = $this->IntraLocationStockTransferVouchers->TransferToLocations->find('list')->where(['company_id'=>$company_id]);
		
		$items = $this->IntraLocationStockTransferVouchers->IntraLocationStockTransferVoucherRows->Items->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('intraLocationStockTransferVoucher', 'companies', 'TransferFromLocations','TransferToLocations','items','voucher_no'));
        $this->set('_serialize', ['intraLocationStockTransferVoucher']);
    }

	 public function approved($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$user_id=$this->Auth->User('id');
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->get($id, [
            'contain' => ['IntraLocationStockTransferVoucherRows'=>['Items']]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->patchEntity($intraLocationStockTransferVoucher, $this->request->getData());  
			$intraLocationStockTransferVoucher->transaction_date = date("Y-m-d",strtotime($this->request->data['transaction_date']));
			$intraLocationStockTransferVoucher->company_id = $company_id;
			$intraLocationStockTransferVoucher->location_id = $location_id;
			$intraLocationStockTransferVoucher->user_id = $user_id;
			$intraLocationStockTransferVoucher->transfer_to = $user_id;
			$intraLocationStockTransferVoucher->status  = 'Approved';
			if ($this->IntraLocationStockTransferVouchers->save($intraLocationStockTransferVoucher)) {
				
				foreach($intraLocationStockTransferVoucher->intra_location_stock_transfer_voucher_rows as $intra_location_stock_transfer_voucher_row)
				{
						
					$itemLedger = $this->IntraLocationStockTransferVouchers->ItemLedgers->newEntity();
					$itemLedger->item_id            = $intra_location_stock_transfer_voucher_row->item_id;
					$itemLedger->transaction_date   = date("Y-m-d",strtotime($intraLocationStockTransferVoucher->transaction_date));
					$itemLedger->quantity           = $intra_location_stock_transfer_voucher_row->receive_quantity;
					$itemLedger->status             = 'in';
					$itemLedger->location_id   		= $intraLocationStockTransferVoucher->transfer_to_location_id;
					$itemLedger->intra_location_stock_transfer_voucher_id          = $intraLocationStockTransferVoucher->id;
					$itemLedger->intra_location_stock_transfer_voucher_row_id = $intra_location_stock_transfer_voucher_row->id;
					$itemLedger->intra_location_transfer		= 'Yes';
					$itemLedger->company_id          = $company_id;
					$this->IntraLocationStockTransferVouchers->ItemLedgers->save($itemLedger);
				}
                $this->Flash->success(__('The intra location stock transfer voucher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The intra location stock transfer voucher could not be saved. Please, try again.'));
        }
        $companies = $this->IntraLocationStockTransferVouchers->Companies->find('list');
        $TransferFromLocations = $this->IntraLocationStockTransferVouchers->TransferFromLocations->find('list')->where(['company_id'=>$company_id]);
		$TransferToLocations = $this->IntraLocationStockTransferVouchers->TransferToLocations->find('list')->where(['company_id'=>$company_id]);
		
		$items = $this->IntraLocationStockTransferVouchers->IntraLocationStockTransferVoucherRows->Items->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('intraLocationStockTransferVoucher', 'companies', 'TransferFromLocations','TransferToLocations','items','voucher_no'));
        $this->set('_serialize', ['intraLocationStockTransferVoucher']);
    }
	
	public function editApproved($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$user_id=$this->Auth->User('id');
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->get($id, [
            'contain' => ['IntraLocationStockTransferVoucherRows'=>['Items']]
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->patchEntity($intraLocationStockTransferVoucher, $this->request->getData());
			$intraLocationStockTransferVoucher->transaction_date = date("Y-m-d",strtotime($this->request->data['transaction_date']));
			$intraLocationStockTransferVoucher->company_id = $company_id;
			$intraLocationStockTransferVoucher->location_id = $location_id;
			$intraLocationStockTransferVoucher->user_id = $user_id;
			$intraLocationStockTransferVoucher->transfer_to = $user_id;
            if ($this->IntraLocationStockTransferVouchers->save($intraLocationStockTransferVoucher)) {
				$query_delete = $this->IntraLocationStockTransferVouchers->ItemLedgers->query();
				$query_delete->delete()
				->where(['intra_location_stock_transfer_voucher_id' => $intraLocationStockTransferVoucher->id,'company_id'=>$company_id])
				->execute();
				
				foreach($intraLocationStockTransferVoucher->intra_location_stock_transfer_voucher_rows as $intra_location_stock_transfer_voucher_row)
				{
					$itemLedger = $this->IntraLocationStockTransferVouchers->ItemLedgers->newEntity();
					$itemLedger->item_id            = $intra_location_stock_transfer_voucher_row->item_id;
					$itemLedger->transaction_date   = date("Y-m-d",strtotime($intraLocationStockTransferVoucher->transaction_date));
					$itemLedger->quantity           = $intra_location_stock_transfer_voucher_row->receive_quantity;
					$itemLedger->status             = 'in';
					$itemLedger->location_id   		= $intraLocationStockTransferVoucher->transfer_to_location_id;
					$itemLedger->intra_location_stock_transfer_voucher_id          = $intraLocationStockTransferVoucher->id;
					$itemLedger->intra_location_stock_transfer_voucher_row_id		= $intra_location_stock_transfer_voucher_row->id;
					$itemLedger->intra_location_transfer		= 'Yes';
					$itemLedger->company_id          = $company_id;
					$this->IntraLocationStockTransferVouchers->ItemLedgers->save($itemLedger);
				}
                $this->Flash->success(__('The intra location stock transfer voucher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The intra location stock transfer voucher could not be saved. Please, try again.'));
        }
        $companies = $this->IntraLocationStockTransferVouchers->Companies->find('list');
        $TransferFromLocations = $this->IntraLocationStockTransferVouchers->TransferFromLocations->find('list')->where(['company_id'=>$company_id]);
		$TransferToLocations = $this->IntraLocationStockTransferVouchers->TransferToLocations->find('list')->where(['company_id'=>$company_id]);
		
		$items = $this->IntraLocationStockTransferVouchers->IntraLocationStockTransferVoucherRows->Items->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('intraLocationStockTransferVoucher', 'companies', 'TransferFromLocations','TransferToLocations','items','voucher_no'));
        $this->set('_serialize', ['intraLocationStockTransferVoucher']);
    }
    /**
     * Delete method
     *
     * @param string|null $id Intra Location Stock Transfer Voucher id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $intraLocationStockTransferVoucher = $this->IntraLocationStockTransferVouchers->get($id);
        if ($this->IntraLocationStockTransferVouchers->delete($intraLocationStockTransferVoucher)) {
            $this->Flash->success(__('The intra location stock transfer voucher has been deleted.'));
        } else {
            $this->Flash->error(__('The intra location stock transfer voucher could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

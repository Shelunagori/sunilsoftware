<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ItemLedgers Controller
 *
 * @property \App\Model\Table\ItemLedgersTable $ItemLedgers
 *
 * @method \App\Model\Entity\ItemLedger[] paginate($object = null, array $settings = [])
 */
class ItemLedgersController extends AppController
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
            'contain' => ['Items']
        ];
        $itemLedgers = $this->paginate($this->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$company_id]));

        $this->set(compact('itemLedgers'));
        $this->set('_serialize', ['itemLedgers']);
    }

    /**
     * View method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $itemLedger = $this->ItemLedgers->get($id, [
            'contain' => ['Items']
        ]);

        $this->set('itemLedger', $itemLedger);
        $this->set('_serialize', ['itemLedger']);
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
		$itemLedger = $this->ItemLedgers->newEntity();
        if ($this->request->is('post')) {
            $itemLedger = $this->ItemLedgers->patchEntity($itemLedger, $this->request->getData());
            if ($this->ItemLedgers->save($itemLedger)) {
                $this->Flash->success(__('The item ledger has been saved.'));

                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
        }
        $items = $this->ItemLedgers->Items->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('itemLedger', 'items'));
        $this->set('_serialize', ['itemLedger']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$itemLedger = $this->ItemLedgers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $itemLedger = $this->ItemLedgers->patchEntity($itemLedger, $this->request->getData());
            if ($this->ItemLedgers->save($itemLedger)) {
                $this->Flash->success(__('The item ledger has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The item ledger could not be saved. Please, try again.'));
        }
        $items = $this->ItemLedgers->Items->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('itemLedger', 'items'));
        $this->set('_serialize', ['itemLedger']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Item Ledger id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $itemLedger = $this->ItemLedgers->get($id);
        if ($this->ItemLedgers->delete($itemLedger)) {
            $this->Flash->success(__('The item ledger has been deleted.'));
        } else {
            $this->Flash->error(__('The item ledger could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function salesReturnReport()
    {
        $this->viewBuilder()->layout('index_layout');
		$status=$this->request->query('status'); 
		if(!empty($status)){ 
			$this->viewBuilder()->layout('excel_layout');	
		}else{ 
			$this->viewBuilder()->layout('index_layout');
		}
		
			$company_id=$this->Auth->User('session_company_id');
			$url=$this->request->here();
			$url=parse_url($url,PHP_URL_QUERY);
        $itemLedgers =$this->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$company_id,'ItemLedgers.sale_return_id >' =>0])
		->contain(['Items','SaleReturns'=>['PartyLedgers']]);
		//pr($itemLedgers->toArray());
		//exit;
        $this->set(compact('itemLedgers','status','url'));
        $this->set('_serialize', ['itemLedgers']);
    }
	
	public function stockReport()
    {
        $this->viewBuilder()->layout('index_layout');
		$status=$this->request->query('status'); 
		if(!empty($status)){ 
			$this->viewBuilder()->layout('excel_layout');	
		}else{ 
			$this->viewBuilder()->layout('index_layout');
		}
		$company_id=$this->Auth->User('session_company_id');
		
		$to_date=date("Y-m-d");
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		$stockGroups = $this->ItemLedgers->Items->StockGroups->find()
		->where(['StockGroups.company_id'=>$company_id,'StockGroups.parent_id IS NULL']);
		$stock_report=[];
		foreach($stockGroups as $stockGroup)
		{
			$childGroups=[];$total_rate_stock=0;$total_qty=0;
			$childStockGroups = $this->ItemLedgers->Items->StockGroups
			->find('children', ['for' => $stockGroup->id])->toArray();
			$childGroups[]=$stockGroup->id;
			foreach($childStockGroups as $childStockGroup){
				$childGroups[]=$childStockGroup->id;
			}
			$items=$this->ItemLedgers->Items->find()->where(['Items.company_id'=>$company_id,'Items.stock_group_id IN'=>$childGroups]);
			$stock_item=[];
			foreach($items as $item){
				$q=0;
				$total_rate=0;
				//$item_id[]=$item->id;
				$result_stock=$this->stockReportQtyRate($to_date,$item->id);
				if(sizeof($result_stock[$item->id]>0))
				{ 
					foreach($result_stock[$item->id] as $stock_rate){
					
					@$total_rate+=$stock_rate;
					 @$q++;
					}
					@$avg_rate=$total_rate/$q;
					@$avg_qty=$q;
					
				} 	
				@$total_rate_stock+=round($avg_rate,2);
				@$total_qty+=$avg_qty;
				$stock_total_rate[$stockGroup->id]=$total_rate_stock;
				$stock_total_qty[$stockGroup->id]=$total_qty;
			}
			
		}
		
		$items_main=$this->ItemLedgers->Items->find()->where(['Items.company_id'=>$company_id,'Items.stock_group_id'=>0]);
		//pr($items_main->toArray());
		//exit;
		
        $this->set(compact('itemLedgers','status','url','stockGroups','stock_total_rate','stock_total_qty'));
        $this->set('_serialize', ['itemLedgers']);
    }
	
	public function stockReportQtyRate($to_date = null, $item_id = null){
		$company_id=$this->Auth->User('session_company_id');
		$locations=$this->ItemLedgers->Locations->find()->where(['Locations.company_id'=>$company_id]);
		$location_stock=[];
		foreach($locations as $location){
		$item_ledgers=$this->ItemLedgers->find()->where(['ItemLedgers.item_id'=>$item_id,'ItemLedgers.company_id'=>$company_id,'ItemLedgers.intra_location_stock_transfer_voucher_id IS NULL','ItemLedgers.transaction_date <=' => $to_date])->order(['ItemLedgers.transaction_date' => 'ASC']);
		$stock_item=[];
			foreach($item_ledgers as $item_ledger){
				if($item_ledger->status=='in'){
					for($inc=0;$inc < $item_ledger->quantity;$inc++){
					@$stock_item[$item_ledger->item_id][]=$item_ledger->rate;
				}
			}
			foreach($item_ledgers as $item_ledger){
				if($item_ledger->status=='out'){
					@$stock_item[$item_ledger->item_id]=array_slice($stock_item[$item_ledger->item_id],$item_ledger->quantity);
				}
			}
		
		}
		
		return $stock_item;
		}	
		
	}
}

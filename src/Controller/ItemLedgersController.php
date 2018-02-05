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
		
		$locations=$this->ItemLedgers->Locations->find()->where(['Locations.company_id'=>$company_id]);
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
			if($childGroups){
			$location_stocks=$this->stockReportGroupQty($to_date,$childGroups);
			@$stock_quantity[$stockGroup->id]=$location_stocks;
			}
			
			
			
			/* $items=$this->ItemLedgers->Items->find()->where(['Items.company_id'=>$company_id,'Items.stock_group_id IN'=>$childGroups]);
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
			} */
		
		}
		
		$items_main=$this->ItemLedgers->Items->find()->where(['Items.company_id'=>$company_id,'Items.stock_group_id'=>0]);
		foreach($items_main as $data){
		$stock_quantity[]=$this->stockReportItemQty($to_date = null, $data->id);
		pr($stock_quantity);
		}
		 exit;
		//pr($items_main->toArray());
		//exit;
		
        $this->set(compact('itemLedgers','status','url','stockGroups','stock_total_rate','stock_quantity','locations','result_stocks','items_main'));
        $this->set('_serialize', ['itemLedgers']);
    }
	
	public function stockReportGroupQty($to_date = null, $childGroups = null){
		$company_id=$this->Auth->User('session_company_id');
		$locations=$this->ItemLedgers->Locations->find()->where(['Locations.company_id'=>$company_id]);
		foreach($childGroups as $childGroup){
		$items=$this->ItemLedgers->Items->find()->where(['Items.company_id'=>$company_id,'Items.stock_group_id'=>$childGroup->id]);	
		}
		foreach($items as $item)
		@$item_stocks[]=$this->stockReportItemQty($to_date,$item->id`);
		
	}
	
	public function stockReportItemQty($to_date = null, $item_id = null){
		$company_id=$this->Auth->User('session_company_id');
		$locations=$this->ItemLedgers->Locations->find()->where(['Locations.company_id'=>$company_id]);
		@$total_stock=[];
		foreach($locations as $location){
			$query = $this->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$company_id]);
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
			->where(['ItemLedgers.item_id' => $item_id, 'ItemLedgers.company_id' => $company_id, 'ItemLedgers.location_id' => $location->id,'ItemLedgers.transaction_date <=' => $to_date])
			->group('item_id')
			->autoFields(true)
			->contain(['Items']);
			$itemLedgers = ($query);
				if($itemLedgers->toArray())
				{
					$remaining=[];
					foreach($itemLedgers as $itemLedger){
					   $available_stock=$itemLedger->total_in;
					   $stock_issue=$itemLedger->total_out;
					   @$remaining[$location->id]=number_format($available_stock-$stock_issue, 2);
					   @$total_stock[$location->id]+=$remaining[$location->id];
					   }
					}
				}
				return $total_stock;
			}	
		

}

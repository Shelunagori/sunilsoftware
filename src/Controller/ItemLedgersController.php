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
		$stock_group_id = $this->request->query('stock_group_id');
		$stock_sub_group_id = $this->request->query('stock_subgroup_id');
		$where=[];
		if(!empty($stock_group_id)){
			$Groups[]=$stock_group_id;
		$stockGroups = $this->ItemLedgers->Items->StockGroups->find('children', ['for' => $stock_group_id]);
		foreach($stockGroups as $stockGroup){
			$Groups[]=$stockGroup->id;
		}
		//pr($stockGroup->toArray()); exit;
		$where['Items.stock_group_id In']=$Groups;
		}
		if(!empty($stock_sub_group_id)){
		$where['Items.stock_group_id']=$stock_sub_group_id;
		}
		$to_date   = $this->request->query('to_date');
		if(!empty($to_date)){
			$to_date   = date("Y-m-d",strtotime($to_date));
		}
		else{
			$to_date   = date("Y-m-d");
		}
		$url=$this->request->here();
		$url=parse_url($url,PHP_URL_QUERY);
		
		$Items=$this->ItemLedgers->Items->find()->contain(['Shades','Sizes','StockGroups'])->where($where)->where(['Items.company_id'=>$company_id,'Items.created_on <='=>$to_date]); //pr($Items->toArray()); exit;
	 	//$Items=$this->ItemLedgers->Items->find()->where(['Items.company_id'=>$company_id]); 
		//pr($Items->toArray()); exit;
		
		$remaining=[];$unit_rate=[];$stock=[];
		foreach($Items as $Item){
			@$total_stock=[];
			$dataexist=$this->ItemLedgers->exists(['ItemLedgers.item_id'=>$Item->id]);
			if($dataexist==1){ 
				/* $ItemRate=$this->ItemLedgers->find()->select(['rate'])->where(['ItemLedgers.company_id'=>$company_id,'ItemLedgers.item_id'=>$Item->id,'ItemLedgers.grn_id >'=> 0])->first(); 
				$unit_rate[$Item->id]=$ItemRate->rate; 
				$query = $this->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$company_id,'ItemLedgers.item_id'=>$Item->id]);
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
				->where(['ItemLedgers.item_id' => $Item->id, 'ItemLedgers.company_id' => $company_id])
				->group('ItemLedgers.item_id')
				->autoFields(true);
				$itemLedgers1 = ($query);
				$remaining[$Item->id]=$itemLedgers1->first()->total_in-$itemLedgers1->first()->total_out;
				/* pr($itemLedgers1->first()->total_in);  exit;
				foreach($itemLedgers1 as $itemLedger1){
					  // $available_stock=$itemLedger1->total_in;
					  // $stock_issue=$itemLedger1->total_out;
					   $remaining[$Item->id]=$itemLedger1->total_in-$itemLedger1->total_out;
					  } */
				  
				$ItemLedgers=$this->ItemLedgers->find()->where(['ItemLedgers.company_id'=>$company_id,'ItemLedgers.transaction_date <='=>$to_date,'ItemLedgers.intra_location_stock_transfer_voucher_id IS NULL','ItemLedgers.item_id'=>$Item->id])->order(['transaction_date'=>'ASC']);
		
				foreach($ItemLedgers as $ItemLedger){
					
					if($ItemLedger->status=="in"){
						for($inc=0;$inc<$ItemLedger->quantity;$inc++){
						$stock[$Item->id][]=$ItemLedger->rate;
						}
					}
				}
		//pr($stock); exit;
				foreach($ItemLedgers as $ItemLedger){
					if($ItemLedger->status=='out'){
						if(sizeof(@$stock[$Item->id])>0){
							$stock[$Item->id] = array_slice($stock[$Item->id], $ItemLedger->quantity); 
						}
					}
				}
				
				$closingValue=0;
				$rate=0;
				foreach($stock[$Item->id]  as $stockRow){
				$remaining[$Item->id]=count($stock[$Item->id]);
				$rate+=$stockRow;
				$unit_rate[$Item->id]=	$rate;
				}
			}	
		}
		$companies=$this->ItemLedgers->Companies->find()->contain(['States'])->where(['Companies.id'=>$company_id])->first();
	
		$stockGroups = $this->ItemLedgers->Items->StockGroups->find('list')->where(['StockGroups.company_id'=>$company_id,'StockGroups.parent_id IS NULL']);
		$stockSubgroups=$this->ItemLedgers->Items->StockGroups->find('list')->where(['StockGroups.company_id'=>$company_id]);
        $this->set(compact('companies','status','url','stockGroups','unit_rate','remaining','Items','stockGroups','to_date','stockSubgroups','stock_sub_group_id','stock_group_id'));
        $this->set('_serialize', ['itemLedgers']);
    }
	
		

}

<?php
namespace App\Controller;
use App\Controller\AppController;
use Cake\View\Helper\BarcodeHelper;
/**
 * Items Controller
 *
 * @property \App\Model\Table\ItemsTable $Items
 *
 * @method \App\Model\Entity\Item[] paginate($object = null, array $settings = [])
 */
class ItemsController extends AppController
{

	function arrayToCsvDownload($array, $filename = "export.csv", $delimiter=";") 
	{
		// open raw memory as file so no temp files needed, you might run out of memory though
		$f = fopen('php://memory', 'w'); 
		// loop over the input array
		foreach ($array as $line) { 
			// generate csv lines from the inner arrays
			fputcsv($f, $line, $delimiter); 
		}
		// reset the file pointer to the start of the file
		fseek($f, 0);
		// tell the browser it's going to be a csv file
		header('Content-Type: application/csv');
		// tell the browser we want to save it instead of displaying it
		header('Content-Disposition: attachment; filename="'.$filename.'";');
		// make php send the generated csv lines to the browser
		fpassthru($f);
	}
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
            'contain' => ['Units', 'StockGroups']
        ];
        $items = $this->paginate($this->Items->find()->where(['Items.company_id'=>$company_id]));

        $this->set(compact('items'));
        $this->set('_serialize', ['items']);
    }

    /**
     * View method
     *
     * @param string|null $id Item id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $item = $this->Items->get($id, [
            'contain' => ['Units', 'StockGroups']
        ]);

        $this->set('item', $item);
        $this->set('_serialize', ['item']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
        $item = $this->Items->newEntity();
		$company_id  = $this->Auth->User('session_company_id');
		$location_id = $this->Auth->User('session_location_id');
		$this->request->data['company_id'] =$company_id;
		if ($this->request->is('post')) {
			$item = $this->Items->patchEntity($item, $this->request->getData());
			$quantity = $this->request->data['quantity'];

			$gst_type = $item->kind_of_gst;
			if($gst_type=='fix')
			{
				$first_gst_figure_id        = $item->first_gst_figure_id;
				$item->second_gst_figure_id = $first_gst_figure_id;
				$item->gst_amount           = 0;
			}
			if($item->barcode_decision==1){
				$item->item_code=strtoupper(uniqid());
				$data_to_encode = $item->item_code;
			}else{
				$item->item_code=strtoupper($item->provided_item_code);
				$data_to_encode = strtoupper($item->provided_item_code);
			}
			$item->sales_rate_update_on = $this->Auth->User('session_company')->books_beginning_from;
            if ($this->Items->save($item))
			{
				$barcode = new BarcodeHelper(new \Cake\View\View());
				
					
				// Generate Barcode data
				$barcode->barcode();
				$barcode->setType('C128');
				$barcode->setCode($data_to_encode);
				$barcode->setSize(40,100);
					
				// Generate filename     
				$file = 'img/barcode/'.$item->id.'.png';
					
				// Generates image file on server    
				$barcode->writeBarcodeFile($file);
			
			
				$transaction_date=$this->Auth->User('session_company')->books_beginning_from;
				if($quantity>0)
				{
					$itemLedger = $this->Items->ItemLedgers->newEntity();
					$itemLedger->item_id            = $item->id;
					$itemLedger->transaction_date   = date("Y-m-d",strtotime($transaction_date));
					$itemLedger->quantity           = $this->request->data['quantity'];
					$itemLedger->rate               = $this->request->data['rate'];
					$itemLedger->amount             = $this->request->data['amount'];
					$itemLedger->status             = 'in';
					$itemLedger->is_opening_balance = 'yes';
					$itemLedger->company_id         = $company_id;
					$itemLedger->location_id        = $location_id;
					$this->Items->ItemLedgers->save($itemLedger);
				}
				
                $this->Flash->success(__('The item has been saved.'));

                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('The item could not be saved. Please, try again.'));
        }
        $units = $this->Items->Units->find('list')->where(['company_id'=>$company_id]);
        $stockGroups = $this->Items->StockGroups->find('list')->where(['company_id'=>$company_id]);
        $shades = $this->Items->Shades->find('list')->where(['company_id'=>$company_id]);
        $sizes = $this->Items->Sizes->find('list')->where(['company_id'=>$company_id]);
        $gstFigures = $this->Items->GstFigures->find('list')->where(['GstFigures.company_id'=>$company_id]);
        $this->set(compact('item', 'units', 'stockGroups','sizes','shades','gstFigures'));
        $this->set('_serialize', ['item']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Item id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $item = $this->Items->get($id, [
            'contain' => ['ItemLedgers' => function($q) {
				return $q->where(['ItemLedgers.is_opening_balance'=>'yes']);
			}]
        ]);
		$company_id=$this->Auth->User('session_company_id');
		$location_id = $this->Auth->User('session_location_id');
		
        if ($this->request->is(['patch', 'post', 'put'])) {
            $item = $this->Items->patchEntity($item, $this->request->getData());
			
			$gst_type = $item->kind_of_gst;
			if($gst_type=='fix')
			{
				$first_gst_figure_id        = $item->first_gst_figure_id;
				$item->second_gst_figure_id = $first_gst_figure_id;
				$item->gst_amount           = 0;
			}
			$item->sales_rate_update_on = $this->Auth->User('session_company')->books_beginning_from;
			if ($this->Items->save($item)) {
				if($item->quantity>0)
				{
					$transaction_date=$this->Auth->User('session_company')->books_beginning_from;
					$query_delete = $this->Items->ItemLedgers->query();
						$query_delete->delete()
						->where(['item_id' => $id,'is_opening_balance'=>'yes','company_id'=>$company_id])
						->execute();
						
					$itemLedger = $this->Items->ItemLedgers->newEntity();
					$itemLedger->item_id            = $item->id;
					$itemLedger->transaction_date   = date("Y-m-d",strtotime($transaction_date));
					$itemLedger->quantity           = $this->request->data['quantity'];
					$itemLedger->rate               = $this->request->data['rate'];
					$itemLedger->amount             = $this->request->data['amount'];
					$itemLedger->status             = 'in';
					$itemLedger->is_opening_balance = 'yes';
					$itemLedger->company_id         = $company_id;
					$itemLedger->location_id        = $location_id;
					$this->Items->ItemLedgers->save($itemLedger);
				}
				$this->Flash->success(__('The item has been saved.'));
				

                return $this->redirect(['action' => 'index']);
            }
			else
			{ 
				$this->Flash->error(__('The item could not be saved. Please, try again.'));
			}
        }
        $units = $this->Items->Units->find('list')->where(['company_id'=>$company_id]);
        $stockGroups = $this->Items->StockGroups->find('list')->where(['company_id'=>$company_id]);
		$shades = $this->Items->Shades->find('list')->where(['company_id'=>$company_id]);
        $sizes = $this->Items->Sizes->find('list')->where(['company_id'=>$company_id]);
		$gstFigures = $this->Items->GstFigures->find('list')->where(['GstFigures.company_id'=>$company_id]);
        $this->set(compact('item', 'units', 'stockGroups','sizes','shades','gstFigures'));
        $this->set('_serialize', ['item']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Item id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
		
        $this->request->allowMethod(['post', 'delete']);
        $item = $this->Items->get($id);
        if ($this->Items->delete($item)) {
            $this->Flash->success(__('The item has been deleted.'));
        } else {
            $this->Flash->error(__('The item could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
	
	public function uplodeCsv()
    {
		$this->viewBuilder()->layout('index_layout');
        $uplode_csv = $this->Items->newEntity();
		
		if ($this->request->is('post')) 
		{
			
			$csv = $this->request->data['csv'];
			if(!empty($csv['tmp_name']))
			{
				
				$ext = substr(strtolower(strrchr($csv['name'], '.')), 1); //get the extension 
				
				$arr_ext = array('csv'); 									   
				if (in_array($ext, $arr_ext)) 
				{
								
					$f = fopen($csv['tmp_name'], 'r') or die("ERROR OPENING DATA");
					$batchcount=0;
					$records=0;
					while (($line = fgetcsv($f, 4096, ';')) !== false) 
					{
						$numcols = count($line);
						$test[]=$line;
						++$records;
					}
					foreach($test as $test1)
					{ 
					
						 $data = explode(",",$test1[0]);
						 $item = $this->Items->newEntity();
						 $item->name           = $data[0];
						 $item->item_code      = $data[1]; 
						 $item->hsn_code       = $data[2];
						 $item->unit_id        = $data[3];
						 $item->stock_group_id = $data[4];
						 $item->company_id     = $data[5];
						 $this->Items->save($item);
					} 
					fclose($f);
					$records;

					move_uploaded_file($csv['tmp_name'], WWW_ROOT . '/csv/csv_'.date("d-m-Y").'.'.$ext);
				}
			   
				
			}
		}
        $this->set(compact('uplode_csv'));
        $this->set('_serialize', ['uplode_csv']);
    }
	
	public function checkUnique($provided_item_code){
		
		$company_id=$this->Auth->User('session_company_id');
		$itemcode = $this->Items->find()->where(['Items.item_code'=>$provided_item_code,'Items.company_id'=>$company_id]);
		
		$data['is_unique'] = "yes";
		echo json_encode($data);
		
		exit;
	}
	
	public function generateBarcode(){
		
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$item = $this->Items->newEntity();
		if ($this->request->is('put','post','patch')) {
			$item_name = $this->Items->patchEntity($item, $this->request->getData());
			
			$itemids=array_filter($item_name->item_name);
			$encodeitemids=json_encode($itemids);
			 return $this->redirect(['action' => 'viewBarcode', $encodeitemids]);
			
		}
			
		$items = $this->Items->find()
			->where(['Items.company_id'=>$company_id]);
					
		$itemOptions=[];
		foreach($items as $item)
		{
			$itemOptions[]=['text' =>$item->item_code.' '.$item->name, 'value' => $item->id, 'gst_figure_tax_name'=>@$item->gst_figure->name];
		}
        $this->set(compact('items','item','itemOptions'));
        $this->set('_serialize', ['item']);
	}
	
	public function viewBarcode($encodeitemids=null){
		$items=json_decode($encodeitemids);
		$this->viewBuilder()->layout('');
		$company_id=$this->Auth->User('session_company_id');
		$item_barcodes=[];
		foreach($items as $item){
			$item_barcodes[] = $this->Items->get($item, [
				'contain'=>['Shades','Sizes']
			]);
			 
		}
		
        $this->set(compact('item_barcodes'));
        $this->set('_serialize', ['items']);
	}
}

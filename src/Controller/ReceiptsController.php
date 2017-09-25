<?php
namespace App\Controller;
use App\Controller\AppController;
//	use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\FormHelper;


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
		
		if($prty->bank== '1'){
				$partyOptions[]=['text' =>str_pad(@$Partyledger->customer->customer_id, 4, '0', STR_PAD_LEFT).' - '.$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id, 'open_window'=>'bank'];
			}
			 else if($Partyledger->bill_to_bill_accounting == 'yes'){
				$partyOptions[]=['text' =>str_pad(@$Partyledger->customer->customer_id, 4, '0', STR_PAD_LEFT).' - '.$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id, 'open_window'=>'reference'];
			}
			else if($prty->bank== '0' && $Partyledger->bill_to_bill_accounting== 'no' && $prty->name== 'Sundry Creditors'){
				$partyOptions[]=['text' =>str_pad(@$Partyledger->customer->customer_id, 4, '0', STR_PAD_LEFT).' - '.$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id, 'open_window'=>'on_account'];
			}
			else if($prty->bank== '0' && $Partyledger->bill_to_bill_accounting== 'no' && $prty->name== 'Sundry Debtors'){
				$partyOptions[]=['text' =>str_pad(@$Partyledger->customer->customer_id, 4, '0', STR_PAD_LEFT).' - '.$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id, 'open_window'=>'on_account'];
			}
			else{
			$partyOptions[]=['text' =>str_pad(@$Partyledger->customer->customer_id, 4, '0', STR_PAD_LEFT).' - '.$Partyledger->name, 'value' => $Partyledger->id ,'party_state_id'=>@$Partyledger->customer->state_id, 'open_window'=>'no'];
			}
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
		//$html = new HtmlHelper(new \Cake\View\View());
		$html = new FormHelper(new \Cake\View\View());
	    //$this->viewBuilder()->layout('');
		$company_id=$this->Auth->User('session_company_id');
		$query = $this->Receipts->ReceiptRows->ReferenceDetails->find();
		$totalInCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['debit >' => 0]),
				$query->newExpr()->add(['debit']),
				'integer'
			);
		$totalOutCase = $query->newExpr()
			->addCase(
				$query->newExpr()->add(['credit >' => 0]),
				$query->newExpr()->add(['credit']),
				'integer'
			);
		$query->select([
			'total_in' => $query->func()->sum($totalInCase),
			'total_out' => $query->func()->sum($totalOutCase),'id','ledger_id'
		])
		->where(['ReferenceDetails.company_id'=>$company_id])
		->group('ref_name')
		->autoFields(true);
        $refdetails = ($query);
		$partyOptions=[];
		foreach($refdetails as $data)
		{
		           $ref_name=$data->ref_name;
		           $refeDebitBill=$data->total_in;
				   $refeCreditBill=$data->total_out;
				   $tot=$refeDebitBill-$refeCreditBill;
		$refOptions[]=['text' =>$ref_name.' - '.$tot, 'value' => $ref_name];
		}
		if($itemValue=='Agst Ref')
		{
		echo $html->input('ref_name', ['options'=>$refOptions,'label' => false,'class' => 'form-control input-medium ref_name','required'=>'required']); 
		}
		else{
		echo $html->input('ref_name', ['label' => false,'class' => 'form-control input-medium ref_name','required'=>'required']); 
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

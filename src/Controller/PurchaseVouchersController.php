<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PurchaseVouchers Controller
 *
 * @property \App\Model\Table\PurchaseVouchersTable $PurchaseVouchers
 *
 * @method \App\Model\Entity\PurchaseVoucher[] paginate($object = null, array $settings = [])
 */
class PurchaseVouchersController extends AppController
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
            'contain' => ['Companies']
        ];
        $purchaseVouchers = $this->paginate($this->PurchaseVouchers->find()->where(['PurchaseVouchers.company_id'=>$company_id]));

        $this->set(compact('purchaseVouchers'));
        $this->set('_serialize', ['purchaseVouchers']);
    }

    /**
     * View method
     *
     * @param string|null $id Purchase Voucher id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $purchaseVoucher = $this->PurchaseVouchers->get($id, [
            'contain' => ['Companies', 'PurchaseVoucherRows'=>['Ledgers']]
        ]);

        $this->set('purchaseVoucher', $purchaseVoucher);
        $this->set('_serialize', ['purchaseVoucher']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
		$purchaseVoucher = $this->PurchaseVouchers->newEntity();
		$company_id=$this->Auth->User('session_company_id');
		if ($this->request->is('post')) 
		{
			$purchaseVoucher = $this->PurchaseVouchers->patchEntity($purchaseVoucher, $this->request->getData());
			$Voucher_no = $this->PurchaseVouchers->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
			if($Voucher_no)
			{
				$purchaseVoucher->voucher_no = $Voucher_no->voucher_no+1;
			}
			else
			{
				$purchaseVoucher->voucher_no = 1;
			} 
			if(!empty($purchaseVoucher->supplier_invoice_date))
			{
				$purchaseVoucher->supplier_invoice_date = date("Y-m-d",strtotime($purchaseVoucher->supplier_invoice_date));
			}
			$purchaseVoucher->transaction_date      = date("Y-m-d",strtotime($purchaseVoucher->transaction_date));
			$purchaseVoucher->company_id            = $company_id;
			//pr($purchaseVoucher);exit;
			if ($this->PurchaseVouchers->save($purchaseVoucher)) 
			{
				foreach($purchaseVoucher->purchase_voucher_rows as $purchase_voucher_row)
				{
					$accountEntry = $this->PurchaseVouchers->AccountingEntries->newEntity();
					$accountEntry->ledger_id                  = $purchase_voucher_row->ledger_id;
					$accountEntry->debit                      = $purchase_voucher_row->debit;
					$accountEntry->credit                     = $purchase_voucher_row->credit;
					$accountEntry->transaction_date           = $purchaseVoucher->transaction_date;
					$accountEntry->company_id                 = $company_id;
					$accountEntry->purchase_voucher_id        = $purchaseVoucher->id;
					$accountEntry->purchase_voucher_row_id    = $purchase_voucher_row->id;
					
					$this->PurchaseVouchers->AccountingEntries->save($accountEntry);
				}
				
				$this->Flash->success(__('The purchase voucher has been saved.'));

				return $this->redirect(['action' => 'add']);
			}
			$this->Flash->error(__('The purchase voucher could not be saved. Please, try again.'));
		}
		$Voucher_no = $this->PurchaseVouchers->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
		if($Voucher_no)
		{
			$voucher_no=$Voucher_no->voucher_no+1;
		}
		else
		{
			$voucher_no=1;
		} 

		$ledgers = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->find('list')->where(['company_id'=>$company_id]);
		$accountGroupCredits = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups->find()->where(['purchase_voucher_party'=>1,'company_id'=>$company_id]);
		foreach($accountGroupCredits as $accountGroupCredit)
		{
			$accountingGroups = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups
			->find('children', ['for' => $accountGroupCredit->id])
			->find('List')->toArray();
			$accountingGroups[$accountGroupCredit->id]=$accountGroupCredit->name;
		}
		ksort($accountingGroups);
		if($accountingGroups)
		{   
			$account_ids="";
			foreach($accountingGroups as $key=>$accountingGroup)
			{
				$account_ids .=$key.',';
			}
			$account_ids = explode(",",trim($account_ids,','));
			$Creditledgers = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
		
		$accountGroupdebit = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups->find()->where(['purchase_voucher_purchase_account'=>1,'company_id'=>$company_id])->first();

		$accountingGroups = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups
		->find('children', ['for' => $accountGroupdebit->id])
		->find('List')->toArray();
		$accountingGroups[$accountGroupdebit->id]=$accountGroupdebit->name;
		ksort($accountingGroups);
		if($accountingGroups)
		{   
			$account_ids="";
			foreach($accountingGroups as $key=>$accountingGroup)
			{
				$account_ids .=$key.',';
			}
			$account_ids = explode(",",trim($account_ids,','));
			$Debitledgers = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
		$this->set(compact('purchaseVoucher','voucher_no','Creditledgers','Debitledgers','ledgers'));
		$this->set('_serialize', ['purchaseVoucher']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Purchase Voucher id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
        $purchaseVoucher = $this->PurchaseVouchers->get($id, [
            'contain' => ['PurchaseVoucherRows'=>['Ledgers']]
        ]);
		//pr($purchaseVoucher->toArray());exit;
		$company_id=$this->Auth->User('session_company_id');
        if ($this->request->is(['patch', 'post', 'put'])) {
            $purchaseVoucher = $this->PurchaseVouchers->patchEntity($purchaseVoucher, $this->request->getData());
			if(!empty($purchaseVoucher->supplier_invoice_date))
			{
				$purchaseVoucher->supplier_invoice_date = date("Y-m-d",strtotime($purchaseVoucher->supplier_invoice_date));
			}
			$purchaseVoucher->transaction_date      = date("Y-m-d",strtotime($purchaseVoucher->transaction_date));
            if ($this->PurchaseVouchers->save($purchaseVoucher)) {
				$query_delete = $this->PurchaseVouchers->AccountingEntries->query();
					$query_delete->delete()
					->where(['purchase_voucher_id' => $purchaseVoucher->id,'company_id'=>$company_id])
					->execute();
				foreach($purchaseVoucher->purchase_voucher_rows as $purchase_voucher_row)
				{
					$accountEntry = $this->PurchaseVouchers->AccountingEntries->newEntity();
					$accountEntry->ledger_id                  = $purchase_voucher_row->ledger_id;
					$accountEntry->debit                      = $purchase_voucher_row->debit;
					$accountEntry->credit                     = $purchase_voucher_row->credit;
					$accountEntry->transaction_date           = $purchaseVoucher->transaction_date;
					$accountEntry->company_id                 = $company_id;
					$accountEntry->purchase_voucher_id        = $purchaseVoucher->id;
					$accountEntry->purchase_voucher_row_id    = $purchase_voucher_row->id;
					
					$this->PurchaseVouchers->AccountingEntries->save($accountEntry);
				}
                $this->Flash->success(__('The purchase voucher has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase voucher could not be saved. Please, try again.'));
        }
		$ledgers = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->find('list')->where(['company_id'=>$company_id]);
		$accountGroupCredits = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups->find()->where(['purchase_voucher_party'=>1,'company_id'=>$company_id]);
		foreach($accountGroupCredits as $accountGroupCredit)
		{
			$accountingGroups = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups
			->find('children', ['for' => $accountGroupCredit->id])
			->find('List')->toArray();
			$accountingGroups[$accountGroupCredit->id]=$accountGroupCredit->name;
		}
		ksort($accountingGroups);
		if($accountingGroups)
		{   
			$account_ids="";
			foreach($accountingGroups as $key=>$accountingGroup)
			{
				$account_ids .=$key.',';
			}
			$account_ids = explode(",",trim($account_ids,','));
			$Creditledgers = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
		
		$accountGroupdebit = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups->find()->where(['purchase_voucher_purchase_account'=>1,'company_id'=>$company_id])->first();

		$accountingGroups = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->AccountingGroups
		->find('children', ['for' => $accountGroupdebit->id])
		->find('List')->toArray();
		$accountingGroups[$accountGroupdebit->id]=$accountGroupdebit->name;
		ksort($accountingGroups);
		if($accountingGroups)
		{   
			$account_ids="";
			foreach($accountingGroups as $key=>$accountingGroup)
			{
				$account_ids .=$key.',';
			}
			$account_ids = explode(",",trim($account_ids,','));
			$Debitledgers = $this->PurchaseVouchers->PurchaseVoucherRows->Ledgers->find('list')->where(['Ledgers.accounting_group_id IN' =>$account_ids]);
        }
        $this->set(compact('purchaseVoucher','Creditledgers','Debitledgers','ledgers'));
        $this->set('_serialize', ['purchaseVoucher']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Purchase Voucher id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purchaseVoucher = $this->PurchaseVouchers->get($id);
        if ($this->PurchaseVouchers->delete($purchaseVoucher)) {
            $this->Flash->success(__('The purchase voucher has been deleted.'));
        } else {
            $this->Flash->error(__('The purchase voucher could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

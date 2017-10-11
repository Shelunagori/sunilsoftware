<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * PurchaseReturns Controller
 *
 * @property \App\Model\Table\PurchaseReturnsTable $PurchaseReturns
 *
 * @method \App\Model\Entity\PurchaseReturn[] paginate($object = null, array $settings = [])
 */
class PurchaseReturnsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['PurchaseInvoices', 'Companies']
        ];
        $purchaseReturns = $this->paginate($this->PurchaseReturns);

        $this->set(compact('purchaseReturns'));
        $this->set('_serialize', ['purchaseReturns']);
    }

    /**
     * View method
     *
     * @param string|null $id Purchase Return id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $purchaseReturn = $this->PurchaseReturns->get($id, [
            'contain' => ['PurchaseInvoices', 'Companies', 'PurchaseReturnRows']
        ]);

        $this->set('purchaseReturn', $purchaseReturn);
        $this->set('_serialize', ['purchaseReturn']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id=null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
		$location_id=$this->Auth->User('session_location_id');
		$stateDetails=$this->Auth->User('session_company');
		$state_id=$stateDetails->state_id;
        $PurchaseInvoice = $this->PurchaseReturns->PurchaseInvoices->get($id, [
            'contain' => (['PurchaseInvoiceRows'=>['Items'=>['FirstGstFigures']],'SupplierLedgers'=>['Suppliers']])
        ]);
		$supplier_state_id=$PurchaseInvoice->supplier_ledger->supplier->state_id;
		//pr($PurchaseInvoice->supplier->state_id); exit;
        $purchaseReturn = $this->PurchaseReturns->newEntity();
        if ($this->request->is('post')) {
            $purchaseReturn = $this->PurchaseReturns->patchEntity($purchaseReturn, $this->request->getData());
			$purchaseReturn->transaction_date = date("Y-m-d",strtotime($this->request->getData()['transaction_date']));
			$Voucher_no = $this->PurchaseReturns->find()->select(['voucher_no'])->where(['company_id'=>$company_id])->order(['voucher_no' => 'DESC'])->first();
			if($Voucher_no)
			{
				$purchaseReturn->voucher_no = $Voucher_no->voucher_no+1;
			}
			else
			{
				$purchaseReturn->voucher_no = 1;
			} 
			$purchaseReturn->company_id = $company_id;
			pr($purchaseReturn); exit;
            if ($this->PurchaseReturns->save($purchaseReturn)) {
                $this->Flash->success(__('The purchase return has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase return could not be saved. Please, try again.'));
        }
        $purchaseInvoices = $this->PurchaseReturns->PurchaseInvoices->find('list', ['limit' => 200]);
        $companies = $this->PurchaseReturns->Companies->find('list', ['limit' => 200]);
        $this->set(compact('purchaseReturn', 'purchaseInvoices', 'companies','PurchaseInvoice','state_id','supplier_state_id'));
        $this->set('_serialize', ['purchaseReturn']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Purchase Return id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $purchaseReturn = $this->PurchaseReturns->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $purchaseReturn = $this->PurchaseReturns->patchEntity($purchaseReturn, $this->request->getData());
            if ($this->PurchaseReturns->save($purchaseReturn)) {
                $this->Flash->success(__('The purchase return has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase return could not be saved. Please, try again.'));
        }
        $purchaseInvoices = $this->PurchaseReturns->PurchaseInvoices->find('list', ['limit' => 200]);
        $companies = $this->PurchaseReturns->Companies->find('list', ['limit' => 200]);
        $this->set(compact('purchaseReturn', 'purchaseInvoices', 'companies'));
        $this->set('_serialize', ['purchaseReturn']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Purchase Return id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purchaseReturn = $this->PurchaseReturns->get($id);
        if ($this->PurchaseReturns->delete($purchaseReturn)) {
            $this->Flash->success(__('The purchase return has been deleted.'));
        } else {
            $this->Flash->error(__('The purchase return could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

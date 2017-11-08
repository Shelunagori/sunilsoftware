<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AccountingGroups Controller
 *
 * @property \App\Model\Table\AccountingGroupsTable $AccountingGroups
 *
 * @method \App\Model\Entity\AccountingGroup[] paginate($object = null, array $settings = [])
 */
class AccountingGroupsController extends AppController
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
            'contain' => ['NatureOfGroups', 'ParentAccountingGroups', 'Companies']
        ];
        $accountingGroups = $this->paginate($this->AccountingGroups->find()->where(['AccountingGroups.company_id'=>$company_id]));

        $this->set(compact('accountingGroups'));
        $this->set('_serialize', ['accountingGroups']);
    }

    /**
     * View method
     *
     * @param string|null $id Accounting Group id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $accountingGroup = $this->AccountingGroups->get($id, [
            'contain' => ['NatureOfGroups', 'ParentAccountingGroups', 'Companies', 'ChildAccountingGroups', 'Ledgers']
        ]);

        $this->set('accountingGroup', $accountingGroup);
        $this->set('_serialize', ['accountingGroup']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
        $accountingGroup = $this->AccountingGroups->newEntity();
		$company_id=$this->Auth->User('session_company_id');
        if ($this->request->is('post')) {
            $accountingGroup = $this->AccountingGroups->patchEntity($accountingGroup, $this->request->getData());
			$accountingGroup->company_id = $company_id;
            if ($this->AccountingGroups->save($accountingGroup)) {
                $this->Flash->success(__('The accounting group has been saved.'));

                return $this->redirect(['action' => 'add']);
            }
            $this->Flash->error(__('The accounting group could not be saved. Please, try again.'));
        }
        $natureOfGroups = $this->AccountingGroups->NatureOfGroups->find('list');
        $parentAccountingGroups = $this->AccountingGroups->ParentAccountingGroups->find('list')->where(['ParentAccountingGroups.company_id'=>$company_id]);
        $this->set(compact('accountingGroup', 'natureOfGroups', 'parentAccountingGroups'));
        $this->set('_serialize', ['accountingGroup']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Accounting Group id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
        $accountingGroup = $this->AccountingGroups->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $accountingGroup = $this->AccountingGroups->patchEntity($accountingGroup, $this->request->getData());
            if ($this->AccountingGroups->save($accountingGroup)) {
                $this->Flash->success(__('The accounting group has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The accounting group could not be saved. Please, try again.'));
        }
        $natureOfGroups = $this->AccountingGroups->NatureOfGroups->find('list');
        $parentAccountingGroups = $this->AccountingGroups->ParentAccountingGroups->find('list')->where(['company_id'=>$company_id]);
        $this->set(compact('accountingGroup', 'natureOfGroups', 'parentAccountingGroups'));
        $this->set('_serialize', ['accountingGroup']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Accounting Group id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $accountingGroup = $this->AccountingGroups->get($id);
        if ($this->AccountingGroups->delete($accountingGroup)) {
            $this->Flash->success(__('The accounting group has been deleted.'));
        } else {
            $this->Flash->error(__('The accounting group could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

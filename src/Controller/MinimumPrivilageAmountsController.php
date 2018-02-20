<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * MinimumPrivilageAmounts Controller
 *
 * @property \App\Model\Table\MinimumPrivilageAmountsTable $MinimumPrivilageAmounts
 *
 * @method \App\Model\Entity\MinimumPrivilageAmount[] paginate($object = null, array $settings = [])
 */
class MinimumPrivilageAmountsController extends AppController
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
        $minimumPrivilageAmounts = $this->paginate($this->MinimumPrivilageAmounts->find()->where(['company_id'=>$company_id]));

        $this->set(compact('minimumPrivilageAmounts'));
        $this->set('_serialize', ['minimumPrivilageAmounts']);
    }

    /**
     * View method
     *
     * @param string|null $id Minimum Privilage Amount id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $minimumPrivilageAmount = $this->MinimumPrivilageAmounts->get($id, [
            'contain' => ['Companies']
        ]);

        $this->set('minimumPrivilageAmount', $minimumPrivilageAmount);
        $this->set('_serialize', ['minimumPrivilageAmount']);
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
        $minimumPrivilageAmount = $this->MinimumPrivilageAmounts->newEntity();
		$this->request->data['company_id'] =$company_id;
		$this->request->data['created_on'] = date('Y-m-d');
        if ($this->request->is('post')) {
            $minimumPrivilageAmount = $this->MinimumPrivilageAmounts->patchEntity($minimumPrivilageAmount, $this->request->getData());
            if ($this->MinimumPrivilageAmounts->save($minimumPrivilageAmount)) {
                $this->Flash->success(__('The minimum privilage amount has been saved.'));
				return $this->redirect(['action' => 'index']);
            }
			
            $this->Flash->error(__('The minimum privilage amount could not be saved. Please, try again.'));
        }

        $this->set(compact('minimumPrivilageAmount', 'companies'));
        $this->set('_serialize', ['minimumPrivilageAmount']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Minimum Privilage Amount id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$company_id=$this->Auth->User('session_company_id');
        $minimumPrivilageAmount = $this->MinimumPrivilageAmounts->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $minimumPrivilageAmount = $this->MinimumPrivilageAmounts->patchEntity($minimumPrivilageAmount, $this->request->getData());
            if ($this->MinimumPrivilageAmounts->save($minimumPrivilageAmount)) {
                $this->Flash->success(__('The minimum privilage amount has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The minimum privilage amount could not be saved. Please, try again.'));
        }
        
        $this->set(compact('minimumPrivilageAmount', 'companies'));
        $this->set('_serialize', ['minimumPrivilageAmount']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Minimum Privilage Amount id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $minimumPrivilageAmount = $this->MinimumPrivilageAmounts->get($id);
        if ($this->MinimumPrivilageAmounts->delete($minimumPrivilageAmount)) {
            $this->Flash->success(__('The minimum privilage amount has been deleted.'));
        } else {
            $this->Flash->error(__('The minimum privilage amount could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

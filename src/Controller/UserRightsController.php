<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * UserRights Controller
 *
 * @property \App\Model\Table\UserRightsTable $UserRights
 *
 * @method \App\Model\Entity\UserRight[] paginate($object = null, array $settings = [])
 */
class UserRightsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Pages', 'Users']
        ];
        $userRights = $this->paginate($this->UserRights);

        $this->set(compact('userRights'));
        $this->set('_serialize', ['userRights']);
    }

    /**
     * View method
     *
     * @param string|null $id User Right id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userRight = $this->UserRights->get($id, [
            'contain' => ['Pages', 'Users']
        ]);

        $this->set('userRight', $userRight);
        $this->set('_serialize', ['userRight']);
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
		$userRight = $this->UserRights->newEntity();
        if ($this->request->is('post')) {
			$userData=$this->request->getData();
			$this->UserRights->deleteAll(['UserRights.user_id'=>$userData['user_id']]);
			foreach($userData['page_id'] as $data)
			{
				$rightsData = $this->UserRights->query();
								$rightsData->insert(['user_id','page_id'])
										->values([
										'user_id' => $userData['user_id'],
										'page_id' => $data])
					  ->execute();
            }
			 $this->Flash->success(__('The user right has been saved.'));
             return $this->redirect(['action' => 'add']);
	}
	           // $this->Flash->error(__('The user right could not be saved. Please, try again.'));
        $pages = $this->UserRights->Pages->find('All');
        $users = $this->UserRights->Users->find('list', ['limit' => 200]);
        $this->set(compact('userRight', 'pages', 'users'));
        $this->set('_serialize', ['userRight']);
    }
	public function ajaxUserRights($userid=null)
    {
	    $this->viewBuilder()->layout('');
		//$company_id=$this->Auth->User('session_company_id');
		//$stateDetails=$this->Auth->User('session_company');
		//$location_id=$this->Auth->User('session_location_id');
		//$state_id=$stateDetails->state_id;
		$userData = $this->UserRights->Pages->find()
					->contain(['UserRights'=>function($q) use($userid){
						return $q->where(['UserRights.user_id'=>$userid]);
						}])
					->autoFields(true);
					echo '
					<div class="row"><div class="col-md-12"><div class="col-md-4"><h3 style="color:#000; font-size:15px; font-family:georgia"><input type="checkbox" class="checkAll" name="" value="" id="checkAll">Check All</h3></div></div></div>';
					
								 foreach($userData->toArray() as $data)
								{ 
							        if(!empty(@$data->user_rights[0]->page_id))
									{?><?php $ckd='checked';?>
									<?php } else {?>
									<?php $ckd=' ';  ?>
									<?php } 
										echo "<div class='col-md-4 list-group-item' style='color:#acb3b9; font-size:15px; font-family:georgia'><input type='checkbox' name='page_id[]' value=".$data['id']." ".$ckd.">".$data['controller_name']." / ".$data['action']."</div>";
							    }
						exit;
}

    /**
     * Edit method
     *
     * @param string|null $id User Right id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userRight = $this->UserRights->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userRight = $this->UserRights->patchEntity($userRight, $this->request->getData());
            if ($this->UserRights->save($userRight)) {
                $this->Flash->success(__('The user right has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user right could not be saved. Please, try again.'));
        }
        $pages = $this->UserRights->Pages->find('list', ['limit' => 200]);
        $users = $this->UserRights->Users->find('list', ['limit' => 200]);
        $this->set(compact('userRight', 'pages', 'users'));
        $this->set('_serialize', ['userRight']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User Right id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userRight = $this->UserRights->get($id);
        if ($this->UserRights->delete($userRight)) {
            $this->Flash->success(__('The user right has been deleted.'));
        } else {
            $this->Flash->error(__('The user right could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}

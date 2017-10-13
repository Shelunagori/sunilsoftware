<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

		
		FrozenTime::setToStringFormat('dd-MM-yyyy hh:mm a');  // For any immutable DateTime
		FrozenDate::setToStringFormat('dd-MM-yyyy');  // For any immutable Date
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
		$this->loadComponent('Auth', [
		 'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'username',
                        'password' => 'password'
                    ],
                      'userModel' => 'Users'
                ]
            ],
            'logoutRedirect' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
			'unauthorizedRedirect' => $this->referer(),
        ]);
		
		if($this->Auth->User('session_company')){
			$coreVariable = [
				'company_name' => $this->Auth->User('session_company')->name,
				'fyValidFrom' => $this->Auth->User('fyValidFrom'),
				'fyValidTo' => $this->Auth->User('fyValidTo'),
				'location_name' => $this->Auth->User('location_name'),
			];
			$this->coreVariable = $coreVariable;
			$this->set(compact('coreVariable'));
		}
		
		
		
        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        //$this->loadComponent('Csrf');
    }
	
	public function repairRef(){    
		$this->loadModel('ReferenceDetails');
		$company_id=$this->Auth->User('session_company_id');
		$contraVouchers = $this->ReferenceDetails->find()
							->where(['ReferenceDetails.type'=>'Against','company_id'=>$company_id])
							->group(['ReferenceDetails.ref_name']);
							
		foreach($contraVouchers as $contraVoucher)
		{   
			 $totalRef = $this->ReferenceDetails->find()
			                 ->where(['ReferenceDetails.ref_name'=>$contraVoucher->ref_name,'company_id'=>$company_id,'ReferenceDetails.type'=>'New Ref'])->count(); 
			if($totalRef<1)
			{
				$query = $this->ReferenceDetails->query();
				$query->update()
					->set(['type' => 'New Ref'])
					->where(['id' => $contraVoucher->id])
					->execute();
			}
			
		}
	}

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
			
            $this->set('_serialize', true);
        }
    }
	
	
	
}

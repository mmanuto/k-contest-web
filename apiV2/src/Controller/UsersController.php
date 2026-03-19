<?php
declare(strict_types=1);

namespace App\Controller;
use RestApi\Controller\ApiController;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $users = $this->paginate($this->Users);

        $this->set(compact('users'));
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('user'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login(){

        $data = $this->request->getData();
    
        $user = $this->Users->find()
            ->where([
                'username' => $data['username'],
                'password' => md5($data['password'])
            ])
            ->first();
    
        if (empty($user)) {
            $this->apiResponse['success'] = false;
            $this->apiResponse['error'] = 'Username o password errati';
    
            return;
        }else{
            $this->apiResponse['data'] = $user;
            $this->apiResponse['success'] = true;
            $this->apiResponse['message'] = 'Logged in successfully.';   
        }
            
    }


        public function updateUserData(){
            $data = $this->request->getData();
            /* print_r("dati: ");
            print_r($data); */
            if(isset($data['password'])){
                $data['password'] = md5($data['password']);
            }

            $user = $this->Users->get($data['id'], [
                'contain' => []
            ]);
                $user = $this->Users->patchEntity($user, $data);
                if ($this->Users->save($user)) {
                    $this->apiResponse['success'] = true;
                }else{
                    $this->apiResponse['success'] = false;
                }
                
        }

        /** =========================================================================================================
         * ASSEGNAZIONE TATAMI - Aggiornamento da locale
         * ==========================================================================================================
         */
        public function addTatamiAssignments(){
            
            $this->loadModel('TatamiAssignments');
            $data = $this->request->getData();

           //controllo che non sia già assegnata ad un altro tatami
            $categoryStatus = $this->TatamiAssignments->find()
            ->where([
                'categorycode_id' => $data['categorycode_id'],
                'competition_id' => $data['competition_id']
            ])->first();

            

            //se è già su un altro tatami la sposto mantenendo lo stato
            if ($categoryStatus){
                $categoryStatus->tatami =  $data['tatami'];
                if ($this->TatamiAssignments->save($categoryStatus)) {
                    $this->apiResponse['success'] = true;
                }else{
                    $this->apiResponse['message'] = 'The user category could not be saved. Please, try again.';
                    $this->apiResponse['success'] = false;
                }
            }else{ //Altrimenti creo un nunovo record
                
                $userCategory = $this->TatamiAssignments->newEmptyEntity();
                $userCategory = $this->TatamiAssignments->patchEntity($userCategory, $data);

                if ($this->TatamiAssignments->save($userCategory)) {
                    $this->apiResponse['success'] = true;
                }else{
                    $this->apiResponse['message'] = 'The user category could not be saved. Please, try again.';
                    $this->apiResponse['success'] = false;
                }
            }

        }


        /** =========================================================================================================
         * AGGIORNAMENTO STATO TATAMI - Aggiornamento da locale
         * ==========================================================================================================
         */

        public function updateCategoryStatus(){
            
            $this->loadModel('TatamiAssignments');
            $data = $this->request->getData();

            $usersCategorycode = $this->TatamiAssignments->find()
                                    ->where([
                                        'categorycode_id' => $data['categorycode_id'],
                                        'competition_id' => $data['competition_id'],
                                        'tatami' => $data['tatami']
                                    ])->first();
            $usersCategorycode->status = $data['status'];
            if ($this->TatamiAssignments->save($usersCategorycode)) {
                $this->apiResponse['success'] = true;
            }else{
                $this->apiResponse['message'] = 'The user category could not be saved. Please, try again.';
                $this->apiResponse['success'] = false;
            }


        }

}
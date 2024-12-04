<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;
/**
 * Belts Controller
 *
 * @property \App\Model\Table\BeltsTable $Belts
 * @method \App\Model\Entity\Belt[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class BeltsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $belts = $this->paginate($this->Belts);

        $this->set(compact('belts'));
    }

    /**
     * View method
     *
     * @param string|null $id Belt id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $belt = $this->Belts->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('belt'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $belt = $this->Belts->newEmptyEntity();
        if ($this->request->is('post')) {
            $belt = $this->Belts->patchEntity($belt, $this->request->getData());
            if ($this->Belts->save($belt)) {
                $this->Flash->success(__('The belt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The belt could not be saved. Please, try again.'));
        }
        $this->set(compact('belt'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Belt id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $belt = $this->Belts->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $belt = $this->Belts->patchEntity($belt, $this->request->getData());
            if ($this->Belts->save($belt)) {
                $this->Flash->success(__('The belt has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The belt could not be saved. Please, try again.'));
        }
        $this->set(compact('belt'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Belt id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $belt = $this->Belts->get($id);
        if ($this->Belts->delete($belt)) {
            $this->Flash->success(__('The belt has been deleted.'));
        } else {
            $this->Flash->error(__('The belt could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function getBeltList(){

        $beltList = $this->Belts->find('all');

        if ($beltList) {
            $this->apiResponse['data'] = $beltList;
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = 'Errore nel recupero dei dati';
        }
    }
}

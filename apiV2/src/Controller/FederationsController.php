<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;

/**
 * Federations Controller
 *
 * @property \App\Model\Table\FederationsTable $Federations
 * @method \App\Model\Entity\Federation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class FederationsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $federations = $this->paginate($this->Federations);

        $this->set(compact('federations'));
    }

    /**
     * View method
     *
     * @param string|null $id Federation id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $federation = $this->Federations->get($id, [
            'contain' => ['Athletes'],
        ]);

        $this->set(compact('federation'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $federation = $this->Federations->newEmptyEntity();
        if ($this->request->is('post')) {
            $federation = $this->Federations->patchEntity($federation, $this->request->getData());
            if ($this->Federations->save($federation)) {
                $this->Flash->success(__('The federation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The federation could not be saved. Please, try again.'));
        }
        $this->set(compact('federation'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Federation id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $federation = $this->Federations->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $federation = $this->Federations->patchEntity($federation, $this->request->getData());
            if ($this->Federations->save($federation)) {
                $this->Flash->success(__('The federation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The federation could not be saved. Please, try again.'));
        }
        $this->set(compact('federation'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Federation id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $federation = $this->Federations->get($id);
        if ($this->Federations->delete($federation)) {
            $this->Flash->success(__('The federation has been deleted.'));
        } else {
            $this->Flash->error(__('The federation could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

        // -----------------------------------------------------------------------------------------------------
    // @ ELENCO FEDERAZIONI
    // -----------------------------------------------------------------------------------------------------

    public function getFederations()
    {
        $this->loadModel('Federations');
        $federationList = $this->Federations->find('all');

        if ($federationList) {
            $this->apiResponse['data'] = $federationList;
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = 'Errore nel recupero dei dati';
        }
    }
}

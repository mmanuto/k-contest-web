<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;

/**
 * Agecategories Controller
 *
 * @property \App\Model\Table\AgecategoriesTable $Agecategories
 * @method \App\Model\Entity\Agecategory[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AgecategoriesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $agecategories = $this->paginate($this->Agecategories);

        $this->set(compact('agecategories'));
    }

    /**
     * View method
     *
     * @param string|null $id Agecategory id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $agecategory = $this->Agecategories->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('agecategory'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $agecategory = $this->Agecategories->newEmptyEntity();
        if ($this->request->is('post')) {
            $agecategory = $this->Agecategories->patchEntity($agecategory, $this->request->getData());
            if ($this->Agecategories->save($agecategory)) {
                $this->Flash->success(__('The agecategory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The agecategory could not be saved. Please, try again.'));
        }
        $this->set(compact('agecategory'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Agecategory id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $agecategory = $this->Agecategories->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $agecategory = $this->Agecategories->patchEntity($agecategory, $this->request->getData());
            if ($this->Agecategories->save($agecategory)) {
                $this->Flash->success(__('The agecategory has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The agecategory could not be saved. Please, try again.'));
        }
        $this->set(compact('agecategory'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Agecategory id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $agecategory = $this->Agecategories->get($id);
        if ($this->Agecategories->delete($agecategory)) {
            $this->Flash->success(__('The agecategory has been deleted.'));
        } else {
            $this->Flash->error(__('The agecategory could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // =========================================== CATEGORIA DATO L'ANNO DI NASCITA =================================================
    public function getAgeCategory()
    {
        $data = $this->request->getData();
        $competitionDate = explode("-", $data['competition_date']);
        $age = $competitionDate[0] - $data['anno'];

        $category = $this->Agecategories->find()
        ->where(['age_min <=' => $age, 'age_max >=' => $age])->first();


        if ($category) {
            $this->apiResponse['data'] = $category;
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }
}

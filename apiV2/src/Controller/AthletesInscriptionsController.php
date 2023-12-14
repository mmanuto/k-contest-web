<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;

/**
 * AthletesInscriptions Controller
 *
 * @property \App\Model\Table\AthletesInscriptionsTable $AthletesInscriptions
 * @method \App\Model\Entity\AthletesInscription[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AthletesInscriptionsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Athletes', 'CategoryCodes', 'Competitions'],
        ];
        $athletesInscriptions = $this->paginate($this->AthletesInscriptions);

        $this->set(compact('athletesInscriptions'));
    }

    /**
     * View method
     *
     * @param string|null $id Athletes Inscription id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $athletesInscription = $this->AthletesInscriptions->get($id, [
            'contain' => ['Athletes', 'CategoryCodes', 'Competitions'],
        ]);

        $this->set(compact('athletesInscription'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $athletesInscription = $this->AthletesInscriptions->newEmptyEntity();
        if ($this->request->is('post')) {
            $athletesInscription = $this->AthletesInscriptions->patchEntity($athletesInscription, $this->request->getData());
            if ($this->AthletesInscriptions->save($athletesInscription)) {
                $this->Flash->success(__('The athletes inscription has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The athletes inscription could not be saved. Please, try again.'));
        }
        $athletes = $this->AthletesInscriptions->Athletes->find('list', ['limit' => 200])->all();
        $categoryCodes = $this->AthletesInscriptions->CategoryCodes->find('list', ['limit' => 200])->all();
        $competitions = $this->AthletesInscriptions->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('athletesInscription', 'athletes', 'categoryCodes', 'competitions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Athletes Inscription id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $athletesInscription = $this->AthletesInscriptions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $athletesInscription = $this->AthletesInscriptions->patchEntity($athletesInscription, $this->request->getData());
            if ($this->AthletesInscriptions->save($athletesInscription)) {
                $this->Flash->success(__('The athletes inscription has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The athletes inscription could not be saved. Please, try again.'));
        }
        $athletes = $this->AthletesInscriptions->Athletes->find('list', ['limit' => 200])->all();
        $categoryCodes = $this->AthletesInscriptions->CategoryCodes->find('list', ['limit' => 200])->all();
        $competitions = $this->AthletesInscriptions->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('athletesInscription', 'athletes', 'categoryCodes', 'competitions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Athletes Inscription id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $athletesInscription = $this->AthletesInscriptions->get($id);
        if ($this->AthletesInscriptions->delete($athletesInscription)) {
            $this->Flash->success(__('The athletes inscription has been deleted.'));
        } else {
            $this->Flash->error(__('The athletes inscription could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function getAthletesByCategory(){

        $request = $this->request->getData();
        $athleteList = $this->AthletesInscriptions->find()
        ->where([
            'category_code_id' => $request['categorycode_id'],
            'competition_id' => $request['competition_id']
        ])
        ->contain(['Athletes' => ['Clubs'], 'CategoryCodes'])
        ->order(['Clubs.id', 'Athletes.id'])
        ->toArray();

        $this->apiResponse['data'] = $athleteList;
	    $this->apiResponse['success'] = true;
    }



    public function updateCompetitionData(){
        $this->loadModel('Athletes');

        $data = $this->request->getData();

        foreach ($data as $item) {
            $entity = $this->AthletesInscriptions->get($item['id']);
            if ($entity) {
                $entity->category_code_id = $item['categorycode_id'];
                $entity->modificato = $item['modificato'];
                $entity->accorpamento = $item['accorpamento'];
                $entity->old_category = $item['old_category'];

                $this->AthletesInscriptions->save($entity);
            }else{
                $athlete = $this->Athletes->get($item['athlete_id']);
                if($athlete){
                    $newEntity = $this->AthletesInscriptions->newEmptyEntity();
                    $newEntity = $this->AthletesInscriptions->patchEntity($newEntity, $item);
                    $newEntity->id = $item['id'];
                    $this->AthletesInscriptions->save($entity);
                }
            }
        }

        $this->apiResponse['success'] = true;

        
    }
}

<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;
use Cake\Core\Configure;

/**
 * AthleteInscriptions Controller
 *
 * @property \App\Model\Table\AthleteInscriptionsTable $AthleteInscriptions
 * @method \App\Model\Entity\AthleteInscription[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AthleteInscriptionsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Athletes', 'Categorycodes', 'Competitions'],
        ];
        $athleteInscriptions = $this->paginate($this->AthleteInscriptions);

        $this->set(compact('athleteInscriptions'));
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
        $athleteInscription = $this->AthleteInscriptions->get($id, [
            'contain' => ['Athletes', 'Categorycodes', 'Competitions'],
        ]);

        $this->set(compact('athleteInscription'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $athleteInscription = $this->AthleteInscriptions->newEmptyEntity();
        if ($this->request->is('post')) {
            $athleteInscription = $this->AthleteInscriptions->patchEntity($athleteInscription, $this->request->getData());
            if ($this->AthleteInscriptions->save($athleteInscription)) {
                $this->Flash->success(__('The athletes inscription has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The athletes inscription could not be saved. Please, try again.'));
        }
        $athletes = $this->AthleteInscriptions->Athletes->find('list', ['limit' => 200])->all();
        $categorycodes = $this->AthleteInscriptions->Categorycodes->find('list', ['limit' => 200])->all();
        $competitions = $this->AthleteInscriptions->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('athleteInscription', 'athletes', 'categorycodes', 'competitions'));
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
        $athleteInscription = $this->AthleteInscriptions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $athleteInscription = $this->AthleteInscriptions->patchEntity($athleteInscription, $this->request->getData());
            if ($this->AthleteInscriptions->save($athleteInscription)) {
                $this->Flash->success(__('The athletes inscription has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The athletes inscription could not be saved. Please, try again.'));
        }
        $athletes = $this->AthleteInscriptions->Athletes->find('list', ['limit' => 200])->all();
        $categorycodes = $this->AthleteInscriptions->Categorycodes->find('list', ['limit' => 200])->all();
        $competitions = $this->AthleteInscriptions->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('athleteInscription', 'athletes', 'categorycodes', 'competitions'));
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
        $athleteInscription = $this->AthleteInscriptions->get($id);
        if ($this->AthleteInscriptions->delete($athleteInscription)) {
            $this->Flash->success(__('The athletes inscription has been deleted.'));
        } else {
            $this->Flash->error(__('The athletes inscription could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function getAthletesByCategory(){

        $request = $this->request->getData();
        $athleteList = $this->AthleteInscriptions->find()
        ->where([
            'categorycode_id' => $request['categorycode_id'],
            'competition_id' => $request['competition_id'],
            'deleted' => 0
        ])
        ->contain(['Athletes' => ['Clubs'], 'Categorycodes'])
        ->order(['Clubs.id', 'Athletes.id'])
        ->toArray();

        $this->apiResponse['data'] = $athleteList;
	    $this->apiResponse['success'] = true;
    }



    public function updateCompetitionData(){
        $this->loadModel('Athletes');

        $data = $this->request->getData();

        foreach ($data as $item) {
            $entity = $this->AthleteInscriptions->get($item['id']);
            if ($entity) {
                $entity->categorycode_id = $item['categorycode_id'];
                $entity->modificato = $item['modificato'];
                $entity->accorpamento = $item['accorpamento'];
                $entity->old_category = $item['old_category'];

                $this->AthleteInscriptions->save($entity);
            }else{
                $athlete = $this->Athletes->get($item['athlete_id']);
                if($athlete){
                    $newEntity = $this->AthleteInscriptions->newEmptyEntity();
                    $newEntity = $this->AthleteInscriptions->patchEntity($newEntity, $item);
                    $newEntity->id = $item['id'];
                    $this->AthleteInscriptions->save($entity);
                }
            }
        }

        $this->apiResponse['success'] = true;

        
    }

    public function getModifiedInscriptions(){

        $list = $this->AthleteInscriptions->find()
        ->where([
            'modificato' => 1
        ])->toArray();

        $this->apiResponse['data'] = $list;
        $this->apiResponse['success'] = true;
    }

    // =========================================================================================================================================
    // @ ADMIN
    // ===========================================================================================================================================


    //-------------------------------------------- Elenco atleti in competizione -----------------------------------------------------------------
    public function getAthleteList(){
        $request = $this->request->getData();

        $athleteList = $this->AthleteInscriptions->find()
        ->where(['competition_id' => $request['competition_id']])
        ->contain(['Athletes' => ['Clubs'], 'Categorycodes'])
        ->order(['Clubs.id', 'Athletes.id'])
        ->toArray();

        $this->apiResponse['data'] =  $athleteList;
	    $this->apiResponse['success'] = true;
    }

    //-------------------------------------------- Elimina iscrizione -----------------------------------------------------------------

    //TODO: controllare se va gestitita la divisione maschile/Femminile
    public function deleteInscription()
    {
 
        $request = $this->request->getData();
        
        $athleteInscription = $this->AthleteInscriptions->get($request['id']);
        $athleteInscription['deleted'] = 1;
    
        if ($this->AthleteInscriptions->save($athleteInscription)) {

            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }

        
    }

    //-------------------------------------------- Modifica iscrizione -----------------------------------------------------------------

    public function updateCategory(){
        $data = $this->request->getData();
        $athleteInscription = $this->AthleteInscriptions->get($data['id']);
        $athleteInscription['old_category'] = $athleteInscription['categorycode_id'];
        $athleteInscription['categorycode_id'] = $data['categorycode_id'];
        $athleteInscription['accorpamento'] = $data['accorpamento'] ? 1 : 0;
        $athleteInscription['modificato'] = 1;

        if ($this->AthleteInscriptions->save($athleteInscription)) {

            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }

    //-------------------------------------------- Dividi categoria -----------------------------------------------------------------
    public function splitCategory(){
        
        $this->loadModel('Categorycodes');
        $data = $this->request->getData();


        $splittedCategories = $this->Categorycodes->find()
            ->where([
                'OR' => [['id' => $data['id'].'-M'], ['id' => $data['id'].'-F']]
            ])->toArray();

        
        if(sizeof($splittedCategories) >0){

            foreach ($splittedCategories as $categoryItem) {
                if($categoryItem->sesso == 'M'){
                    $categoryMale = $categoryItem;
                }else if($categoryItem->sesso == 'F'){
                    $categoryFemale = $categoryItem;
                }
            }

        }else{
            $categoryMale = $this->Categorycodes->newEmptyEntity();
            $categoryMale = $this->Categorycodes->patchEntity($categoryMale, $data);
    
            $categoryFemale = $this->Categorycodes->newEmptyEntity();
            $categoryFemale = $this->Categorycodes->patchEntity($categoryFemale, $data);
    
            $categoryMale->id = $data['id'].'-M';
            $categoryMale->sesso = 'M';
            $categoryMale = $this->Categorycodes->save($categoryMale);
            
            $categoryFemale->id = $data['id'].'-F';
            $categoryFemale->sesso = 'F';
            $categoryFemale = $this->Categorycodes->save($categoryFemale);
        }

        $athleteList = $this->AthleteInscriptions->find()
            ->where([
                'categorycode_id' => $data['id']
            ])
            ->contain(['Athletes'])->toArray();


        foreach ($athleteList as $athlete) {
            if($athlete->athlete->sesso == 'F'){
                $athlete->old_category = $athlete->categorycode_id;
                $athlete->categorycode_id = $categoryFemale->id;
            }else if($athlete->athlete->sesso == 'M'){
                $athlete->old_category = $athlete->categorycode_id;
                $athlete->categorycode_id = $categoryMale->id;             
            }

            print_r($athlete);
            $this->AthleteInscriptions->save($athlete);
        }
        

    }
}

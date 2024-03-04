<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;
use Cake\Core\Configure;

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
            'competition_id' => $request['competition_id'],
            'deleted' => 0
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

    public function getModifiedInscriptions(){

        $list = $this->AthletesInscriptions->find()
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

        $athleteList = $this->AthletesInscriptions->find()
        ->where(['competition_id' => $request['competition_id']])
        ->contain(['Athletes' => ['Clubs'], 'CategoryCodes'])
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
        
        $athleteInscription = $this->AthletesInscriptions->get($request['id']);
        $athleteInscription['deleted'] = 1;
    
        if ($this->AthletesInscriptions->save($athleteInscription)) {

            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }

        
    }

    //-------------------------------------------- Modifica iscrizione -----------------------------------------------------------------

    public function updateCategory(){
        $data = $this->request->getData();
        $athleteInscription = $this->AthletesInscriptions->get($data['id']);
        $athleteInscription['old_category'] = $athleteInscription['category_code_id'];
        $athleteInscription['category_code_id'] = $data['categorycode_id'];
        $athleteInscription['accorpamento'] = $data['accorpamento'] ? 1 : 0;
        $athleteInscription['modificato'] = 1;

        if ($this->AthletesInscriptions->save($athleteInscription)) {

            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }

    //-------------------------------------------- Dividi categoria -----------------------------------------------------------------
    public function splitCategory(){
        
        $this->loadModel('CategoryCodes');
        $data = $this->request->getData();


        $splittedCategories = $this->CategoryCodes->find()
            ->where([
                'OR' => [['codice' => $data['codice'].'-M'], ['codice' => $data['codice'].'-F']]
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
            $categoryMale = $this->CategoryCodes->newEmptyEntity();
            $categoryMale = $this->CategoryCodes->patchEntity($categoryMale, $data);
    
            $categoryFemale = $this->CategoryCodes->newEmptyEntity();
            $categoryFemale = $this->CategoryCodes->patchEntity($categoryFemale, $data);
    
            $categoryMale->codice = $data['codice'].'-M';
            $categoryMale->sesso = 'M';
            $categoryMale = $this->CategoryCodes->save($categoryMale);
            
            $categoryFemale->codice = $data['codice'].'-F';
            $categoryFemale->sesso = 'F';
            $categoryFemale = $this->CategoryCodes->save($categoryFemale);
        }

        $athleteList = $this->AthletesInscriptions->find()
            ->where([
                'category_code_id' => $data['id']
            ])
            ->contain(['Athletes'])->toArray();


        foreach ($athleteList as $athlete) {
            if($athlete->athlete->sesso == 'F'){
                $athlete->old_category = $athlete->category_code_id;
                $athlete->category_code_id = $categoryFemale->id;
            }else if($athlete->athlete->sesso == 'M'){
                $athlete->old_category = $athlete->category_code_id;
                $athlete->category_code_id = $categoryMale->id;             
            }

            print_r($athlete);
            $this->AthletesInscriptions->save($athlete);
        }
        

    }
}

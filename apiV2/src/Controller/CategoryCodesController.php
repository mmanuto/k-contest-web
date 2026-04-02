<?php
declare(strict_types=1);

namespace App\Controller;
use RestApi\Controller\ApiController;
use Cake\Datasource\ConnectionManager;

/**
 * Categorycodes Controller
 *
 * @property \App\Model\Table\CategorycodesTable $Categorycodes
 * @method \App\Model\Entity\Categorycode[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CategorycodesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $categorycodes = $this->paginate($this->Categorycodes);

        $this->set(compact('categorycodes'));
    }

    /**
     * View method
     *
     * @param string|null $id Category Code id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $categorycode = $this->Categorycodes->get($id, [
            'contain' => ['AthleteInscriptions'],
        ]);

        $this->set(compact('categorycode'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $categorycode = $this->Categorycodes->newEmptyEntity();
        if ($this->request->is('post')) {
            $categorycode = $this->Categorycodes->patchEntity($categorycode, $this->request->getData());
            if ($this->Categorycodes->save($categorycode)) {
                $this->Flash->success(__('The category code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The category code could not be saved. Please, try again.'));
        }
        $this->set(compact('categorycode'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Category Code id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $categorycode = $this->Categorycodes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $categorycode = $this->Categorycodes->patchEntity($categorycode, $this->request->getData());
            if ($this->Categorycodes->save($categorycode)) {
                $this->Flash->success(__('The category code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The category code could not be saved. Please, try again.'));
        }
        $this->set(compact('categorycode'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Category Code id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $categorycode = $this->Categorycodes->get($id);
        if ($this->Categorycodes->delete($categorycode)) {
            $this->Flash->success(__('The category code has been deleted.'));
        } else {
            $this->Flash->error(__('The category code could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    /**
     * Get all categories
     */

     public function getAllCategories(){
        $categoryList = $this->Categorycodes->find()->toArray();

        $this->apiResponse['data'] = $categoryList;
	    $this->apiResponse['success'] = true;

    }

    /**
     * Get all categories with numbers of registrated athletes (Stampa frontespizi)
     */

    public function getCategories(){
        $this->loadModel('AthleteInscriptions');

        $request = $this->request->getData();
        
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT categorycodes.*, COUNT(athlete_inscriptions.id) AS n_athletes 
                                FROM athlete_inscriptions 
                                JOIN categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id 
                                WHERE categorycodes.codiceTipoCategorie = 3 AND athlete_inscriptions.deleted = 0 
                                AND athlete_inscriptions.competition_id = '".$request['competition_id']."' GROUP BY categorycodes.id");

        $categories = $stmt->fetchAll('assoc');

        $this->apiResponse['data'] = $categories;
	    $this->apiResponse['success'] = true;
    }

    /**
     * Get all categories with athleteList (Stampa sintetico categoria)
     */
    public function getCategoriesWithAthletes(){
        $this->loadModel('AthleteInscriptions');
        
        $request = $this->request->getData();
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT categorycodes.*, COUNT(athlete_inscriptions.id) AS n_athletes FROM athlete_inscriptions 
                                JOIN categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id 
                                WHERE categorycodes.codiceTipoCategorie = 3 AND athlete_inscriptions.deleted = 0 
                                AND athlete_inscriptions.competition_id = '".$request['competition_id']."'
                                GROUP BY categorycodes.id");

        $categories = $stmt->fetchAll('assoc');

        $newCategories = [];

        foreach ($categories as $category) {
       
            $athleteList = $this->AthleteInscriptions->find()
            ->where([
                'categorycode_id' => $category['id'],
		        'competition_id' => $request['competition_id'],
                'deleted' => 0
            ])
            ->contain(['Athletes' => ['Clubs'], 'Categorycodes'])
            ->order(['Clubs.id', 'Athletes.id'])
            ->toArray();

            $category['athletes'] = $athleteList;
            
            array_push($newCategories, $category);
        }

        $this->apiResponse['data'] = $newCategories;
	    $this->apiResponse['success'] = true;
    }

    public function getCategoriesBySet(){
        $request = $this->request->getData();

        $categoryList = $this->Categorycodes->find()
        ->where(['codiceTipoCategorie' => $request['selectedSetCode']])->toArray();

        $this->apiResponse['data'] = $categoryList;
	    $this->apiResponse['success'] = true;
    }

    public function updateCategories(){

        $this->Categorycodes->deleteAll(['id IN' => ['PAL22', 'PER22']]);

        $categoryList = $this->Categorycodes->find()
        ->where(['codiceTipoCategorie' => 3])->toArray();

        if($categoryList){
            foreach ($categoryList as $item) {
                $item->anno_min++;
                $item->anno_max++;
                $this->Categorycodes->save($item);
            }
        }
    }
}
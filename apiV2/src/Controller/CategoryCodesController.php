<?php
declare(strict_types=1);

namespace App\Controller;
use RestApi\Controller\ApiController;
use Cake\Datasource\ConnectionManager;

/**
 * CategoryCodes Controller
 *
 * @property \App\Model\Table\CategoryCodesTable $CategoryCodes
 * @method \App\Model\Entity\CategoryCode[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CategoryCodesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $categoryCodes = $this->paginate($this->CategoryCodes);

        $this->set(compact('categoryCodes'));
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
        $categoryCode = $this->CategoryCodes->get($id, [
            'contain' => ['AthletesInscriptions'],
        ]);

        $this->set(compact('categoryCode'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $categoryCode = $this->CategoryCodes->newEmptyEntity();
        if ($this->request->is('post')) {
            $categoryCode = $this->CategoryCodes->patchEntity($categoryCode, $this->request->getData());
            if ($this->CategoryCodes->save($categoryCode)) {
                $this->Flash->success(__('The category code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The category code could not be saved. Please, try again.'));
        }
        $this->set(compact('categoryCode'));
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
        $categoryCode = $this->CategoryCodes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $categoryCode = $this->CategoryCodes->patchEntity($categoryCode, $this->request->getData());
            if ($this->CategoryCodes->save($categoryCode)) {
                $this->Flash->success(__('The category code has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The category code could not be saved. Please, try again.'));
        }
        $this->set(compact('categoryCode'));
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
        $categoryCode = $this->CategoryCodes->get($id);
        if ($this->CategoryCodes->delete($categoryCode)) {
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
        $categoryList = $this->CategoryCodes->find()->toArray();

        $this->apiResponse['data'] = $categoryList;
	    $this->apiResponse['success'] = true;

    }

    /**
     * Get all categories with numbers of registrated athletes (Stampa frontespizi)
     */

    public function getCategories(){
        $this->loadModel('AthletesInscriptions');

        $request = $this->request->getData();
        
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT category_codes.*, COUNT(athletes_inscriptions.id) AS n_athletes 
                                FROM athletes_inscriptions 
                                JOIN category_codes ON category_codes.id = athletes_inscriptions.category_code_id 
                                WHERE category_codes.codiceTipoCategorie = 3 AND athletes_inscriptions.deleted = 0 
                                AND athletes_inscriptions.competition_id = ".$request['competition_id']." GROUP BY category_codes.id");

        $categories = $stmt->fetchAll('assoc');

        $this->apiResponse['data'] = $categories;
	    $this->apiResponse['success'] = true;
    }

    /**
     * Get all categories with athleteList (Stampa sintetico categoria)
     */
    public function getCategoriesWithAthletes(){
        $this->loadModel('AthletesInscriptions');
        
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT category_codes.*, COUNT(athletes_inscriptions.id) AS n_athletes FROM athletes_inscriptions 
                                JOIN category_codes ON category_codes.id = athletes_inscriptions.categorycode_id 
                                WHERE category_codes.codiceTipoCategorie = 3 AND athletes_inscriptions.deleted = 0
                                GROUP BY category_codes.id");

        $categories = $stmt->fetchAll('assoc');

        $newCategories = [];

        foreach ($categories as $category) {
       
            $athleteList = $this->AthletesInscriptions->find()
            ->where([
                'category_code_id' => $category['id'],
                'deleted' => 0
            ])
            ->contain(['Athletes' => ['Clubs'], 'CategoryCodes'])
            ->order(['Clubs.id', 'Athletes.id'])
            ->toArray();

            $category['athletes'] = $athleteList;
            
            array_push($newCategories, $category);
        }

        $this->apiResponse['data'] = $newCategories;
	    $this->apiResponse['success'] = true;
    }
}

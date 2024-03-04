<?php
declare(strict_types=1);

namespace App\Controller;

//use App\Controller\AppController;
use RestApi\Controller\ApiController;
use RestApi\Utility\JwtToken;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;

/**
 * Teams Controller
 *
 * @property \App\Model\Table\TeamsTable $Teams
 * @method \App\Model\Entity\Team[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TeamsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clubs', 'Competitions'],
        ];
        $teams = $this->paginate($this->Teams);

        $this->set(compact('teams'));
    }

    /**
     * View method
     *
     * @param string|null $id Team id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $team = $this->Teams->get($id, [
            'contain' => ['Clubs', 'Competitions'],
        ]);

        $this->set(compact('team'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $team = $this->Teams->newEmptyEntity();
        if ($this->request->is('post')) {
            $team = $this->Teams->patchEntity($team, $this->request->getData());
            if ($this->Teams->save($team)) {
                $this->Flash->success(__('The team has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The team could not be saved. Please, try again.'));
        }
        $clubs = $this->Teams->Clubs->find('list', ['limit' => 200])->all();
        $competitions = $this->Teams->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('team', 'clubs', 'competitions'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Team id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $team = $this->Teams->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $team = $this->Teams->patchEntity($team, $this->request->getData());
            if ($this->Teams->save($team)) {
                $this->Flash->success(__('The team has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The team could not be saved. Please, try again.'));
        }
        $clubs = $this->Teams->Clubs->find('list', ['limit' => 200])->all();
        $competitions = $this->Teams->Competitions->find('list', ['limit' => 200])->all();
        $this->set(compact('team', 'clubs', 'competitions'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Team id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $team = $this->Teams->get($id);
        if ($this->Teams->delete($team)) {
            $this->Flash->success(__('The team has been deleted.'));
        } else {
            $this->Flash->error(__('The team could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    
     // -----------------------------------------------------------------------------------------------------
    // @ RECUPERO ELENCO CATEGORIE E CINTURE
    // -----------------------------------------------------------------------------------------------------
    public function getCategorieCinture()
    {

        $data = $this->request->getData();
        $this->loadModel('CategoryCodes');

        $categorie = $this->CategoryCodes->find()->select(['categoria'])->distinct(['categoria'])->where(['specialita' => 'Kata Squadre', 'codiceTipoCategorie' => $data['code']])->toArray();
        $cinture = $this->CategoryCodes->find()->select(['grado'])->distinct(['grado'])->where(['specialita' => 'Kata Squadre', 'codiceTipoCategorie' => $data['code']])->toArray();


        /* $conn = ConnectionManager::get('default');

        $queryCategorie = "SELECT DISTINCT categoria FROM category_codes WHERE specialita = 'Kata Squadre' AND codiceTipoCategorie = ".$data['code'];
        $stmt = $conn->execute($queryCategorie);

        $categorie = $stmt ->fetchAll('assoc');

        $queryCinture = "SELECT DISTINCT grado FROM category_codes WHERE specialita = 'Kata Squadre' AND codiceTipoCategorie = ".$data['code'];
        $stmt = $conn->execute($queryCinture);
                                

        $cinture = $stmt ->fetchAll('assoc'); */
        $result = array('cinture' => $cinture, 'categorie' => $categorie);

        $this->apiResponse['success'] = true;
        $this->apiResponse['data'] =  $result;
    }

    // -----------------------------------------------------------------------------------------------------
    // @ ELENCO SQUADRE
    // -----------------------------------------------------------------------------------------------------

    public function getListaSquadre()
    {

        $data = $this->request->getData();
        $this->loadModel('Teams');

        $squadre = $this->Teams->find()
            ->where([
                'competition_id' => $data['competition_id'],
                'club_id' => $data['club_id']

            ])
            ->toArray();

        $this->apiResponse['data'] = $squadre;
        $this->apiResponse['success'] = true;
    }

    // -----------------------------------------------------------------------------------------------------
    // @ SAVE
    // -----------------------------------------------------------------------------------------------------
    public function saveSquadra()
    {
        $data = $this->request->getData();
        $this->loadModel('Teams');

        $data['componenti'] = json_encode($data['componenti']);

        if (isset($data['id']) && $data['id'] != "") {
            $squadra = $this->Teams->get($data['id'], [
                'contain' => []
            ]);
        } else {
            $squadra = $this->Teams->newEmptyEntity();
        }



        $squadra = $this->Teams->patchEntity($squadra, $data);
        if ($this->Teams->save($squadra)) {
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }


    // -----------------------------------------------------------------------------------------------------
    // @ DELETE
    // -----------------------------------------------------------------------------------------------------

    public function deleteSquadra()
    {


        $data = $this->request->getData();
        $this->loadModel('Teams');

        $squadra = $this->Teams->get($data['id']);


        if ($this->Teams->delete($squadra)) {
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }
}
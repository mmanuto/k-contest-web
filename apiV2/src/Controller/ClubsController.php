<?php
declare(strict_types=1);

namespace App\Controller;


//use App\Controller\AppController;
use RestApi\Controller\ApiController;
use Cake\Mailer\Mailer;
use Cake\I18n\FrozenTime;

/**
 * Clubs Controller
 *
 * @property \App\Model\Table\ClubsTable $Clubs
 * @method \App\Model\Entity\Club[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ClubsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $clubs = $this->paginate($this->Clubs);

        $this->set(compact('clubs'));
    }

    /**
     * View method
     *
     * @param string|null $id Club id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $club = $this->Clubs->get($id, [
            'contain' => ['Athletes', 'ClubInscriptions', 'Teams'],
        ]);

        $this->set(compact('club'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $club = $this->Clubs->newEmptyEntity();
        if ($this->request->is('post')) {
            $club = $this->Clubs->patchEntity($club, $this->request->getData());
            if ($this->Clubs->save($club)) {
                $this->Flash->success(__('The club has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The club could not be saved. Please, try again.'));
        }
        $this->set(compact('club'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Club id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $club = $this->Clubs->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $club = $this->Clubs->patchEntity($club, $this->request->getData());
            if ($this->Clubs->save($club)) {
                $this->Flash->success(__('The club has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The club could not be saved. Please, try again.'));
        }
        $this->set(compact('club'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Club id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $club = $this->Clubs->get($id);
        if ($this->Clubs->delete($club)) {
            $this->Flash->success(__('The club has been deleted.'));
        } else {
            $this->Flash->error(__('The club could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }




    // -----------------------------------------------------------------------------------------------------
    // @ CONTROLLO CF PER ACCESSO RAPIDO
    // -----------------------------------------------------------------------------------------------------


    public function checkFiscalCode()
    {

        $data = $this->request->getData();

        $clubDetails = $this->Clubs->find()
            ->where([
                'fiscal_code' => $data['codice_fiscale']
            ])->first();

        if ($clubDetails) {
            $this->apiResponse['success'] = true;
            $this->apiResponse['valore'] = $clubDetails;
        } else {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = 'DATI NON TROVATI';
        }
    }

    // -----------------------------------------------------------------------------------------------------
    // @ SALVATAGGIO ISCRIZIONE SOCIETA'
    // -----------------------------------------------------------------------------------------------------

    public function saveClub()
    {
        $this->loadModel('Logs');
        $this->loadModel('Competitions');

        $request = $this->request->getData();
        $dataClub = $request['club'];

        // -------------------- CONTROLLO SE CLUB GIA PRESENTE -----------------

        $club = $this->Clubs->find()
            ->where([
                'fiscal_code' => $dataClub['fiscal_code']
            ])
            ->first();


        $competition = $this->Competitions->get($request['competitionId']);

        if ($club) {
            //---------------- SE C'E' CONTROLLO E SALVO ISCRIZIONE A COMPETIZIONE ------------
            
            if ($this->salvaIscrizione($club, $competition)) {

                $club->last_login = FrozenTime::now('Europe/Rome');
                $this->Clubs->save($club);

                $this->apiResponse['data'] = ['competition' => $competition, 'club' => $club];
                $this->apiResponse['message'] = 'Società già presente';
                $this->apiResponse['success'] = true;
            }
        } else {
            //---------------- ALTRIMENTI SALVO ANAGRAFICA SOCIETA' E ISCRIVO A COMPETIZIONE ------------
            $club = $this->Clubs->newEmptyEntity();
            $club = $this->Clubs->patchEntity($club, $dataClub);
            $club->last_login = FrozenTime::now('Europe/Rome');
            $newClub = $this->Clubs->save($club);

            if ($newClub) {

                if ($this->salvaIscrizione($newClub, $competition)) {

                    //invio mail di riepilogo
                    $testoMail = "Mail automatica di primo accesso al sistema di iscrizione. \r Di seguito i dati inseriti in fase di registrazione, per gli accessi successivi sar� sufficente inserire il codice fiscale della sciet� e cliccare sul pulsante recupera dati.\r Societa: " . $newClub['club_name'] . "\r Codice Fiscale: " . $newClub['fiscal_code'] . "\r Responsabile: " . $newClub['club_manager'] . "\r Telefono: " . $newClub['telefono'] . "\r Email: " . $newClub['mail'] . "\r coach: " . $newClub['coach'];

                    $Email = new Mailer();
                    $Email->setFrom(['info@iscrizionicsenveneto.it' => 'Iscrizioni CSEN Veneto'])
                        ->setTo($dataClub['mail'])
                        ->setBcc('info@iscrizionicsenveneto.it')
                        ->setSubject('Primo accesso sistema iscrizioni')
                        ->deliver($testoMail);


                    $this->apiResponse['data'] = ['competition' => $competition, 'club' => $newClub];
                    $this->apiResponse['message'] = 'Nuova società';
                    $this->apiResponse['success'] = true;
                }
            } else {
                $this->apiResponse['success'] = false;
            }
        }
    }

    //---------------- ISCRIZIONE A COMPETIZIONE ---------------------
    private function salvaIscrizione($club, $competition)
    {
        $this->loadModel('ClubInscriptions');

        //verifica di esistenza di precedente iscrizione
        $iscrizione = $this->ClubInscriptions->find()
            ->where([
                'club_id' => $club->id,
                'competition_id' => $competition->id
            ])->first();

        //se non esiste eseguire salvataggio
        if (!$iscrizione) {

            $inscription = $this->ClubInscriptions->newEmptyEntity();
            $inscription->club_id = $club->id;
            $inscription->competition_id = $competition->id;

            $saveInscription = $this->ClubInscriptions->save($inscription);

            if ($saveInscription) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }


    // -----------------------------------------------------------------------------------------------------
    // @ MODIFICA DATI SOCIETA'
    // -----------------------------------------------------------------------------------------------------

    public function updateData()
    {

        $request = $this->request->getData();
        $club = $this->Clubs->get($request['club']['id']);
        $club = $this->Clubs->patchEntity($club, $request['club']);

        $club->last_login = $club->last_login->toDateTimeString();
        $club->modified_date = $club->modified_date->toDateTimeString();
        $club->created_date = $club->created_date->toDateTimeString();

       $newClub = $this->Clubs->save($club);
        if ($newClub) {
            $this->apiResponse['data'] = $newClub;
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }

    public function provaRecupero()
    {
        //il secondo parametro di get serve per visualizzare bene l'oggetto
        /* $club = $this->Clubs->get($id, [
            'contain' => []
        ]); */

        //metodo per cercare elementi senza usare id
        $club = $this->Clubs->find()
            ->where([
                'nome_societ�' => 'pinco pallino',
                'id' => 4
            ])->first(); //first � necessario per estrarre il primo oggetto, anche se l'unico

        //cerchiamo di estrarre info dall'oggetto
        print_r($club->id);
        print_r($club->coach);
    }

    public function provaJoin()
    {
        //carico la tabella esterna, per poi utilizzarla
        $this->LoadModel('ClubInscriptions');
        $iscritto = $this->ClubInscriptions->find()
            ->where([
                'id' => 1
            ])->first();
        print_r($iscritto);

        //il .* per dire che voglio tutti i campi di quella tabella
        $query = $this->Clubs->find('All')
            ->select(['clubs.*', 'ClubInscriptions.*'])
            ->join([
                'Clubs' => [
                    'table' => 'clubs',
                    'type' => 'LEFT',
                    'conditions' => 'ClubInscriptions.clubs_id=clubs.id'
                ]
            ])
            ->order(['clubs.id' => 'ASC']);
        print_r($query);
    }


    // -----------------------------------------------------------------------------------------------------
    // @ ADMIN
    // -----------------------------------------------------------------------------------------------------

    //================= ELENCO TUTTE LE SOCIETA' ====================================================
    public function getClubs(){

        $clubList = $this->Clubs->find()->toArray();
        $this->apiResponse['success'] = true;
        $this->apiResponse['data'] = $clubList;

    }
}

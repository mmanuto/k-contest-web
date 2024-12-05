<?php
declare(strict_types=1);

namespace App\Controller;

//use App\Controller\AppController;
use RestApi\Controller\ApiController;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;

/**
 * Athletes Controller
 *
 * @property \App\Model\Table\AthletesTable $Athletes
 * @method \App\Model\Entity\Athlete[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AthletesController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Clubs', 'Federations'],
        ];
        $athletes = $this->paginate($this->Athletes);

        $this->set(compact('athletes'));
    }

    /**
     * View method
     *
     * @param string|null $id Athlete id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $athlete = $this->Athletes->get($id, [
            'contain' => ['Clubs', 'Federations', 'AthleteInscriptions'],
        ]);

        $this->set(compact('athlete'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $athlete = $this->Athletes->newEmptyEntity();
        if ($this->request->is('post')) {
            $athlete = $this->Athletes->patchEntity($athlete, $this->request->getData());
            if ($this->Athletes->save($athlete)) {
                $this->Flash->success(__('The athlete has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The athlete could not be saved. Please, try again.'));
        }
        $clubs = $this->Athletes->Clubs->find('list', ['limit' => 200])->all();
        $federations = $this->Athletes->Federations->find('list', ['limit' => 200])->all();
        $this->set(compact('athlete', 'clubs', 'federations'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Athlete id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $athlete = $this->Athletes->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $athlete = $this->Athletes->patchEntity($athlete, $this->request->getData());
            if ($this->Athletes->save($athlete)) {
                $this->Flash->success(__('The athlete has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The athlete could not be saved. Please, try again.'));
        }
        $clubs = $this->Athletes->Clubs->find('list', ['limit' => 200])->all();
        $federations = $this->Athletes->Federations->find('list', ['limit' => 200])->all();
        $this->set(compact('athlete', 'clubs', 'federations'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Athlete id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $athlete = $this->Athletes->get($id);
        if ($this->Athletes->delete($athlete)) {
            $this->Flash->success(__('The athlete has been deleted.'));
        } else {
            $this->Flash->error(__('The athlete could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    // -----------------------------------------------------------------------------------------------------
    // @ ELENCO ATLETI APPARTENENTI ALLA SOCIETA'
    // -----------------------------------------------------------------------------------------------------

    //================= TUTTI GLI ATLETI DELLA SOCIETA' ====================================================
    public function getAthleteList()
    {
        $data = $this->request->getData();

        $athleteList = $this->Athletes->find('all')->where(['club_id' => $data['club_id']])->toArray();
        if ($athleteList) {
            $this->apiResponse['success'] = true;
            $this->apiResponse['data'] = $athleteList;
        } else {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = 'Non risultano atleti associati a qusta societa';
        }
    }


    // =================================== ATLETI ISCRITTI ALLA COMPETIZIONE =================================

    public function getIscritti()
    {
        $data = $this->request->getData();
        $this->loadModel('AthleteInscriptions');
        $this->loadModel('categorycodes');

        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT * FROM athlete_inscriptions 
                                JOIN athletes ON athletes.id = athlete_inscriptions.athlete_id 
                                JOIN categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id 
                                 WHERE athlete_inscriptions.competition_id = '" . $data['competition_id'] . "' AND athletes.club_id = '" . $data['club_id']."'");

        $listaIscritti = $stmt->fetchAll('assoc');

        if ($listaIscritti) {
            $this->apiResponse['success'] = true;
            $this->apiResponse['data'] = $listaIscritti;
        } else {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = 'Nessun iscritto';
        }
    }


    // -----------------------------------------------------------------------------------------------------
    // @ CATEGORIA E PROVE
    // -----------------------------------------------------------------------------------------------------

    // =========================================== ELENCO PROVE PER ISCRIZIONE ===============================================
    public function getElencoProve()
    {
        $this->loadModel('Categorycodes');
        $this->loadModel('Belts');
        $data = $this->request->getData();
        $conn = ConnectionManager::get('default');
        $resultProve = [];

        if($data['anno'] >= 2013){
            $stmt = $conn->execute("SELECT distinct specialita FROM categorycodes WHERE codiceTipoCategorie =" . $data['categorycode'] . " AND grado LIKE '%" . $data['grado'] . "%' AND " . $data['anno'] . " BETWEEN anno_min AND anno_max");

            $result = $stmt->fetchAll('assoc');
            foreach ($result as $prova) {
                $resultProve[] = ['name' => $prova['specialita'], 'code' => ''];
            }
        }else{
           
            $stmt = $conn->execute("SELECT distinct specialita FROM categorycodes WHERE codiceTipoCategorie =" . $data['categorycode'] . " AND grado LIKE '%" . $data['grado'] . "%' AND " . $data['anno'] . " BETWEEN anno_min AND anno_max AND specialita <> 'Kata'");
            $result = $stmt->fetchAll('assoc');
            $beltList = $this->Belts->find()->toArray();

            foreach ($result as $prova) {
                $resultProve[] = ['name' => $prova['specialita'], 'code' => ''];
            }

            $kataCategory = $this->Categorycodes->find()
                ->where([
                    'codiceTipoCategorie' => $data['categorycode'],
                    'sesso LIKE ' => '%'.$data['sesso'].'%',
                    'grado LIKE ' => '%'.$data['grado'].'%',
                    'anno_min <=' => $data['anno'],
                    'anno_max >=' => $data['anno'],
                    'specialita' => 'Kata'
                ])->first();

            $resultProve[] = [
                'name' =>  $kataCategory->specialita.' - '.$kataCategory->categoria.' - '.$kataCategory->sesso.' - '.$kataCategory->grado, 
                'code' => $kataCategory->id];

                
            $kataCategoryList = $this->Categorycodes->find()
                ->where([
                    'codiceTipoCategorie' => $data['categorycode'],
                    'sesso LIKE ' => '%'.$data['sesso'].'%',
                    'specialita' => 'Kata',
                    'categoria NOT LIKE' => 'MASTER'.'%',
                    'id >' => $kataCategory->id
                ])->toArray();
            
            
            $minimalBeltGroup = 0;
            foreach ($beltList as $belt) {
               if($kataCategory->grado == $belt->category){
                $minimalBeltGroup = $belt->group_increment;
                break;
               }
            }
             
            
            foreach ($kataCategoryList as $item) {
                $beltGroup = 0;
                foreach ($beltList as $belt) {
                    if($item->grado == $belt->category){
                        $beltGroup = $belt->group_increment;
                        break;
                    }
                 }

                 if($beltGroup >= $minimalBeltGroup){
                    $name = $item->specialita.' - '.$item->categoria.' - '.$item->sesso.' - '.$item->grado;
                    $resultProve[] = ['name' => $name, 'code' => $item->id];
                 }
            }

                        
        }

        
        $this->apiResponse['data'] = $resultProve;
        $this->apiResponse['success'] = true;
    }


    // -----------------------------------------------------------------------------------------------------
    // @ SALVATAGGIO ATLETA/ISCRIZIONE
    // -----------------------------------------------------------------------------------------------------

    public function saveAtleta()
    {

        $this->loadModel('AthleteInscriptions');
        $request = $this->request->getData();
        $conn = ConnectionManager::get('default');

        $athltete = $request['athlete'];
        $prove = $request['prove'];

        //=============================== CONTROLLO E AGGIORNO ANAGRAFICA ATLETA ======================================
        if (isset($athltete['id'])) {

            $newAtlhete = $this->Athletes->get($athltete['id']);
        } else {
            $newAtlhete = $this->Athletes->find()
                ->where([
                    'cod_fiscale' => $athltete['cod_fiscale']
                ])->first();
            if (!$newAtlhete) {
                $newAtlhete = $this->Athletes->newEmptyEntity();
            }
        }
        $newAtlhete = $this->Athletes->patchEntity($newAtlhete, $athltete);
        $savedAtlhete = $this->Athletes->save($newAtlhete);


        if ($savedAtlhete) {

            $dataNascita = explode("/", $athltete['data_nascita']);

            $query = "SELECT * FROM categorycodes WHERE codiceTipoCategorie =" . $prove['categorycode'] . " AND grado LIKE '%" . $athltete['grado'] . "%' 
                        AND sesso LIKE '%" . $athltete['sesso'] . "%' AND " . $dataNascita[2] . " BETWEEN anno_min AND anno_max";
            if ($athltete['peso']) {
                $query .= "  AND " . $athltete['peso'] . " BETWEEN peso_min AND peso_max-1";
            } else {
                $athltete['peso'] = 0;
            }
            $stmt = $conn->execute($query);


            $result = $stmt->fetchAll('assoc');

            //===================================== ELIMINO EVENTUALI PROVE GIA' INSERITE ========================================
            $this->AthleteInscriptions
                ->deleteAll([
                    'athlete_id' => $savedAtlhete->id,
                    'competition_id' => $prove['competition_id']
                ]);

            //======================================= SALVO PROVE SELEZIONATE =============================================
            foreach ($prove['specialita'] as $prova) {
                foreach ($result as $record) {
                    if ($record['specialita'] == $prova) {
                        $inscription = $this->AthleteInscriptions->newEmptyEntity();
                        $inscription->athlete_id = $savedAtlhete->id;
                        $inscription->categorycode_id = $record['id'];
                        $inscription->competition_id = $prove['competition_id'];
                        $this->AthleteInscriptions->save($inscription);
                    }
                }
            }

            foreach ($prove['codes'] as $code) {
                $inscription = $this->AthleteInscriptions->newEmptyEntity();
                $inscription->athlete_id = $savedAtlhete->id;
                $inscription->categorycode_id = $code;
                $inscription->competition_id = $prove['competition_id'];
                $this->AthleteInscriptions->save($inscription);
            }

            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }

    // -----------------------------------------------------------------------------------------------------
    // @ ELIMINA ISCRIZIONE
    // -----------------------------------------------------------------------------------------------------
    public function deleteInscription()
    {

        $data = $this->request->getData();
        $this->loadModel('AthleteInscriptions');

        if (
            $this->AthleteInscriptions
                ->deleteAll([
                    'athlete_id' => $data['athlete_id'],
                    'competition_id' => $data['competition_id']
                ])
        ) {
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }
    }

    // -----------------------------------------------------------------------------------------------------
    // @ CONFERMA ISCRIZIONE
    // -----------------------------------------------------------------------------------------------------


   public function inviaDati()
    {

        $this->loadModel('Competitions');
        $this->loadModel('Users');

        $data = $this->request->getData();

        //---------------------------- DATI SOCIETA -----------------------------
        $datiSocieta = $data['datiSocieta'];
        $competition = $data['competition'];

        //----------------------------  PDF  -------------------------------------
        $dataPdf = base64_decode($data['pdf']);
        $autodichiarazionePdf = base64_decode($data['autodichiarazione']);

        $filename = $competition['nome_gara'] . "/iscrizioni_" . $datiSocieta['club_name'] . ".pdf";
        $filenameCertificati = $competition['nome_gara'] . "/autodichiarazione_" . $datiSocieta['club_name'] . ".pdf";

        file_put_contents($filename, $dataPdf);
        file_put_contents($filenameCertificati, $autodichiarazionePdf);


        //-----------------------------  EXCEL  -------------------------------------
        $list = $data['datiCsv'];
        $filenameCsv = 'iscrizioni_' . $datiSocieta['club_name'] . '.csv';
        $fp = fopen($filenameCsv, 'w');
        foreach ($list as $fields) {

            fputcsv($fp, $fields, ";");
        }
        fclose($fp);

        if (!file_exists($competition['nome_gara'])) {
            mkdir($competition['nome_gara'], 0777, true);
        }



        //-------------------------------------- INVIO MAIL EXCEL  --------------------------------------
        $user = $this->Users->get(1);

        $destinatariExcel = array($user['email']);

        $testoMail = "Nuova iscrizione effettuata da " . $datiSocieta['club_name'];
        $Email = new Mailer();
        $Email->setFrom(['info@iscrizionicsenveneto.it' => 'Iscrizioni CSEN Veneto'])
            ->setBcc($destinatariExcel)
            ->setSubject('Nuova iscrizione ' . $datiSocieta['club_name'] . ' - ' . $competition['nome_gara'])
            ->setAttachments([$filenameCsv])
            ->deliver($testoMail);



        //------------------------------------ INVIO MAIL PDF RIEPILOGO -------------------------------------
        $destinatariPdf = array($competition['email'], $user['email'], $datiSocieta['mail']);

        $testoSocieta = "Buongiorno, in allegato il riepilogo delle iscrizioni per la manifestazione " . $competition['nome_gara'] . "\r\r Societa: " . $datiSocieta['club_name'] . "\r Codice Fiscale: " . $datiSocieta['CF'] . "\r Responsabile: " . $datiSocieta['club_manager'] . "\r Telefono: " . $datiSocieta['telefono'] . "\r Email: " . $datiSocieta['mail'] . "\r coach: " . $datiSocieta['coach'] . "\r\r Inviamo anche il modulo di autocertificazione per l'affiliazione e i certificati medici che deve essere firmato e consegnato in sede di gara, o inviato via email al responsabile di gara.";

        if ($data['note'] != '') {
            $testoSocieta .= "\r \r NOTE: \r " . $data['note'];
        }

        $EmailRiepilogo = new Mailer();
        $EmailRiepilogo->setFrom(['info@iscrizionicsenveneto.it' => 'Iscrizioni CSEN Veneto'])
            ->setTo('info@iscrizionicsenveneto.it')
            ->setBcc($destinatariPdf)
            ->setSubject('Riepilogo iscrizione ' . $datiSocieta['club_name'] . ' - ' . $competition['nome_gara'])
            ->setAttachments([$filename, $filenameCertificati])
            ->deliver($testoSocieta);


        $this->apiResponse['success'] = true;
    }


    // -----------------------------------------------------------------------------------------------------
    // @ ADMIN
    // -----------------------------------------------------------------------------------------------------

    //================= TUTTI GLI ATLETI A SISTEMA ====================================================
    public function getTotalAthleteList()
    {

        $athleteList = $this->Athletes->find('all')->contain(['Clubs'])->toArray();
        if ($athleteList) {
            $this->apiResponse['success'] = true;
            $this->apiResponse['data'] = $athleteList;
        } else {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = 'Non risultano atleti associati a qusta societa';
        }
    }

}
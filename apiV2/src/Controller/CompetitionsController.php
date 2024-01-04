<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;
use RestApi\Utility\JwtToken;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Cake\Core\Configure;
use ZipArchive;
use Cake\I18n\FrozenTime;

Type::build('date')->setLocaleFormat('yyyy-MM-dd');


/**
 * Competitions Controller
 *
 * @property \App\Model\Table\CompetitionsTable $Competitions
 * @method \App\Model\Entity\Competition[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompetitionsController extends ApiController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $competitions = $this->paginate($this->Competitions);

        $this->set(compact('competitions'));
    }

    /**
     * View method
     *
     * @param string|null $id Competition id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $competition = $this->Competitions->get($id, [
            'contain' => ['AthletesInscriptions', 'ClubInscriptions', 'Teams'],
        ]);

        $this->set(compact('competition'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $competition = $this->Competitions->newEmptyEntity();
        if ($this->request->is('post')) {
            $competition = $this->Competitions->patchEntity($competition, $this->request->getData());
            if ($this->Competitions->save($competition)) {
                $this->Flash->success(__('The competition has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The competition could not be saved. Please, try again.'));
        }
        $this->set(compact('competition'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Competition id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $competition = $this->Competitions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $competition = $this->Competitions->patchEntity($competition, $this->request->getData());
            if ($this->Competitions->save($competition)) {
                $this->Flash->success(__('The competition has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The competition could not be saved. Please, try again.'));
        }
        $this->set(compact('competition'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Competition id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $competition = $this->Competitions->get($id);
        if ($this->Competitions->delete($competition)) {
            $this->Flash->success(__('The competition has been deleted.'));
        } else {
            $this->Flash->error(__('The competition could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    //============================================================================================================================================================                                                                                                                                                              
//   REQUEST                                                                                                                                                                                                                                                                                                                     
//========================================================================================================================================================================================================================================================================================================================

    public function saveRequest()
    {
        $data = $this->request->getData();
        $competition = $this->Competitions->newEntity();


        $richieste = array(
            'referee' => $data['referee'],
            'competitionManagement' => $data['competitionManagement'],
            'tabsManagement' => $data['tabsManagement']
        );

        $record = array(
            'responsabile' => $data['name'],
            'club_name' => $data['companyName'],
            'logo' => null,
            'email' => $data['email'],
            'telephone' => $data['telefono'],
            'nome_gara' => $data['competitionName'],
            'chiusura_iscrizioni' => $data['registrationClose'],
            'apertura_iscrizioni' => null,
            'data_gara' => $data['competitionDate'],
            'nr_atleti' => $data['n_athletes'],
            'richieste' => json_encode($richieste),
            'stato' => '0'
        );

        if ($data['logo']) {
            $info = pathinfo($data['logo']['filename']);
            if (file_put_contents('resources/' . str_replace(" ", '_', $data['competitionName']) . '_logo.' . $info['extension'], base64_decode($data['logo']['value']))) {
                $record['logo'] = str_replace(" ", '_', $data['competitionName']) . '_logo.' . $info['extension'];
            }

        }
        if ($data['locandina']) {
            $info = pathinfo($data['locandina']['filename']);
            if (file_put_contents('resources/' . str_replace(" ", '_', $data['competitionName']) . '_locandina.' . $info['extension'], base64_decode($data['locandina']['value']))) {
                $record['locandina'] = str_replace(" ", '_', $data['competitionName']) . '_locandina.' . $info['extension'];
            }

        }
        if ($data['circolare']) {
            $info = pathinfo($data['circolare']['filename']);
            if (file_put_contents('resources/' . str_replace(" ", '_', $data['competitionName']) . '_circolare.' . $info['extension'], base64_decode($data['circolare']['value']))) {
                $record['circolare'] = str_replace(" ", '_', $data['competitionName']) . '_circolare.' . $info['extension'];
            }

        }

        /* print_r($record); */
        $competition = $this->Competitions->patchEntity($competition, $record);
        /*  print_r($competition); */
        if ($this->Competitions->save($competition)) {

            $dataGara = explode('-', $data['competitionDate']);
            $dataChiusura = explode('-', $data['registrationClose']);

            $testoMail = "Buongiorno, \r la sua richiesta � stata presa in carico e sar� elaborata nei prossimi giorni. Di seguito i dati comunicati: \r\r Societa: " . $data['companyName'] . "\r Responsabile: " . $data['name'] . "\r Telefono: " . $data['telefono'] . "\r Email: " . $data['email'] . "\r Nome manifestazione: " . $data['competitionName'] . "\r\r Data manifestazione: " . $dataGara[2] . '/' . $dataGara[1] . '/' . $dataGara[0] . "\r\r Chiusura iscrizioni: " . $dataChiusura[2] . '/' . $dataChiusura[1] . '/' . $dataChiusura[0];

            $Email = new Email();
            $Email->setFrom(['info@iscrizionicsenveneto.it' => 'Iscrizioni CSEN Veneto'])
                ->setBcc(['info@iscrizionicsenveneto.it', $data['email']])
                ->subject('Richiesta inserimento gara')
                ->send($testoMail);

            $this->apiResponse['success'] = true;
        } else {
            /* debug($competition->errors()); */
            $this->apiResponse['success'] = false;
        }
    }

    //===========================================================================================================================================================                                                                                                                            
// GET                                                                                                                                                                                                                                     
//===========================================================================================================================================================

    public function getPendingCompetitions()
    {

        $competition = $this->Competitions->find()
            ->where([
                'stato' => 0
            ])
            ->toArray();

        $this->apiResponse['data'] = $competition;
        $this->apiResponse['success'] = true;

    }

    public function getCompetitions()
    {

        $competition = $this->Competitions->find()
            ->where([
                'stato' => 1,
                'DATE(data_gara) >=' => date('Y-m-d', mktime(0, 0, 0, intval(date("m")), intval(date("d") - 20), intval(date("Y"))))
            ])
            ->toArray();

        $this->apiResponse['data'] = $competition;
        $this->apiResponse['success'] = true;

    }

    public function getDatiGara()
    {
        Configure::load('constants');
        $data = $this->request->getData();
        $competition = $this->Competitions->find()
            ->where([
                'id' => $data[0]
            ])
            ->first();

            //controllo data e chiusura iscrizioni
        if($competition->reg_status == Configure::read('REG_OPEN') || 
            $competition->reg_status == Configure::read('REG_NOT_YET_OPEN')){
                $data_apertura = $competition->apertura_iscrizioni->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $data_chiusura = $competition->chiusura_iscrizioni->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $today = FrozenTime::now()->i18nFormat('yyyy-MM-dd HH:mm:ss');

                if($competition->reg_status == Configure::read('REG_NOT_YET_OPEN') && $data_apertura <= $today){
                    $competition->reg_status = Configure::read('REG_OPEN');
                    $this->Competitions->save($competition);

                }else if($competition->reg_status == Configure::read('REG_OPEN') && $data_chiusura <= $today){
                    $competition->reg_status = Configure::read('REG_CLOSED_TIME');
                    $this->Competitions->save($competition);
                }


        }

        $this->apiResponse['data'] = $competition;
        $this->apiResponse['success'] = true;
    }


    //==========================================================================================================================================================                                                                                                                                                           
// UPDATE                                                                                                                                                                                                                                                                                                           
//==========================================================================================================================================================

    public function updateDatiGara()
    {
        $data = $this->request->getData();

        $dataCompetition = $data['competition'];

        $dataCompetition['richieste'] = json_encode($dataCompetition['richieste']);
        /* print_r($data); */
        $competition = $this->Competitions->get($dataCompetition['id'], [
            'contain' => []
        ]);

        if (isset($data['circolare'])) {
            $info = pathinfo($data['circolare']['filename']);
            if (file_put_contents($dataCompetition['nome_gara'] . '_circolare.' . $info['extension'], base64_decode($data['circolare']['value']))) {
                $dataCompetition['circolare'] = $dataCompetition['nome_gara'] . '_circolare.' . $info['extension'];
            }
        }
        if (isset($data['categorie'])) {
            $result = $this->caricaCategorie($data['categorie']);
            if ($result['success']) {
                $dataCompetition['codiceTipoCategorie'] = $result['id'];
            }
        }

        /*  print_r($competition); */
        $competition = $this->Competitions->patchEntity($competition, $dataCompetition);
        /* print_r($competition); */
        if ($this->Competitions->save($competition)) {
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }


    }

    //========================================================================================================================================================                                                                                                                                                                           
    //  CATEGORIE                                                                                                                                                                                                                                                                                                                                   
    //========================================================================================================================================================

    public function caricaCategorie($categorie)
    {

        $this->loadModel('CategoryCodes');

        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT MAX(codiceTipoCategorie) as codice FROM category_codes");

        $result = $stmt->fetchAll('assoc');

        $codiceCategoria = $result[0]['codice'] + 1;

        $dataRecords = array();
        $row = 1;
        $lines = explode("\n", base64_decode($categorie['value']));
        $array = array_map('str_getcsv', $lines);
        array_shift($array);
        foreach ($array as $riga) {
            //print_r($riga);
            $recordTemp = $this->buildRecord($riga[0], $codiceCategoria);
            if ($recordTemp != null) {
                $dataRecords[] = $recordTemp;
            }

        }
        //print_r($dataRecords);

        $categories = $this->CategoryCodes->newEntities($dataRecords);

        foreach ($categories as $entity) {
            $this->CategoryCodes->save($entity);
        }

        return array(
            'success' => true,
            'id' => $codiceCategoria
        );

    }

    private function buildRecord($line, $codiceCategoria)
    {

        $data = explode(';', $line);
        //print_r($data);

        if ($data[0] != "") {
            $record = array(
                'codice' => $data[0],
                'categoria' => $data[1],
                'anno_min' => $data[2],
                'anno_max' => $data[3],
                'sesso' => $data[4],
                'grado' => $data[5],
                'cat_peso' => $data[6],
                'peso_min' => $data[7],
                'peso_max' => $data[8],
                'specialita' => $data[9],
                'costo_intero' => 0,
                'costo_scontato' => 0,
                'codiceTipoCategorie' => $codiceCategoria
            );
            return $record;
        } else {
            return null;
        }

    }

    //========================================================================================================================================================================================================================================================================================================================================
    //                                                                                                                                                                                                                                                                                                                                        
    //  ######    ###    #####   #####  ##      ##       #####   ##     ##  ##                                                                                                                                                                                                                                                              
    //    ##     ## ##   ##  ##  ##     ##      ##      ##   ##  ####   ##  ##                                                                                                                                                                                                                                                              
    //    ##    ##   ##  #####   #####  ##      ##      ##   ##  ##  ## ##  ##                                                                                                                                                                                                                                                              
    //    ##    #######  ##  ##  ##     ##      ##      ##   ##  ##    ###  ##                                                                                                                                                                                                                                                              
    //    ##    ##   ##  #####   #####  ######  ######   #####   ##     ##  ##                                                                                                                                                                                                                                                              
    //                                                                                                                                                                                                                                                                                                                                        
    //========================================================================================================================================================================================================================================================================================================================================


    public function uploadTabelloni()
    {

        $data = $this->request->getData();

        //print_r($data);

        if ($data["zipFile"]["filename"]) {
            $filename = $data["zipFile"]["filename"];
            $source = $data["zipFile"]["value"];
            $type = $data["zipFile"]["filetype"];

            $name = explode(".", $filename);
            $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
            foreach ($accepted_types as $mime_type) {
                if ($mime_type == $type) {
                    $okay = true;
                    break;
                }
            }

            $continue = strtolower($name[1]) == 'zip' ? true : false;
            if (!$continue) {
                $message = "The file you are trying to upload is not a .zip file. Please try again.";
            }

            $target_path = $data["nomeGara"] . "/" . $filename;  // change this to the correct site path
            //if(move_uploaded_file($source, $target_path)) {
            if (file_put_contents($target_path, base64_decode($source))) {
                $zip = new ZipArchive();
                $x = $zip->open($target_path);
                if ($x === true) {
                    $zip->extractTo($data["nomeGara"] . "/" . "tabelloni/"); // change this to the correct site path
                    $zip->close();

                    unlink($target_path);
                }
                $message = "Your .zip file was uploaded and unpacked.";
            } else {
                $message = "There was a problem with the upload. Please try again.";
            }

            //print_r($message);
        }
    }


    public function getClassifiche()
    {

        $data = $this->request->getData();
        //print_r($data);
        $dir = $data['gara'] . '/tabelloni';
        //$files1 = scandir($dir);
        $scanned_directory = array_diff(scandir($dir), array('..', '.'));
        //print_r( $scanned_directory);

        $record = array();
        foreach ($scanned_directory as $tabellone) {
            $record[] = array('name' => $tabellone, 'docpath' => $dir . '/' . $tabellone);
        }
        $this->apiResponse['data'] = $record;
        $this->apiResponse['success'] = true;
    }



    // ===================================================== ATHLETES IN CATEGORIES ===========================================================================


    protected function getAthletesInCategories($data)
    {
        $result = [];

        $kataEso = [];
        $kumiteEso = [];
        $kataCadetti = [];
        $kumiteCadetti = [];
        $kataJunior = [];
        $kumiteJunior = [];
        $kataSenior = [];
        $kumiteSenior = [];
        $kataMaster = [];
        $kumiteMaster = [];

        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT category_codes.categoria, category_codes.specialita, category_codes.grado, COUNT(DISTINCT athletes_inscriptions.athlete_id) AS n_athletes FROM athletes_inscriptions
                            JOIN category_codes ON category_codes.id = athletes_inscriptions.category_code_id
                            WHERE category_codes.codiceTipoCategorie = 3 and competition_id = " . $data['competition_id'] . " and specialita = 'Percorso'
                            GROUP BY category_codes.categoria, category_codes.grado, category_codes.specialita
                            ORDER BY specialita, categoria");

        $resultCombinata = $stmt->fetchAll('assoc');

        foreach ($resultCombinata as $category) {
            $category['specialita'] = 'Combinata';
            $category['sesso'] = 'M/F';
            $result[] = $category;
        }

        $stmt2 = $conn->execute("SELECT category_codes.categoria, category_codes.specialita, category_codes.grado, category_codes.sesso, COUNT(DISTINCT athletes_inscriptions.athlete_id) AS n_athletes FROM athletes_inscriptions
                            JOIN category_codes ON category_codes.id = athletes_inscriptions.category_code_id
                            WHERE category_codes.codiceTipoCategorie = 3 and competition_id = " . $data['competition_id'] . " and categoria NOT IN ('BAMBINI A', 'BAMBINI B', 'FANCIULLI', 'RAGAZZI')
                            GROUP BY category_codes.categoria, category_codes.grado, category_codes.specialita, category_codes.sesso
                            ORDER BY specialita, categoria");


        $resultAgonisti = $stmt2->fetchAll('assoc');

        foreach ($resultAgonisti as $category) {
            switch ($category['categoria']) {
                case 'ESORDIENTI':
                    if ($category['specialita'] == 'Kata') {
                        $kataEso[] = $category;
                    } else {
                        $kumiteEso[] = $category;
                    }
                    break;
                case 'CADETTI':
                    if ($category['specialita'] == 'Kata') {
                        $kataCadetti[] = $category;
                    } else {
                        $kumiteCadetti[] = $category;
                    }
                    break;
                case 'JUNIORES':
                    if ($category['specialita'] == 'Kata') {
                        $kataJunior[] = $category;
                    } else {
                        $kumiteJunior[] = $category;
                    }
                    break;
                case 'SENIORES':
                    if ($category['specialita'] == 'Kata') {
                        $kataSenior[] = $category;
                    } else {
                        $kumiteSenior[] = $category;
                    }
                    break;
                case 'MASTER A/B':
                case 'MASTER C/D':
                case 'MASTER E/F':
                    if ($category['specialita'] == 'Kata') {
                        $kataMaster[] = $category;
                    } else {
                        $kumiteMaster[] = $category;
                    }
                    break;
                default:
                    break;
            }
        }

        $result = array_merge($result, $kataEso);
        $result = array_merge($result, $kataCadetti);
        $result = array_merge($result, $kataJunior);
        $result = array_merge($result, $kataSenior);
        $result = array_merge($result, $kataMaster);
        $result = array_merge($result, $kumiteEso);
        $result = array_merge($result, $kumiteCadetti);
        $result = array_merge($result, $kumiteJunior);
        $result = array_merge($result, $kumiteSenior);
        $result = array_merge($result, $kumiteMaster);

        return $result;
    }


    public function getTotaleIscrizioni()
    {
        $data = $this->request->getData();
        $conn = ConnectionManager::get('default');
        $stmt2 = $conn->execute("SELECT COUNT(DISTINCT athletes_inscriptions.athlete_id) AS n_athletes FROM athletes_inscriptions
                        WHERE competition_id = " . $data['competition_id']);

        $result = $stmt2->fetchAll('assoc');

        $this->apiResponse['data'] = $result[0]['n_athletes'];
        $this->apiResponse['success'] = true;

    }

    protected function getCategories($data){
        
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT category_codes.*, COUNT(athletes_inscriptions.id) AS n_athletes FROM athletes_inscriptions 
                                JOIN category_codes ON category_codes.id = athletes_inscriptions.category_code_id 
                                WHERE category_codes.codiceTipoCategorie = 3 AND competition_id = " . $data['competition_id'] ."
                                GROUP BY category_codes.id");

        $categories = $stmt->fetchAll('assoc');


        return $categories;
    }

    public function getTabelloni(){

        $data = $this->request->getData();

        $competition = $this->Competitions->get($data['competition_id'], [
            'contain' => []
        ]); 

        $categories = [];
        $tabelloni = $competition['tabelloni'] == 0 ? false : true;
        $result = [];

        if($tabelloni){
            $categories = $this->getCategories($data);
        }else{
            $categories = $this->getAthletesInCategories($data);
        }
        
        $result['categories'] = $categories;
        $result['tabs'] = $tabelloni;


        $this->apiResponse['data'] = $result;
	    $this->apiResponse['success'] = true;

    }

    public function syncCompetitionData(){

        $this->loadModel('Athletes');
        $this->loadModel('CategoryCodes');
        $this->loadModel('Competitions');
        $this->loadModel('AthletesInscriptions');
        $this->loadModel('Clubs');
        $this->loadModel('ClubInscriptions');
        
        $data = $this->request->getData();

        $clubs = $this->Clubs->find('all')->toArray();

        $athletes = $this->Athletes->find('all')->toArray();

        $categoryCodes = $this->CategoryCodes->find('all')->toArray();

        $competition = $this->Competitions->get($data['competition_id'], [
            'contain' => []
        ]);
        $atheleInscirption = $this->AthletesInscriptions->find()->where([
            'competition_id' =>$data['competition_id']
        ])
        ->toArray();
        $clubInscirption = $this->ClubInscriptions->find()->where([
            'competition_id' =>$data['competition_id']
        ])
        ->toArray();

        $response = [];

        $response['clubs'] = $clubs;
        $response['athletes'] = $athletes;
        $response['categoryCodes'] = $categoryCodes;
        $response['competition'] = $competition;
        $response['atheleInscirption'] = $atheleInscirption;
        $response['clubInscirption'] = $clubInscirption;

        $this->apiResponse['data'] = $response;
	    $this->apiResponse['success'] = true;

    }


}
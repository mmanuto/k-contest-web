<?php
declare(strict_types=1);

namespace App\Controller;

use RestApi\Controller\ApiController;
use RestApi\Utility\JwtToken;
use Cake\Database\Type;
use Cake\Datasource\ConnectionManager;
use Cake\Mailer\Email;
use Cake\Core\Configure;
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
    
//============================================================================================================================================================                                                                                                                                                              
//   REQUEST                                                                                                                                                                                                                                                                                                                     
//========================================================================================================================================================================================================================================================================================================================

    public function saveRequest()
    {
        $data = $this->request->getData();
        $competition = $this->Competitions->newEmptyEntity();

        $record = array(
            'id' => $data['id'],
            'type' => 'CPM',
            'responsabile' => $data['name'],
            'club_name' => $data['companyName'],
            'place' => $data['place'],
            'email' => $data['email'],
            'telephone' => $data['telefono'],
            'nome_gara' => $data['competitionName'],
            'chiusura_iscrizioni' => $data['registrationClose'],
            'apertura_iscrizioni' => $data['registrationOpen'],
            'data_gara' => $data['competitionDate'],
            'nr_atleti' => $data['n_athletes'],
            'comp_status' => 'DRAFT'
        );

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

        $competition = $this->Competitions->patchEntity($competition, $record);

        $competition['id'] = $data['id'];

        if ($this->Competitions->save($competition)) {

            $this->apiResponse['success'] = true;
        } else {
            debug($competition->errors());
            $this->apiResponse['success'] = false;
        }
    }

public function addGattamelata()
    {
        $competition = $this->Competitions->newEmptyEntity();
        $richieste = array(
            'referee' => true,
            'competitionManagement' => true,
            'tabsManagement' => true
        );

        $record = array(
            'id' => 'GTTMLT-2026',
            'type' => 'CMP',
            'place' => 'KIOENE Padova',
            'responsabile' => 'CSEN Veneto',
            'club_name' => 'CSEN Veneto',
            'logo' => 'TROFEO_GATTAMELATA_logo.png',
            'email' => 'luciana@csenveneto.it',
            'telephone' => '3923939904',
            'nome_gara' => 'Trofeo Gattamelata',
            'chiusura_iscrizioni' => new FrozenTime('2026-01-18'),
            'apertura_iscrizioni' => new FrozenTime('2025-12-24'),
            'data_gara' => new FrozenTime('2026-01-25'),
            'nr_atleti' => '700',
            'circolare' => 'TROFEO_GATTAMELATA_informativa.pdf',
            'locandina' => 'TROFEO_GATTAMELATA_locandina.png',
            'richieste' => json_encode($richieste),
            'comp_status' => 'REG_OPEN'
        );

        $competition = $this->Competitions->patchEntity($competition, $record);

        $competition->id = 'GTTMLT-2026';

        print_r($competition);

        if ($this->Competitions->save($competition)) {

            $this->apiResponse['success'] = true;
        } else {
            /* debug($competition->errors()); */
            $this->apiResponse['success'] = false;
        }
    }

    public function updateGattamelata(){

        $competition = $this->Competitions->get('GTTMLT-2026');
        $competition->codiceTipoCategorie = 3;
    
        if ($this->Competitions->save($competition)) {

            $this->apiResponse['success'] = true;
            $this->apiResponse['data'] = $competition;
        } else {
            /* debug($competition->errors()); */
            $this->apiResponse['success'] = false;
        }

    }

    public function getCategoryCode(){
        $this->loadModel('Categorycodes');

        $categorycodes = $this->Categorycodes->find()->toArray();
         $this->apiResponse['success'] = true;
          $this->apiResponse['data'] =  $categorycodes;


    }
//===========================================================================================================================================================                                                                                                                            
// GET                                                                                                                                                                                                                                     
//===========================================================================================================================================================

public function getAllCompetitions()
{
    $data = $this->request->getData();
    $conn = ConnectionManager::get('default');

    $competitions = $this->Competitions->find()
        ->toArray();

    foreach ($competitions as $competitionItem) {

        
        
       $query = $conn->execute("SELECT COUNT(DISTINCT athlete_inscriptions.athlete_id) AS n_athletes FROM athlete_inscriptions
                        WHERE competition_id = '" . $competitionItem->id."'");

        $result = $query->fetchAll('assoc');
        $competitionItem['inscriptions'] = $result[0]['n_athletes'];

        

    }
    //print_r($competitions);

    $this->apiResponse['data'] = $competitions;
    $this->apiResponse['success'] = true;

}

    public function getCompetitions()
    {
        Configure::load('constants');

        $competition = $this->Competitions->find()
            ->where([
                'comp_status NOT IN' => [Configure::read('DRAFT'), Configure::read('ARCHIVED')],
                'DATE(data_gara) >=' => date('Y-m-d', mktime(0, 0, 0, intval(date("m")), intval(date("d") - 20), intval(date("Y"))))
            ])
            ->order(['data_gara'])
            ->toArray();

        $this->apiResponse['data'] = $competition;
        $this->apiResponse['success'] = true;

    }

    public function getCompetitionList(){
        
        Configure::load('constants');
        $this->loadModel('Categorycodes');
        $competitions = $this->Competitions->find()
            ->where(['comp_status NOT IN' => [Configure::read('DRAFT')]])
            ->order(['data_gara' => 'DESC'])
            ->toArray();

        $this->apiResponse['data'] = $competitions;
        $this->apiResponse['success'] = true;
    }

    public function update()
    {
        Configure::load('constants');

        
        $competition = $this->Competitions->find()
            ->where([
                'id' => 'DKKC-2025'
            ])
            ->first();
	
	    $competition->comp_status = Configure::read('REG_OPEN');
	
	    $competition->chiusura_iscrizioni = FrozenTime::now()->i18nFormat('yyyy-MM-dd HH:mm:ss');
	    $this->Competitions->save($competition);

        $this->apiResponse['data'] = $competition;
        $this->apiResponse['success'] = true;
    }

    public function getDatiGara()
    {
        Configure::load('constants');
        $this->request->allowMethod('post');
        $data = $this->request->getData();

        $competition = $this->Competitions->find()
            ->where([
                'id' => $data['idGara']
            ])
            ->first();
        

            //controllo data e chiusura iscrizioni
        if($competition->comp_status == Configure::read('REG_OPEN') || 
            $competition->comp_status == Configure::read('PUBLISHED')){
                $data_apertura = $competition->apertura_iscrizioni->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $data_chiusura = $competition->chiusura_iscrizioni->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $today = FrozenTime::now()->i18nFormat('yyyy-MM-dd HH:mm:ss');

                if($competition->comp_status == Configure::read('PUBLISHED') && $data_apertura <= $today){
                    $competition->comp_status = Configure::read('REG_OPEN');
                    $this->Competitions->save($competition);

                }else if($competition->comp_status == Configure::read('REG_OPEN') && $data_chiusura <= $today){
                    $competition->comp_status = Configure::read('REG_CLOSED_TIME');
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
        $dataCompetition = $this->request->getData();

        $competition = $this->Competitions->get($dataCompetition['id']);

        $competition = $this->Competitions->patchEntity($competition, $dataCompetition);

        if ($this->Competitions->save($competition)) {
            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }


    }

    public function getClassifiche()
    {

        $data = $this->request->getData();
        //print_r($data);

        $projectFolder = WWW_ROOT;
        $relativePath = $data['gara'] . '/tabelloni';

        $dir_path = $projectFolder . $relativePath;

        
        if(is_dir($dir_path)){

            $files1 = scandir($dir_path);
            $scanned_directory = array_diff($files1, array('..', '.'));

            $record = array();

            // 4. Costruiamo l'URL PUBBLICO (per il frontend)
            // Questo serve al frontend per creare il link <a href="...">
            $baseUrl = "https://" . $_SERVER['HTTP_HOST'] . $projectFolder . $relativePath;

            foreach ($scanned_directory as $tabellone) {
                $record[] = array('name' => $tabellone, 'docpath' => $relativePath . DS . $tabellone);
            }
            $this->apiResponse['data'] = $record;
            $this->apiResponse['success'] = true;
        }else{
            $this->apiResponse['message'] = 'Classifiche non presenti';
            $this->apiResponse['success'] = false;
        }
        
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
        $stmt = $conn->execute("SELECT categorycodes.categoria, categorycodes.specialita, categorycodes.grado, COUNT(DISTINCT athlete_inscriptions.athlete_id) AS n_athletes FROM athlete_inscriptions
                            JOIN categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id
                            WHERE categorycodes.codiceTipoCategorie = 3 and competition_id = '" . $data['competition_id'] . "' and specialita = 'Percorso'
                            GROUP BY categorycodes.categoria, categorycodes.grado, categorycodes.specialita
                            ORDER BY specialita, categoria");

        $resultCombinata = $stmt->fetchAll('assoc');

        foreach ($resultCombinata as $category) {
            $category['specialita'] = 'Combinata';
            $category['sesso'] = 'M/F';
            $result[] = $category;
        }

        $stmt2 = $conn->execute("SELECT categorycodes.categoria, categorycodes.specialita, categorycodes.grado, categorycodes.sesso, COUNT(DISTINCT athlete_inscriptions.athlete_id) AS n_athletes FROM athlete_inscriptions
                            JOIN categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id
                            WHERE categorycodes.codiceTipoCategorie = 3 and competition_id = '" . $data['competition_id'] . "' and categoria NOT IN ('BAMBINI A', 'BAMBINI B', 'FANCIULLI', 'RAGAZZI')
                            GROUP BY categorycodes.categoria, categorycodes.grado, categorycodes.specialita, categorycodes.sesso
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
        Configure::load('constants');


        
        $conn = ConnectionManager::get('default');
        if($data['competition_type'] == Configure::read('STAGE')){
            
            $query = "SELECT categorycode_id AS prova, specialita, atleti_max, COUNT(DISTINCT athlete_inscriptions.athlete_id) AS n_athletes FROM athlete_inscriptions JOIN 
                        categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id
                        WHERE competition_id = '" . $data['competition_id']."' GROUP BY categorycode_id ORDER BY order_number";
            
            $stmt2 = $conn->execute($query);

            $result = $stmt2->fetchAll('assoc');

            $this->apiResponse['data'] = $result;
            $this->apiResponse['success'] = true;

        }else{

            $query = "SELECT COUNT(DISTINCT athlete_inscriptions.athlete_id) AS n_athletes FROM athlete_inscriptions
                        WHERE competition_id = '" . $data['competition_id']."'";
            $stmt2 = $conn->execute($query);

            $result = $stmt2->fetchAll('assoc');

            $this->apiResponse['data'] = $result[0]['n_athletes'];
            $this->apiResponse['success'] = true;
            
        }

    }

    public function getTotaleCategorie()
    {
        $data = $this->request->getData();
        $conn = ConnectionManager::get('default');
        $stmt = $conn->execute("SELECT COUNT(DISTINCT athlete_inscriptions.categorycode_id) AS n_categories FROM athlete_inscriptions
                                WHERE competition_id = '" . $data['competition_id']."'");

        $result = $stmt->fetchAll('assoc');

        $this->apiResponse['data'] = $result[0]['n_categories'];
        $this->apiResponse['success'] = true;

    }

    protected function getCategories($data){
        
        $conn = ConnectionManager::get('default');
        $sql = "SELECT categorycodes.*, 
                                        COUNT(athlete_inscriptions.id) AS n_athletes, 
                                        tatami_assignments.tatami,
                                        tatami_assignments.status AS tatami_status
                                FROM athlete_inscriptions 
                                JOIN categorycodes ON categorycodes.id = athlete_inscriptions.categorycode_id 
                                LEFT JOIN tatami_assignments ON categorycodes.id = tatami_assignments.categorycode_id AND tatami_assignments.competition_id = :comp_id
                                WHERE categorycodes.codiceTipoCategorie = 3 
                                    AND athlete_inscriptions.competition_id = :comp_id
                                GROUP BY categorycodes.id, tatami_assignments.tatami, tatami_assignments.status 
                                ORDER BY categorycodes.order_number";
        
        $stmt = $conn->execute($sql, ['comp_id' => $data['competition_id']]);

        $categories = $stmt->fetchAll('assoc');


        return $categories;
    }

    public function getTabelloni(){

        $data = $this->request->getData();

        $competition = $this->Competitions->get($data['competition_id']); 

        $categories = [];
        $tabelloni = $competition['flag_tabs'] == 0 ? false : true;
        $result = [];

        $categories = $this->getCategories($data);
        
        $result['categories'] = $categories;
        $result['tabs'] = $tabelloni;


        $this->apiResponse['data'] = $result;
	    $this->apiResponse['success'] = true;

    }

    public function syncCompetitionData(){

        $this->loadModel('Athletes');
        $this->loadModel('Categorycodes');
        $this->loadModel('Competitions');
        $this->loadModel('AthleteInscriptions');
        $this->loadModel('Clubs');
        $this->loadModel('ClubInscriptions');
        
        $data = $this->request->getData();

        $clubs = $this->Clubs->find('all')->toArray();

        $athletes = $this->Athletes->find('all')->toArray();

        $categorycodes = $this->Categorycodes->find('all')->toArray();

        $competition = $this->Competitions->get($data['competition_id'], [
            'contain' => []
        ]);
        $atheleInscirption = $this->AthleteInscriptions->find()->where([
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
        $response['categorycodes'] = $categorycodes;
        $response['competition'] = $competition;
        $response['atheleInscirption'] = $atheleInscirption;
        $response['clubInscirption'] = $clubInscirption;

        $this->apiResponse['data'] = $response;
	    $this->apiResponse['success'] = true;

    }

    //==============================================================================================================================================
    // CHIUSURA ISCRIZIONI
    //==============================================================================================================================================

    /**
     * Aggiornamento stato iscrizioni e invio automatico email a tutte le società iscritte
     */
    public function closeRegistrations(){
        $this->loadModel('ClubInscriptions');

        $data = $this->request->getData();

        $competition = $this->Competitions->get($data['competition_id'], [
            'contain' => []
        ]);

        $competition->comp_status = $data['comp_status'];

        if ($this->Competitions->save($competition)) {            

            $this->apiResponse['data'] = $competition;
            $this->apiResponse['success'] = true;
            
        } else {
            $this->apiResponse['success'] = false;
        }
    }

    public function toggleFlags(){
        $data = $this->request->getData();

        $competition = $this->Competitions->get($data['competition_id']);

        switch ($data['flag']) {
            case 'TABS':
                $competition->flag_tabs = 1;
                break;
            case 'TIMETABLE':
                $competition->flag_timetable = 1;
                break;
            case 'CLASSIFICATIONS':
                $competition->flag_classifications = 1;
                break;
            default:
                break;
        }

        if ($this->Competitions->save($competition)) {            

            $this->apiResponse['data'] = $competition;
            $this->apiResponse['success'] = true;
            
        } else {
            $this->apiResponse['success'] = false;
        }
    }

   public function getTimetables(){

    $this->loadModel('Timetables');

    $data = $this->request->getData();

    $timetables = $this->Timetables->find()
        ->where(['competition_id' => $data['competition_id']])
        ->toArray();
    
    $this->apiResponse['data'] = $timetables;
    $this->apiResponse['success'] = true;

   }


}
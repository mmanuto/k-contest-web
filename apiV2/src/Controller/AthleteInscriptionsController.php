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
    public function updateCategoryRanking()
    {
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();
        $this->loadModel('TatamiAssignments');
        try {
            if (empty($data['competition_id']) || empty($data['categorycode_id']) ||
                empty($data['rankings']) || !is_array($data['rankings'])) {
                throw new \RuntimeException('Gara, categoria e classifica obbligatorie.');
            }
            $this->AthleteInscriptions->getConnection()->transactional(function () use ($data) {
                $scope = [
                    'competition_id' => $data['competition_id'],
                    'categorycode_id' => $data['categorycode_id'],
                ];
                $validated = [];
                foreach ($data['rankings'] as $row) {
                    if (!isset($row['id'], $row['final_ranking']) ||
                        filter_var($row['final_ranking'], FILTER_VALIDATE_INT) === false ||
                        (int)$row['final_ranking'] < 1 || isset($validated[(string)$row['id']])) {
                        throw new \RuntimeException('Classifica non valida o iscrizione duplicata.');
                    }
                    $entity = $this->AthleteInscriptions->find()->where($scope + ['id' => $row['id'], 'deleted' => 0])->first();
                    if (!$entity) {
                        throw new \RuntimeException('Iscrizione non trovata nella gara e categoria indicate: ' . $row['id']);
                    }
                    $validated[(string)$row['id']] = [$entity, (int)$row['final_ranking']];
                }
                $assignment = $this->TatamiAssignments->find()->where($scope)->first();
                if (!$assignment) {
                    throw new \RuntimeException('Assegnazione categoria non presente online.');
                }
                $this->AthleteInscriptions->updateAll(['final_ranking' => null], $scope);
                foreach ($validated as [$entity, $rank]) {
                    $entity->final_ranking = $rank;
                    $entity->setDirty('final_ranking', true);
                    $this->AthleteInscriptions->saveOrFail($entity);
                }
                $assignment->status = 'CAT_AWARD';
                $this->TatamiAssignments->saveOrFail($assignment);
            });
            $this->apiResponse['success'] = true;
        } catch (\Throwable $exception) {
            $this->apiResponse['success'] = false;
            $this->apiResponse['message'] = $exception->getMessage();
        }
    }

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

    //-------------------------------------------- Add Inscription -------------------------------------------------------------------

    public function addInscription(){
        $data = $this->request->getData(); 
        $athleteInscription = $this->AthleteInscriptions->get($data['inscription_id']);

        $newInscription = $this->AthleteInscriptions->newEmptyEntity();
        $newInscription->athlete_id = $athleteInscription->athlete_id;
        $newInscription->competition_id = $athleteInscription->competition_id;
        $newInscription->categorycode_id = $data['categorycode_id'];

        if ($this->AthleteInscriptions->save($newInscription)) {

            $this->apiResponse['success'] = true;
        } else {
            $this->apiResponse['success'] = false;
        }

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

    // ========================================================================
    // STAMPA TABELLONI KUMITE
    // ========================================================================
    public function printBracketPdf() {
        
        $this->loadModel('Categorycodes');
        $this->loadModel('AthleteInscriptions');

        $data = $this->request->getData();
        $pdf = new \App\Controller\Component\BracketPdf('P', 'mm', 'A4', true, 'UTF-8', false);
        // Imposta margini più stretti per sfruttare tutto il foglio
        $pdf->SetMargins(20, 25, 10); // Sx, Top, Dx
        $pdf->SetAutoPageBreak(false); // Importante: gestiamo noi i cambi pagina se servono
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(false);

        // Determina la lista delle categorie da stampare
        $categoriesToPrint = [];

        if (!empty($data['categoryId'])){

            // Caso Singolo
            $categoriesToPrint[] = $this->Categorycodes->get($data['categoryId']);
            $filename = 'SingleKumite.pdf';

        }else{

            $categoriesToPrint = $this->Categorycodes->find()
                ->where([
                    'specialita IN' => ['Kumite', 'Kumite U12']
                    ])
                ->order(['order_number' => 'ASC'])
                ->toArray();
            $filename = 'Kumite_All.pdf';
        }

        foreach ($categoriesToPrint as $category) {

            // Contiamo gli iscritti PRIMA di aggiungere la pagina
            $count = $this->AthleteInscriptions->find()
                ->where(['categorycode_id' => $category->id, 'deleted' => 0])
                ->count();
            
            if ($count == 0) {
            continue; // Salta questa categoria, non crea la pagina
        }
            $headerText = $category->categoria . ' ' . 
                        $category->sesso . ' ' . 
                        $category->grado . ' ' . 
                        $category->cat_peso;
                        
            $pdf->headerTitle = $headerText; 

            $pdf->AddPage();
            
            // Passiamo l'ID e l'oggetto $pdf per disegnare
            $this->printSingleBracketPdf($category->id, $pdf);
        }

        // In CakePHP restituisci la response per forzare il download corretto
        $pdfContent = $pdf->Output($filename, 'S');

        return $this->response
            ->withType('application/pdf')
            ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->withHeader('Content-Transfer-Encoding', 'binary')
            ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->withStringBody($pdfContent);
    }


    private function printSingleBracketPdf($categoryId, $pdf) {
        
        // 1. Recupera gli iscritti
        $inscriptions = $this->AthleteInscriptions->find()
            ->contain(['Athletes.Clubs'])
            ->where(['categorycode_id' => $categoryId, 'deleted' => 0])
            ->order(['Clubs.id', 'Athletes.id']) 
            ->toArray(); // Importante convertire in array
        
        $count = count($inscriptions);

        // 2. SOLUZIONE GIRONE ALL'ITALIANA (3 Atleti)
        if ($count == 3) {
            // Chiama la nuova funzione specifica per il girone
            $pdf->drawRoundRobin3($inscriptions);
            return; // Esci, non disegnare l'albero
        }

        // 2. Ottieni la struttura virtuale
        $brackets = $this->_getVirtualBracket($inscriptions);

        // --- CONFIGURAZIONE SINCRONIZZATA ---
        $boxHeight = 7; 
        $internalGap = 4; // DEVE ESSERE UGUALE A $pdf->matchGap
        $pdf->matchGap = $internalGap; // Passiamo il valore al PDF per sicurezza
        
        // Parametri pagina
        $pageWidth = $pdf->getPageWidth(); 
        $pageHeight = $pdf->getPageHeight();
        $xStart = 10;
        $yStart = 35;
        
        // Larghezza colonne
        $margins = 20;    
        $usableWidth = $pageWidth - $margins;
        $maxRound = max(array_keys($brackets));
        $colWidth = $usableWidth / $maxRound; 
        if ($colWidth > 60) $colWidth = 60;

        $boxWidth = $colWidth - 17; 
        $pdf->setBoxWidth($boxWidth);
        
        $coordinates = []; 

        // --- DISEGNA ROUND 1 ---
        $round1 = $brackets[1];
        $countR1 = count($round1);
        $i = 0;
        
        // Spazio per ripescaggi: Alziamo il limite (es. 80mm dal fondo)
        $footerHeight = 80; // Aumentato per lasciare più spazio sotto
        $availableHeight = $pageHeight - $yStart - $footerHeight;

        // Gap tra i nomi nello stesso match (deve corrispondere a quello in drawMatch)
        $matchBlockHeight = ($boxHeight * 2) + $internalGap;

        // Calcolo gap verticale tra i match
        if ($countR1 > 1) {
            $totalBlockSpace = $countR1 * $matchBlockHeight;
            $remainingSpace = $availableHeight - $totalBlockSpace;
            $verticalGap = $remainingSpace / ($countR1 - 1);
            
            // Limiti estetici
            if ($verticalGap < 4) $verticalGap = 4; 
            if ($verticalGap > 18) $verticalGap = 18;
        } else {
            $verticalGap = 10;
        }

        foreach ($round1 as $matchNum => $match) {
            $x = $xStart;
            $y = $yStart + ($i * ($matchBlockHeight + $verticalGap)); 
            
            $coords = $pdf->drawMatch($x, $y, $match['aka_name'], $match['ao_name']);
            $coordinates[1][$matchNum] = $coords;
            $i++;
        }

        $treeBottomY = 0;

        // --- DISEGNA ROUND SUCCESSIVI ---
        // Variabile per tracciare l'ultimo punto Y disegnato (utile per i ripescaggi)
        for ($r = 2; $r <= $maxRound; $r++) {
            foreach ($brackets[$r] as $matchNum => $match) {
                $prev1 = ($matchNum * 2) - 1;
                $prev2 = ($matchNum * 2);
                
                if (isset($coordinates[$r-1][$prev1]) && isset($coordinates[$r-1][$prev2])) {
                    $p1 = $coordinates[$r-1][$prev1];
                    $p2 = $coordinates[$r-1][$prev2];
                    
                    $newX = $xStart + (($r - 1) * $colWidth);
                    
                    // CALCOLO CENTRATURA PERFETTA
                    // Vogliamo che la linea orizzontale (a midY) colpisca esattamente
                    // lo spazio vuoto tra i due nomi del nuovo match.
                    $midY = ($p1['y'] + $p2['y']) / 2;
                    
                    // Formula corretta: Centro - (AltezzaBox + MetàGap)
                    // BoxHeight (7) + HalfGap (1) = 8mm
                    $newY = $midY - (($boxHeight * 1.5) + ($internalGap / 2)); 
                
                    $coords = $pdf->drawMatch($newX, $newY, $match['aka_name'], $match['ao_name']);
                    $coordinates[$r][$matchNum] = $coords;
                    
                    // Disegna connessione
                    // Ordina i punti per avere pTop (alto) e pBottom (basso)
                    $pTop = ($p1['y'] < $p2['y']) ? $p1 : $p2;
                    $pBottom = ($p1['y'] < $p2['y']) ? $p2 : $p1;
                    $pdf->drawConnection($pTop, $pBottom, $newX);

                    if ($r == $maxRound) {
                    $pdf->drawWinnerLine($coords);
                }

                    $currentBottom = $newY + ($boxHeight * 2) + $internalGap;
                    if ($currentBottom > $treeBottomY) $treeBottomY = $currentBottom;
                }
            }
        }

        // --- DISEGNA TABELLA RIPESCAGGI VUOTA (In basso) ---
        // Calcola una posizione Y sicura. 
        // Se il tabellone è corto, mettila a 150. Se è lungo, mettila sotto l'ultimo match.
        
        $idealY = $pageHeight - 70; 

        // Se l'albero finisce dopo la posizione ideale, spostati sotto l'albero
        $repechageY = max($idealY, $treeBottomY + 15);
        
        // Se sfori la pagina, nuova pagina
        if ($repechageY > ($pageHeight - 40)) {
            $pdf->AddPage();
            $repechageY = 30;
        }
        
        $pdf->drawEmptyRepechageGrid($repechageY);
    }

    private function _getVirtualBracket($atleti_inscriptions) {
        $count = count($atleti_inscriptions);
        
        // 1. Calcola dimensione tabellone (4, 8, 16, 32, 64...)
        $bracketSize = (int) pow(2, ceil(log($count, 2)));
        if ($bracketSize < 4) $bracketSize = 4; // Minimo tabellone da 4

        // 2. Padding con NULL (BYE)
        // È CRUCIALE usare array_values per resettare le chiavi numeriche se arrivano sporche
        $atleti = array_values($atleti_inscriptions);
        $atletiPaddati = array_pad($atleti, $bracketSize, null);

        // 3. Logica di Ordinamento
        $half = $bracketSize / 2;
        $indicesOrder = [];
        
        // Prima metà (Dispari: 0, 2, 4...) - Seconda metà (Pari: 1, 3, 5...)
        for ($i = 0; $i < $half; $i += 2) $indicesOrder[] = $i;
        for ($i = 1; $i < $half; $i += 2) $indicesOrder[] = $i;

        // 4. Costruiamo la struttura dati per il PDF
        $virtualMatches = [];
        $totalRounds = (int) log($bracketSize, 2); // Es. 32 atleti = 5 round

        // --- GENERAZIONE ROUND 1 (Con i Nomi) ---
        $matchCounter = 1;
        foreach ($indicesOrder as $i) {
            $aka = $atletiPaddati[$i] ?? null;
            $ao  = $atletiPaddati[$i + $half] ?? null;

            $virtualMatches[1][$matchCounter] = [
                'round' => 1,
                'number' => $matchCounter,
                'aka_name' => $aka ? $aka->athlete->cognome . ' ' . $aka->athlete->nome : '', // O lascia vuoto se preferisci
                'ao_name'  => $ao  ? $ao->athlete->cognome  . ' ' . $ao->athlete->nome  : '',
                'aka_club' => $aka ? $aka->athlete->club->club_name : '', // Utile per la stampa
                'ao_club'  => $ao  ? $ao->athlete->club->club_name : '',
            ];
            $matchCounter++;
        }

        // --- GENERAZIONE ROUND SUCCESSIVI ---
        // Servono per disegnare le caselle vuote nel PDF
        $matchesInRound = $bracketSize / 2; 

        for ($r = 2; $r <= $totalRounds; $r++) {
            $matchesInRound /= 2; // Dimezza i match (16 -> 8 -> 4...)
            for ($k = 1; $k <= $matchesInRound; $k++) {

                // INDIVIDUA I MATCH "GENITORI" DEL ROUND PRECEDENTE
                // Il match K del round R proviene da:
                // - Aka: Match (K*2)-1 del round R-1
                // - Ao:  Match (K*2)   del round R-1
                
                $prevMatchAkaIdx = ($k * 2) - 1;
                $prevMatchAoIdx  = ($k * 2);
                
                $prevMatchAka = $virtualMatches[$r-1][$prevMatchAkaIdx];
                $prevMatchAo  = $virtualMatches[$r-1][$prevMatchAoIdx];

                // --- SIMULAZIONE AVANZAMENTO ---
            
                // 1. Chi va nello slot AKA (Rosso)?
                // Controlliamo il match genitore ($prevMatchAka):
                // Se c'è un nome in AKA e NESSUNO in AO -> Vince AKA per BYE
                $currentAkaName = '';
                $currentAkaClub = '';
                
                if (!empty($prevMatchAka['aka_name']) && empty($prevMatchAka['ao_name'])) {
                    // BYE! Passa il turno automaticamente
                    $currentAkaName = $prevMatchAka['aka_name'];
                    $currentAkaClub = $prevMatchAka['aka_club'];
                }

                // 2. Chi va nello slot AO (Blu)?
                // Controlliamo il match genitore ($prevMatchAo):
                // Se c'è un nome in AKA e NESSUNO in AO -> Vince AKA per BYE
                // (Nota: in un tabellone standard il vincitore di un match è sempre 'aka' se l'altro è vuoto, 
                // perché nel round 1 abbiamo riempito prima aka e poi ao)
                $currentAoName = '';
                $currentAoClub = '';
                
                if (!empty($prevMatchAo['aka_name']) && empty($prevMatchAo['ao_name'])) {
                    // BYE! Passa il turno automaticamente
                    $currentAoName = $prevMatchAo['aka_name'];
                    $currentAoClub = $prevMatchAo['aka_club'];
                }

                // CREA IL MATCH VIRTUALE
                $virtualMatches[$r][$k] = [
                    'round' => $r,
                    'number' => $k,
                    'aka_name' => $currentAkaName, // Sarà pieno se c'era un BYE, vuoto se si deve combattere
                    'ao_name'  => $currentAoName,
                    'aka_club' => $currentAkaClub,
                    'ao_club'  => $currentAoClub
                ];
            }
        }

        return $virtualMatches;
    }

    // ========================================================================
    // DOWNLOAD EXCEL ACCORPAMENTI
    // ========================================================================



    // ========================================================================
    // STAMPA CARTELLINI
    // ========================================================================

    public function downloadAllPassesZip() {
        $this->loadModel('AthleteInscriptions');
        $this->loadModel('Categorycodes');

        // 1. Definiamo le tue liste (Hardcoded o da DB)
        $ageCategories = ['UNDER 6', 'UNDER 8', 'UNDER 10', 'UNDER 12'];
        $beltCategories = ['Bianca', 'Gialla/Arancio', 'Verde/Blu/Marrone'];

        // 2. Prepariamo lo ZIP
        $zip = new \ZipArchive();
        $tmpFile = tempnam(sys_get_temp_dir(), 'pass_zip');
        
        if ($zip->open($tmpFile, \ZipArchive::CREATE) !== TRUE) {
            $this->Flash->error("Impossibile creare il file ZIP");
            return $this->redirect(['action' => 'index']);
        }

        $filesAdded = 0;

        // 3. DOPPIO CICLO (Combinazione Età x Cintura)
        foreach ($ageCategories as $age) {
            foreach ($beltCategories as $belt) {
                
                // A. Trova gli ID delle categorie che corrispondono a questa coppia
                // Nota: Adatto la query ai nomi delle tue colonne (categoria, grado)
                // Usa il LIKE per flessibilità (es. "Gialla/Arancio" trova anche "Gialla")
                $categoryIds = $this->Categorycodes->find()
                    ->select(['id'])
                    ->where([
                        'categoria LIKE' => "%$age%", // Es. Cerca 'Under 6'
                        'grado LIKE' => "%$belt%",     // Es. Cerca 'Bianca'
                        'specialita NOT LIKE' => "%Kumite%"
                    ])
                    ->extract('id')
                    ->toArray();

                if (empty($categoryIds)) continue; // Nessuna categoria trovata per questa combinazione

                // B. Trova gli atleti iscritti a queste categorie
                $inscriptions = $this->AthleteInscriptions->find()
                    ->contain(['Athletes.Clubs', 'Categorycodes'])
                    ->where([
                        'categorycode_id IN' => $categoryIds,
                        'deleted' => 0
                    ])
                    ->group(['Athletes.id'])
                    ->order(['Clubs.id' => 'ASC'])
                    ->toArray();

                // Se non ci sono atleti, saltiamo la generazione del file
                if (empty($inscriptions)) continue;

                // C. Genera il contenuto del PDF per questa combinazione
                // Passiamo un titolo personalizzato per il file
                $pdfTitle = "$age - $belt";
                $pdfContent = $this->_generatePdfStringForPass($inscriptions, $pdfTitle);

                // D. Aggiungi il file allo ZIP
                // Puliamo il nome file da caratteri strani (es. / diventa -)
                $cleanAge = str_replace(['/', '\\', ' '], '-', $age);
                $cleanBelt = str_replace(['/', '\\', ' '], '-', $belt);
                $filename = "Pass_{$cleanAge}_{$cleanBelt}.pdf";

                $zip->addFromString($filename, $pdfContent);
                $filesAdded++;
            }
        }

        $zip->close();

        // 4. Scarica lo ZIP
        if ($filesAdded > 0) {
            $response = $this->response->withFile($tmpFile, [
                'download' => true,
                'name' => 'Tutti_Pass_Atleti.zip'
            ]);
            
            // Importante: Elimina il file temporaneo dopo l'invio (CakePHP lo gestisce, 
            // ma con withFile su temp a volte serve pulizia, qui ci affidiamo al sistema tmp)
            return $response;
        } else {
            $this->Flash->warning("Nessun atleta trovato per le combinazioni richieste.");
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Funzione Helper che genera il PDF in memoria (Stringa) 
     * Riutilizza la logica A6 che abbiamo fatto prima
     */
    private function _generatePdfStringForPass($inscriptions, $groupTitle) {
        
        // Inizializza TCPDF
        $pdf = new \TCPDF('P', 'mm', 'A6', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 5);
        $logoFile = WWW_ROOT.'resources/logoCsen.jpg';

        foreach ($inscriptions as $ins) {
            $pdf->AddPage();
            
            // Recupero Dati
            $nome = strtoupper($ins->athlete->cognome . ' ' . $ins->athlete->nome);
            $societa = $ins->athlete->club->club_name;
            $cintura = $ins->athlete->grado;
            $catName = $ins->categorycode->categoria . ' ' . $ins->categorycode->cat_peso;

            // --- DISEGNO BORDO ESTERNO SPESSO ---
            $pdf->SetLineWidth(0.3); // Bordo esterno spesso
            $pdf->Rect(5, 5, 95, 138);

            // Intestazione Gruppo (Utile per capire che file è stampando)
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetXY(10, 8);
            $pdf->Cell(85, 5, "Gruppo: $groupTitle", 0, 1, 'C');

            if (file_exists($logoFile)) {
                
                // Logo Sinistro (X=8, Y=16, Larghezza=16mm)
                $pdf->Image($logoFile, 9, 13, 13, '', '', '', '', false, 300, '', false, false, 0);
                
                // Logo Destro (X=81, Y=16, Larghezza=16mm)
                $pdf->Image($logoFile, 82, 13, 13, '', '', '', '', false, 300, '', false, false, 0);
            }

            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetY(32);
            $pdf->MultiCell(85, 8, "TROFEO GATTAMELATA 2025", 0, 'C');
            
            $pdf->Ln(2);
            $pdf->Line(20, $pdf->GetY(), 85, $pdf->GetY());
            $pdf->Ln(2);

            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->MultiCell(85, 8, $nome, 0, 'C');
            
            $pdf->SetFont('helvetica', 'I', 11);
            $pdf->MultiCell(85, 6, $societa, 0, 'C');

            $pdf->Ln(4);

            $pdf->SetLineWidth(0.1);

            // Tabella Dati
            $pdf->SetFillColor(240, 240, 240); 
            $pdf->SetFont('helvetica', '', 10);
            
            $wLabel = 30; $wValue = 55; $hRow = 6;
            
            $rows = [
                "Cintura" => $cintura,
                "Categoria" => $catName,
                "Genere" => $ins->athlete->sesso,
                "Data di Nascita" => $ins->athlete->data_nascita
            ];

            foreach($rows as $label => $val) {
                $pdf->SetFont('helvetica', 'B', 9);
                $pdf->Cell($wLabel, $hRow, $label, 1, 0, 'L', 1);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->Cell($wValue, $hRow, substr($val, 0, 28), 1, 1, 'L', 0);
            }

            $pdf->Ln(8); // Spazio tra le due tabelle

            // Definisco larghezze colonne (Totale larghezza scrivibile su A6 ~ 85mm)
            // 34mm per il nome prova, 17mm per le celle numeri
            $wSpec = 31; 
            $wNum = 18; 
            $hScoreRow = 8; // Altezza righe punteggi

            // Intestazione Tabella Punteggi
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(240, 240, 240); // Grigio chiaro
            
            // Prima riga: Headers
            $pdf->Cell($wSpec, $hScoreRow, "SPECIALITA'", 1, 0, 'L', 1);
            $pdf->Cell($wNum, $hScoreRow, "PUNTI", 1, 0, 'L', 1);
            $pdf->Cell($wNum, $hScoreRow, "PENALITA'", 1, 0, 'L', 1);
            $pdf->Cell($wNum, $hScoreRow, "TOTALI", 1, 1, 'L', 1);

            // Righe Attività
            $activities = ['PERCORSO', 'PALLONCINO', 'KATA'];
            
            foreach($activities as $act) {
                $pdf->Cell($wSpec, $hScoreRow, $act, 1, 0, 'L', 0); // 0 = no fill
                $pdf->Cell($wNum, $hScoreRow, "", 1, 0, 'C', 0);
                $pdf->Cell($wNum, $hScoreRow, "", 1, 0, 'C', 0);
                $pdf->Cell($wNum, $hScoreRow, "", 1, 1, 'C', 0);
            }

        }

        // Ritorna il PDF come stringa grezza ('S')
        return $pdf->Output('doc.pdf', 'S');
    }


    public function getRiepilogoMedaglieDettagliato()
    {
        $data = $this->request->getData();
        $competitionId = $data['competition_id'] ?? 'SCCSC-2026'; // ID competizione

        // 1. Recupera il conteggio degli atleti raggruppati per categoria
        $categorieConConteggio = $this->AthleteInscriptions->find()
            ->select([
                'categorycode_id', 
                'count' => $this->AthleteInscriptions->find()->func()->count('id')
            ])
            ->where([
                'competition_id' => $competitionId,
                'deleted' => 0 // Considera solo iscritti attivi
            ])
            ->group(['categorycode_id'])
            ->all();

        // 2. Inizializza i contatori
        $totOro = 0;
        $totArgento = 0;
        $totBronzo = 0;
        $totCategorie = 0;

        // 3. Applica la logica condizionale per ogni categoria
        foreach ($categorieConConteggio as $cat) {
            $n = $cat->count;
            $totCategorie++;

            if ($n >= 1) {
                $totOro++; // Tutte le categorie con almeno 1 atleta hanno l'oro
            }
            
            if ($n >= 2) {
                $totArgento++; // Se sono in 2 o più, c'è l'argento
            }

            if ($n == 3) {
                $totBronzo += 1; // Se sono esattamente 3, un solo bronzo
            } elseif ($n >= 4) {
                $totBronzo += 2; // Se sono 4 o più, due bronzi (come nel Kumite standard)
            }
        }

        // 4. Prepara la risposta
        $riepilogo = [
            'totale_categorie_attive' => $totCategorie,
            'oro' => $totOro,
            'argento' => $totArgento,
            'bronzo' => $totBronzo,
            'totale_medaglie' => ($totOro + $totArgento + $totBronzo)
        ];

        $this->apiResponse['success'] = true;
        $this->apiResponse['data'] = $riepilogo;
    }

    /**
     * Calcola statistiche iscritti e prove con output mappato per età e specialità
     */
    public function getStatisticheDettagliate()
    {
        $this->loadModel('Categorycodes');
        $competitionId = $this->request->getData('competition_id') ?? 'SCCSC-2026';

        // 1. Inizializzazione struttura come richiesto
        $stats = [
            'PERCORSO_UNDER6' => ['iscritti' => 0, 'prove' => 0],
            'PERCORSO_UNDER8' => ['iscritti' => 0, 'prove' => 0],
            'PERCORSO_UNDER10' => ['iscritti' => 0, 'prove' => 0],
            'PERCORSO_UNDER12' => ['iscritti' => 0, 'prove' => 0],
            'PALLONCINO_UNDER6' => ['iscritti' => 0, 'prove' => 0],
            'PALLONCINO_UNDER8' => ['iscritti' => 0, 'prove' => 0],
            'PALLONCINO_UNDER10' => ['iscritti' => 0, 'prove' => 0],
            'PALLONCINO_UNDER12' => ['iscritti' => 0, 'prove' => 0],
            'KATA_UNDER6' => ['iscritti' => 0, 'prove' => 0],
            'KATA_UNDER8' => ['iscritti' => 0, 'prove' => 0],
            'KATA_UNDER10' => ['iscritti' => 0, 'prove' => 0],
            'KATA_UNDER12' => ['iscritti' => 0, 'prove' => 0],
            'KATA_ESORDIENTI' => ['iscritti' => 0, 'prove' => 0],
            'KATA_CADETTI' => ['iscritti' => 0, 'prove' => 0],
            'KATA_JUNIORES' => ['iscritti' => 0, 'prove' => 0],
            'KATA_SENIORES' => ['iscritti' => 0, 'prove' => 0],
            'KATA_MASTER' => ['iscritti' => 0, 'prove' => 0],
            'KUMITE_UNDER12' => ['iscritti' => 0, 'prove' => 0],
            'KUMITE_ESORDIENTI' => ['iscritti' => 0, 'prove' => 0],
            'KUMITE_CADETTI' => ['iscritti' => 0, 'prove' => 0],
            'KUMITE_JUNIORES' => ['iscritti' => 0, 'prove' => 0],
            'KUMITE_SENIORES' => ['iscritti' => 0, 'prove' => 0],
        ];

        // 2. Recupero categorie con iscritti filtrati per competizione
        $categories = $this->Categorycodes->find()
            ->contain(['AthleteInscriptions' => function($q) use ($competitionId) {
                return $q->where(['AthleteInscriptions.competition_id' => $competitionId, 'AthleteInscriptions.deleted' => 0]);
            }])
            ->all();

        foreach ($categories as $cat) {
            $n = count($cat->athlete_inscriptions);
            if ($n == 0) continue;

            // Normalizzazione etichetta età per il match con le chiavi delle stats
            $etaKey = strtoupper(str_replace(' ', '', $cat->categoria));
            // Fix per allineamento JUNIOR/SENIOR -> JUNIORES/SENIORES
            if ($etaKey == 'JUNIOR') $etaKey = 'JUNIORES';
            if ($etaKey == 'SENIOR') $etaKey = 'SENIORES';
            if (strpos($etaKey, 'MASTER') !== false) {
                $etaKey = 'MASTER';
            }

            $idPrefix = substr($cat->id, 0, 3);
            $fullKey = "";

            // --- A. PERCORSO (PER) ---
            if ($idPrefix == 'PER') {
                $fullKey = "PERCORSO_" . $etaKey;
                if (isset($stats[$fullKey])) {
                    $stats[$fullKey]['iscritti'] += $n;
                    $stats[$fullKey]['prove'] += $n;
                }
            }

            // --- B. PALLONCINO (PAL) ---
            elseif ($idPrefix == 'PAL') {
                $fullKey = "PALLONCINO_" . $etaKey;
                if (isset($stats[$fullKey])) {
                    $stats[$fullKey]['iscritti'] += $n;
                    $stats[$fullKey]['prove'] += ceil($n / 2); // Prove a coppie
                }
            }

            // --- C. KATA BAMBINI (KAG) E ADULTI (KIA/KIB) ---
            elseif ($idPrefix == 'KAG' || $idPrefix == 'KIA' || $idPrefix == 'KIB') {
                $fullKey = "KATA_" . $etaKey;
                if (isset($stats[$fullKey])) {
                    $stats[$fullKey]['iscritti'] += $n;
                    
                    if ($idPrefix == 'KAG') {
                        // Bambini: 1 prova a testa
                        $stats[$fullKey]['prove'] += $n;
                    } else {
                        // Adulti: Punteggio per tutti + Tabellone eventuale
                        $provePunteggio = $n;
                        $proveTabellone = 0;

                        if ($cat->grado == 'Marrone/Nera') {
                            if ($n > 8) $proveTabellone = 7; // Top 8 (4+2+1)
                            elseif ($n > 4) $proveTabellone = 3; // Top 4 (2+1)
                            // Fino a 4: solo punteggio (0 prove tabellone)
                        } else {
                            // Bianca -> Blu
                            if ($n >= 4) $proveTabellone = 3;
                            elseif ($n >= 2) $proveTabellone = 1;
                        }
                        $stats[$fullKey]['prove'] += ($provePunteggio + $proveTabellone);
                    }
                }
            }

            // --- D. KUMITE (KUA/KUG) ---
            elseif ($idPrefix == 'KUA' || $idPrefix == 'KUG') {
                $fullKey = "KUMITE_" . $etaKey;
                if (isset($stats[$fullKey])) {
                    $stats[$fullKey]['iscritti'] += $n;
                    if ($n == 3) {
                        $stats[$fullKey]['prove'] += 3; // Girone all'italiana
                    } elseif ($n >= 2) {
                        $stats[$fullKey]['prove'] += ($n - 1); // Eliminazione diretta standard
                    }
                }
            }
        }

        $this->apiResponse['success'] = true;
        $this->apiResponse['data'] = $stats;
    }
}

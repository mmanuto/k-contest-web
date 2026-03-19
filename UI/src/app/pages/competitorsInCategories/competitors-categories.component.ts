import { Component, ViewEncapsulation, ViewChild, OnInit, OnDestroy } from '@angular/core';
import { LocalStorageService } from 'angular-2-local-storage';
import { ToastrService } from 'ngx-toastr';
import { NgbModal} from '@ng-bootstrap/ng-bootstrap';
import { HomeService } from '../home/home.service';
import { Router } from '@angular/router';
import { CompetitorsInCategoriesService } from './competitors-categories.service';
import { ModalDirective } from 'ngx-bootstrap/modal';
import * as Dataset from './datasets';
import { InMemoryDatabase } from './storage/memory';
import { BracketsManager } from 'brackets-manager';
import * as Constants from '../../constants';
import { Subscription, interval } from 'rxjs';

declare global {
  interface JQuery {
    (any): JQuery;
    bracket(options: any): JQuery;
  }
}

const TOURNAMENT_ID = 0;

function getNearestPowerOfTwo(input: number): number {
  return Math.pow(2, Math.ceil(Math.log2(input)));
}

async function process(dataset: Dataset) {
  const db = new InMemoryDatabase();
  const manager = new BracketsManager(db);

  db.setData({
    participant: dataset.roster.map((player) => ({
      ...player,
      tournament_id: TOURNAMENT_ID,
    })),
    stage: [],
    group: [],
    round: [],
    match: [],
    match_game: [],
  });

  await manager.create({
    name: dataset.title,
    tournamentId: TOURNAMENT_ID,
    type: dataset.type,
    seeding: dataset.roster.map((player) => player.name),
    settings: {
      seedOrdering: ['natural'],
      size: getNearestPowerOfTwo(dataset.roster.length),
    },
  });

  const data = await manager.get.stageData(0);

  return {
    stages: data.stage,
    matches: data.match,
    matchGames: data.match_game,
    participants: data.participant,
    data: data
  };
}

@Component({
  selector: 'competitors-in-categories',
  templateUrl: './competitors-categories.html',
  styleUrls: ['./competitors-categories.scss'],
  encapsulation: ViewEncapsulation.None
})


export class CompetitorsInCategoriesComponent {

  @ViewChild('listModal') public listModal: ModalDirective;
  @ViewChild('kumiteModal') public kumiteModal: ModalDirective;
  

  public datiSocieta;
  public datiGara;
  public athletestNumber;
  public tabelloni = false;
  public dettaglio = false;

  public searchText: string;
  public p:any;
  public categories: any[] = [];
  public athleteList: any[] = [];
  public kumiteMatches:any[] = [];
  public selectedCategory = null;
  public minimalData = {};
  public secondGirone = {};
  public thirdGirone = {};
  public CONST = Constants;

  // Variabile per gestire l'abbonamento all'aggiornamento automatico
  private pollingSubscription: Subscription;


  constructor(
    public toastrService: ToastrService,
    public storageService: LocalStorageService,
    public competitorsService: CompetitorsInCategoriesService,
    public homeService: HomeService,
    public modalService: NgbModal,
    public router: Router
  ) {

    this.datiSocieta = this.storageService.get('currentUser');
    this.datiGara = this.storageService.get('currentCompetition');

  }

  ngOnInit() {  
  
    // Primo caricamento dei dati
    this.loadTabelloni();
    
    // Imposta il polling: aggiorna i tabelloni e i tatami ogni 30 secondi (30000 millisecondi)
    this.pollingSubscription = interval(30000).subscribe(() => {
        this.loadTabelloni();
    });
    
    this.homeService.getTotaleIscrizioni({competition_id: this.datiGara.id }).subscribe((response: any) => {
      this.athletestNumber = response.result.data;
    });
  }

  ngOnDestroy() {
    if (this.pollingSubscription) {
        this.pollingSubscription.unsubscribe();
    }
  }

  // Funzione separata per il caricamento per poterla richiamare sia all'init che nel polling
  loadTabelloni() {
    this.competitorsService.getTabelloni({competition_id: this.datiGara.id }).subscribe((response: any) => {
        const newCategories = response.result.data.categories;

        // 1. Ripuliamo subito la stringa del tatami (rimuovendo la 'T' o 't') per ogni categoria ricevuta
        newCategories.forEach(newCat => {
            if (newCat.tatami) {
                newCat.tatami = newCat.tatami.replace(/T/ig, '').trim();
            }
        });
        
        // 2. Se avevamo già delle categorie caricate, controlliamo i cambiamenti per l'animazione
        if (this.categories && this.categories.length > 0) {
            newCategories.forEach(newCat => {
                // Troviamo la stessa categoria nei dati vecchi
                const oldCat = this.categories.find(c => c.id === newCat.id);
                
                // Se la categoria ha un tatami e questo è diverso da prima (o prima non c'era)
                if (oldCat && newCat.tatami && newCat.tatami !== oldCat.tatami) {
                    newCat.isNewTatami = true; // Attiva il lampeggio
                    
                    // Rimuove la classe dopo 5 secondi (il tempo dell'animazione CSS)
                    setTimeout(() => {
                        newCat.isNewTatami = false;
                    }, 5000);
                }
            });
        }
        
        this.categories = newCategories;
        this.tabelloni = response.result.data.tabs;       
    });
  }


   //--------------------------------------------------------------------- LISTA ATLETI --------------------------------------------------------------
   openCategory(category){
    console.log(category);
    this.selectedCategory = category;
    this.competitorsService.getAthleteList({categorycode_id: category.id, competition_id: this.datiGara.id }).subscribe((response: any) => {
      this.athleteList = response.result.data;
    });

    this.listModal.show();
  }

  openKumiteBrackets(category){
    this.kumiteMatches = [];
    this.dettaglio = true;
    this.selectedCategory = category;
    this.competitorsService.getAthleteList({categorycode_id: category.id, competition_id: this.datiGara.id }).subscribe((response: any) => {
      this.athleteList = response.result.data;
      this.kumiteMatches = [];


      if(this.athleteList.length == 3){
        let firstMatch = [this.athleteList[0]['athlete']['cognome'] + ' ' + this.athleteList[0]['athlete']['nome'] + ' - ' + this.athleteList[0]['athlete']['club']['club_name'],
          this.athleteList[1]['athlete']['cognome'] + ' ' + this.athleteList[1]['athlete']['nome'] + ' - ' + this.athleteList[1]['athlete']['club']['club_name']
        ];
        let firstResult = [null, null];

        let secondMatch = [
          this.athleteList[0]['athlete']['cognome'] + ' ' + this.athleteList[0]['athlete']['nome'] + ' - ' + this.athleteList[0]['athlete']['club']['club_name'],
          this.athleteList[2]['athlete']['cognome'] + ' ' + this.athleteList[2]['athlete']['nome'] + ' - ' + this.athleteList[2]['athlete']['club']['club_name']
        ];
        let secondResult = [null, null];

        let thirdMatch = [
          this.athleteList[1]['athlete']['cognome'] + ' ' + this.athleteList[1]['athlete']['nome'] + ' - ' + this.athleteList[1]['athlete']['club']['club_name'],
          this.athleteList[2]['athlete']['cognome'] + ' ' + this.athleteList[2]['athlete']['nome'] + ' - ' + this.athleteList[2]['athlete']['club']['club_name']
        ];  
        
        let thirdResult = [null, null];
        

        console.log(firstMatch);
        console.log(secondMatch);
        console.log(thirdMatch);

        this.minimalData = {
          teams: [firstMatch],
          results: firstResult
        }

        this.secondGirone = {
          teams: [secondMatch],
          results: secondResult
        }

        this.thirdGirone = {
          teams: [thirdMatch],
          results: thirdResult
        }  

        function render_fn(container, data, score, state) {
          switch(state) {
            case "empty-bye":
              container.append("--")
              return;
            case "empty-tbd":
              container.append("Upcoming")
              return;
         
            case "entry-no-score":
            case "entry-default-win":
            case "entry-complete":
              container.append(data)
              return;
          }
        }

        jQuery('#minimal').show();
        jQuery('#girone-2').show();

        jQuery('#girone-3').show();

        jQuery('#minimal').bracket({
          teamWidth: 250,
          scoreWidth: 45,
          matchMargin: 70,
          roundMargin: 70,
          init: this.minimalData,
          decorator:{edit:edit_fn, render: render_fn},
        });

        function edit_fn(container, data, doneCb) {
          
          doneCb(data);
        }
        

        jQuery('#girone-2').bracket({
          teamWidth: 250,
          scoreWidth: 45,
          matchMargin: 70,
          roundMargin: 70,
          init: this.secondGirone,
          save: function(){},
          decorator:{edit:edit_fn, render: render_fn},
        });

        jQuery('#girone-3').bracket({
          teamWidth: 250,
          scoreWidth: 45,
          matchMargin: 70,
          roundMargin: 70,
          init: this.thirdGirone,
          save: function(){},
          decorator:{edit:edit_fn, render: render_fn},
        });

      }else{
        let position = 1;
        let dataset:Tabset = null;
        
        console.log(this.athleteList.length);
        switch (true) {
          case (this.athleteList.length == 2):
            dataset = Dataset.dataset2;
            break;
          case (this.athleteList.length == 4):
            dataset = Dataset.dataset4;
            break;
          case (this.athleteList.length <= 8):
            dataset = Dataset.dataset8;
            break;
          case (this.athleteList.length <= 16):
            dataset = Dataset.dataset16;
            break;
          case (this.athleteList.length <= 32):
            dataset = Dataset.dataset32;
            break;
          default:
            console.log('ERROREEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEEE');
            break;
        }
  
        while (position <= dataset.number) {
          let index = position-1;
          if(this.athleteList[index]){
            this.kumiteMatches.push({
              position: position, 
              id: this.athleteList[index].id, 
              name: this.athleteList[index].athlete.cognome + ' ' + this.athleteList[index].athlete.nome, 
              club:this.athleteList[index].athlete.club.club_name
            })
          }else{
            this.kumiteMatches.push({
              position: position, 
              id: null, 
              name: null, 
              club: null
            })
          }
          
          position++;
        }
  
        this.kumiteMatches = this.sortMarkets(this.kumiteMatches, dataset.positions);
  
        const datasetTemp: Dataset = {
          title: 'Kumite',
          type: 'single_elimination',
          roster: this.kumiteMatches,
        };
  
        this.dettaglio = true;
      
        process(datasetTemp).then((data) =>
          {
              let teams = [];
              let results = [];
              results.push([]);
  
              data.matches.forEach(match => {
  
                if(match.round_id == 0){

                  let opponent1 = null;
                  let opponent2 = null;
                  let result = [null,null];
    
                  if(match.opponent1 && match.opponent1.id){
                    let firstAthlete = this.kumiteMatches.find(x => x.id == match.opponent1.id);
                    opponent1 = firstAthlete.name + ' - ' + firstAthlete.club;
                  }
    
                  if(match.opponent2 && match.opponent2.id){
                    let firstAthlete = this.kumiteMatches.find(x => x.id == match.opponent2.id);
                    opponent2 = firstAthlete.name + ' - ' + firstAthlete.club;
                  }
    
                  teams.push([opponent1, opponent2]);
                  results[0].push(result);

                }       
                
              });
              console.log(teams);
  
              function render_fn(container, data, score, state) {
                switch(state) {
                  case "empty-bye":
                    container.append("--")
                    return;
                  case "empty-tbd":
                    container.append("--")
                    return;
               
                  case "entry-no-score":
                  case "entry-default-win":
                  case "entry-complete":
                    container.append(data)
                    return;
                }
              }
  
              function edit_fn(container, data, doneCb) {
                return;
              }
  
              var customData = {
                teams : teams,
                results : []
              }

              jQuery('#minimal').bracket({
                teamWidth: 300,
                scoreWidth: 45,
                matchMargin: 70,
                roundMargin: 70,
                disableHighlight: true,
                skipConsolationRound: true,
                init: customData,
                decorator:{edit: edit_fn, render: render_fn}
            })
  
          }
        );
      }

      
      


    });
  }

  sortMarkets(array, sortArray){
    return [...array].sort(
      (a, b) => sortArray.indexOf(a.position) - sortArray.indexOf(b.position)
    )
  }

  backTocategory(){
    this.dettaglio = false;
  }

}





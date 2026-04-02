import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { LocalStorageService } from 'angular-2-local-storage';
import { ToastrService } from 'ngx-toastr';
import { MyAthletesService } from './my-athletes.service';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { DatePipe } from '@angular/common';
import { HomeService } from '../home/home.service';
import * as Constants from '../../constants';
import { Subscription, interval } from 'rxjs';


@Component({
  selector: 'my-athletes',
  templateUrl: './my-athletes.html',
  styleUrls: ['./my-athletes.scss'],
  encapsulation: ViewEncapsulation.None
})


export class MyAthletesComponent {

  // Sostituiamo listaIscritti con groupedIscritti
  public groupedIscritti: any[] = [];

  public listaIscritti = [];
  public idSelezionati = [];
  public listaSquadre = [];
  public datiSocieta;
  public competition;
  public modalRef: NgbModalRef;
  datePipe = new DatePipe("en-US");
  public CONST = Constants;

  // Gestione dell'aggiornamento automatico
  private pollingSubscription: Subscription;

  constructor(
    public toastrService: ToastrService,
    public storageService: LocalStorageService,
    public myAthletesService: MyAthletesService,
    public homeService: HomeService,
    public modalService: NgbModal
  ) {

    this.datiSocieta = this.storageService.get('currentUser');
    this.competition = this.storageService.get('currentCompetition');
  }



  //=========================================================================================================================================================
  // INIT                                           
  //=========================================================================================================================================================

  ngOnInit() {

    this.homeService.getDatiGara(this.competition.id).subscribe((response:any) => {
      this.competition = response.result.data;
      
      this.storageService.set('currentCompetition', this.competition);
    });

    // 1. Caricamento iniziale degli iscritti
    this.loadIscritti();

    // 2. Imposta il Polling: si aggiorna ogni 30 secondi
    this.pollingSubscription = interval(30000).subscribe(() => {
        this.loadIscritti();
    });


  }

  ngOnDestroy() {
    if (this.pollingSubscription) {
        this.pollingSubscription.unsubscribe();
    }
  }

  // NUOVA FUNZIONE: Carica e raggruppa gli iscritti per Categoria + Specialità
  loadIscritti() {
    this.myAthletesService.getListaIscritti({ club_id: this.datiSocieta.id, competition_id: this.competition.id }).subscribe((response: any) => {
      let iscritti = response.result.data;
      let newGrouped = [];

      if (iscritti) {
        for (let item of iscritti) {
          
          // Costruiamo il nome completo della categoria per il raggruppamento
          let groupTitle = `${item.id} - ${item.specialita} - ${item.categoria} ${item.grado}`;
          if (item.sesso) groupTitle += ` ${item.sesso}`;
          if (item.cat_peso) groupTitle += ` ${item.cat_peso}`;

          // Controlliamo se questo gruppo esiste già nell'array temporaneo
          let existingGroup = newGrouped.find(g => g.titolo === groupTitle);

          if (!existingGroup) {
            // Creiamo un nuovo gruppo se non esiste
            existingGroup = {
              titolo: groupTitle,
              tatami: item.tatami ? item.tatami.replace(/T/ig, '').trim() : null,
              tatami_status: item.tatami_status,
              isNewTatami: false,
              atleti: []
            };
            newGrouped.push(existingGroup);
          }

          // Inseriamo l'atleta all'interno del gruppo
          existingGroup.atleti.push({
            id: item.athlete_id,
            nome: item.cognome + ' ' + item.nome,
            data_nascita: item.data_nascita
          });
        }
      }

      // Logica di lampeggio (Blinking) per i nuovi Tatami
      if (this.groupedIscritti && this.groupedIscritti.length > 0) {
          newGrouped.forEach(newGroup => {
              const oldGroup = this.groupedIscritti.find(g => g.titolo === newGroup.titolo);
              // Se c'è un tatami e prima non c'era, o se è cambiato numero
              if (oldGroup && newGroup.tatami && newGroup.tatami !== oldGroup.tatami) {
                  newGroup.isNewTatami = true;
                  setTimeout(() => { newGroup.isNewTatami = false; }, 5000);
              }
          });
      }

      this.groupedIscritti = newGrouped;
    });
  }
}



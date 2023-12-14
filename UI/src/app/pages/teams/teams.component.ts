import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { LocalStorageService } from 'angular-2-local-storage';
import { ToastrService } from 'ngx-toastr';
import { TeamsService } from './teams.service';


@Component({
  selector: 'app-teams',
  templateUrl: './teams.component.html',
  styleUrls: ['./teams.component.scss'],
  encapsulation: ViewEncapsulation.None
})
export class TeamsComponent implements OnInit {
  public atleti: any[];
  public atletiRim: any[];
  public squadre: any[];
  public cinture: any[];
  public categorie: any[];
  public iscrizioniChiuse: boolean;
  public currentCompetition: any;
  //public showCintureCategorie: boolean = false;

  //================= da controllare ====================
  public squadraCorrente: Squadra;
  public listaIscritti = [];
  public listaNonSelezionati = [];
  public listaSquadre = [];
  public datiSocieta;
  idSelezionati: string[];

  constructor(
    public toastrService: ToastrService,
    public storageService: LocalStorageService,
    public teamsService: TeamsService
  ) {

    this.datiSocieta = this.storageService.get('currentUser');
    this.iscrizioniChiuse = this.storageService.get('iscrizioniChiuse');
    this.currentCompetition = this.storageService.get('currentCompetition');

    this.squadraCorrente = {
      id: '',
      club_id: this.datiSocieta.id,
      competition_id: this.currentCompetition.id,
      categoria: '',
      grado: '',
      componenti: [],
      specialita: '',
      nComponenti: 0
    };

    this.squadre = [];

  }

  // -----------------------------------------------------------------------------------------------------
  // @ Inizializzazione
  // -----------------------------------------------------------------------------------------------------


  ngOnInit() {
    //All'inizio chiamo getListasquadre e GetListaIscritti.

    this.teamsService.getCategorieCinture(this.currentCompetition.codiceTipoCategorie).subscribe((response: any) => {
      console.log(response);
      if (response.result.success) {
        this.categorie = response.result.data.categorie;
        this.cinture = response.result.data.cinture;
      }
    });

    this.teamsService.getListaSquadre({ 'competition_id': this.currentCompetition.id, 'club_id': this.datiSocieta.id   }).subscribe((response: any) => {
      console.log(response);
      this.listaSquadre = [];
      this.listaIscritti = [];
      this.listaNonSelezionati = [];
      this.idSelezionati = [];

      if (response.result.success) {
        for (let team of response.result.data) {
          team.componenti = JSON.parse(team.componenti);

          let componentiSquadra = " ";
          for (let componente of team.componenti) {
            if (componente.checked) {
              this.idSelezionati.push(componente.id);
              if (componentiSquadra == " ") {
                componentiSquadra = componente.nome;
              } else {
                componentiSquadra = componentiSquadra + ", " + componente.nome;
              }


            }

          }
          team.componentiSquadra = componentiSquadra;
          this.listaSquadre.push(team);
        }

        console.log(this.listaSquadre);
      }

      this.teamsService.getListaIscritti({ 'competition_id': this.currentCompetition.id, 'club_id': this.datiSocieta.id  }).subscribe((response: any) => {

        console.log(response);

        if (response.result.success) {
          for (let atleta of response.result.data) {
            /* 
             if ((prova.label == "Kata Squadre" && prova.checked) || (prova.label == "Krav Maga" && prova.checked)) {
               */
            if (atleta.specialita == "Kata Squadre") {
              if (this.idSelezionati.indexOf(atleta.athlete_id) == -1) {
                atleta.selected = false;
              } else {
                atleta.selected = true;
              }
              this.listaIscritti.push(atleta);

            }

          }
        }

/*
        if (response.result.success) {
          for (let atleta of response.result.data) {
            atleta.prove_scelte = JSON.parse(atleta.prove_scelte);
            for (let prova of atleta.prove_scelte) {
              if ((prova.label == "Kata Squadre" && prova.checked) || (prova.label == "Krav Maga" && prova.checked)) {

                if (this.idSelezionati.indexOf(atleta.id) == -1) {
                  atleta.selected = false;
                } else {
                  atleta.selected = true;
                }
                this.listaIscritti.push(atleta);
              }
            }

          }
        }*/
      });
    });
  }

  /*selectSpecialita(){
    if(this.squadraCorrente.specialita == 'Kata'){
      this.showCintureCategorie = true;
    }else{
      this.showCintureCategorie = false;
      this.squadraCorrente.categoria = "";
      this.squadraCorrente.grado = "";
      this.getAtleti();
    }
  }*/

  getAtleti() {
    console.log(this.squadraCorrente);
    if (this.squadraCorrente.categoria != "" && this.squadraCorrente.grado != "") {

     /* let catTemp = this.squadraCorrente.categoria.split('/');
      let gradoTemp = this.squadraCorrente.grado.split('/');
      console.log(catTemp);
      console.log(gradoTemp);*/

      this.squadraCorrente.componenti = [];

      for (let atleta of this.listaIscritti) {

        if (atleta.grado == this.squadraCorrente.grado && atleta.categoria == this.squadraCorrente.categoria) {

          let datiAtleta = {
            id: atleta.athlete_id,
            nome: atleta.cognome + " " + atleta.nome,
            checked: false
          }

          this.squadraCorrente.componenti.push(datiAtleta);
        }
      }

      /*for (let atleta of this.listaIscritti) {

        var cintura = gradoTemp.find(function (cintura) { return cintura == atleta.grado; });
        var categoria = catTemp.find(function (categoria) { return categoria == atleta.categoria.split(" ")[0]; });
        console.log(cintura);
        console.log(categoria);
        if (cintura && categoria) {
          atleta.checked = false;

          this.squadraCorrente.componenti.push(atleta);
        }
      }*/

    }
   /* else{

      this.squadraCorrente.componenti = [];

      for (let atleta of this.listaIscritti) {

        for(let prova of atleta.prove_scelte){
          if(prova.label== 'Krav Maga'){
            atleta.checked = false;

            this.squadraCorrente.componenti.push(atleta);
            break;
          }
        }

      }

    }*/
    console.log(this.squadraCorrente);

  }

  checkNumber() {
    let count = 0;

    for (let component of this.squadraCorrente.componenti) {
      if (component.checked) {
        count++;
      }
    }

    this.squadraCorrente.nComponenti = count;
  }

 // -----------------------------------------------------------------------------------------------------
  // @ Salvataggio squadra
  // -----------------------------------------------------------------------------------------------------

  saveSquadra() {

    let squadraDoppia = false;
    let ids = [];
    console.log(this.squadraCorrente);
    for (let atleta of this.squadraCorrente.componenti) {
      if (atleta.checked) {
        ids.push(atleta.id);
      }
    }
    console.log(this.listaSquadre);
    if (this.listaSquadre.length != 0) {
      for (let squadra of this.listaSquadre) {
        let temIds = [];

        for (let atleta of squadra.componenti) {
          console.log(atleta);
          if (atleta.checked) {
            temIds.push(atleta.id);
          }
        }
        console.log(ids);
        console.log(temIds);
        if (ids[0] == temIds[0] && ids[1] == temIds[1] && ids[2] == temIds[2] && squadra.id != this.squadraCorrente.id) {
          squadraDoppia = true;
        }
      }
    }

    if (squadraDoppia) {
      this.toastrService.error('ATTENZIONE! Si sta tentando di inserire una squadra doppia');

    } else {

      this.teamsService.saveSquadra(this.squadraCorrente).subscribe((response: any) => {
        //console.log(response);
        if (response.result.success) {
          location.reload();
        }

      });
    }

  }

    // -----------------------------------------------------------------------------------------------------
  // @ Delete squadra
  // -----------------------------------------------------------------------------------------------------

  deleteSquadra(squadra) {
    this.teamsService.deleteSquadra(squadra).subscribe((response: any) => {
      console.log(response);
      if (response.result.success) {
        location.reload();
      }

    });
  }

  editSquadra(squadra) {
    this.squadraCorrente = squadra;
  }

}

declare interface Squadra {
  id: string;
  club_id: string;
  competition_id: string;
  categoria: string;
  grado: string;
  componenti: any[];
  specialita: string;
  nComponenti: number;
}

declare interface Atleta {
  id: string;
  registered_club_id: string;
  competition_id: string;
  nome: string;
  grado: string;
  categoria: string;
  checked: boolean;
}

import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { LocalStorageService } from 'angular-2-local-storage';
import { ToastrService } from 'ngx-toastr';
import { RiepilogoService } from './riepilogo.service';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { DatePipe } from '@angular/common';
import { HomeService } from '../home/home.service';
import { Router } from '@angular/router';
import * as Constants from '../../constants';

import jsPDF from 'jspdf'
import autoTable from 'jspdf-autotable'


@Component({
  selector: 'riepilogo',
  templateUrl: './riepilogo.html',
  styleUrls: ['./riepilogo.scss'],
  encapsulation: ViewEncapsulation.None
})


export class RiepilogoComponent {

  public listaIscritti = [];
  public idSelezionati = [];
  public listaSquadre = [];
  public datiSocieta;
  public dichiarazione: boolean = false;
  public competition;
  public modalRef: NgbModalRef;
  datePipe = new DatePipe("en-US");
  public note = '';
  public atletiSpaiati: boolean = false;
  public CONST = Constants;

  constructor(
    public toastrService: ToastrService,
    public storageService: LocalStorageService,
    public riepilogoService: RiepilogoService,
    public homeService: HomeService,
    public modalService: NgbModal,
    public router: Router
  ) {

    this.datiSocieta = this.storageService.get('currentUser');
    this.competition = this.storageService.get('currentCompetition');
    this.competition.richieste = JSON.parse(this.competition.richieste);
  }



  //=========================================================================================================================================================
  // INIT                                           
  //=========================================================================================================================================================

//TODO: da verificare
  ngOnInit() {

    this.homeService.getDatiGara(this.competition.id).subscribe((response:any) => {
      this.competition = response.result.data;
      
      this.storageService.set('currentCompetition', this.competition);
    });

    this.riepilogoService.getListaIscritti({ club_id: this.datiSocieta.id, competition_id: this.competition.id }).subscribe((response: any) => {
      console.log(response);
      let iscritti = response.result.data;
      this.listaIscritti = [];
      if (iscritti) {
        for (let item of response.result.data) {
          let index = this.listaIscritti.findIndex(x => x.id === item.athlete_id);
          if (index >= 0) {
            this.listaIscritti[index].prove += ' - ' + item.specialita + ' ' + item.categoria + ' ' + item.grado;
            this.listaIscritti[index].codiciProve.push(item.id);
          } else {
            let iscritto = {
              id: item.athlete_id,
              nome: item.cognome + ' ' + item.nome,
              cod_fiscale: item.cod_fiscale,
              data_nascita: item.data_nascita,
              n_tessera: item.n_tessera,
              categoria: item.categoria,
              grado: item.grado,
              sesso: item.sesso,
              peso: item.peso,
              prove: item.specialita,
              codiciProve:[item.id]
            };
            this.listaIscritti.push(iscritto);
          }
        }
      }
      console.log(this.listaIscritti);
    });

    this.riepilogoService.getListaSquadre({ 'competition_id': this.competition.id, 'club_id': this.datiSocieta.id }).subscribe((response: any) => {
      this.listaSquadre = [];
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
    });

  }

  openModal(modalContent) {
    this.modalRef = this.modalService.open(modalContent, { container: '.app' });
  }

  public closeModal() {
    this.modalRef.close();
  }

  public confirm() {
    //chiamare service con creazione excel e invio dati
    this.closeModal();
  }


  //=========================================
  // INVIO DATI                                                                                
  //=========================================
//TODO: tutto da rivedere!! EXCEL + PDF

  inviaDati() {


    let pdf = this.creaPdfIscrizioni();
    let autodichiarazione = this.creaPdfCertificati();

    let data;
    data = this.creaExcelCsen();

  /*  if (this.datiGara.richieste.tabsManagement) {
      data = this.creaExcelCsen();
      console.log(data);
    } else {
      data = this.creaExcelSocieta();
      console.log(data);
    }*/


    var params = {
      'datiSocieta': this.datiSocieta,
      'competition':this.competition,
      'pdf': pdf,
      'autodichiarazione': autodichiarazione,
      'datiCsv': data,
      'note': this.note
    };

    this.riepilogoService.inviaDati(params).subscribe((response: any) => {

    });

    this.closeModal();
    this.toastrService.success('Dati inviati con successo');
    this.router.navigate(['/home']);

  }

  //=============================================================================
  // PDF
  //=============================================================================

  //crea pdf con elenco iscritti
  creaPdfIscrizioni() {

    let doc = new jsPDF({
      orientation: 'landscape'
    });
    doc.setFontSize(12);

    var columns = ["Nr.", "Cognome e Nome", "Data di nascita", "Sesso", "Cod. Fiscale", "Tessera CSEN", "Peso", "Grado", "Categoria", "Prove"];
    var rows = [];
    let count = 1;
    for (let atleta of this.listaIscritti) {
      let record = [count, atleta.nome, atleta.data_nascita, atleta.sesso, atleta.cod_fiscale == null ? '' : atleta.cod_fiscale, atleta.n_tessera == null ? '' : atleta.n_tessera, atleta.peso, atleta.grado, atleta.categoria, atleta.prove];
      rows.push(record);
      count++;
    }

    if (this.listaSquadre.length > 0) {
      rows.push([" ", " ", " ", " ", " ", " ", " "]);
      rows.push([" ", " ", " ", " ", " ", " ", " "]);
      rows.push(["COMPONENTI SQUADRA", " ", " ", " ", " ", " ", " ", "CATEGORIA"]);

      for (let squadra of this.listaSquadre) {
        console.log(squadra);
        let recordSquadra = [squadra.componentiSquadra, " ", " ", " ", " ", " ", " ", squadra.categoria];
        rows.push(recordSquadra);
      }
    }
    var nome = 'ELENCO ATLETI ' + this.datiSocieta.club_name;

    doc.text(nome, 15, 10);

    doc.setFontSize(10);
    autoTable(doc, {
      margin: { top: 15 },
      head: [columns],
      body: rows
    });

    return btoa(doc.output());
  }

  //crea PDF  con autocertificazione certificati medici

  creaPdfCertificati() {
    var doc = new jsPDF("p", "mm", "a4");

    //let imgData = this.getBase64ImageFromURL("assets/img/logo/LogoCSEN.png");

    var lMargin = 15; //left margin in mm
    var rMargin = 15; //right margin in mm
    var pdfInMM = 210;  // width of A4 in mm
    var pageCenter = pdfInMM / 2;


    let img = new Image();
    img.src = "assets/img/logo/LogoCSEN.png";
    let imgData = this.getBase64Image(img);

    doc.setFont("times");
    doc.addImage(imgData, 'PNG', 80, 20, 40, 40);
    doc.setFontSize(17);

    doc.text('MODULO CERTIFICATI MEDICI', 55, 75);
    doc.text('MANIFESTAZIONE: ' + this.competition.nome_gara, 15, 110);

    doc.setFontSize(15);

    var paragraph = 'Il sottoscritto  ' + this.datiSocieta.club_manager + ', Responsabile della Società ' + this.datiSocieta.club_name + ', dichiara: ';
    var lines = doc.splitTextToSize(paragraph, (pdfInMM - lMargin - rMargin));
    doc.text(lines, 15, 123);

    var paragraph2 = '\u2022' + '  '+' che gli atleti partecipanti alla manifestazione in oggetto sono tutti in possesso del certificato medico;';
    var lines2 = doc.splitTextToSize(paragraph2, (pdfInMM - lMargin - rMargin));

    doc.text(lines2, 20, 133);

    var paragraph3 = '\u2022' + '  '+' di essere in possesso della liberatoria all’utilizzo delle immagini degli atleti;';
    var lines3 = doc.splitTextToSize(paragraph3, (pdfInMM - lMargin - rMargin));

    doc.text(lines3, 20, 145);

    var paragraph4 = '\u2022' + '  '+' di essere in possesso della copia dei documenti d’identità dell’atleta e dei genitori (in caso di atleti minorenni).';
    var lines4 = doc.splitTextToSize(paragraph4, (pdfInMM - lMargin - rMargin));

    doc.text(lines4, 20, 153);

    doc.text('Data e Luogo', 20, 250);
    doc.text('Firma', 155, 250);
    doc.save('test.pdf');
    return btoa(doc.output());
  }

  getBase64Image(img: HTMLImageElement) {
    var canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0);
    return canvas.toDataURL("image/png");
  }


  //====================================================================================================
  //                                                                                                    
  //  #####  ##    ##   ####  #####  ##             ####   #####    ####  ##  #####  ######    ###    
  //  ##      ##  ##   ##     ##     ##            ##     ##   ##  ##     ##  ##       ##     ## ##   
  //  #####    ####    ##     #####  ##             ###   ##   ##  ##     ##  #####    ##    ##   ##  
  //  ##      ##  ##   ##     ##     ##               ##  ##   ##  ##     ##  ##       ##    #######  
  //  #####  ##    ##   ####  #####  ######        ####    #####    ####  ##  #####    ##    ##   ##  
  //                                                                                                    
  //====================================================================================================


  creaExcelSocieta() {
    let data = [
      {
        name: 'Societa: ' + this.datiSocieta.club_name
      },
      {
        name: "Responsabile: " + this.datiSocieta.club_manager
      },
      {
        name: "Email: " + this.datiSocieta.mail
      },
      {
        name: "Telefono: " + this.datiSocieta.telefono
      },
      {
        name: "Coach: " + this.datiSocieta.coach
      },
      {
        name: 'COGNOME E NOME',
        categoria: "CATEGORIA",
        dataNascita: "DATA DI NASCITA",
        sesso: "SESSO",
        peso: "PESO",
        grado: "GRADO",
        percorso: "PERCORSO",
        palloncino: "PALLONCINO",
        kata: "KATA",
        squadre: "KATA SQUADRE",
        kumite: "KUMITE"
      }
    ];

    for (let iscritto of this.listaIscritti) {

      let percorso = iscritto.prove_scelte.find(obj => { return obj.label == "Percorso" });
      let palloncino = iscritto.prove_scelte.find(obj => { return obj.label == "Palloncino" });
      let kata = iscritto.prove_scelte.find(obj => { return obj.label == "Kata" });
      let squadre = iscritto.prove_scelte.find(obj => { return obj.label == "Squadre" });
      let kumite = iscritto.prove_scelte.find(obj => { return obj.label == "Kumite" });

      data.push({
        name: iscritto.nome,
        categoria: iscritto.categoria,
        dataNascita: this.datePipe.transform(iscritto.data_nascita, 'dd/MM/yyyy'),
        sesso: iscritto.sesso,
        peso: iscritto.peso,
        grado: iscritto.grado,
        percorso: (percorso && percorso.checked) ? "X" : '',
        palloncino: (palloncino && palloncino.checked) ? "X" : '',
        kata: (kata && kata.checked) ? "X" : '',
        squadre: (squadre && squadre.checked) ? "X" : '',
        kumite: (kumite && kumite.checked) ? "X" : ''
      });

    }

    if (this.listaSquadre.length > 0) {
      for (let squadra of this.listaSquadre) {
        data.push({
          name: '#SQ#',
          categoria: squadra.categoria,
          dataNascita: squadra.componentiSquadra
        });
      }
    }

    return data;

  }

  //=================================================================================
  //                                                                                 
  //  #####  ##    ##   ####  #####  ##             ####   ####  #####  ##     ##  
  //  ##      ##  ##   ##     ##     ##            ##     ##     ##     ####   ##  
  //  #####    ####    ##     #####  ##            ##      ###   #####  ##  ## ##  
  //  ##      ##  ##   ##     ##     ##            ##        ##  ##     ##    ###  
  //  #####  ##    ##   ####  #####  ######         ####  ####   #####  ##     ##  
  //                                                                                 
  //=================================================================================


  creaExcelCsen() {
    console.log(this.datiSocieta);
    let data = [
      {
        name: 'Societa: ' + this.datiSocieta.club_name
      },
      {
        name: "Responsabile: " + this.datiSocieta.club_manager
      },
      {
        name: "Codice Fiscale: " + this.datiSocieta.CF
      },
      {
        name: "Email: " + this.datiSocieta.mail
      },
      {
        name: "Telefono: " + this.datiSocieta.telefono
      },
      {
        name: "Coach: " + this.datiSocieta.coach
      },
      {
        name: 'COGNOME E NOME',
        prove: "PROVE",
        peso: "PESO",
        dataNascita: "DATA DI NASCITA",
        societa: "SOCIETA"
        /* categoria: "CATEGORIA",
        
        grado: "GRADO",
        sesso: "SESSO",    */     
      }
    ];

    for (let iscritto of this.listaIscritti) {
      console.log(this.listaIscritti);

      for (let prova of iscritto.codiciProve) {
        data.push({
          name: iscritto.nome,
          prove: prova,
          peso: iscritto.peso,
          dataNascita: iscritto.data_nascita,
          societa: this.datiSocieta.club_name
          
        });

      }
    }

    if (this.listaSquadre.length > 0) {
      for (let squadra of this.listaSquadre) {
        data.push({
          name: '#SQ#',
          prove: squadra.categoria,
          dataNascita: squadra.componentiSquadra
        });
      }
    }

    return data;
  }



}


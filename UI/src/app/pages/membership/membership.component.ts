import { Component, OnInit, ViewEncapsulation} from '@angular/core';
import { FormGroup, FormBuilder, Validators} from '@angular/forms';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import { MembershipService } from './membership.service';
import { LocalStorageService } from 'angular-2-local-storage';
import { HomeService } from '../home/home.service';
import * as Constants from '../../constants';
 
@Component({
  selector: 'app-membership',
  templateUrl: './membership.component.html',
  styleUrls: ['./membership.component.scss'],
  encapsulation: ViewEncapsulation.None
})

export class MembershipComponent implements OnInit {

  public iscritti: any[];
  public athletes: any[];
  public searchText: string;
  public type: string = 'list';
  public modalRef: NgbModalRef;

  public datiSocieta: any;
  public steps: any[];
  public athleteForm: FormGroup;
  public weightForm: FormGroup;
  public atleta: any = {};
  public showConfirm: boolean;
  public confirmed: boolean;
  public prove: any[];
  public show = false;
  public categoria = {description: ""};
  public erroreCategoria = false;
  public errorePeso = false;
  public competition: any;
  public federationsList = [];
  public enableTessera = false;
  public CONST = Constants;
  public athletestNumber:number = 0;
  public corsiAthletestNumber: any [];
  public athletestMaxNumber:number = 0;
  public beltList = {};

  config = {
    search:true,
    height: '400px', //height of the list so that if there are more no of items it can show a scroll defaults to auto. With auto height scroll will never appear
    placeholder:'Recupera atleti inseriti in gare precedenti', // text to be displayed when no item is selected defaults to Select,
    noResultsFound: 'Nessun risultato', // text to be displayed when no items are found while searching
    searchPlaceholder:'Cerca', // label thats displayed in search input,
    displayKey: 'nominativo', 
    searchOnKey: 'nominativo', // key on which search should be performed this will be selective search. if undefined this will be extensive search on all keys
    inputDirection: 'lrtl', // the direction of the search input can be rtl or ltr(default)
    enableSelectAll: false, // enable select all option to select all available items, default is false
  }
 
 
  
  constructor(public fb: FormBuilder,
    public toastrService: ToastrService,
    public storageService: LocalStorageService,
    public membershipService: MembershipService,
    public modalService: NgbModal,
    public homeService: HomeService) {

      //TODO: da inserire chiamata per controllo in tempo reale
      this.athletestMaxNumber = this.storageService.get('athletestMaxNumber');

      /*elementi iscrizione*/
      this.steps = [
        { name: 'dati atleta', icon: 'fa-lock', active: true, valid: false, hasError: false },
        { name: 'selezione prove', icon: 'fa-user', active: false, valid: false, hasError: false }
      ]

      this.prove = [];

      this.athleteForm = this.fb.group({
      'id': [''],
        'name': ['', Validators.required],
        'surname': ['', Validators.required],
        'birthdate_day': ['', Validators.required],
        'birthdate_month': ['', Validators.required],
        'birthdate_year': ['', Validators.required],
        'cod_fiscale': ['', Validators.required],
        'federation': ['', Validators.required],
        'n_tessera': [''],
        'sesso': ['', Validators.required],
        'cintura': ['', Validators.required]
      });

      this.weightForm = this.fb.group({
        'peso': ['', Validators.required],
        'tesserino': ['']
      });
  }

  ngOnInit() {

    this.datiSocieta = this.storageService.get('currentUser');
    this.competition = this.storageService.get('currentCompetition');

    let errorMessage = 'ATTENZIONE! Errore nel caricamento dei dati. Riprovare più tardi, se il problema persiste rivolgersi a info@iscrizionicsenveneto.it';

    this.membershipService.getBeltList().subscribe((response: any) => {
      if (response.result.success) {
        this.beltList = response.result.data;
      } else {
        this.toastrService.error(errorMessage);
      }
    });
    
    this.membershipService.getFederationsList().subscribe((response: any) => {
      if (response.result.success) {
        this.federationsList = response.result.data;
      } else {
        this.toastrService.error(errorMessage);
      }
    });

    this.membershipService.getAthleteList({ club_id: this.datiSocieta.id }).subscribe((response: any) => {
      this.athletes = response.result.data;
      this.athletes.forEach(element => {
        element.nominativo = element.cognome + ' ' + element.nome;
      });
    })
    this.membershipService.getListaIscritti({ club_id: this.datiSocieta.id, competition_id: this.competition.id }).subscribe((response: any) => {

      let listaIscritti = response.result.data;
      this.iscritti = [];
      if (listaIscritti) {
        for (let item of response.result.data) {
          let index = this.iscritti.findIndex(x => x.id === item.athlete_id);
          if (index >= 0) {
            this.iscritti[index].prove += ' - ' + item.specialita;
          } else {
            let iscritto = {
              id: item.athlete_id,
              nome: item.cognome + ' ' + item.nome,
              cod_fiscale: item.cod_fiscale,
              data_nascita: item.data_nascita,
              categoria: item.categoria,
              grado: item.grado,
              sesso: item.sesso,
              peso: item.peso,
              prove: item.specialita
            };
            this.iscritti.push(iscritto);
          }
        }
      }
    });

    //TODO: da togliere e inserire commonservices
    this.homeService.getTotaleIscrizioni({competition_id: this.competition.id, competition_type: this.competition.type }).subscribe((response: any) => {
      
      if(this.competition.type == Constants.EventType.Stage){
        this.corsiAthletestNumber =[];

        response.result.data.forEach(prova => {
          prova.closed = false;
          
          if(prova.n_athletes == prova.atleti_max){
            prova.closed =true;
          }
          
        });

        this.corsiAthletestNumber =  response.result.data;

      }else{
          this.athletestNumber = response.result.data;

          if(this.athletestNumber == this.athletestMaxNumber){
            this.competition.comp_status = this.CONST.CompetitionState.RegistrationClosedFull;
            this.membershipService.closeRegistrations({competition_id: this.competition.id, comp_status: this.CONST.CompetitionState.RegistrationClosedFull}).subscribe((responseClose:any)=> {
            });
          }
      }
      
    });

  }

  selectionChanged(event){
    this.atleta = event.value;

    let birthdate = this.atleta.data_nascita.split('/');

    this.athleteForm = this.fb.group({
      'id': [this.atleta.id],
      'name': [this.atleta.nome, Validators.required],
      'surname': [this.atleta.cognome, Validators.required],
      'birthdate_day': [birthdate[0], Validators.required],
      'birthdate_month': [birthdate[1], Validators.required],
      'birthdate_year': [birthdate[2], Validators.required],
      'cod_fiscale': [this.atleta.cod_fiscale, Validators.required],
      'federation': [this.atleta.federation_id, Validators.required],
      'n_tessera': [this.atleta.n_tessera],
      'sesso': [this.atleta.sesso, Validators.required],
      'cintura': [this.atleta.grado, Validators.required]
    });

    this.checkCategory();
  }

  checkCategory() {
    let params = {
      'anno': this.athleteForm.value.birthdate_year,
      'categorycode': this.competition.codiceTipoCategorie,
      'competition_date': this.competition.data_gara    
    }

    this.membershipService.getAgeCategory(params).subscribe((response: any) => {
      if (response.result.success) {
        this.categoria = response.result.data;
      } else {
        this.categoria = {description: ""};
      }
    });
  }

  showTessera() {
    if (this.athleteForm.value.federation == 1) {
      this.enableTessera = true;
    } else {
      this.enableTessera = false;
    }
  }

  public showPeso() {
    this.show = false;

    for (let item of this.prove) {
      if ((item.label == "Kumite" || item.label == "Kumite U12" || item.code == "BST-ISTR") && item.checked) {
        this.show = true;
        break;
      }
    }

  }

  public next() {
    let athleteForm = this.athleteForm;

    if (this.steps[this.steps.length - 1].active)
      return false;
    if (this.steps[0].active) {
      if ((athleteForm.valid || this.competition.type == Constants.EventType.Stage) && this.categoria.description != "") {
        this.erroreCategoria = false;

        let request = { anno: this.athleteForm.value.birthdate_year, agecategory: this.categoria, sesso: this.athleteForm.value.sesso, grado: this.athleteForm.value.cintura, categorycode: this.competition.codiceTipoCategorie, competition_type: this.competition.type };
        this.membershipService.getElencoProve(request).subscribe((response: any) => {

          if (response.result.success) {
            this.prove = [];
            for (let prova of response.result.data) {
              let closed = false;
              this.corsiAthletestNumber.forEach(element => {
                
                if (prova.code == element.code && element.closed) {
                  closed = true;
                }
                
              });
              this.prove.push({ label: prova.name, code: prova.code, checked: false, closed: closed });
            }
          }
          
          this.steps[0].active = false;
          this.steps[0].valid = true;
          this.steps[1].active = true;
          return true;


        });

      }
      else {
        this.steps[0].hasError = true;
        if (this.categoria.description == "") {
          this.erroreCategoria = true;
        }
      }
    }


    this.atleta.nome = this.athleteForm.value.name;
    this.atleta.cognome = this.athleteForm.value.surname;
    this.atleta.data_nascita = this.athleteForm.value.birthdate_day + '/' + this.athleteForm.value.birthdate_month + '/' + this.athleteForm.value.birthdate_year;
    this.atleta.cod_fiscale = this.athleteForm.value.cod_fiscale;
    this.atleta.federation_id = this.athleteForm.value.federation;
    this.atleta.n_tessera = this.athleteForm.value.n_tessera;
    this.atleta.sesso = this.athleteForm.value.sesso;
    this.atleta.grado = this.athleteForm.value.cintura;
    this.atleta.categoria = this.categoria.description;
  }
  /*pulsante per tornare indietro nel widget*/
  public prev() {
    if (this.steps[0].active)
      return false;
    this.steps.some(function (step, index, steps) {
      if (index != 0) {
        if (step.active) {
          step.active = false;
          steps[index - 1].active = true;
          return true;
        }
      }
    });
  }


  //===================================================================================================================================================
  // SAVE
  //===================================================================================================================================================

  public confirm() {

    //Controllo sia stata selezionata almeno una prova
    let provaScelta = false;
    for (let item of this.prove) {
      if (item.checked) {
        provaScelta = true;
        break;
      }
    }

    if (provaScelta) {

      let kumiteOk = false;
      let stageOk = false;

      switch (this.competition.type) {
        case Constants.EventType.Stage:
          stageOk = this.checkTesserino();
          break;
      
        default:
          kumiteOk = this.checkKumite();
          break;
      }      
      
      if (kumiteOk || stageOk) {

        this.errorePeso = false;
        //TODO: Controllare recupero corretto dati società e gara
        this.atleta.club_id = this.datiSocieta['id'];
        this.atleta.nome = this.athleteForm.value.name;
        this.atleta.cognome = this.athleteForm.value.surname;
        this.atleta.data_nascita = this.athleteForm.value.birthdate_day + '/' + this.athleteForm.value.birthdate_month + '/' + this.athleteForm.value.birthdate_year;
        this.atleta.cod_fiscale = this.athleteForm.value.cod_fiscale;
        this.atleta.federation_id = this.athleteForm.value.federation;
        this.atleta.n_tessera = this.athleteForm.value.n_tessera;
        this.atleta.sesso = this.athleteForm.value.sesso;
        this.atleta.grado = this.athleteForm.value.cintura;
        //this.atleta.categoria = this.categoria;
        this.atleta.peso = this.weightForm.value.peso;
        this.atleta.tesserino = this.weightForm.value.tesserino;

        let elencoProve = {
          'categorycode': this.competition.codiceTipoCategorie,
          'competition_id': this.competition.id,
          'specialita': [],
          'codes': []
        }
        for (let prova of this.prove) {
          if (prova.checked) {
            if(prova.code && prova.code != ''){
              elencoProve.codes.push(prova.code);
            }else{
              elencoProve.specialita.push(prova.label);
            }
           
          }
        }

        let atletaDoppio = false;
        let double = this.iscritti.filter(obj => {
          return obj.cod_fiscale ==this.atleta.cod_fiscale;
        });

        if(double && double.length > 0 && (this.atleta.id != double[0].id)){
          atletaDoppio = true;
        }

        if (!atletaDoppio) {
          this.membershipService.saveAtleta({ 'athlete': this.atleta, 'prove': elencoProve }).subscribe((response: any) => {
            if (response.result.success) {
              this.steps = [
                { name: 'dati atleta', icon: 'fa-lock', active: true, valid: false, hasError: false },
                { name: 'selezione prove', icon: 'fa-user', active: false, valid: false, hasError: false }
              ];
              this.prove = [];
              this.categoria = {description: ""};
              //this.annoNascita = '';
              this.closeModal();
              this.toastrService.success('Atleta salvato');
              location.reload();
            }
          });
        } else {
          this.toastrService.error("ATTENZIONE! E' già stato inserito un nominativo con qesto codice fiscale");
          this.confirmed = false;
        }

      }
      else {
        this.errorePeso = true;
        this.confirmed = false;
      }

    }
    else {
      this.confirmed = false;
    }


  }

  checkKumite(){
      var objKumite = this.prove.find(function (obj) { return obj.label == "Kumite"; });
      var objKumiteRagazzi = this.prove.find(function (obj) { return obj.label == "Kumite U12"; });

      var kumiteOk = false;
      var kumiteRagazziOk = false;

      if (objKumite == undefined || (objKumite != undefined && objKumite.checked && this.weightForm.value.peso != "" && this.weightForm.value.peso > 10) || (objKumite != undefined && !objKumite.checked)) {
        kumiteOk = true;
      }
      if (objKumiteRagazzi == undefined || (objKumiteRagazzi != undefined && objKumiteRagazzi.checked && this.weightForm.value.peso != "" && this.weightForm.value.peso > 10) || (objKumiteRagazzi != undefined && !objKumiteRagazzi.checked)) {
        kumiteRagazziOk = true;
      }

      return kumiteOk && kumiteRagazziOk;
  }

  checkTesserino(){
    var istruttori = this.prove.find(function (obj) { return obj.code == "BST-ISTR";});

    return (istruttori == undefined || (istruttori != undefined && istruttori.checked && this.weightForm.value.tesserino != "" && this.weightForm.value.tesserino != null) || (istruttori != undefined && !istruttori.checked));
        
  }

  public toggle(type) {
    this.type = type;
  }

  //=======================================================================
  //                                                                       
  //  #####  ####    ##  ######            ##     ##  #####  ##      ##  
  //  ##     ##  ##  ##    ##              ####   ##  ##     ##      ##  
  //  #####  ##  ##  ##    ##    ########  ##  ## ##  #####  ##  ##  ##  
  //  ##     ##  ##  ##    ##              ##    ###  ##     ##  ##  ##  
  //  #####  ####    ##    ##              ##     ##  #####   ###  ###   
  //                                                                       
  //=======================================================================


  public openModal(modalContent, user) {
    this.erroreCategoria = false;
    this.steps = [
      { name: 'dati atleta', icon: 'fa-lock', active: true, valid: false, hasError: false },
      { name: 'selezione prove', icon: 'fa-user', active: false, valid: false, hasError: false }
    ];
    if (user) {
      this.atleta = this.athletes.filter(obj => {
        return obj.id == user.id
      });

      this.atleta = this.atleta[0];
      let birthdate = this.atleta.data_nascita.split('/');

      this.athleteForm = this.fb.group({
        'id': [this.atleta.id],
        'name': [this.atleta.nome, Validators.required],
        'surname': [this.atleta.cognome, Validators.required],
        'birthdate_day': [birthdate[0], Validators.required],
        'birthdate_month': [birthdate[1], Validators.required],
        'birthdate_year': [birthdate[2], Validators.required],
        'cod_fiscale': [this.atleta.cod_fiscale, Validators.required],
        'federation': [this.atleta.federation_id, Validators.required],
        'n_tessera': [this.atleta.n_tessera],
        'sesso': [this.atleta.sesso, Validators.required],
        'cintura': [this.atleta.grado, Validators.required]
      });
      this.showTessera();

      //TODO: controllare
      this.categoria.description = user.categoria;

      this.weightForm = this.fb.group({
        'peso': [this.atleta.peso, Validators.required],
        'tesserino': ['']
      });

      this.prove = [];
    }
    else {
      this.atleta = {};
      this.categoria = {description: ""};
      this.prove = [];
      this.athleteForm.reset();
      this.weightForm.reset();
    }
    this.modalRef = this.modalService.open(modalContent, { container: '.app', backdrop: 'static', size: 'lg' });

    this.modalRef.result.then((result) => {
      this.athleteForm.reset();
      this.weightForm.reset();
    }, (reason) => {
      this.athleteForm.reset();
      this.weightForm.reset();
    });
  }

  public closeModal() {
    this.modalRef.close();
  }

  public deleteUser(user) {
    this.membershipService.deteleAtleta({ 'athlete_id': user.id, 'competition_id': this.competition.id }).subscribe((response: any) => {

      if (response.result.success) {
        location.reload();
      }

    });
  }
  

}

import { Component, OnInit, ViewEncapsulation, Injectable } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { CompetitionDetailsService } from './competitionDetails.service';
import { environment } from '../../../environments/environment';
import { LocalStorageService } from 'angular-2-local-storage';
import { ToastrService } from 'ngx-toastr';
import { NgbDateParserFormatter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';


@Injectable()
export class CustomDateParserFormatter extends NgbDateParserFormatter {
  readonly DELIMITER = '/';
  parse(value: string): NgbDateStruct | null {
    if (value) {
      const date = value.split(this.DELIMITER);
      return { day: parseInt(date[0], 10), month: parseInt(date[1], 10), year: parseInt(date[2], 10) };
    }
    return null;
  }
  format(date: NgbDateStruct | null): string {
    return date ? date.day + this.DELIMITER + ('0' + date.month).slice(-2) + this.DELIMITER + date.year : '';
  }
}

@Component({
  selector: 'competitionDetails',
  templateUrl: './competitionDetails.component.html',
  styleUrls: ['./competitionDetails.scss'],
  providers: [
    { provide: NgbDateParserFormatter, useClass: CustomDateParserFormatter }
  ]
})
export class CompetitionDetailsComponent implements OnInit {
  public imgUrl: string = environment.IMAGES_WEBROOT;
  public competitionId: number;
  public currentCompetition: any = null;
  public editMode: boolean = false;
  public selectedSetCode: string = null;
  public previewCategories: any[] = [];
  public categorySets: any[] = [];


  constructor(
    private route: ActivatedRoute,
    private competitionDetailsService: CompetitionDetailsService,
    protected localStorageService: LocalStorageService,
    private toastr: ToastrService
  ) {}

  ngOnInit() {
    this.route.params.subscribe(params => {
      this.competitionId = params.id;
      this.getDatiGara();
      this.loadAvailableSets();
    });
  }

  // Funzione di utilità per trasformare stringa DB in oggetto Datepicker
  stringToNgbDate(dateString: string): NgbDateStruct | null {
    if (!dateString) return null;
    const date = new Date(dateString);
    return { year: date.getFullYear(), month: date.getMonth() + 1, day: date.getDate() };
  }

  // Funzione per trasformare oggetto Datepicker in stringa YYYY-MM-DD per DB
  ngbDateToString(date: NgbDateStruct): string {
    return date ? `${date.year}-${('0' + date.month).slice(-2)}-${('0' + date.day).slice(-2)}` : null;
  }

  getDatiGara() {
    this.competitionDetailsService.getDatiGara(this.competitionId).subscribe((res: any) => {
      this.currentCompetition = res.result.data;

      this.currentCompetition.data_apertura_dp = this.stringToNgbDate(res.result.data.apertura_iscrizioni);
      this.currentCompetition.data_gara_dp = this.stringToNgbDate(res.result.data.data_gara);
      this.currentCompetition.chiusura_iscrizioni_dp = this.stringToNgbDate(res.result.data.chiusura_iscrizioni);

      console.log(this.currentCompetition);
      // Aggiorniamo il local storage per i componenti figli
      this.localStorageService.set('currentCompetition', this.currentCompetition);
    });
  }

  loadAvailableSets() {
    this.competitionDetailsService.getAvailableCategorySets().subscribe((res: any) => {
        this.categorySets = res.result.data; // SELECT DISTINCT codiceTipoCategorie FROM categorycodes
    });
  }

  previewSet() {
    if (!this.selectedSetCode) {
        this.previewCategories = [];
        return;
    }
    // Recupera le categorie associate a quel codice per l'anteprima
    this.competitionDetailsService.getCategoriesBySet(this.selectedSetCode).subscribe((res: any) => {
        this.previewCategories = res.result.data;
    });
}

  assignExistingSet() {
      if (confirm(`Vuoi assegnare il set ${this.selectedSetCode} a questa gara? Le modifiche al set influenzeranno tutte le gare collegate.`)) {
          this.currentCompetition.codiceTipoCategorie = this.selectedSetCode;
          this.saveModifiche(); // Aggiorna la tabella competitions
      }
  }

  // Funzione per scaricare il set visualizzato in anteprima come CSV
  downloadCsvTemplate() {
      if (this.previewCategories.length === 0) return;

      const headers = ['id', 'categoria', 'agecategory_id', 'anno_min', 'anno_max', 'sesso', 'grado', 'cat_peso', 'peso_min', 'peso_max', 'specialita', 'costo_intero', 'costo_scontato', 'atleti_max'];
      
      // Creazione righe CSV
      const rows = this.previewCategories.map(cat => 
          headers.map(header => `"${cat[header] || ''}"`).join(',')
      );

      const csvContent = [headers.join(','), ...rows].join('\n');
      const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement("a");
      const url = URL.createObjectURL(blob);
      
      link.setAttribute("href", url);
      link.setAttribute("download", `Template_Set_${this.selectedSetCode}.csv`);
      link.style.visibility = 'hidden';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
  }

// Funzione per caricare il nuovo CSV modificato
  uploadNewSetCsv(event: any) {
      const file = event.target.files[0];
      if (file) {
          const newSetName = prompt("Inserisci un nome identificativo per questo nuovo set di categorie:");
          if (!newSetName) return;

          const formData = new FormData();
          formData.append('csvFile', file);
          formData.append('codiceTipoCategorie', newSetName);
          formData.append('competitionId', this.currentCompetition.id);

          this.competitionDetailsService.uploadCategoryCsv(formData).subscribe((res: any) => {
              if (res.result.success) {
                  this.toastr.success('Nuovo set caricato e assegnato con successo');
                  this.getDatiGara(); // Refresh dati gara
                  this.selectedSetCode = newSetName;
                  this.previewSet(); // Refresh anteprima
              } else {
                  this.toastr.error('Errore durante il caricamento del CSV');
              }
          });
      }
  }


  toggleEdit() {
    if (this.editMode) {
      this.saveModifiche();
    }
    this.editMode = !this.editMode;
  }

  onFileChange(event: any, tipo: 'locandina' | 'circolare') {
    const file = event.target.files[0];
    if (file) {
      this.toastr.info(`File ${tipo} pronto: ${file.name}`);
      // Logica futura: qui potresti settare il file su una proprietà temporanea per l'upload
    }
  }

  saveModifiche() {
    if (this.editMode) {

      if (this.currentCompetition.apertura_iscrizioni_dp) {
        this.currentCompetition.apertura_iscrizioni = this.ngbDateToString(this.currentCompetition.data_apertura_dp);
      }
      if (this.currentCompetition.chiusura_iscrizioni_dp) {
        this.currentCompetition.chiusura_iscrizioni = this.ngbDateToString(this.currentCompetition.chiusura_iscrizioni_dp);
      }
      if (this.currentCompetition.data_gara_dp) {
        this.currentCompetition.data_gara = this.ngbDateToString(this.currentCompetition.data_gara_dp);
      }
      
      this.competitionDetailsService.updateGara(this.currentCompetition).subscribe((res: any) => {
        if (res.result.success) {
          this.toastr.success('Dati aggiornati con successo');
          this.editMode = false;
        }
      });
    }
  }

  cambiaStatoGara(nuovoStato: number) {
    if (!this.currentCompetition.codiceTipoCategorie) {
      this.toastr.error('Impossibile pubblicare: associa prima un set di categorie.');
      return;
    }
    
    this.currentCompetition.comp_status = nuovoStato;
    this.saveModifiche();
  }
}
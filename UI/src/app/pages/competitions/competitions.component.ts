import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { environment } from '../../../environments/environment';
import { CompetitionService } from './competition.service';
import { FormBuilder } from '@angular/forms';
import { DatePipe } from '@angular/common';
import * as Constants from '../../constants';
import { Router } from '@angular/router';
import { LocalStorageService } from 'angular-2-local-storage';

@Component({
  selector: 'competitions',
  templateUrl: './competitions.component.html',
  styleUrls: ['./competitions.scss'],
  encapsulation: ViewEncapsulation.None
})

export class CompetitionsComponent implements OnInit {
  datePipe = new DatePipe("en-US");
  public imgUrl:string = environment.IMAGES_WEBROOT;
  public zipFile = null;
  public data = [];
  public router: Router;

  public filteredCompetitions: any[] = [];
  public filterTerm: string = '';
  public statusFilter: string = 'all';
  public sortAsc: boolean = false; // Partiamo dalla più recente
  public CONST = Constants;



competitions: any[];
  constructor(private competitionService: CompetitionService, 
    public fb:FormBuilder,
    protected localStorageService: LocalStorageService,
    router:Router) {
      this.router = router;
  }

  ngOnInit() {
    this.competitionService.getCompetitions().subscribe((response: any) => {
        this.competitions = response.result.data;
        this.applyFilters(); // Inizializza la lista filtrata
    });
  }

  // Logica di Filtro e Ordinamento
  applyFilters() {
      let temp = [...this.competitions];

      // Filtro Testuale
      if (this.filterTerm) {
          temp = temp.filter(c => 
              c.nome_gara.toLowerCase().includes(this.filterTerm.toLowerCase()) ||
              c.responsabile.toLowerCase().includes(this.filterTerm.toLowerCase())
          );
      }

      // Filtro Stato
      if (this.statusFilter !== 'all') {
          temp = temp.filter(c => c.comp_status == this.statusFilter);
      }

      // Ordinamento
      temp.sort((a, b) => {
          const dateA = new Date(a.data_gara).getTime();
          const dateB = new Date(b.data_gara).getTime();
          return this.sortAsc ? dateA - dateB : dateB - dateA;
      });

      this.filteredCompetitions = temp;
  }

  toggleSort() {
      this.sortAsc = !this.sortAsc;
      this.applyFilters();
  }

  getStatusLabel(status) {
      switch(status) {
          case this.CONST.CompetitionState.RegistrationOpen: return 'Aperta';
          case this.CONST.CompetitionState.Published: return 'Pre-gara';
          case this.CONST.CompetitionState.RegistrationClosedTime: return 'Chiusa';
          default: return status;
      }
  }



  public onCustom(event) {
    this.competitionService.getDatiGara(event.data.id).subscribe((response:any) => {
      this.localStorageService.set('currentCompetition', response.result.data);
      this.router.navigate(['/competitionDetails/' + event.data.id]);
    });
    
  }

  addCompetition(){
    this.router.navigate(['/add-competition']);
  }

}

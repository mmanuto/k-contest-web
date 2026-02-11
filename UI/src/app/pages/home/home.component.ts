import { Component, OnInit, ViewEncapsulation, ElementRef, ViewChild } from '@angular/core';
import { environment } from '../../../environments/environment';
import { LocalStorageService } from 'angular-2-local-storage';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { HomeService } from './home.service';
import * as Constants from '../../constants';

@Component({
  selector: 'home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss'],
  encapsulation: ViewEncapsulation.None
})

export class HomeComponent implements OnInit {

  @ViewChild('modalContent') modalContent: ElementRef;

  public competition: any;
  public modalRefIscrizioni: NgbModalRef;
  public currentClub: any;
  public imgUrl: string = environment.IMAGES_WEBROOT;
  public athletestNumber:number = 0;
  public athletestMaxNumber:number = 0;
  public CONST = Constants;
  public tabelloni;
  public appUrl;
  public timetable;

  constructor(protected localStorageService: LocalStorageService, public modalService: NgbModal, public homeService: HomeService) {
    this.competition = this.localStorageService.get('currentCompetition');
    this.currentClub = this.localStorageService.get('currentUser');
  }

  ngOnInit() {

    this.appUrl = environment.WEBROOT;
    
    this.homeService.getDatiGara(this.competition.id).subscribe((response:any) => {
      this.competition = response.result.data;
      this.localStorageService.set('currentCompetition', this.competition);
      this.localStorageService.set('iscrizioniStatus', this.competition.comp_status);
      this.athletestMaxNumber = this.competition.nr_atleti;
      this.localStorageService.set('athletestMaxNumber', this.athletestMaxNumber);

      this.timetable = null;

      if(this.competition.flag_timetable){
        this.homeService.getTimetables({competition_id: this.competition.id }).subscribe((response: any) => {
          
          if(response.result.success){
            this.timetable = response.result.data;
          }
          

        });
      }


      //se sono state pubblicate le classifiche, recupero i files corrispondenti
      if (this.competition.flag_classifications) {

        this.homeService.getTabelloni(this.competition.nome_gara).subscribe((response: any) => {
          this.tabelloni = response.result.data;
          for (let tab of this.tabelloni) {
            tab.name = tab.name.split('.')[0];
          }
        });
  
      }
  
    });
    
    this.homeService.getTotaleIscrizioni({competition_id: this.competition.id }).subscribe((response: any) => {
      this.athletestNumber = response.result.data;
    });
    
  }


  ngAfterViewInit() {
    document.getElementById('preloader').classList.add('hide');
  }

}

import { Component, OnInit, ViewEncapsulation, ElementRef, ViewChild } from '@angular/core';
import { FormGroup, FormControl, FormBuilder, Validators } from '@angular/forms';
import { CustomValidators } from 'ng2-validation';
import { LocalStorageService } from 'angular-2-local-storage';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { HomeService } from './home.service';
import * as Constants from '../../constants';
import { listFiles } from 'list-files-in-dir';
//import * as fs from 'fs'
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
  public imgUrl: string = Constants.IMAGES_WEBROOT;
  public iscrizioniChiuse: boolean;
  public pregara: boolean = false;
  public classifiche: boolean = false;
  public tabelloni;
  public appUrl;
  public athletestNumber;

  constructor(protected localStorageService: LocalStorageService, public modalService: NgbModal, public homeService: HomeService) {
    this.iscrizioniChiuse = false
    ;
    this.pregara = this.localStorageService.get('pregara');
    this.classifiche = this.localStorageService.get('classifiche');
    this.competition = this.localStorageService.get('currentCompetition');
    this.appUrl = Constants.BASEAPPURL;
  }

  ngOnInit() {

    this.currentClub = this.localStorageService.get('currentUser');

    
    this.homeService.getDatiGara(this.competition.id).subscribe((response:any) => {
      this.competition = response.result.data;
      this.classifiche = this.competition.classifiche == 1 ? true : false;
      this.localStorageService.set('currentCompetition', this.competition);
      let today = new Date();
      let dataChiusura = new Date(this.competition.chiusura_iscrizioni);
      dataChiusura.setDate(dataChiusura.getDate() + 1);
      console.log(dataChiusura);

      if (today > dataChiusura) {
          this.localStorageService.set('iscrizioniChiuse', true);
          this.iscrizioniChiuse = true;
      } 
      if (this.iscrizioniChiuse) {

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

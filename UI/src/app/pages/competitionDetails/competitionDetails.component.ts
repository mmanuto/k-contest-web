import { Component, ViewEncapsulation, OnInit, ElementRef, ViewChild } from '@angular/core';
import { CompetitionDetailsService } from './competitionDetails.service';
import { environment } from '../../../environments/environment';
import { ToastrService } from 'ngx-toastr';
import { DatePipe } from '@angular/common';
import * as Constants from '../../constants';
import { ActivatedRoute } from '@angular/router';
import { LocalStorageService } from 'angular-2-local-storage';

@Component({
  selector: 'competitionDetails',
  templateUrl: './competitionDetails.component.html',
  styleUrls: ['./competitionDetails.scss'],
  encapsulation: ViewEncapsulation.None
})

export class CompetitionDetailsComponent implements OnInit {
  datePipe = new DatePipe("en-US");
  categorie = null;
  circolareNew = null;
  public imgUrl:string = environment.IMAGES_WEBROOT;
  public competitionId;
  public currentCompetition = null;

competitions: any[];
  constructor(private competitionDetailsService: CompetitionDetailsService, 
    route: ActivatedRoute, 
    protected localStorageService: LocalStorageService) {
    route.params.subscribe((params) => {
      console.log(params.id);
      this.competitionId = params.id;
      this.currentCompetition = this.localStorageService.get('currentCompetition');
    });
  }

  ngOnInit(): void {
    

  }

  /*

  ngOnInit() {
    

    this.pendingCompetitionService.getPendingCompetitions().subscribe((response: any) => {
      this.competitions = response.result.data;
      for(let item of this.competitions){
        item.richieste = JSON.parse(item.richieste);
        item.chiusura_iscrizioni = this.datePipe.transform(item.chiusura_iscrizioni, 'yyyy-MM-dd');
        item.data_gara = this.datePipe.transform(item.data_gara, 'yyyy-MM-dd');
      }
      console.log(this.competitions);
    });
  }

  editCategorie(event) {
    let reader = new FileReader();
    if (event.target.files && event.target.files.length > 0) {
        let file = event.target.files[0];
        reader.readAsDataURL(file);
        reader.onload = () => {
          this.categorie = {
                filename: file.name,
                filetype: file.type,
                value: reader.result
            };
        };
    }
}

editCircolare(event) {
  let reader = new FileReader();
  if (event.target.files && event.target.files.length > 0) {
      let file = event.target.files[0];
      reader.readAsDataURL(file);
      reader.onload = () => {
        this.circolareNew = {
              filename: file.name,
              filetype: file.type,
              value: reader.result
          };
      };
  }
}

  updateDatiGara(item, active = false){
    console.log(item);
    if(active){
      item.stato = 1;
    }
    this.pendingCompetitionService.updateDatiGara(item, this.circolareNew, this.categorie).subscribe((response: any) => {
      //console.log(response);
      if(response.result.success){
        window.location.reload();
    }
    });
  }

  */
}

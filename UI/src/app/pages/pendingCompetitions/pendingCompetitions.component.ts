import { Component, ViewEncapsulation, OnInit, ElementRef, ViewChild } from '@angular/core';
import { PendingCompetitionService } from './pendingCompetition.service';
import { ToastrService } from 'ngx-toastr';
import { DatePipe } from '@angular/common';
import * as Constants from '../../constants';

@Component({
  selector: 'pendingCompetitions',
  templateUrl: './pendingCompetitions.component.html',
  styleUrls: ['./pendingCompetitions.scss'],
  encapsulation: ViewEncapsulation.None
})

export class PendingCompetitionsComponent implements OnInit {
  datePipe = new DatePipe("en-US");
  categorie = null;
  circolareNew = null;
  public imgUrl:string = Constants.IMAGES_WEBROOT;
 /*  @ViewChild('inputCat') inputCat: ElementRef;
  @ViewChild('inputCirc') inputCirc: ElementRef; */

competitions: any[];
  constructor(private pendingCompetitionService: PendingCompetitionService) {
  }

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

}

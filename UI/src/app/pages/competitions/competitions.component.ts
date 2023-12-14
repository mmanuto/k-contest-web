import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { CompetitionService } from './competition.service';
import { FormBuilder } from '@angular/forms';
import { DatePipe } from '@angular/common';
import * as Constants from '../../constants';

@Component({
  selector: 'competitions',
  templateUrl: './competitions.component.html',
  styleUrls: ['./competitions.scss'],
  encapsulation: ViewEncapsulation.None
})

export class CompetitionsComponent implements OnInit {
  datePipe = new DatePipe("en-US");
  public imgUrl:string = Constants.IMAGES_WEBROOT;
  public zipFile = null;

competitions: any[];
  constructor(private competitionService: CompetitionService, 
    public fb:FormBuilder,) {
  }

  ngOnInit() {
    

    this.competitionService.getCompetitions().subscribe((response: any) => {
      this.competitions = response.result.data;
      for(let item of this.competitions){
        item.richieste = JSON.parse(item.richieste);
        item.chiusura_iscrizioni = this.datePipe.transform(item.chiusura_iscrizioni, 'yyyy-MM-dd');;
      }
      console.log(this.competitions);
    });
  }

  //TODO: Test this function
  editTabelloni(event) {
    console.log(event);
    let reader = new FileReader();
    if (event.target.files && event.target.files.length > 0) {
        let file = event.target.files[0];
        reader.readAsDataURL(file);
        reader.onload = () => {
          this.zipFile = {
                filename: file.name,
                filetype: file.type,
                value: reader.result
            };
        };
    }
  }
  
  uploadTabelloni(nomeGara){
    console.log(this.zipFile);
    
      this.competitionService.uploadTabelloni({nomeGara: nomeGara, zipFile: this.zipFile}).subscribe((response: any) => {
        console.log(response);
      });
  }

}

import { Component, ViewEncapsulation, OnInit } from '@angular/core';
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
  public imgUrl:string = Constants.IMAGES_WEBROOT;
  public zipFile = null;
  public data = [];
  public router: Router;

  public settings = {
    selectMode: 'single',  //single|multi
    hideHeader: false,
    hideSubHeader: false,

    actions: {
      columnTitle: 'Actions',
      add: false,
      edit: false,
      delete: false,
      custom: [{
        name: 'edit-category',
        title: '<i class="fa fa-pencil mr-3 text-primary"></i>'
      }],
      position: 'right' // left|right
    },
    noDataMessage: 'No data found',
    rowClassFunction: (row) =>{
      if(row.data.deleted){
        return 'red-background';
      }else if(row.data.modified){
        return 'orange-background'
      }else{
        return 'none-background';
      }
    },
    columns: {     
      id: {
        title: 'ID',
        editable: false,
        type: 'html',
        width: '60px',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }       
      },
      name: {
        title: 'Nome',
        type: 'string',
        filter: true,
        width: '260px',
      },
      leader: {
        title: 'Responsabile',
        type: 'string'
      },
      competitionDate: {
        title: 'Data gara',
        type: 'html',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      closeDate: {
        title: 'Chiusura inscrizioni',
        type: 'html',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      inscriptions: {
        title: 'N. Iscritti',
        type: 'html',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      status: {
        title: 'Stato',
        type: 'html',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      tabs: {
        title: 'Tabelloni',
        type: 'html',
        width: '60px',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      timetable: {
        title: 'Orari',
        type: 'html',
        width: '60px',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      classifications: {
        title: 'CLassifiche',
        type: 'html',
        width: '60px',
        valuePrepareFunction: (value) => { return '<div class="text-center">' + value + '</div>'; }
      },
      locandina: {
        title: 'Locandina',
        type: 'html',
        width: '70px',
      },
      circolare: {
        title: 'Circolare',
        type: 'html',
        width: '70px',
      }
    },
    pager: {
      display: true,
      perPage: 25
    }
  };

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
      this.buildTable();
      console.log(this.competitions);
    });
  }

  buildTable(){
    this.data = [];
    console.log(this.competitions);

    this.competitions.forEach(element => {
      this.data.push({
        id: element.id,
        name: element.nome_gara,
        leader: element.responsabile,
        competitionDate: this.datePipe.transform(element.data_gara, 'dd/MM/yyyy'),
        closeDate: this.datePipe.transform(element.chiusura_iscrizioni, 'dd/MM/yyyy'),
        inscriptions: element.inscriptions + '/' + element.nr_atleti,
        status: element.comp_status,
        tabs: element.flag_tabs ? "<i class='fa fa-check text-success'></i>" : "<i class='fa fa-close text-danger'></i>",
        timetable: element.flag_timetable ? "<i class='fa fa-check text-success'></i>" : "<i class='fa fa-close text-danger'></i>",
        classifications: element.flag_classifications ? "<i class='fa fa-check text-success'></i>" : "<i class='fa fa-close text-danger'></i>",
        locandina: "<a target='blank' href='"+ this.imgUrl + element.locandina +"' class='documents text-primary'><i class='fa fa-file-image-o'></i></a>",
        circolare: "<a target='blank' href='"+ this.imgUrl + element.circolare +"' class='documents text-primary'><i class='fa fa-file-image-o'></i></a>"
        
      })

      
                                            
    });
  }

  public onCustom(event) {
    this.competitionService.getDatiGara(event.data.id).subscribe((response:any) => {
      this.localStorageService.set('currentCompetition', response.result.data);
      this.router.navigate(['/competitionDetails/' + event.data.id]);
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

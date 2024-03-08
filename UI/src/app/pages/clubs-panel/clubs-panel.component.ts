import { Component, ViewEncapsulation, OnInit, Input } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { DatePipe } from '@angular/common';
import * as Constants from '../../constants';
import { Router } from '@angular/router';
import { CompetitionDetailsService } from '../competitionDetails/competitionDetails.service';

@Component({
  selector: 'clubs-panel',
  templateUrl: './clubs-panel.component.html',
  encapsulation: ViewEncapsulation.None
})

export class ClubsPanelComponent implements OnInit {
    @Input() competitionData?: any;
    public data = [];

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
  constructor(private competitionDetailsService: CompetitionDetailsService) {

  }

  ngOnInit() {
    

  }





}

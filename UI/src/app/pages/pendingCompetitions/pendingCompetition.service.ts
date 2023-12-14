import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';


@Injectable()
export class PendingCompetitionService
{
    constructor(private http: HttpClient)
    {
    }

    getPendingCompetitions(){
        return this.http.post(Constants.BASEAPPURL + 'competitions/getPendingCompetitions', {});
    }

    updateDatiGara(competition, circolare, categorie){
        let request = {competition: competition};
        if(circolare != null){
            request['circolare'] = circolare;
        }
        if(categorie != null){
            request['categorie'] = categorie;
        }

        console.log(request);
        return this.http.post(Constants.BASEAPPURL + 'competitions/updateDatiGara', request);
    }

}

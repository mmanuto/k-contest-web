import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class RiepilogoService {
    constructor(public http:HttpClient) { }
    getSubmissionContext(competitionId, clubId){
        return this.http.post(environment.BASEAPPURL + 'competitions/getClubSubmissionContext', {competition_id: competitionId, club_id: clubId});
    }


    getDatiGara(idGara)
     {
         return this.http.post(environment.BASEAPPURL + 'competitions/getDatiGara', {idGara:idGara});
                 
    }

    getListaIscritti(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getIscritti", request);
    }


    inviaDati(request){
        console.log(request);
        return this.http.post(environment.BASEAPPURL + "athletes/inviaDati", request);
    }



    //TODO: ------------------------------------- METODI DA RIVEDERE ------------------------------------------
    getListaSquadre(request){
        return this.http.post(environment.BASEAPPURL + "teams/getListaSquadre", request);
        
    }

}

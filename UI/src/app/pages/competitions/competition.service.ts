import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class CompetitionService
{
    private _postsURL = "http://" + document.location.hostname + "/iscrizioniCsenServer/";
    public url = "api/users";
    constructor(private http: HttpClient)
    {
    }

    getCompetitions()
     {
         return this.http.post(environment.BASEAPPURL + 'competitions/getAllCompetitions', {});
                 
    }

    uploadTabelloni(zipFile)
     {
         console.log('SERVIZIO: UPLOADTABELLONI');
         return this.http.post(environment.BASEAPPURL + 'competitions/uploadTabelloni', zipFile);
                 
    }

    getDatiGara(idGara)
    {
        console.log(idGara);
        return this.http.post(environment.BASEAPPURL + 'competitions/getDatiGara', {idGara: idGara});
                
   }

}

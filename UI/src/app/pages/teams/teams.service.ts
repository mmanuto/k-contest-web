import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class TeamsService {
    //public url = "api/users";
    constructor(public http:HttpClient) { }

    getCategorieCinture(categorycode){
        return this.http.post(environment.BASEAPPURL + "teams/getCategorieCinture", {code: categorycode});
    }

    getListaIscritti(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getIscritti", request);
    }

    getListaSquadre(request){
        return this.http.post(environment.BASEAPPURL + "teams/getListaSquadre", request);
        
    }

    saveSquadra(request){
        return this.http.post(environment.BASEAPPURL + "teams/saveSquadra", request);
    }

    deleteSquadra(request){
        return this.http.post(environment.BASEAPPURL + "teams/deleteSquadra", request);
    }

} 
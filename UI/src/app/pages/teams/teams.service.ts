import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';
import { Observable } from 'rxjs';

@Injectable()
export class TeamsService {
    //public url = "api/users";
    constructor(public http:HttpClient) { }

    getCategorieCinture(categorycode){
        return this.http.post(Constants.BASEAPPURL + "teams/getCategorieCinture", {code: categorycode});
    }

    getListaIscritti(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getIscritti", request);
    }

    getListaSquadre(request){
        return this.http.post(Constants.BASEAPPURL + "teams/getListaSquadre", request);
        
    }

    saveSquadra(request){
        return this.http.post(Constants.BASEAPPURL + "teams/saveSquadra", request);
    }

    deleteSquadra(request){
        return this.http.post(Constants.BASEAPPURL + "teams/deleteSquadra", request);
    }

} 
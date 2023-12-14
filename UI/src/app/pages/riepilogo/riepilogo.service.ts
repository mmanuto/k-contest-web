import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';

@Injectable()
export class RiepilogoService {
    constructor(public http:HttpClient) { }


    getDatiGara(idGara)
     {
         return this.http.post(Constants.BASEAPPURL + 'competitions/getDatiGara', idGara);
                 
    }

    getListaIscritti(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getIscritti", request);
    }


    inviaDati(request){
        console.log(request);
        return this.http.post(Constants.BASEAPPURL + "athletes/inviaDati", request);
    }



    //TODO: ------------------------------------- METODI DA RIVEDERE ------------------------------------------
    getListaSquadre(request){
        return this.http.post(Constants.BASEAPPURL + "teams/getListaSquadre", request);
        
    }

} 
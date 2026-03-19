import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class MyAthletesService {
    constructor(public http:HttpClient) { }


    getDatiGara(idGara)
     {
         return this.http.post(environment.BASEAPPURL + 'competitions/getDatiGara', {idGara:idGara});
                 
    }

    getListaIscritti(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getIscritti", request);
    }

} 
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import * as Constants from '../../constants';
@Injectable()
export class HomeService 
{
/*     results: any;
    private _postsURL = "http://" + document.location.hostname + "/sygmundserver/"; */

    constructor(private http: HttpClient)
    {

    }


     //================================= LOGIN =======================================
     getDatiGara(idGara)
     {
         return this.http.post(Constants.BASEAPPURL + 'competitions/getDatiGara', {idGara:idGara});
                 
    }

    getTabelloni(nomeGara){
        console.log(nomeGara);
        return this.http.post(Constants.BASEAPPURL + 'competitions/getClassifiche', {'gara':nomeGara});
    }

    getTotaleIscrizioni(idGara){
        return this.http.post(Constants.BASEAPPURL + 'competitions/getTotaleIscrizioni', idGara);
    }


}

    




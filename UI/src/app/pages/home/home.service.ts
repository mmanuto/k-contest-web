import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

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
         return this.http.post(environment.BASEAPPURL + 'competitions/getDatiGara', {idGara:idGara});
                 
    }

    getTabelloni(nomeGara){
        console.log(nomeGara);
        return this.http.post(environment.BASEAPPURL + 'competitions/getClassifiche', {'gara':nomeGara});
    }

    getTotaleIscrizioni(idGara){
        return this.http.post(environment.BASEAPPURL + 'competitions/getTotaleIscrizioni', idGara);
    }


}

    




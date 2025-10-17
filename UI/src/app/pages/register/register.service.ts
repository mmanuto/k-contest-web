import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
@Injectable()
export class RegisterService 
{
/*     results: any;
    private _postsURL = "http://" + document.location.hostname + "/sygmundserver/"; */

    constructor(private http: HttpClient)
    {

    }


     //================================= LOGIN =======================================
     getCompetitions()
     {
         return this.http.get(environment.BASEAPPURL + 'competitions/getCompetitions', {});
                 
    }

    /* saveClub(request)
     {
         return this.http.post(environment.BASEAPPURL + 'registeredClubs/saveClub', request);
                 
    } */

         checkFiscalCode(request)
     {
         return this.http.post(environment.BASEAPPURL + 'clubs/checkFiscalCode', request);
                 
    }

    saveClub(request)
     {
         return this.http.post(environment.BASEAPPURL + 'clubs/saveClub', request);
                 
    }

}

    




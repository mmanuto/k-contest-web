import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';
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
         return this.http.get(Constants.BASEAPPURL + 'competitions/getCompetitions', {});
                 
    }

    /* saveClub(request)
     {
         return this.http.post(Constants.BASEAPPURL + 'registeredClubs/saveClub', request);
                 
    } */

    checkCf(request)
     {
         return this.http.post(Constants.BASEAPPURL + 'clubs/checkCf', request);
                 
    }

    saveClub(request)
     {
         return this.http.post(Constants.BASEAPPURL + 'clubs/saveClub', request);
                 
    }

}

    




import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';


@Injectable()
export class AddCompeetitionService
{

    constructor(private http: HttpClient)
    {
    }


    saveRequest(request){
        console.log(request);
        return this.http.post(environment.BASEAPPURL + 'competitions/saveRequest', request);
    }

}

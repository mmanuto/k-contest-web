import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { BehaviorSubject, Observable } from 'rxjs';
import * as Constants from '../../constants';


@Injectable()
export class RequestService
{

    constructor(private http: HttpClient)
    {
    }


    saveRequest(request){
        console.log(request);
        return this.http.post(Constants.BASEAPPURL + 'competitions/saveRequest', request);
    }

}

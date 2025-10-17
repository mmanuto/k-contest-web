import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class PersonalService
{
    constructor(private http: HttpClient)
    {
    }

    updateUserData(request){
        return this.http.post(environment.BASEAPPURL + "clubs/updateData", request);
    }

}
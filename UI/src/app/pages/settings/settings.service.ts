import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class SettingsService
{
    constructor(private http: HttpClient)
    {
    }

    updateUserData(request){
        return this.http.post(environment.BASEAPPURL + "users/updateUserData", request);
    }

}

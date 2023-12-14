import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';

@Injectable()
export class SettingsService
{
    constructor(private http: HttpClient)
    {
    }

    updateUserData(request){
        return this.http.post(Constants.BASEAPPURL + "users/updateUserData", request);
    }

}

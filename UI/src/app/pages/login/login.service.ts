import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import * as Constants from '../../constants';
@Injectable()
export class LoginService 
{
/*     results: any;
    private _postsURL = "http://" + document.location.hostname + "/sygmundserver/"; */

    constructor(private http: HttpClient)
    {

    }


     //================================= LOGIN =======================================
     login(request)
     {
         return this.http.post(Constants.BASEAPPURL + 'users/login', {'username': request.username, 'password': request.password});
                 
    }


}

    




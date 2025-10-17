import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';

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
         return this.http.post(environment.BASEAPPURL + 'users/login', {'username': request.username, 'password': request.password});
                 
    }


}

    




import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable()
export class CompetitorsInCategoriesService {
    constructor(public http:HttpClient) { }

    getTabelloni(request){
        return this.http.post(environment.BASEAPPURL + "competitions/getTabelloni", request);
    }

    getAthleteList(request){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/getAthletesByCategory', request);
    }

} 
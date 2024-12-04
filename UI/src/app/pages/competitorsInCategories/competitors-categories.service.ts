import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';

@Injectable()
export class CompetitorsInCategoriesService {
    constructor(public http:HttpClient) { }

    getTabelloni(request){
        return this.http.post(Constants.BASEAPPURL + "competitions/getTabelloni", request);
    }


    /*getAthletesInCategories(request){
        return this.http.post(Constants.BASEAPPURL + "competitions/getAthletesInCategories", request);
    }

    getCategories()
     {
        console.log('entra');
         return this.http.post(Constants.BASEAPPURL + 'competitions/getCategories', {});
                 
    }*/

    getAthleteList(request){
        return this.http.post(Constants.BASEAPPURL + 'athleteInscriptions/getAthletesByCategory', request);
    }

} 
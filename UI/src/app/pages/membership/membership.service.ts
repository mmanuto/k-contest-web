import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import * as Constants from '../../constants';
import { environment } from '../../../environments/environment';

@Injectable()
export class MembershipService {
    constructor(public http:HttpClient) { }

    getFederationsList(){
        return this.http.post(environment.BASEAPPURL + "federations/getFederations", {});
    }

    getBeltList(){
        return this.http.post(environment.BASEAPPURL + "belts/getBeltList", {});
    }

    getAgeCategory(request){
        return this.http.post(environment.BASEAPPURL + "agecategories/getAgeCategory", request);
        
    }

    getElencoProve(request){
        if(request.competition_type == Constants.EventType.Competition){  
            return this.http.post(environment.BASEAPPURL + "athletes/getElencoProve", request);
        }else{
            return this.http.post(environment.BASEAPPURL + "athletes/getCorsiStage", request);
        }
        
    }

    getAthleteList(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getAthleteList", request);
    }


    getListaIscritti(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getIscritti", request);
    }

    saveAtleta(request){
        return this.http.post(environment.BASEAPPURL + "athletes/saveAtleta", request);
    }

    deteleAtleta(request){
        console.log(request);
        return this.http.post(environment.BASEAPPURL + "athletes/deleteInscription", request);
    }

    closeRegistrations(request){
        return this.http.post(environment.BASEAPPURL + "competitions/closeRegistrations", request);
    }
} 
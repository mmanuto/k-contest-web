import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import * as Constants from '../../constants';

@Injectable()
export class MembershipService {
    constructor(public http:HttpClient) { }

    getFederationsList(){
        return this.http.post(Constants.BASEAPPURL + "federations/getFederations", {});
    }

    getBeltList(){
        return this.http.post(Constants.BASEAPPURL + "belts/getBeltList", {});
    }

    getAgeCategory(request){
        return this.http.post(Constants.BASEAPPURL + "agecategories/getAgeCategory", request);
        
    }

    getElencoProve(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getElencoProve", request);
    }

    getAthleteList(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getAthleteList", request);
    }


    getListaIscritti(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getIscritti", request);
    }

    saveAtleta(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/saveAtleta", request);
    }

    deteleAtleta(request){
        console.log(request);
        return this.http.post(Constants.BASEAPPURL + "athletes/deleteInscription", request);
    }

    closeRegistrations(request){
        return this.http.post(Constants.BASEAPPURL + "competitions/closeRegistrations", request);
    }
} 
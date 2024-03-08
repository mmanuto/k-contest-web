import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import * as Constants from '../../constants';


@Injectable()
export class CompetitionDetailsService
{
    constructor(private http: HttpClient)
    {
    }

    // ====================================================================================================
    // @ ADMIN PANEL
    // =====================================================================================================

    getCategories(request)
     {
        console.log('entra');
         return this.http.post(Constants.BASEAPPURL + 'categoryCodes/getCategories', request);
                 
    }

    getCategoriesWithAthletes(request){
        return this.http.post(Constants.BASEAPPURL + 'categoryCodes/getCategoriesWithAthletes', request);
    }

    getModifiedInscriptions(){
        return this.http.post(Constants.BASEAPPURL + 'athletesInscriptions/getModifiedInscriptions', {});
    }

    toggleFlags(request){
        return this.http.post(Constants.BASEAPPURL + 'competitions/toggleFlags', request);
    }

    //--------------------------------------------------- PRINT KUMITE ------------------------------------------------------------------

    getKumiteList(){
        return this.http.post(Constants.BASEAPPURL + 'athleteInscriptions/getKumiteList', {});
    }

    // ====================================================================================================
    // @ ATHELTES PANEL
    // ====================================================================================================

    getAthleteList(request){
        return this.http.post(Constants.BASEAPPURL + 'athletesInscriptions/getAthleteList', request);
    }

    getTotalAthleteList(){
        return this.http.post(Constants.BASEAPPURL + "athletes/getTotalAthleteList", {});
    }

    getClubs(){
        return this.http.post(Constants.BASEAPPURL + 'clubs/getClubs', {});
    }

    getAllCategories(){
        return this.http.post(Constants.BASEAPPURL + "categoryCodes/getAllCategories", {});
    }

    //-------------------- Inserimento nuovo atleta --------------------------------------
    //TODO: Tutto da rivedere

    getCategoria(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getCategoria", request);
        
    }

    getElencoProve(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/getElencoProve", request);
    }

    saveAtleta(request){
        return this.http.post(Constants.BASEAPPURL + "athletes/saveAtleta", request);
    }



    //-------------------------------------- Modifica Atleta -----------------------------------
    deteleAtletaInscription(request){
        console.log(request);
        return this.http.post(Constants.BASEAPPURL + "athletesInscriptions/deleteInscription", request);
    }   

    updateCategory(request){
        return this.http.post(Constants.BASEAPPURL + "athletesInscriptions/updateCategory", request);
    }

    // ====================================================================================================
    // @ CATEGORIES PANEL
    // ====================================================================================================

    getAthletesByCategory(request){
        return this.http.post(Constants.BASEAPPURL + 'athletesInscriptions/getAthletesByCategory', request);
    }

    getTotaleIscrizioni(request){
        return this.http.post(Constants.BASEAPPURL + 'competitions/getTotaleIscrizioni', request);
    }

    getTotaleCategorie(request){
        return this.http.post(Constants.BASEAPPURL + 'competitions/getTotaleCategorie', request);
    }

    splitCategory(request){
        return this.http.post(Constants.BASEAPPURL + 'athletesInscriptions/splitCategory', request);
    }

    // ====================================================================================================
    // @ CLUBS PANEL
    // ====================================================================================================

    getClubsWithAthletes(request){
        return this.http.post(Constants.BASEAPPURL + 'athletesInscriptions/getClubsWithAthletes', request);
    }
    
}

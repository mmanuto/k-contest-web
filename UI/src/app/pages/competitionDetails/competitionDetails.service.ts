import { Injectable, Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';


@Injectable()
export class CompetitionDetailsService
{
    constructor(private http: HttpClient)
    {
    }

    getDatiGara(idGara)
    {
        console.log(idGara);
        return this.http.post(environment.BASEAPPURL + 'competitions/getDatiGara', {idGara: idGara});
                
   }

    getRegisteredClubs(competitionId){
        return this.http.post(environment.BASEAPPURL + 'competitions/getRegisteredClubs', {competition_id: competitionId});
    }

    updateGara(request)
    {
        return this.http.post(environment.BASEAPPURL + 'competitions/updateDatiGara', request);                    
    }

    getAvailableCategorySets(){
        return this.http.post(environment.BASEAPPURL + 'competitions/getCompetitionList', {});
    }

    getCategoriesBySet(selectedSetCode){
        return this.http.post(environment.BASEAPPURL + 'categorycodes/getCategoriesBySet', {selectedSetCode: selectedSetCode});
    }

    uploadCategoryCsv(formData: FormData) {
        return this.http.post(environment.BASEAPPURL + 'categorycodes/uploadCategoryCsv', formData);
    }
    
    // ====================================================================================================
    // @ ADMIN PANEL
    // =====================================================================================================

    getCategories(request)
     {
        console.log('entra');
         return this.http.post(environment.BASEAPPURL + 'categorycodes/getCategories', request);
                 
    }

    getCategoriesWithAthletes(request){
        return this.http.post(environment.BASEAPPURL + 'categorycodes/getCategoriesWithAthletes', request);
    }

    getModifiedInscriptions(){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/getModifiedInscriptions', {});
    }

    toggleFlags(request){
        return this.http.post(environment.BASEAPPURL + 'competitions/toggleFlags', request);
    }

    //--------------------------------------------------- PRINT KUMITE ------------------------------------------------------------------

    getKumiteList(){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/getKumiteList', {});
    }

    // ====================================================================================================
    // @ ATHELTES PANEL
    // ====================================================================================================

    getAthleteList(request){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/getAthleteList', request);
    }

    getTotalAthleteList(){
        return this.http.post(environment.BASEAPPURL + "athletes/getTotalAthleteList", {});
    }

    getClubs(){
        return this.http.post(environment.BASEAPPURL + 'clubs/getClubs', {});
    }

    getAllCategories(){
        return this.http.post(environment.BASEAPPURL + "categorycodes/getAllCategories", {});
    }

    //-------------------- Inserimento nuovo atleta --------------------------------------
    //TODO: Tutto da rivedere

    getCategoria(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getCategoria", request);
        
    }

    getElencoProve(request){
        return this.http.post(environment.BASEAPPURL + "athletes/getElencoProve", request);
    }

    saveAtleta(request){
        return this.http.post(environment.BASEAPPURL + "athletes/saveAtleta", request);
    }



    //-------------------------------------- Modifica Atleta -----------------------------------
    deteleAtletaInscription(request){
        console.log(request);
        return this.http.post(environment.BASEAPPURL + "athleteInscriptions/deleteInscription", request);
    }   

    updateCategory(request){
        return this.http.post(environment.BASEAPPURL + "athleteInscriptions/updateCategory", request);
    }

    // ====================================================================================================
    // @ CATEGORIES PANEL
    // ====================================================================================================

    getAthletesByCategory(request){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/getAthletesByCategory', request);
    }

    getTotaleIscrizioni(request){
        return this.http.post(environment.BASEAPPURL + 'competitions/getTotaleIscrizioni', request);
    }

    getTotaleCategorie(request){
        return this.http.post(environment.BASEAPPURL + 'competitions/getTotaleCategorie', request);
    }

    splitCategory(request){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/splitCategory', request);
    }

    // ====================================================================================================
    // @ CLUBS PANEL
    // ====================================================================================================

    getClubsWithAthletes(request){
        return this.http.post(environment.BASEAPPURL + 'athleteInscriptions/getClubsWithAthletes', request);
    }
    
}

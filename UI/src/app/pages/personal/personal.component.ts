import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { LocalStorageService } from 'angular-2-local-storage';
import { PersonalService } from './personal.service';
import { Router } from '@angular/router';


declare var $:any;

@Component({
    selector: 'personal',
    templateUrl: './personal.html',
    styleUrls: ['./personal.scss'],
    encapsulation: ViewEncapsulation.None
  })

export class PersonalComponent implements OnInit {
    public club: any = {};
    public user: any = [];
    public password: string = '';
    public confirmPassword: string = '';
    public notConfirmPsw:boolean = false;

  constructor(protected storageService: LocalStorageService, 
                private personalService: PersonalService, 
                protected router: Router){
                    //this.user = {name: '', society: '', email: '', phone: '', coach: ''};
                    this.club = this.storageService.get('currentUser');
        console.log(this.club);
  }


    ngOnInit(){
        
        if(this.user == null){
            this.router.navigate(['/login']);
        }
    }

  updateDates(model){

      this.personalService.updateUserData({club:this.club}).subscribe((response:any) => {
        console.log(response);
        if(response.result.success){
            this.storageService.set('currentUser', this.club);
            window.location.reload();
            //aggiungere pop-up informativo di avvenuta modifica?
        }
    });
   
  }



  //NON SERVE
  changePassword(model){
      console.log(model);
      if(model.password == model.confirmPassword){
          this.notConfirmPsw = false;
          this.personalService.updateUserData({id: this.user.id, password:model.password}).subscribe((response:any) => {
              console.log(response);
              if(response.result.success){
                window.location.reload();
            }
          });
      }else{
              this.notConfirmPsw = true;
          }
      }
  }


import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { LocalStorageService } from 'angular-2-local-storage';
import { SettingsService } from './settings.service';
import { Router } from '@angular/router';


declare var $:any;

@Component({
    selector: 'settings',
    templateUrl: './settings.html',
    styleUrls: ['./settings.scss'],
    encapsulation: ViewEncapsulation.None
  })

export class SettingsComponent implements OnInit {
    public user: any = [];
    public password: string = '';
    public confirmPassword: string = '';
    public notConfirmPsw:boolean = false;

  constructor(protected storageService: LocalStorageService, 
                private settingsService: SettingsService, 
                protected router: Router){
                    this.user = this.storageService.get('currentUser');
        console.log(this.user);
  }


    ngOnInit(){
        
        if(this.user == null){
            this.router.navigate(['/login']);
        }
    }

  updateEmail(model){
      console.log(model);
      this.settingsService.updateUserData({id: this.user.id, email:model.email}).subscribe((response:any) => {
        console.log(response);
        if(response.result.success){
            window.location.reload();
        }
    });
   
  }

  changePassword(model){
      console.log(model);
      if(model.password == model.confirmPassword){
          this.notConfirmPsw = false;
          this.settingsService.updateUserData({id: this.user.id, password:model.password}).subscribe((response:any) => {
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


import { Component, OnInit, ViewEncapsulation, HostListener } from '@angular/core';
import { trigger,  state,  style, transition, animate } from '@angular/animations';
import { AppSettings } from '../../../app.settings';
import { Settings } from '../../../app.settings.model';
import { MenuService } from '../menu/menu.service';
import { Router } from '@angular/router';
import { LocalStorageService } from 'angular-2-local-storage';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.scss'],
  encapsulation: ViewEncapsulation.None,
  providers: [ MenuService ],
  animations: [
    trigger('showInfo', [
      state('1' , style({ transform: 'rotate(180deg)' })),
      state('0', style({ transform: 'rotate(0deg)' })),
      transition('1 => 0', animate('400ms')),
      transition('0 => 1', animate('400ms'))
    ])
  ]
})
export class HeaderComponent implements OnInit {
  public showHorizontalMenu:boolean = true; 
  public showInfoContent:boolean = false;
  public settings: Settings;
  public menuItems:Array<any>;
  public currentUser;

  constructor(public appSettings:AppSettings, public menuService:MenuService, public router: Router, public storageService: LocalStorageService) {
      this.settings = this.appSettings.settings;
      this.menuItems = this.menuService.getHorizontalMenuItems();
      this.currentUser = this.storageService.get('currentUser');
  }
  
  ngOnInit() {
    if(window.innerWidth <= 768) 
      this.showHorizontalMenu = false;
  }

  logout() {
    this.storageService.remove('authToken');

    this.storageService.remove('currentUser');
    this.storageService.remove('userType');

    this.router.navigate(['register']);
  }


  public closeSubMenus(){
    let menu = document.querySelector("#menu0"); 
    if(menu){
      for (let i = 0; i < menu.children.length; i++) {
          let child = menu.children[i].children[1];
          if(child){          
              if(child.classList.contains('show')){            
                child.classList.remove('show');
                menu.children[i].children[0].classList.add('collapsed'); 
              }             
          }
      }
    }
  }

  // Toggle menu per mobile
  public toggleMenu() {
      this.settings.theme.showMenu = !this.settings.theme.showMenu;
  }

  @HostListener('window:resize')
  public onWindowResize():void {
     if(window.innerWidth <= 768){
        this.showHorizontalMenu = false;
     }      
      else{
        this.showHorizontalMenu = true;
      }
  }
  
}

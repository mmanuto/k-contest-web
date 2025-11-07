import { Component, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { UntypedFormGroup, FormControl, AbstractControl, UntypedFormBuilder, Validators} from '@angular/forms'; 
import { EmailValidators } from 'ngx-validators'
import { LoginService } from './login.service';
import { LocalStorageService } from 'angular-2-local-storage';
import { ToastrService } from 'ngx-toastr';
import * as Constants from '../../constants';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
  encapsulation: ViewEncapsulation.None
})
export class LoginComponent {
  public router: Router;
  public form:UntypedFormGroup;
  public username:AbstractControl;
  public password:AbstractControl;

  constructor(router:Router, 
    fb:UntypedFormBuilder,
    public loginService: LoginService,
    protected localStorageService: LocalStorageService,
    public toastrService: ToastrService) {
      this.router = router;
      this.form = fb.group({
          'username': ['', Validators.required],
          'password': ['', Validators.required]
      });

      this.username = this.form.controls['username'];
      this.password = this.form.controls['password'];
  }

  public onSubmit(values:Object):void {
      if (this.form.valid) {
        this.loginService.login(this.form.value).subscribe((response: any) => {
            
            if (response.result.success) {

                //Salvo in sessione l'utente e il token
                this.localStorageService.set('currentUser', response.result.data);
                this.localStorageService.set('userType', Constants.UserRole.Admin);

                //reindirizzo alla pagina del profilo
                this.router.navigate(['/competitions']);
            } else {
                this.toastrService.error('CREDENZIALI ERRATE',);
            }

        });
      }
  }

  ngAfterViewInit(){
      document.getElementById('preloader').classList.add('hide');                 
  }

}

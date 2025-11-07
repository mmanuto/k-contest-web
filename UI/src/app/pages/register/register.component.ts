import { Component, ViewEncapsulation, ElementRef, ViewChild } from '@angular/core';
import { environment } from '../../../environments/environment';
import { Router } from '@angular/router';
import { UntypedFormGroup, UntypedFormControl, AbstractControl, UntypedFormBuilder, Validators, FormGroup, FormControl, FormBuilder} from '@angular/forms';
import { CustomValidators } from 'ng2-validation';
import { RegisterService } from './register.service';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { LocalStorageService } from 'angular-2-local-storage';
import * as Constants from '../../constants';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss'],
  encapsulation: ViewEncapsulation.None
})
export class RegisterComponent {
    
    @ViewChild('openAdv') openAdv: ElementRef;

    public steps: any[];
    public personalForm: FormGroup;
    public club: any = {};
    public details: any = { club: '', competitionId: '', competitionName: '' };
    public showConfirm: boolean;
    public confirmed: boolean;
    public openCompetitions;
    public modalRefAdv: NgbModalRef;
    public imgUrl: string = environment.IMAGES_WEBROOT;
    public CONST = Constants;
    public selectedEvent = null;
    
    constructor(public router: Router, 
        private formBuilder: FormBuilder, 
        public toastrService: ToastrService, 
        public registerService: RegisterService, 
        public modalService: NgbModal, 
        protected localStorageService: LocalStorageService){

        this.steps = [
            { name: 'Events', icon: 'fa-trophy', active: true, valid: false, hasError: false },
            { name: 'Register', icon: 'fa-user', active: false, valid: false, hasError: false }
        ];


        this.personalForm = this.formBuilder.group({
            'login_cf': [''],
            'name': ['', Validators.required],
            'society': ['', Validators.required],
            'cod_fiscale': ['', Validators.required],
            'email': ['', Validators.compose([Validators.required, CustomValidators.email])],
            'phone': ['', Validators.required],
            'coach': ['', Validators.required]
        });
    }

    ngOnInit() {

        this.registerService.getCompetitions().subscribe((response: any) => {
            this.openCompetitions = response.result.data;
            //console.log(response);
        });
    }

    public next(event){

        this.selectedEvent = event;
        if (this.steps[this.steps.length - 1].active)
            return false;

        this.steps.some(function (step, index, steps) {
            if (index < steps.length - 1) {
                if (step.active) {
                    if (step.name == 'Events') {
                        if (event && event.id) {
                            step.active = false;
                            step.valid = true;
                            steps[index + 1].active = true;
                            return true;
                        }
                        else {
                            step.hasError = true;
                        }
                    }
                }
            }
        });

    }

    /*
    public nextOld() {
        let selectedEvent = this.selectedEvent;
        let personalForm = this.personalForm;

        if (this.steps[this.steps.length - 1].active)
            return false;

        this.steps.some(function (step, index, steps) {
            if (index < steps.length - 1) {
                if (step.active) {
                    if (step.name == 'Events') {
                        if (selectedEvent) {
                            step.active = false;
                            step.valid = true;
                            steps[index + 1].active = true;
                            return true;
                        }
                        else {
                            step.hasError = true;
                        }
                    }
                    if (step.name == 'Register') {
                        if (personalForm.valid) {
                            step.active = false;
                            step.valid = true;
                            steps[index + 1].active = true;
                            return true;
                        }
                        else {
                            step.hasError = true;
                        }
                    }
                }
            }
        });

        
    }*/

    public prev() {
        if (this.steps[0].active)
            return false;
        this.steps.some(function (step, index, steps) {
            if (index != 0) {
                if (step.active) {
                    step.active = false;
                    steps[index - 1].active = true;
                    return true;
                }
            }
        });
    }

    public confirm() {

        if(this.personalForm.value.cod_fiscale == '94535440153' || this.personalForm.value.cod_fiscale == '04833350269'){
            this.openAdv.nativeElement.click();
            return;
        }

        this.club.club_manager = this.personalForm.value.name;
        this.club.club_name = this.personalForm.value.society;
        this.club.fiscal_code = this.personalForm.value.cod_fiscale;
        this.club.mail = this.personalForm.value.email;
        this.club.telephone_n = this.personalForm.value.phone;
        this.club.coach = this.personalForm.value.coach;
        this.details.club = this.club;
        this.details.competitionId = this.selectedEvent.id;
        this.details.competitionName = this.selectedEvent.nome_gara;

        this.saveClub();


    }

    public saveClub(){
        this.registerService.saveClub(this.details).subscribe((response: any) => {
            console.log(response);

            this.localStorageService.set('authToken', response.result.token);

            this.localStorageService.set('currentUser', response.result.data.club);
            this.localStorageService.set('currentCompetition', response.result.data.competition);
            this.localStorageService.set('userType', Constants.UserRole.Authenticated);

            let selectedCompetition = response.result.data.competition;
            console.log(selectedCompetition);

            let today = new Date();
            let dataChiusura = new Date(selectedCompetition.chiusura_iscrizioni);
            dataChiusura.setDate(dataChiusura.getDate() + 1);
            console.log(dataChiusura);
            let dataAperturaIscrizioni = new Date(selectedCompetition.apertura_iscrizioni);
            let dataGara = new Date(selectedCompetition.data_gara);

            if (today > dataChiusura) {
                this.localStorageService.set('iscrizioniChiuse', true);

            } else {
                this.localStorageService.set('iscrizioniChiuse', false);
            }

            console.log(dataAperturaIscrizioni);
            console.log(today);

            if (dataAperturaIscrizioni != null && today < dataAperturaIscrizioni) {
                this.localStorageService.set('pregara', true);
            } else {
                this.localStorageService.set('pregara', false);
            }

            this.localStorageService.set('classifiche', selectedCompetition.classifiche == 1 ? true : false);
            this.localStorageService.set('tabelloni', selectedCompetition.tabelloni == 1 ? true : false);



            this.router.navigate(['/home']);
        });
    }
    ngAfterViewInit() {
        document.getElementById('preloader').classList.add('hide');
    }

    openModalAdv(modalContent) {

        this.modalRefAdv = this.modalService.open(modalContent, { container: '.app', backdrop: 'static' });
    }

    public closeModalAdv() {
        this.modalRefAdv.close();
    }

    public accedi() {

        if(this.personalForm.value.login_cf == '94535440153' || this.personalForm.value.login_cf == '04833350269'){
            this.openAdv.nativeElement.click();
            return;
        }
        this.registerService.checkFiscalCode({ 'codice_fiscale': this.personalForm.value.login_cf }).subscribe((response: any) => {
            //console.log(response.result.valore.id);

            //fare automaticamente il next e confirm (o solo il confirm?)
            if (response.result.success) {
                this.club.club_manager = response.result.valore.club_manager;
                this.club.club_name = response.result.valore.club_name;
                this.club.fiscal_code = response.result.valore.fiscal_code;
                this.club.mail = response.result.valore.mail;
                this.club.telephone_n = response.result.valore.telephone_n;
                this.club.coach = response.result.valore.coach;
                //incapsulo club in details assieme all'id della competizione
                this.details.club = this.club;
                this.details.competitionId = this.selectedEvent.id;
                this.details.competitionName = this.selectedEvent.nome_gara;
                this.saveClub();
            }else{
                this.toastrService.error('DATI NON TROVATI! Non risulta alcuna società associata al codice fiscale inserito. Controllare che il dato sia corretto, se il problema persiste rivolgersi a info@iscrizionicsenveneto.it');
            }

        });
    }

    public showTabsClassifications(event, page){

        this.localStorageService.set('currentUser', null);
        this.localStorageService.set('currentCompetition', event);
        this.localStorageService.set('userType', Constants.UserRole.Public);

        this.router.navigate([page]);
    }
}

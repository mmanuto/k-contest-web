import { Component, ViewEncapsulation, ElementRef, ViewChild } from '@angular/core';
import { FormGroup, FormControl, AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { CustomValidators } from 'ng2-validation';
import { AddCompeetitionService } from './add-competition.service';
import { DatePipe } from '@angular/common';
import { ToastrService } from 'ngx-toastr';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';
import { Router } from '@angular/router';

@Component({
    selector: 'add-competition',
    templateUrl: './add-competition.component.html',
    styleUrls: ['./add-competition.component.scss'],
    encapsulation: ViewEncapsulation.None
})

export class AddCompeetitionComponent {

     public logo:string = '';
    public locandina: string = '';
    public circolare: string = '';
    datePipe = new DatePipe("en-US");
    public requestForm: FormGroup;
    public modalRef: NgbModalRef;

    //Multiple months
    public navigation = 'select';
    @ViewChild('inputLogo') inputLogo: ElementRef;
    @ViewChild('inputLoc') inputLoc: ElementRef;
    @ViewChild('inputCirc') inputCirc: ElementRef;

    //Datepicker in a popup
    public modelPopup: NgbDateStruct = { year: new Date().getFullYear(), month: new Date().getMonth() + 1, day: new Date().getDate() - 3 };

    constructor(private formBuilder: FormBuilder, private addCompetitionService: AddCompeetitionService, public modalService: NgbModal) {

        this.requestForm = this.formBuilder.group({
            'name': ['', Validators.required],
            'companyName': ['', Validators.required],
            'email': ['', Validators.compose([Validators.required, CustomValidators.email])],
            'telefono': ['', Validators.required],
            'competitionName': ['', Validators.required],
            'competitionDate': ['', Validators.compose([Validators.required, Validators.pattern('^(0?[1-9]|[12][0-9]|3[01])[/.](0?[1-9]|1[012])[/.](19|20)\\d\\d$')])],
            'registrationClose': ['', Validators.compose([Validators.required, Validators.pattern('^(0?[1-9]|[12][0-9]|3[01])[/.](0?[1-9]|1[012])[/.](19|20)\\d\\d$')])],
            'n_athletes': ['', Validators.required],
            'referee': [false],
            'competitionManagement': [false],
            'tabsManagement': [false],
            'logo': null,
            'locandina': null,
            'circolare': null
        });

    }

    ngAfterViewInit() {
        document.getElementById('preloader').classList.add('hide');
    }

    //===================================================  SAVE DATA ====================================================

    public onSubmit(values: Object): void {
        if (this.requestForm.valid) {
            console.log(this.requestForm.value);
            let dataGara = this.requestForm.value.competitionDate.split('/');
            let dataChiusura = this.requestForm.value.registrationClose.split('/');
            values['competitionDate'] =  dataGara[2] + '-' + dataGara[1] + '-' + dataGara[0];
            values['registrationClose'] =  dataChiusura[2] + '-' + dataChiusura[1] + '-' + dataChiusura[0];
            console.log(this.requestForm.value);
            this.addCompetitionService.saveRequest(this.requestForm.value).subscribe((response: any) => {
                console.log(response);
                /*                 if(response.result.success){
                                    this.toastrService.success('Richiesta inviata con successo, a breve riceverà una mail riepilogativa.');
                                    
                                } */

            });
        }
    }

    openModal(modalContent) {
        this.modalRef = this.modalService.open(modalContent, { container: '.app' });
    }

    public closeModal() {
        this.modalRef.close();
        window.location.reload();
    }



    logoChange(event) {
        let reader = new FileReader();
        if (event.target.files && event.target.files.length > 0) {
            let file = event.target.files[0];
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.requestForm.get('logo').setValue({
                    filename: file.name,
                    filetype: file.type,
                    value: (<string>reader.result).split(',')[1]
                })
            };
            /* this.inputLogo.nativeElement.value = file.name; */
            this.logo = file.name;
        }
    }

    removeLogo(): void {
        this.requestForm.get('logo').setValue(null);
        this.inputLogo.nativeElement.value = '';
        this.logo = '';
    }

    locandinaChange(event) {
        let reader = new FileReader();
        if (event.target.files && event.target.files.length > 0) {
            let file = event.target.files[0];
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.requestForm.get('locandina').setValue({
                    filename: file.name,
                    filetype: file.type,
                    value: (<string>reader.result).split(',')[1]
                })
            };
            this.locandina = file.name;
        }
    }

    removeLocandina(): void {
        this.requestForm.get('locandina').setValue(null);
        /* this.inputLoc.nativeElement.value = ''; */
        this.locandina = '';
    }

    circolareChange(event) {
        let reader = new FileReader();
        if (event.target.files && event.target.files.length > 0) {
            let file = event.target.files[0];
            reader.readAsDataURL(file);
            reader.onload = () => {
                this.requestForm.get('circolare').setValue({
                    filename: file.name,
                    filetype: file.type,
                    value: (<string>reader.result).split(',')[1]
                })
            };
            this.circolare = file.name;
        }
    }

    removeCircolare(): void {
        this.requestForm.get('circolare').setValue(null);
        /* this.inputCirc.nativeElement.value = ''; */
        this.circolare = '';
    }




}

export function emailValidator(control: FormControl): { [key: string]: any } {
    var emailRegexp = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/;
    if (control.value && !emailRegexp.test(control.value)) {
        return { invalidEmail: true };
    }
}

import { Component, ViewEncapsulation, ElementRef, ViewChild, Injectable } from '@angular/core';
import { FormGroup, FormControl, AbstractControl, FormBuilder, Validators } from '@angular/forms';
import { NgbDateParserFormatter, NgbDateStruct } from '@ng-bootstrap/ng-bootstrap';
import { CustomValidators } from 'ng2-validation';
import { AddCompeetitionService } from './add-competition.service';
import { DatePipe } from '@angular/common';
import { ToastrService } from 'ngx-toastr';
import { NgbModal, NgbModalRef } from '@ng-bootstrap/ng-bootstrap';


@Injectable()
export class CustomDateParserFormatter extends NgbDateParserFormatter {
  readonly DELIMITER = '/';

  parse(value: string): NgbDateStruct | null {
    if (value) {
      const date = value.split(this.DELIMITER);
      return {
        day: parseInt(date[0], 10),
        month: parseInt(date[1], 10),
        year: parseInt(date[2], 10),
      };
    }
    return null;
  }

  format(date: NgbDateStruct | null): string {
    return date
      ? date.day + this.DELIMITER + ('0' + date.month).slice(-2) + this.DELIMITER + date.year
      : '';
  }
}

@Component({
    selector: 'add-competition',
    templateUrl: './add-competition.component.html',
    styleUrls: ['./add-competition.component.scss'],
    encapsulation: ViewEncapsulation.None,
    providers: [
        // Aggiungi questa riga per "istruire" il componente a usare il nuovo formato
        { provide: NgbDateParserFormatter, useClass: CustomDateParserFormatter }
    ]
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

    constructor(private formBuilder: FormBuilder, 
        private addCompetitionService: AddCompeetitionService, 
        public modalService: NgbModal,
        public toastrService: ToastrService) {

        this.requestForm = this.formBuilder.group({
            'id': ['', Validators.required],
            'name': ['', Validators.required],
            'companyName': ['', Validators.required],
            'place': ['', Validators.required],
            'email': ['', Validators.compose([Validators.required, CustomValidators.email])],
            'telefono': ['', Validators.required],
            'competitionName': ['', Validators.required],
            'competitionDate': [null, Validators.required],
            'registrationOpen': [null, Validators.required],
            'registrationClose': [null, Validators.required],
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
            const formVal = this.requestForm.value;

            const formatNGDate = (d: NgbDateStruct) => 
                d ? `${d.year}-${('0' + d.month).slice(-2)}-${('0' + d.day).slice(-2)}` : null;

            const payload = {
                ...formVal,
                registrationOpen: formatNGDate(formVal.registrationOpen),
                registrationClose: formatNGDate(formVal.registrationClose),
                competitionDate: formatNGDate(formVal.competitionDate)
            };
            this.addCompetitionService.saveRequest(payload).subscribe((response: any) => {
                console.log(response);
                if(response.result.success){
                    this.toastrService.success('Richiesta inviata con successo.');
                                    
                } 

            });
        }
    }

    openModal(modalContent) {
        this.modalRef = this.modalService.open(modalContent, { container: '.app' });
    }

    public closeModal() {
        this.modalRef.close();
        //window.location.reload();
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

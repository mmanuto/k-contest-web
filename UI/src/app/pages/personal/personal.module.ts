import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { PersonalComponent } from './personal.component';
import { CustomFormsModule } from 'ng2-validation';
import { PersonalService } from './personal.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

export const routes: Routes = [
  { path: '', component: PersonalComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    CustomFormsModule,
    NgbModule,
    RouterModule.forChild(routes)
  ],
  declarations: [
    PersonalComponent
  ],
  providers: [
    PersonalService
  ]
})
export class PersonalModule { }
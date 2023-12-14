import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RegisterComponent } from './register.component';
import { CustomFormsModule } from 'ng2-validation';
import { RegisterService } from './register.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

export const routes: Routes = [
  { path: '', component: RegisterComponent, pathMatch: 'full' }
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
    RegisterComponent
  ],
  providers: [
    RegisterService
  ]
})
export class RegisterModule { }

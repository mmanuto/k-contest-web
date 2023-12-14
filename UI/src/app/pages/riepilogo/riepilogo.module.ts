import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { RiepilogoComponent } from './riepilogo.component';
import { CustomFormsModule } from 'ng2-validation';
import { RiepilogoService } from './riepilogo.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HomeService } from '../home/home.service';

export const routes: Routes = [
  { path: '', component: RiepilogoComponent, pathMatch: 'full' }
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
    RiepilogoComponent
  ],
  providers: [
    RiepilogoService, HomeService
  ]
})
export class RiepilogoModule { }

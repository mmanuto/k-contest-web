import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { MyAthletesComponent } from './my-athletes.component';
import { CustomFormsModule } from 'ng2-validation';
import { MyAthletesService } from './my-athletes.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HomeService } from '../home/home.service';

export const routes: Routes = [
  { path: '', component: MyAthletesComponent, pathMatch: 'full' }
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
    MyAthletesComponent
  ],
  providers: [
    MyAthletesService, HomeService
  ]
})
export class MyAthletesModule { }

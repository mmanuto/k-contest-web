import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HomeComponent } from './home.component';
import { CustomFormsModule } from 'ng2-validation';
import { HomeService } from './home.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

export const routes: Routes = [
  { path: '', component: HomeComponent, pathMatch: 'full' }
];


@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    CustomFormsModule,
    NgbModule,
    RouterModule.forChild(routes),
  ],
  declarations: [
    HomeComponent
  ],
  providers: [
    HomeService
  ]
})
export class HomeModule { }

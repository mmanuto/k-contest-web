import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CustomFormsModule } from 'ng2-validation';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { AddCompeetitionComponent } from './add-competition.component';
import { AddCompeetitionService } from './add-competition.service'

export const routes: Routes = [
  { path: '', component: AddCompeetitionComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    CustomFormsModule,
    RouterModule.forChild(routes)
  ],
  declarations: [
    AddCompeetitionComponent
  ],
  providers: [
    AddCompeetitionService
]
})

export class AddCompetitionModule { 

}
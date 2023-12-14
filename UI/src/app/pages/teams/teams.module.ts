import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { TeamsComponent } from './teams.component';
import { CustomFormsModule } from 'ng2-validation';
import { TeamsService } from './teams.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

export const routes: Routes = [
  { path: '', component: TeamsComponent, pathMatch: 'full' }
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
    TeamsComponent
  ],
  providers: [
    TeamsService
  ]
})
export class TeamsModule { }

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CompetitionsComponent } from './competitions.component';
import {CompetitionService } from './competition.service';
import { DirectivesModule } from '../../theme/directives/directives.module';

export const routes: Routes = [
  { path: '', component: CompetitionsComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    HttpClientModule,
    DirectivesModule,
    RouterModule.forChild(routes),
    FormsModule,
    ReactiveFormsModule,
  ],
  declarations: [
    CompetitionsComponent
  ],
  providers: [
    CompetitionService,
]
})

export class CompetitionsModule { 

}

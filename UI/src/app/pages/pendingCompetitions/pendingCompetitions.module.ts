import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { PendingCompetitionsComponent } from './pendingCompetitions.component';
import { PendingCompetitionService } from './pendingCompetition.service';
import { DirectivesModule } from '../../theme/directives/directives.module';

export const routes: Routes = [
  { path: '', component: PendingCompetitionsComponent, pathMatch: 'full' }
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
    PendingCompetitionsComponent
  ],
  providers: [
    PendingCompetitionService,
]
})

export class PendingCompetitionsModule { 

}

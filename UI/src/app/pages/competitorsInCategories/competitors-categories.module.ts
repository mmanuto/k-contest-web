import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CustomFormsModule } from 'ng2-validation';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { HomeService } from '../home/home.service';
import { CompetitorsInCategoriesService } from './competitors-categories.service';
import { CompetitorsInCategoriesComponent } from './competitors-categories.component';
import { NgxPaginationModule } from 'ngx-pagination';
import { ModalModule, BsModalService } from 'ngx-bootstrap/modal';
import { PipesModule } from '../../theme/pipes/pipes.module';
import { StageType } from 'brackets-model';

declare global {
  interface Window {
    bracketsViewer?: any | undefined;
  }

  interface Dataset {
    title: string;
    type: StageType;
    roster: { position: string, id: number, name: string, club:string }[];
  }

  interface Tabset {
    number: number;
    positions:  any;
  }
}

export const routes: Routes = [
  { path: '', component: CompetitorsInCategoriesComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    CustomFormsModule,
    NgbModule,
    RouterModule.forChild(routes),
    ModalModule.forRoot(),
    NgxPaginationModule,
    PipesModule
  ],
  declarations: [
    CompetitorsInCategoriesComponent
  ],
  providers: [
    CompetitorsInCategoriesService, HomeService, BsModalService
  ]
})
export class CompetitorsInCategoriesModule { }

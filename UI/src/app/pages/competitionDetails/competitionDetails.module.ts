import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CompetitionDetailsComponent } from './competitionDetails.component';
import { CompetitionDetailsService } from './competitionDetails.service';
import { DirectivesModule } from '../../theme/directives/directives.module';
import { AdminPanleComponent } from '../admin-panel/admin-panel.component';
import { AdminPanelService } from '../admin-panel/admin-panel.service';
import { BsModalService, ModalModule } from 'ngx-bootstrap/modal';
import { AthletesComponent } from '../athletes/athletes.component';
import { NgxDatatableModule } from '@swimlane/ngx-datatable';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { MultiselectDropdownModule } from 'angular-2-dropdown-multiselect';
import { NgxPaginationModule } from 'ngx-pagination';
import { PipesModule } from 'src/app/theme/pipes/pipes.module';
import { SelectDropDownModule } from 'ngx-select-dropdown';
import { Ng2SmartTableModule } from 'ng2-smart-table';
import { CategoriesComponent } from '../categories/categories.component';
import { TimetableComponent } from '../timetable/timetable.component';

export const routes: Routes = [
  { path: '', component: CompetitionDetailsComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    HttpClientModule,
    DirectivesModule,
    RouterModule.forChild(routes),
    ModalModule.forRoot(),
    FormsModule,
    ReactiveFormsModule,
    NgxDatatableModule,
    NgbModule,
    MultiselectDropdownModule,
    NgxPaginationModule,
    PipesModule,
    SelectDropDownModule,
    Ng2SmartTableModule,
    DirectivesModule
  ],
  declarations: [
    CompetitionDetailsComponent,
    AdminPanleComponent,
    AthletesComponent,
    CategoriesComponent,
    TimetableComponent
  ],
  providers: [
    CompetitionDetailsService,
    AdminPanelService,
    BsModalService
]
})

export class CompetitionDetailsModule { 

}

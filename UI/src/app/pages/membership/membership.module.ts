import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClientModule } from '@angular/common/http';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { NgxPaginationModule } from 'ngx-pagination';
import { PipesModule } from '../../theme/pipes/pipes.module';
import { MembershipComponent } from './membership.component';
import { MembershipService } from './membership.service';
import { HomeService } from '../home/home.service';
import { SelectDropDownModule } from 'ngx-select-dropdown'

export const routes: Routes = [
  { path: '', component: MembershipComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    HttpClientModule,
    //InMemoryWebApiModule.forRoot(MembershipData, { delay: 0 }),
    RouterModule.forChild(routes),
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    NgxPaginationModule,
    SelectDropDownModule,
    PipesModule
  ],
  declarations: [
    MembershipComponent
  ],
  providers: [ MembershipService, HomeService]
})
export class MembershipModule { }

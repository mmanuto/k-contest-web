import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { CustomFormsModule } from 'ng2-validation';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { RequestComponent } from './request.component';
import { RequestService } from './request.service'

export const routes = [
  { path: '', component: RequestComponent, pathMatch: 'full' }
];

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    CustomFormsModule,
    RouterModule.forChild(routes),
    NgbModule.forRoot()
  ],
  declarations: [
    RequestComponent
  ],
  providers: [
    RequestService
]
})
export class RequestModule { }

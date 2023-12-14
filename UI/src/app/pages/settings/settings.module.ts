import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Routes } from '@angular/router';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { SettingsComponent } from './settings.component';
import { CustomFormsModule } from 'ng2-validation';
import { SettingsService } from './settings.service';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';

export const routes: Routes = [
  { path: '', component: SettingsComponent, pathMatch: 'full' }
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
    SettingsComponent
  ],
  providers: [
    SettingsService
  ]
})
export class SettingsModule { }

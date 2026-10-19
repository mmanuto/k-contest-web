import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ClassificationsComponent } from './classifications.component';

@NgModule({
  declarations: [ClassificationsComponent],
  imports: [CommonModule, FormsModule, RouterModule.forChild([
    {path: '', component: ClassificationsComponent}
  ])]
})
export class ClassificationsModule {}

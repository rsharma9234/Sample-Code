import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { StatisticsRoutingModule } from './statistics-routing.module';
import { StatisticsComponent } from './statistics.component';
import { FlexLayoutModule } from '@angular/flex-layout';
import { ChartsModule } from '../../shared/modules/charts/charts.module';
import { MatFormFieldModule, MatInputModule, MatOptionModule, MatSelectModule, MatDatepickerModule, MatNativeDateModule, MatButtonModule } from '@angular/material';
import { ReactiveFormsModule } from '@angular/forms';
import {DatePipe} from '@angular/common';

@NgModule({
  imports: [
      CommonModule,
      StatisticsRoutingModule,
      ChartsModule,
      FlexLayoutModule,
      MatFormFieldModule,
      MatInputModule,
      MatOptionModule,
      MatSelectModule,
      MatNativeDateModule,
      MatDatepickerModule,
      ReactiveFormsModule,
      MatButtonModule
  ],
  declarations: [StatisticsComponent],
  providers:[DatePipe]
})
export class StatisticsModule { }

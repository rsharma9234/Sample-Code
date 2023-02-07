import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { CheckoutEmailComponent } from './checkout-email.component';

const routes: Routes = [
  {
      path: '',
      component: CheckoutEmailComponent
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class CheckoutEmailRoutingModule { }

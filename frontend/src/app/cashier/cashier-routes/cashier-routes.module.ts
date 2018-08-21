import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { CashierLayoutComponent } from '../cashier-layout/cashier-layout.component';
import { CashierDashboardComponent } from '../cashier-dashboard/cashier-dashboard.component';

const routes: Routes = [
  { path: '', component: CashierLayoutComponent, children: [
  	{ path: 'cashier', component: CashierDashboardComponent },
  ] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})

export class CashierRoutesModule { }
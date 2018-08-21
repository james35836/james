import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import { DashboardComponent } from '../dashboard/dashboard.component';
import { MemberComponent } from '../member/member.component';
import { ProductComponent } from '../product/product.component';
import { TestComponent } from '../test/test.component';
import { PayoutComponent } from '../payout/payout.component';
import { LoginComponent } from '../login/login.component';
import { CutoffComponent } from '../cutoff/cutoff.component';
import { MarketingComponent } from '../marketing/marketing.component';
import { CashierComponent } from '../cashier/cashier.component';
import { DeveloperComponent } from '../developer/developer.component';
import { ReportComponent } from '../report/report.component';
import { CashinComponent } from '../cashin/cashin.component';
import { MainRoutingRoutingModule } from './main-routing-routing.module';

const routes: Routes = [
  { path: '', component: MainLayoutComponent, children: [
  	{ path: 'admin', component: DashboardComponent },
  	{ path: 'admin/test', component: TestComponent },
    { path: 'admin/dashboard', component: DashboardComponent },
    { path: 'admin/test', component: TestComponent },
    { path: 'admin/member', component: MemberComponent },
    { path: 'admin/payout', component: PayoutComponent },
    { path: 'admin/cutoff', component: CutoffComponent },
    { path: 'admin/marketing', component: MarketingComponent },
    { path: 'admin/cashier', component: CashierComponent },
    { path: 'admin/developer', component: DeveloperComponent },
    { path: 'admin/report', component: ReportComponent },
    { path: 'admin/product', component: ProductComponent },
    { path: 'admin/cashin', component: CashinComponent },
  ] },
  { path: 'admin/login', component: LoginComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})
export class MainRoutingModule { }
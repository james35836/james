import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { MemberDashboardComponent } from '../member-dashboard/member-dashboard.component';
import { MemberCashinComponent } from '../member-cashin/member-cashin.component';
import { MemberCashoutComponent } from '../member-cashout/member-cashout.component';
import { MemberCodevaultComponent } from '../member-codevault/member-codevault.component';
import { MemberGenealogyComponent } from '../member-genealogy/member-genealogy.component';
import { MemberShoppingComponent } from '../member-shopping/member-shopping.component';
import { MemberShoppingProductComponent } from '../member-shopping-product/member-shopping-product.component';
import { MemberEarningComponent } from '../member-earning/member-earning.component';
import { MemberCheckoutComponent } from '../member-checkout/member-checkout.component';
import { MemberSlotComponent } from '../member-slot/member-slot.component';
import { MemberSettingsComponent } from '../member-settings/member-settings.component';
import { MemberOrderComponent } from '../member-order/member-order.component';
import { MemberLoginComponent } from '../member-login/member-login.component';
import { MemberRegisterComponent } from '../member-register/member-register.component';
import { MemberInitializeComponent } from '../member-initialize/member-initialize.component';


const routes: Routes = [
  { path: '', component: MemberLayoutComponent, children: [
    { path: 'member', component: MemberDashboardComponent },
  	{ path: 'member/dashboard', component: MemberDashboardComponent },
  	{ path: 'member/cash-in', component: MemberCashinComponent },
  	{ path: 'member/cash-out', component: MemberCashoutComponent },
  	{ path: 'member/codevault', component: MemberCodevaultComponent },
  	{ path: 'member/genealogy', component: MemberGenealogyComponent },
  	{ path: 'member/shopping', component: MemberShoppingComponent },
    { path: 'member/shopping/product', component: MemberShoppingProductComponent },
    { path: 'member/earning', component: MemberEarningComponent },
    { path: 'member/checkout', component: MemberCheckoutComponent },
    { path: 'member/slot', component: MemberSlotComponent },
    { path: 'member/settings', component: MemberSettingsComponent },
    { path: 'member/order', component: MemberOrderComponent },
    { path: 'member/initialize', component: MemberInitializeComponent },
  ]},
  { path: 'member/login', component: MemberLoginComponent },
  { path: 'member/register', component: MemberRegisterComponent },
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})

export class MemberRoutesModule { }
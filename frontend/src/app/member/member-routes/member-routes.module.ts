import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { MemberDashboardComponent } from '../member-dashboard/member-dashboard.component';
import { MemberSlotComponent } from '../member-slot/member-slot.component';
import { MemberSettingsComponent } from '../member-settings/member-settings.component';
import { MemberLoginComponent } from '../member-login/member-login.component';
import { MemberRegisterComponent } from '../member-register/member-register.component';
import { MemberInitializeComponent } from '../member-initialize/member-initialize.component';


const routes: Routes = [
  { path: '', component: MemberLayoutComponent, children: [
    { path: 'member', component: MemberDashboardComponent },
  	{ path: 'member/dashboard', component: MemberDashboardComponent },
  
  

    { path: 'member/slot', component: MemberSlotComponent },
    { path: 'member/settings', component: MemberSettingsComponent },
 
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
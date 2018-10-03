import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { AdminLayoutComponent } from '../admin-layout/admin-layout.component';
import { AdminDashboardComponent } from '../admin-dashboard/admin-dashboard.component';
import { AdminEventComponent } from '../admin-event/admin-event.component';
import { AdminFrontComponent } from '../admin-front/admin-front.component';
import { AdminJobComponent } from '../admin-job/admin-job.component';
import { AdminUserComponent } from '../admin-user/admin-user.component';
import { AdminStoryComponent } from '../admin-story/admin-story.component';


import { AdminRouteRoutingModule } from './admin-route-routing.module';

const routes: Routes = [
  { path: '', component: AdminLayoutComponent, children: [
  	{ path: 'admin', component: AdminDashboardComponent },
  	
    { path: 'admin/dashboard', component: AdminDashboardComponent },
    { path: 'admin/user', component: AdminUserComponent },
    { path: 'admin/front', component: AdminFrontComponent },
    { path: 'admin/event', component: AdminEventComponent },
    { path: 'admin/carrer', component: AdminJobComponent },
    { path: 'admin/story', component: AdminStoryComponent },
    
    
 
  ] },
 
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})
export class AdminRouteModule { }



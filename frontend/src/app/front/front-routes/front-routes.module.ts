import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';


import { FrontLayoutComponent } from '../front-layout/front-layout.component';
import { HomeComponent } from '../home/home.component';
import { ContactComponent } from '../contact/contact.component';
import { AboutComponent } from '../about/about.component';
import { EventComponent } from '../event/event.component';
import { JobComponent } from '../job/job.component';
import { StoryComponent } from '../story/story.component';


import { UserLoginComponent } from '../user-login/user-login.component';
import { UserRegisterComponent } from '../user-register/user-register.component';

import { FrontRoutesRoutingModule } from './front-routes-routing.module';


const routes: Routes = [
  { path: '', component: FrontLayoutComponent, children: [
  	{ path: '', component: HomeComponent },
    { path: 'home', component: HomeComponent },
    { path: 'about', component: AboutComponent },
    { path: 'event', component: EventComponent },
  	{ path: 'contact', component: ContactComponent },
    { path: 'login', component: UserLoginComponent },
    { path: 'carrer', component: JobComponent },
    { path: 'story', component: StoryComponent },
    { path: 'register', component: UserRegisterComponent }
  ] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})
export class FrontRoutesModule { }

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';


import { FrontLayoutComponent } from '../front-layout/front-layout.component';
import { HomeComponent } from '../home/home.component';
import { FeatureComponent } from '../feature/feature.component';
import { CompensationComponent } from '../compensation/compensation.component';
import { WorkComponent } from '../work/work.component';
import { ContactComponent } from '../contact/contact.component';


import { FrontRoutesRoutingModule } from './front-routes-routing.module';

const routes: Routes = [
  { path: '', component: FrontLayoutComponent, children: [
  	{ path: '', component: HomeComponent },
  	{ path: 'feature', component: FeatureComponent },
  	{ path: 'compensation', component: CompensationComponent },
  	{ path: 'work', component: WorkComponent },
  	{ path: 'contact', component: ContactComponent }
  ] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})
export class FrontRoutesModule { }

import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';

import { WizardLayoutComponent } from '../wizard-layout/wizard-layout.component';
import { Step1Component } from '../step1/step1.component';
import { Step2Component } from '../step2/step2.component';
import { Step3Component } from '../step3/step3.component';
import { Step4Component } from '../step4/step4.component';
import { Step5Component } from '../step5/step5.component';
import { Step6Component } from '../step6/step6.component';

import { WizardRoutingRoutingModule } from './wizard-routing-routing.module';

const routes: Routes = [
  { path: '', component: WizardLayoutComponent, children: [
  	{ path: 'wizard/1', component: Step1Component },
    { path: 'wizard/2', component: Step2Component },
    { path: 'wizard/3', component: Step3Component },
  	{ path: 'wizard/4', component: Step4Component },
    { path: 'wizard/5', component: Step5Component },
    { path: 'wizard/6', component: Step6Component }
  ] }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})
export class WizardRoutingModule { }

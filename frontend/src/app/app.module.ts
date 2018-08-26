import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { AngularFontAwesomeModule } from 'angular-font-awesome';
import { FormsModule }   from '@angular/forms';
import { CommonModule } from '@angular/common';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { ToastrModule } from 'ngx-toastr';
import { NgxPaginationModule } from 'ngx-pagination';
import { BsDropdownModule } from 'ngx-bootstrap/dropdown';
import { DragScrollModule } from 'ngx-drag-scroll';
import { CustomPipeModule } from './pipe/custom-pipe.module';
import { HttpClientModule, HttpClient } from '@angular/common/http';


import { FrontRoutesModule } from './front/front-routes/front-routes.module';
import { MemberRoutesModule } from './member/member-routes/member-routes.module';
import { WizardRoutingModule } from './admin/wizard/wizard-routing/wizard-routing.module';
import { MainRoutingModule } from './admin/main/main-routing/main-routing.module';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { FrontLayoutComponent } from './front/front-layout/front-layout.component';
import { WizardLayoutComponent } from './admin/wizard/wizard-layout/wizard-layout.component';
import { MainLayoutComponent } from './admin/main/main-layout/main-layout.component';

import { HomeComponent } from './front/home/home.component';

import { ContactComponent } from './front/contact/contact.component';

import { Step1Component } from './admin/wizard/step1/step1.component';
import { Step2Component } from './admin/wizard/step2/step2.component';
import { Step3Component } from './admin/wizard/step3/step3.component';
import { Step4Component } from './admin/wizard/step4/step4.component';
import { Step5Component } from './admin/wizard/step5/step5.component';
import { Step6Component } from './admin/wizard/step6/step6.component';
import { DashboardComponent } from './admin/main/dashboard/dashboard.component';
import { MemberComponent } from './admin/main/member/member.component';
import { TestComponent } from './admin/main/test/test.component';

import { LoginComponent } from './admin/main/login/login.component';
import { ProductComponent } from './admin/main/product/product.component';
import { UserService } from './user.service';
import { PayoutComponent } from './admin/main/payout/payout.component';
import { CutoffComponent } from './admin/main/cutoff/cutoff.component';
import { CashierComponent } from './admin/main/cashier/cashier.component';
import { MarketingComponent } from './admin/main/marketing/marketing.component';
import { DeveloperComponent } from './admin/main/developer/developer.component';
import { ReportComponent } from './admin/main/report/report.component';
import { Ng2GoogleChartsModule } from 'ng2-google-charts';
import { MemberLayoutComponent } from './member/member-layout/member-layout.component';
import { MemberDashboardComponent } from './member/member-dashboard/member-dashboard.component';
import { MemberSlotComponent } from './member/member-slot/member-slot.component';
import { MemberSettingsComponent } from './member/member-settings/member-settings.component';
import { CashinComponent } from './admin/main/cashin/cashin.component';

import { MemberRegisterComponent } from './member/member-register/member-register.component';
import { MemberLoginComponent } from './member/member-login/member-login.component';

//social
import { SocialLoginModule, AuthServiceConfig, GoogleLoginProvider, FacebookLoginProvider} from "angular-6-social-login";
import { MemberInitializeComponent } from './member/member-initialize/member-initialize.component';
import { AboutComponent } from './front/about/about.component';
import { JobComponent } from './front/job/job.component';
import { EventComponent } from './front/event/event.component';
import { UserLoginComponent } from './front/user-login/user-login.component';
import { MemberDirectoryComponent } from './member/member-directory/member-directory.component';
import { MemberChatComponent } from './member/member-chat/member-chat.component';

export function getAuthServiceConfigs() {
  let config = new AuthServiceConfig(
      [
        {
          id: FacebookLoginProvider.PROVIDER_ID,
          provider: new FacebookLoginProvider("216088645731820") // developer.facebook.com
        },
        {
          id: GoogleLoginProvider.PROVIDER_ID,
          provider: new GoogleLoginProvider("490702702916-ugf6s2t4em30a407oht7k7jm8va5o02r.apps.googleusercontent.com") // developers.google.com
        }
      ]
  );
  return config;
}

@NgModule({
  declarations: [
    AppComponent,
    FrontLayoutComponent,
    HomeComponent,
    AboutComponent,
    JobComponent,
    EventComponent,

    ContactComponent,

    Step1Component,
    Step2Component,
    Step3Component,
    Step4Component,
    Step5Component,
    Step6Component,
    DashboardComponent,
    MemberComponent,
    WizardLayoutComponent,
    MainLayoutComponent,
    TestComponent,
    LoginComponent,
    ProductComponent,
    PayoutComponent,
    CutoffComponent,
    CashierComponent,
    MarketingComponent,
    DeveloperComponent,
    ReportComponent,
    MemberLayoutComponent,
    MemberDashboardComponent,
  
    MemberSlotComponent,
    MemberSettingsComponent,
  
    CashinComponent,
    MemberRegisterComponent,
    MemberLoginComponent,
    MemberInitializeComponent,
    UserLoginComponent,
    MemberDirectoryComponent,
    MemberChatComponent
    
  ],
  imports: [
    HttpClientModule,
    BrowserModule,
    FrontRoutesModule,
    AppRoutingModule,
    WizardRoutingModule,
    AngularFontAwesomeModule,
    MainRoutingModule,
    MemberRoutesModule,
    FormsModule,
    Ng2GoogleChartsModule,
    CommonModule,
    BrowserAnimationsModule,
    ToastrModule.forRoot(),
    NgxPaginationModule,
    DragScrollModule,
    SocialLoginModule,
    BsDropdownModule.forRoot(),
    CustomPipeModule
  ],
  providers: [
    UserService,
    {
      provide: AuthServiceConfig,
      useFactory: getAuthServiceConfigs
    }],
  bootstrap: [AppComponent]
})
export class AppModule { }

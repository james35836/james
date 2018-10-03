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
import { AdminRouteModule } from './admin/admin-route/admin-route.module';




import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';

import { FrontLayoutComponent } from './front/front-layout/front-layout.component';

import { HomeComponent } from './front/home/home.component';

import { ContactComponent } from './front/contact/contact.component';


import { LoginComponent } from './admin/main/login/login.component';
import { UserService } from './user.service';
import { Ng2GoogleChartsModule } from 'ng2-google-charts';
import { MemberLayoutComponent } from './member/member-layout/member-layout.component';
import { MemberDashboardComponent } from './member/member-dashboard/member-dashboard.component';
import { MemberSettingsComponent } from './member/member-settings/member-settings.component';

//social
import { SocialLoginModule, AuthServiceConfig, GoogleLoginProvider, FacebookLoginProvider} from "angular-6-social-login";
import { MemberInitializeComponent } from './member/member-initialize/member-initialize.component';
import { AboutComponent } from './front/about/about.component';
import { JobComponent } from './front/job/job.component';
import { EventComponent } from './front/event/event.component';
import { UserLoginComponent } from './front/user-login/user-login.component';
import { MemberDirectoryComponent } from './member/member-directory/member-directory.component';
import { MemberChatComponent } from './member/member-chat/member-chat.component';
import { UserRegisterComponent } from './front/user-register/user-register.component';
import { MemberTimelineComponent } from './member/member-timeline/member-timeline.component';
import { AdminDashboardComponent } from './admin/admin-dashboard/admin-dashboard.component';
import { AdminLayoutComponent } from './admin/admin-layout/admin-layout.component';
import { StoryComponent } from './front/story/story.component';
import { AdminEventComponent } from './admin/admin-event/admin-event.component';
import { AdminStoryComponent } from './admin/admin-story/admin-story.component';
import { AdminJobComponent } from './admin/admin-job/admin-job.component';
import { AdminUserComponent } from './admin/admin-user/admin-user.component';
import { AdminFrontComponent } from './admin/admin-front/admin-front.component';
import { MemberGalleryComponent } from './member/member-gallery/member-gallery.component';

export function getAuthServiceConfigs() {
  let config = new AuthServiceConfig(
      [
        {
          id: FacebookLoginProvider.PROVIDER_ID,
          provider: new FacebookLoginProvider("681218792265032") // developer.facebook.com
        },
        {
          id: GoogleLoginProvider.PROVIDER_ID,
          provider: new GoogleLoginProvider("296381879262-ovra98bvgenrdbe5cmq92vejt348foif.apps.googleusercontent.com") // developers.google.com
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

    
  
    
    LoginComponent,

    
    MemberLayoutComponent,
    MemberDashboardComponent,
  
    MemberSettingsComponent,
  
    
    MemberInitializeComponent,
    UserLoginComponent,
    MemberDirectoryComponent,
    MemberChatComponent,
    UserRegisterComponent,
    MemberTimelineComponent,
    AdminDashboardComponent,
    AdminLayoutComponent,
    StoryComponent,
    AdminEventComponent,
    AdminStoryComponent,
    AdminJobComponent,
    AdminUserComponent,
    AdminFrontComponent,
    MemberGalleryComponent
  ],
  imports: [
    HttpClientModule,
    BrowserModule,
    FrontRoutesModule,
    AdminRouteModule,
    AppRoutingModule,
    AngularFontAwesomeModule,
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

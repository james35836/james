import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Routes, RouterModule } from '@angular/router';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { MemberDashboardComponent } from '../member-dashboard/member-dashboard.component';
import { MemberSettingsComponent } from '../member-settings/member-settings.component';
import { MemberDirectoryComponent } from '../member-directory/member-directory.component';

import { MemberChatComponent } from '../member-chat/member-chat.component';
import { MemberTimelineComponent } from '../member-timeline/member-timeline.component';

import { MemberInitializeComponent } from '../member-initialize/member-initialize.component';
import { MemberGalleryComponent } from '../member-gallery/member-gallery.component';


const routes: Routes = [
  { path: '', component: MemberLayoutComponent, children: [
    { path: 'member', component: MemberDashboardComponent },
  	{ path: 'member/dashboard', component: MemberDashboardComponent },
  
  
    { path: 'member/settings', component: MemberSettingsComponent },
 
    { path: 'member/directory', component: MemberDirectoryComponent },
    { path: 'member/chat', component: MemberChatComponent },
    { path: 'member/timeline', component: MemberTimelineComponent },
    { path: 'member/initialize', component: MemberInitializeComponent },
    { path: 'member/gallery', component: MemberGalleryComponent },
  ]},
 
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
  declarations: []
})

export class MemberRoutesModule { }
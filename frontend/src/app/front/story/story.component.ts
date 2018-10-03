
import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { Router } from '@angular/router';

import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular-6-social-login';

@Component({
  selector: 'app-story',
  templateUrl: './story.component.html',
  styleUrls: ['./story.component.scss']
})
export class StoryComponent implements OnInit
{
	submitted     	= false;
	error_message 	= null;
	headers       	= null;
	story_list :any = null;
	story_details:any = {};
	enabled     :any  = "list";
	key               = 0;

	constructor(private userService: UserService, private router: Router, private http: HttpClient) 
	{

	}

	ngOnInit() 
	{
		this.load_story();
	}
	load_story()
	{
		this.http.post(this.userService.domain + "/api/get_story", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.story_list = response;
			console.log(this.story_list);
		},
		error =>
		{
			console.log(error,"dsad");
		});
	}

	event_view_details(key)
	{
		this.story_details = this.story_list[key];
		this.key           = key;
		this.enabled       = "details";


	}
	go_to(content)
	{
		this.enabled       = content;
	}
	
}




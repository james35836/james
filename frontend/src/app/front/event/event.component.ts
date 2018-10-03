import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { Router } from '@angular/router';

import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular-6-social-login';

@Component({
  selector: 'app-event',
  templateUrl: './event.component.html',
  styleUrls: ['./event.component.scss']
})
export class EventComponent implements OnInit
{
	submitted     	= false;
	error_message 	= null;
	headers       	= null;
	event_list :any = null;
	event_details:any = {};
	enabled     :any  = "list";
	key               = 0;

	constructor(private userService: UserService, private router: Router, private http: HttpClient) 
	{

	}

	ngOnInit() 
	{
		this.load_event();
	}
	load_event()
	{
		this.http.post(this.userService.domain + "/api/get_event", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.event_list = response;
			console.log(this.event_list);
		},
		error =>
		{
			console.log(error,"dsad");
		});
	}

	event_view_details(key)
	{
		this.event_details = this.event_list[key];
		this.key           = key;
		this.enabled       = "details";


	}
	go_to(content)
	{
		this.enabled       = content;
	}
	
}




import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { Router } from '@angular/router';

import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular-6-social-login';

@Component({
  selector: 'app-job',
  templateUrl: './job.component.html',
  styleUrls: ['./job.component.scss']
})
export class JobComponent implements OnInit 
{

	submitted     	= false;
	error_message 	= null;
	headers       	= null;
	job_list :any = null;
	job_details:any = {};
	enabled     :any  = "list";
	key               = 0;

	constructor(private userService: UserService, private router: Router, private http: HttpClient) 
	{

	}

	ngOnInit() 
	{
		this.load_carrer();
	}
	load_carrer()
	{
		this.http.post(this.userService.domain + "/api/get_carrer", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.job_list = response;
			console.log(this.job_list);
		},
		error =>
		{
			console.log(error,"dsad");
		});
	}

	job_view_details(key)
	{
		this.job_details = this.job_list[key];
		this.key           = key;
		this.enabled       = "details";


	}
	go_to(content)
	{
		this.enabled       = content;
	}
	
}





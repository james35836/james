import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';

import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular-6-social-login';

@Component({
  selector: 'app-contact',
  templateUrl: './contact.component.html',
  styleUrls: ['./contact.component.scss']
})
export class ContactComponent implements OnInit
{
	submitted     		= false;
	error_message 		= null;
	headers       		= null;

	guest_details   :any= {};
	

	constructor(private rest: UserService, private router: Router, private http: HttpClient, private toastr: ToastrService) 
	{

	}

	ngOnInit() 
	{
		
	}
	submitEmail()
	{
		this.submitted = true;
		this.http.post(this.rest.domain + "/api/submit_email", this.guest_details, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.submitted = false;
			if(response["status"] == "success")
			{
				this.guest_details = new Array(Number(7)).fill("");
				this.toastr.success(response["status_message"], 'SUCCESS');
			}
			else
			{

				for (let data of response["status_message"]) 
				{
					this.toastr.error(data, 'Error');
				}
			}
		},
		error =>
		{
			console.log(error);
		});
	}

	
	
}





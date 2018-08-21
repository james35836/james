import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../../user.service';
import { User } from '../../../user';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit 
{
  	model         = new Login('', '');
	submitted     = false;
	error_message = null;
	auth		  = localStorage.getItem('auth');

	constructor(private userService: UserService, private router: Router) 
	{
		if (this.auth) 
		{
			this.router.navigate(['/admin']);
		}
	}

	ngOnInit() 
	{

	}

	ngOnDestroy()
	{

	}

	onSubmit()
	{
		this.submitted = true;
		this.userService.getClientSecret()
		.subscribe(get_secret => 
		{
			if (get_secret) 
			{
				this.userService.getAccessToken(this.model.email, this.model.password, get_secret["secret"])
		        .subscribe(data => 
		        {
		        	localStorage.setItem('auth', data["access_token"]);

		        	this.userService.getUserData(localStorage.getItem('auth'))
			        .subscribe(user_data => 
			        {
			            if (user_data["type"] == "admin") 
			            {
			            	localStorage.setItem('type', 'admin');
			            	this.router.navigate(['/admin']);
			            }
			            else if (user_data["type"] == "member") 
			            {
			            	localStorage.setItem('type', 'member');
			            	this.router.navigate(['/member']);
			            }
			            else
			            {
			            	localStorage.removeItem('auth');

			            	this.error_message = "Your account does not exist.";
		        			this.submitted = false;
			            }
			        },
			        error =>
			        {
			        	localStorage.removeItem('auth');

			        	this.error_message = error.error.hint ? error.error.hint : error.error.message;
		        		this.submitted = false;
			        });
		        },
		        error => 
		        {
		        	this.error_message = error.error.hint ? error.error.hint : error.error.message;
		        	this.submitted = false;
		        });
			}
			else
			{
				this.error_message = "Some error occurred. Please contact the administrator.";
	        	this.submitted = false;
			}
		});
	}

}

export class Login 
{

  constructor(
    public email: string,
    public password: string,
  ) {  }

}
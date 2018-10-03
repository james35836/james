import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { Router } from '@angular/router';

import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular-6-social-login';

@Component({
  selector: 'app-user-login',
  templateUrl: './user-login.component.html',
  styleUrls: ['./user-login.component.scss']
})
export class UserLoginComponent implements OnInit
{
	model         = new Login('', '');
	submitted     = false;
	error_message = null;
	auth		  = localStorage.getItem('auth');
	social_id : any = {};
	social_datas : any = {};
	signup_params : any = {};
	username : any;
	password = null;
	headers = null;
	data: any;
	schol_year_list :any = null;

	constructor(private socialAuthService: AuthService, private userService: UserService, private router: Router, private http: HttpClient) 
	{

		if(this.auth) 
		{
			this.router.navigate(['/admin']);
		}
	}

	ngOnInit() 
	{
		this.loadSchoolYear();
	}
	loadSchoolYear()
	{
		this.http.post(this.userService.domain + "/api/get_school_year", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.schol_year_list = response;
		},
		error =>
		{
			console.log(error,"dsad");
		});
	}

	ngOnDestroy()
	{

	}

	socialSignIn(socialPlatform : string) 
	{
		let socialPlatformProvider;
		if(socialPlatform == "facebook")
		{
			socialPlatformProvider = FacebookLoginProvider.PROVIDER_ID;
		}
		else if(socialPlatform == "google")
		{
			socialPlatformProvider = GoogleLoginProvider.PROVIDER_ID;
		}

		this.socialAuthService.signIn(socialPlatformProvider).then(
			(userData) => 
			{
				this.social_id = userData;
				if(socialPlatform == "facebook")
				{
					this.social_datas = "https://graph.facebook.com/"+this.social_id.id+"?access_token="+this.social_id.token;
				}
				else
				{
					this.social_datas = "https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token="+this.social_id.token;
				}

				this.http.get(this.social_datas).subscribe(data=>
				{
					this.onSubmit(data["id"], socialPlatform);
				})
			}
			);
	}

	onSubmit(social = null , platform : string = "system")
	{
		this.submitted = true;
		this.username 	= platform == "system" ? this.model.email : social;

		if(platform != "system")
		{
			this.http.post(this.userService.domain + "/api/member/check_credentials", 
				{
					member : this.username
				},
				{}).subscribe(response=>
			{
				if(response)
				{
					this.password = response;
					this.login(platform, this.password)
				}
				else
				{
					this.error_message = "The user credentials were incorrect."
					this.submitted = false;
				}
			});
		}
		else
		{
			this.login(platform, this.model.password);
		}
		
	}

	login(platform, password)
	{
		if(this.model.email == this.model.password && platform == "system")
		{
			this.error_message = "The user credentials were incorrect."
			this.submitted = false;
		}
		else 
		{
			this.userService.getClientSecret()
			.subscribe(get_secret => 
			{
				if (get_secret) 
				{
					this.userService.getAccessToken(this.username, password, get_secret["secret"])
					.subscribe(data => 
					{
						localStorage.setItem('auth', data["access_token"]);

						this.userService.getUserData(localStorage.getItem('auth'))
						.subscribe(user_data => 
						{

							if (user_data["type"] == "admin") 
							{
								localStorage.setItem('type', 'admin');
								this.router.navigate(['/admin/dashboard']);
							}
							else if (user_data["type"] == "member") 
							{
								localStorage.setItem('type', 'member');
								this.router.navigate(['/member/dashboard']);
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
}


export class Login 
{

	constructor(
		public email: string,
		public password: string,
		) {  }

}

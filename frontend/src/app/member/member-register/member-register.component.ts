import { Component, OnInit, OnDestroy } from '@angular/core';
import * as $ from 'jquery';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService, FacebookLoginProvider, GoogleLoginProvider } from 'angular-6-social-login';

@Component({
	selector: 'app-member-register',
	templateUrl: './member-register.component.html',
	styleUrls: ['./member-register.component.scss']
})
export class MemberRegisterComponent implements OnInit {

	add_member            :any = {};
	country_list          :any = null;
	model         			 = new Login('', '');
	submitted     			 = false;
	error_message 			 = null;
	auth		  				 = localStorage.getItem('auth');
	social_id : any = {};
	social_datas : any = {};
	signup_params : any = {};
	login_id : string;

	constructor(private socialAuthService: AuthService, private rest: UserService,private toastr: ToastrService, private http: HttpClient, private router: Router) { }

	ngOnInit() 
	{
		this.load_country();
	}

	socialSignUp(socialPlatform : string) 
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
					this.social_datas = "https://graph.facebook.com/"+this.social_id.id+"?access_token="+this.social_id.token+"&fields=first_name,last_name,email";
				}
				else
				{
					this.social_datas = "https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token="+this.social_id.token;
				}

				this.http.get(this.social_datas).subscribe(data=>
				{
					this.onSubmit(data, socialPlatform);
				})
			}
			);
	}


	onSubmit(social = null, platform : string = "system")
	{
		this.signup_params = social ? social : this.add_member;
		this.signup_params.register_platform = platform;
		this.signup_params.social_id 		 = platform == "system" ? null : social.id;

		if(platform == "google")
		{
			this.signup_params.first_name = this.signup_params.given_name;
			this.signup_params.last_name = this.signup_params.family_name;
		}

		this.http.post(this.rest.domain + "/api/new_register", this.signup_params, 
		{

		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.model.email    = platform == "system" ? this.add_member.email : this.signup_params.id;
				this.model.password = platform == "system" ? this.add_member.password : this.signup_params.id;
				this.login_function();
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

	login_function()
	{
		this.submitted = true;
		this.rest.getClientSecret()
		.subscribe(get_secret => 
		{
			if (get_secret) 
			{
				this.rest.getAccessToken(this.model.email, this.model.password, get_secret["secret"])
				.subscribe(data => 
				{
					localStorage.setItem('auth', data["access_token"]);

					this.rest.getUserData(localStorage.getItem('auth'))
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

	load_country()
	{
		this.http.post(this.rest.domain+"/api/get_country", {}, 
		{

		})
		.subscribe(response =>
		{
			this.country_list = response;
			if(response != null)
			{
				this.add_member.country_id = this.country_list[0].country_id;
			}
		},
		error =>
		{
			console.log(error);
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
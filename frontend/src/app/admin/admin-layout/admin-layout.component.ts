import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-admin-layout',
  templateUrl: './admin-layout.component.html',
  styleUrls: ['./admin-layout.component.scss']
})


export class AdminLayoutComponent implements OnInit 
{
	auth    = localStorage.getItem('auth');
	type	= localStorage.getItem('type');
	user    = null;
	headers = null;
	

  	constructor(private userService: UserService, private router: Router, private http: HttpClient) 
	{
		if (!this.auth) 
		{
			this.router.navigate(['/login']);
		}

		if(this.type != 'admin')
		{
			localStorage.clear();
			this.router.navigate(['/']);
		}

		this.headers = new HttpHeaders({
            "Accept": "application/json",
            "Authorization": "Bearer " + this.auth,
        });
	    this.getUserData(this.auth);
	}

	ngOnInit() 
	{
		
		
	}

	onLogout()
	{
		this.http.post(this.userService.domain + "/api/logout", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			localStorage.clear();

			this.router.navigate(['/']);
		},
		error =>
		{
			console.log(error);
		});
	}
	

	getUserData(accessToken: string) 
	{
	    this.userService.getUserData(accessToken)
        .subscribe(user_data => 
        {
            this.user = user_data;
        },
        error =>
        {
        	localStorage.clear();
        });
	}

	
}

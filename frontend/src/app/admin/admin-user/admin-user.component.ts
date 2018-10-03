import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AdminLayoutComponent } from '../admin-layout/admin-layout.component';
import { ToastrService } from 'ngx-toastr';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-admin-user',
  templateUrl: './admin-user.component.html',
  styleUrls: ['./admin-user.component.scss']
})

export class AdminUserComponent implements OnInit 
{
	
	headers  = null;
	users_list    :any = null;

  	
	constructor(private rest: UserService, private http: HttpClient, private layout: AdminLayoutComponent, private toastr: ToastrService) 
	{

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.load_user()
	}

	load_user()
	{
		this.http.post(this.rest.domain + "/api/admin_user/get_users", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.users_list = response;
			console.log(this.users_list);
		},
		error =>
		{
			console.log(error);
		});
	}
	

	

	
}


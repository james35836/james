import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';
@Component({
  selector: 'app-member-directory',
  templateUrl: './member-directory.component.html',
  styleUrls: ['./member-directory.component.scss']
})
export class MemberDirectoryComponent implements OnInit {

	headers    				= null;
	enabled    				= "list";
	member_list :any		=null;
	member_details :any		=null;
	connection_status :any 	=0;
	key               :any  = null;

	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit() 
	{
		
		this.load_member();
	}

	memeber_details(key)
	{
		this.key = key;
		this.enabled = "details";
		this.member_details = this.member_list[key];
		
	}

	check_status()
	{
		this.http.post(this.rest.domain + "/api/member_directory/check_status",this.member_details, 
		{
			headers: this.layout.headers
		})
		.subscribe(response =>
		{
			this.connection_status = response;
		
		},
		error =>
		{
			console.log(error);
		});
	}



	go_to(enabled)
	{
		this.enabled = enabled;

	}

	load_member()
	{
		this.http.post(this.rest.domain + "/api/member_directory/get_member", {}, 
		{
			headers: this.layout.headers
		})
		.subscribe(response =>
		{
			this.member_list = response;
			console.log(this.member_list);
		},
		error =>
		{
			console.log(error);
		});
	}
	connect_now(id,status)
	{
		this.http.post(this.rest.domain + "/api/member_directory/connect_submit", {user_id:id,status:status}, 
		{
			headers: this.layout.headers
		})
		.subscribe(response =>
		{
			this.member_list = response;
			this.check_status();
		},
		error =>
		{
			console.log(error);
		});
	}
}

import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-earning',
  templateUrl: './member-earning.component.html',
  styleUrls: ['./member-earning.component.scss']
})

export class MemberEarningComponent implements OnInit
{
	headers                       = null;
	active_complan 			: any = {};
	direct_log              : any = null;
	indirect_log            : any = null;
	binary_log              : any = null;
	unilevel_log            : any = null;
	stairstep_log            : any = null;

	constructor(private rest: UserService, private http: HttpClient, private layout: MemberLayoutComponent, private toastr: ToastrService)
	{
		this.headers = this.layout.headers;
	}

	ngOnInit()
	{
		this.initialize();
		this.direct_earning();
		this.indirect_earning();
		this.binary_earning();
		this.unilevel_earning();
		this.stairstep_earning();
	}

	direct_earning()
	{
		this.http.post(this.rest.domain + "/api/earning/direct", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
		    this.direct_log = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	indirect_earning()
	{
		this.http.post(this.rest.domain + "/api/earning/indirect", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
		    this.indirect_log = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	binary_earning()
	{
		this.http.post(this.rest.domain + "/api/earning/binary", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
		    this.binary_log = response;
			console.log(response);
			console.log(123123);
		},
		error => 
		{
			console.log(error);
		});
	}

	unilevel_earning()
	{
		this.http.post(this.rest.domain + "/api/earning/unilevel", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
		    this.unilevel_log = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	stairstep_earning()
	{
		this.http.post(this.rest.domain + "/api/earning/stairstep", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
		    this.stairstep_log = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	initialize()
	{
		this.active_complan = "direct";
	}

	changeActiveComplan(new_active_complan)
	{
		this.active_complan = new_active_complan;
	}
}

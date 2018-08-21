import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-codevault',
  templateUrl: './member-codevault.component.html',
  styleUrls: ['./member-codevault.component.scss']
})
export class MemberCodevaultComponent implements OnInit 
{
	user 	= localStorage.getItem('identification');
	length 	= localStorage.getItem('id');
	user_id = null;
	headers = null;
	codeList:any = [];
	codeFilter:any ={};
	claimcodeList:any=[];
	constructor(private rest: UserService, private http: HttpClient, private layout: MemberLayoutComponent, private toastr: ToastrService,private router: Router) 
	{ 
		
	}	

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.get_codes();
		this.get_claimCodes();
	}	

	get_codes()
	{
		this.user_id = parseInt(this.user.substring(this.user.length - parseInt(this.length)));
		this.http.post(this.rest.domain + "/api/get_codes", { user_id : this.user_id}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.codeList = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	code_filter()
	{
		console.log(this.codeFilter);
	}
	
	get_claimCodes()
	{
		this.http.post(this.rest.domain + "/api/get_claim_codes", { slot_id : this.layout.current_slot.slot_id}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.claimcodeList = response;
		},
		error => 
		{
			console.log(error);
		});
	}
}

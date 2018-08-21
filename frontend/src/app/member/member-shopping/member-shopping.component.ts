import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';



@Component({
selector: 'app-member-shopping',
templateUrl: './member-shopping.component.html',
styleUrls: ['./member-shopping.component.scss']

})
export class MemberShoppingComponent implements OnInit 
{
	headers = null;
	membershipList: any = [];
	productList: any = []
	constructor(private rest: UserService, private http: HttpClient, private layout: MemberLayoutComponent, private toastr: ToastrService,private router: Router) 
	{ 

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.load_products();
		localStorage.removeItem('item_id');
	}

	load_products()
	{
		this.http.post(this.rest.domain + "/api/member/get_all_products", 
		{ 
			
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{	
			this.membershipList = response['membership_kit'];
			this.productList = response['product'];
		},
		error => 
		{
			console.log(error);
		});
	}

	product_id(item_id)
	{
		localStorage.setItem('item_id', item_id);
	}

}

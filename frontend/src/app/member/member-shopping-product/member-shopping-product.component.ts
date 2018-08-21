import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-shopping-product',
  templateUrl: './member-shopping-product.component.html',
  styleUrls: ['./member-shopping-product.component.scss']
})
export class MemberShoppingProductComponent implements OnInit 
{
	headers = null;
	item_id = null;
	productInfo: any = [];
	cart_items: any = [];
	constructor(private rest: UserService, private http: HttpClient, private layout: MemberLayoutComponent, private toastr: ToastrService,private router: Router) 
	{ 

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.item_id = localStorage.getItem('item_id');
		this.load_product();
	}

	load_product()
	{
		this.http.post(this.rest.domain + "/api/member/get_product", 
		{ 
			item_id : this.item_id
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{	
			this.productInfo = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	add_cart(item_id)
	{
		this.layout.get_cart(item_id);
	}

}

import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-checkout',
  templateUrl: './member-checkout.component.html',
  styleUrls: ['./member-checkout.component.scss']
})
export class MemberCheckoutComponent implements OnInit {

	checkout_items = [];
	grandTotal = null;
	checkout_method = null;
	headers = null;
	branchList = {};
	pickup_location = null;
	subTotal= null;
	current_wallet:any;
	wallet = null;
	current_slot = {};
	constructor(private rest: UserService, private http: HttpClient, private layout: MemberLayoutComponent, private toastr: ToastrService,private router: Router)
	{

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers
		this.wallet = this.layout.wallet
		this.current_slot = this.layout.current_slot
		this.get();
		this.get_branch();
		this.changeActiveComplan();
		this.get_php_wallet();
	}

	get()
	{
		this.checkout_items = JSON.parse(localStorage.getItem('checkout_items'));
		this.get_total();
	}

	get_php_wallet()
	{
		let holder = 0;
		$.each(this.wallet, function(key,value)
		{
			if(value.currency_id == 1)
			{
				holder = value.wallet.wallet_amount
			}
		})
		this.current_wallet = holder;
	}

	get_total()
	{
		let sum = 0;
		for( let items of this.checkout_items)
		{
			
			sum = sum + (items.item_price * items.item_qty);
			
		}

		this.subTotal = sum;
	}

	changeActiveComplan(method = null)
	{
		if(method == 'direct'|| method == null)
		{
			this.checkout_method = 'direct';
			this.grandTotal = this.subTotal;
		}
		else
		{
			this.checkout_method = 'indirect';
			this.grandTotal = this.subTotal + 120;
		}
	}

	checkout_submit()
	{
		if(this.checkout_method != 'direct')
		{
			this.pickup_location = null
		}
		this.http.post(this.rest.domain + "/api/checkout", 
		{
			method  : this.checkout_method,
			slot 	: this.current_slot,
			items 	: this.checkout_items,
			branch_id: this.pickup_location
		}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
			if(response['status_code'] == 400)
			{
				this.toastr.error(response['status_message'], "Error");
			}
			else
			{
				this.toastr.success(response['status_message'], 'Success');
			}
			
		},
		error => 
		{
			if (typeof error.error.status_message != 'undefined') 
			{
				for (let data of error.error.status_message) 
				{
					this.toastr.error(data, 'Error');
				}
			}
		});
	}

	

	get_branch()
	{
		this.http.post(this.rest.domain + "/api/get_branch", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.branchList = response;
			
		},
		error => 
		{
			console.log(error);
		});
	}
}

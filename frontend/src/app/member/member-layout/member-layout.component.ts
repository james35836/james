import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';
import Swiper from 'swiper';

@Component({
  selector: 'app-member-layout',
  templateUrl: './member-layout.component.html',
  styleUrls: ['./member-layout.component.scss'],
})
export class MemberLayoutComponent implements OnInit 
{
	auth   					 	= localStorage.getItem('auth');
	type						= localStorage.getItem('type');
	user    					= null;
	headers 					= null;
	cart_items : any 			= [];
	show_cart : any 			= [];
	cart_item_qty : any 		= [];
	total 						= null;
	cart_count 					= null;
	current_slot 				= null;
	wallet 						= "0.00";
	initialize_loading_message 	= "Initializing...";


	constructor(private userService: UserService, private rest: UserService, private router: Router, private http: HttpClient)
	{ 
		if (!this.auth) 
		{
			this.router.navigate(['/admin/login']);
		}

		if (this.type !='member')
		{
			this.router.navigate(['/'+this.type]);
		}

		this.initialize_page();
	}

	initialize_page()
	{
		this.router.navigate(['/member/initialize']);
	}

	ngOnInit() 
	{
		this.navigationSwiper();
		this.sideNav();
		this.sideNavOn();
		this.sideNavOff();
	}

	sideNav(): void
	{
		const $menuLeft = $('.pushmenu-left');
		const $nav_list = $('#nav_list');

		$nav_list.on("click", function () {
			$(this).toggleClass('active');
			/* $('.pushmenu-push').toggleClass('pushmenu-push-toright');*/
			$menuLeft.toggleClass('pushmenu-open');
		});
	}

	sideNavOn(): void
	{
		document.getElementById("overlay").style.display = "block";
	}
	
	sideNavOff(): void
	{
		document.getElementById("overlay").style.display = "none";
		$('.pushmenu').removeClass("pushmenu-open");
	}

	navigationSwiper(): void
	{
		const swiper = new Swiper('.nav-swiper', {
			slidesPerView: 3,
			spaceBetween: 0,
			freeMode: true,
			breakpoints: {
				1024: {
					slidesPerView: 5
				},
				768: {
					slidesPerView: 5
				},
				640: {
					slidesPerView: 3
				}
			}
		});
	}

	onLogout()
	{
		this.http.post(this.userService.domain + "/api/logout", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			localStorage.removeItem('auth');
			localStorage.removeItem('type');
			localStorage.removeItem('member');

			this.router.navigate(['/']);
		},
		error =>
		{
			console.log(error);
		});
	}

	get_cart(item_id)
	{
		this.cart_items.push(item_id)

		let cart_object = {...this.cart_items};

		this.http.post(this.rest.domain + "/api/cart/get_items", 
		{ 
			items : cart_object
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{	
			this.show_cart = response;
			this.get_total();
			this.get_cart_count();
		},
		error => 
		{
			console.log(error);
		});
	}

	get_total()
	{
		let sum = 0;
		for( let items of this.show_cart)
		{
			
			sum = sum + (items.item_price * items.item_qty);
			
		}

		this.total = sum;
	}

	get_cart_count()
	{
		this.cart_count = this.show_cart.length;
	}

	hideDropdown()
	{
		$(".dropdown").addClass("hidden");

		setTimeout(function()
		{
			$(".dropdown").removeClass("hidden");
		}, 500);
	}

	checkout()
	{
		localStorage.setItem('checkout_items', JSON.stringify(this.show_cart));
		this.router.navigate(['/member/checkout']);
	}

	makeid() 
	{
		var text = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

		for (var i = 0; i < 5; i++)
		text += possible.charAt(Math.floor(Math.random() * possible.length));

		return text;
	}

	getUserData(accessToken: string) 
	{
	    this.userService.getUserData(accessToken)
        .subscribe(user_data => 
        {
            this.user = user_data;
            var id = this.user.id
            var id_length = id.toString().length;
            localStorage.setItem('identification', this.makeid()+this.user.id);
            localStorage.setItem('id', id_length);
        },
        error =>
        {
        	localStorage.removeItem('auth');
        	localStorage.removeItem('type');
        	localStorage.removeItem('member');
			this.router.navigate(['/admin/login']);
        });

       	this.get_current_slot();
	}

	get_current_slot()
	{
		this.initialize_loading_message = "Loading Slot Information";

		this.http.post(this.rest.domain + "/api/current_slot", { slot_id : localStorage.getItem("slot_id") }, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
			this.current_slot = response;

			if(this.current_slot)
			{
			    localStorage.setItem("slot_id", this.current_slot.slot_id);
				this.wallet = this.current_slot.get_wallets;
			}

			this.router.navigate(['/member/dashboard']);
		},
		error => 
		{
			console.log(error);
		});
	}
}

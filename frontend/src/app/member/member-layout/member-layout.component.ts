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
	user    			  : any = null;
	headers 					= null;
	messages              : any = null;
	send_message                = {};


	constructor(private userService: UserService, private rest: UserService, private router: Router, private http: HttpClient)
	{ 
		if (!this.auth) 
		{
			this.router.navigate(['/login']);
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
		// this.navigationSwiper();
		// this.sideNav();
		// this.sideNavOn();
		// this.sideNavOff();
		
	}

	chat_box()
	{
	  	$(".chat-body").slideToggle( "slow");
	  	if ($(".chat-option").find('i').hasClass('fa-plus'))
	    {		
	      	$(".chat-option").find('i').removeClass('fa-plus').addClass('fa-minus');	
	    }
		else 
	    {		
	        $(".chat-option").find('i').removeClass('fa-minus').addClass('fa-plus');	
	    }
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

	

	
	hideDropdown()
	{
		$(".dropdown").addClass("hidden");

		setTimeout(function()
		{
			$(".dropdown").removeClass("hidden");
		}, 500);
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
            setTimeout(()=>
            {    
            	console.log(this.user,"user");
            	console.log(this.user.profile,"pro");
            	this.router.navigate(['/member/dashboard']);
			}, 1000);
            
        },
        error =>
        {
        	localStorage.removeItem('auth');
        	localStorage.removeItem('type');
        	localStorage.removeItem('member');
			this.router.navigate(['/admin/login']);
        });

    }

    load_message()
    {
    	this.http.post(this.rest.domain + "/api/messages", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.messages = response;
		},
		error =>
		{
			console.log(error,"dsad");
		});
    }
    onSubmitMessage()
    {

    	this.http.post(this.rest.domain + "/api/messages_submit", this.send_message, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			$('#message').val("");
			$(".chat-body-content").stop().animate({ scrollTop: $(".chat-body-content")[0].scrollHeight}, 1000);
			this.load_message();
		},
		error =>
		{
			console.log(error,"dsad");
		});
    }


	
}

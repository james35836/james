import { Component, OnInit, OnDestroy } from '@angular/core';
import WOW from 'wow.js';
import * as $ from 'jquery';

@Component({
  selector: 'app-front-layout',
  templateUrl: './front-layout.component.html',
  styleUrls: ['./front-layout.component.scss']
})

export class FrontLayoutComponent implements OnInit, OnDestroy 
{
	constructor() 
	{ 

	}

	ngOnInit() 
	{
		$('body').on('click',function() 
		{
		  	$('.navbar-collapse').collapse('hide');
		});
		var header = $(".navbar");
  
	    $(window).scroll(function() 
	    {    
	        var scroll = $(window).scrollTop();
	        if (scroll >= 50) 
	        {
	        	if(window.matchMedia('(min-width: 800px)').matches)
				{
				    header.addClass("scrolled");
				}
	            
	        } 
	        else 
	        {
	            header.removeClass("scrolled");
	        }
	    });



		
	}

	ngOnDestroy()
	{
		// $('body').removeClass('bg-front');
	}
}

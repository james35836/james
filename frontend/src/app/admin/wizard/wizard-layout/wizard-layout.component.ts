import { Component, OnInit, OnDestroy } from '@angular/core';
import WOW from 'wow.js'
import * as $ from 'jquery';

@Component({
  selector: 'app-wizard-layout',
  templateUrl: './wizard-layout.component.html',
  styleUrls: ['./wizard-layout.component.scss']
})

export class WizardLayoutComponent implements OnInit, OnDestroy
{
     constructor() 
     {
     	$(function() {
	$('.item').matchHeight();
});
     }

	ngOnInit() 
	{
		new WOW().init();

		$('body').addClass('bg-wiz');
	}

	ngOnDestroy()
	{
		$('body').removeClass('bg-wiz');
	}
}

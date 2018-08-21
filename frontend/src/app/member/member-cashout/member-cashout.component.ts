import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-member-cashout',
  templateUrl: './member-cashout.component.html',
  styleUrls: ['./member-cashout.component.scss']
})
export class MemberCashoutComponent implements OnInit
{
	active_method 			: any = {};

	constructor()
	{
	}

	ngOnInit()
	{
		this.active_method = 1;
	}

	changeMethod(method)
	{
		this.active_method = method;
	}

}

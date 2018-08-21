import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import * as $ from 'jquery';
import { ToastrService } from 'ngx-toastr';

@Component({
  selector: 'app-payout',
  templateUrl: './payout.component.html',
  styleUrls: ['./payout.component.scss']
})
export class PayoutComponent implements OnInit 
{
	charges: any = {};
	headers = null;
	payoutConfig :any = {};
	payoutMethod :any = {};
	payoutMethodBank :any = {};
	payoutMethodRemit :any = {};

  constructor(private rest: UserService, private http: HttpClient, private layout: MainLayoutComponent, private toastr: ToastrService) 
  { 
  	
  }

  ngOnInit() 
  {
  	this.headers = this.layout.headers;
  	this.load_ChargesTax();
  	this.payoutConfig.process_type = 'wallet';
  }

  onSubmitCharges()
  {
  	this.http.post(this.rest.domain + "/api/payout/charge_settings", this.charges, 
	{
		headers: this.headers	
	})
	.subscribe(response =>
	{

		if(response['status_code'] == 200)
		{
			this.toastr.success(response['status_message'], 'Success');
			
		}
		else
		{
			this.toastr.error(response['status_message'], "Error");
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

  load_ChargesTax()
  {
  	this.http.post(this.rest.domain + "/api/payout/get_charge_settings", {}, 
	{
		headers: this.headers	
	})
	.subscribe(response =>
	{
		// this.charges.payout_charges = response.data.payout_charges_charge;
		// this.charges.payout_tax = response.data.payout_charges_tax;
		// this.charges.giftcard = response.data.payout_charges_giftcard;
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

  onSubmitPayoutConfig()
  {
  	console.log(this.payoutConfig, this.payoutMethod, this.payoutMethodBank, this.payoutMethodRemit);
  	this.http.post(this.rest.domain + "/api/payout/payout_configuration", 
  	{
  		config : this.payoutConfig,
  		method : this.payoutMethod,
  		bank 	: this.payoutMethodBank,
  		remit	: this.payoutMethodRemit
  	}, 
	{
		headers: this.headers	
	})
	.subscribe(response =>
	{
		console.log(response)
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

  enable_bank()
  {
  	if(this.payoutMethod.bank == true)
  	{
  		$("#show_bank").css('display', 'block');
  	}
  	else
  	{
  		$("#show_bank").css('display', 'none');
  	}
  }

  enable_remittance()
  {
  	if(this.payoutMethod.remittance == true)
  	{
  		$("#show_remittance").css('display', 'block');
  	}
  	else
  	{
  		$("#show_remittance").css('display', 'none');
  	}
  }
}

import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-cashin',
  templateUrl: './member-cashin.component.html',
  styleUrls: ['./member-cashin.component.scss']
})

export class MemberCashinComponent implements OnInit
{
	headers                 = null;
	active_method 	: any 	= {};

	//methods //cashin
	method_loading 			= false;
	submitting 				= true;
	method_table 			= null;
	category 				= null;
	data_focus 		:any 	= {};
	total_due 		:number = 0;
	total_charge 	:number = 0;
	cash_in_amount 	:number = 0;
	uploading 				= false;
	uploaded 				= false;
	attachment_link 		= null;
	form_data 				= null;
	check_pending 			= null;
	charge 					= null;

  	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit()
	{
		this.headers = this.layout.headers;
		this.active_method = 1;
		this.checkTransactions();

	}

	checkTransactions()
	{
		this.rest.getServiceCharge('cash_in', this.headers).subscribe(response=>
			{
				this.charge = response;
			});
		this.http.post(this.rest.domain + "/api/cashin/get_transactions",
		{
			cash_in_status : "pending",
			slot_id : localStorage.getItem("slot_id")
		},
		{
			headers : this.headers
		}).subscribe(response=>
		{
			this.check_pending = response;
			if(this.check_pending.length == 0)
			{
				this.loadMethodList();
			}
		});
	}

	submitCashIn()
	{
		this.submitting = true;
		if(!this.uploaded)
		{
			this.toastr.error("Please upload payment proof to proceed.", 'Error');
		}
		else if(!this.cash_in_amount)
		{
			this.toastr.error("Cash In amount cannot be 0 or less.", 'Error');
		}
		else
		{
			this.http.post(this.rest.domain + "/api/cashin/record_cash_in",
			{
				slot_id 					: localStorage.getItem("slot_id"),
				cash_in_amount 				: this.cash_in_amount,
				total_due 					: this.total_due,
				cash_in_proof 				: this.attachment_link,
				cash_in_method_id 			: this.data_focus.cash_in_method_id,
				cash_in_method_currency 	: this.data_focus.cash_in_method_currency
			},
			{
				headers : this.headers
			}).subscribe(response=>
			{
				this.toastr.success("Cash In request successfully submitted!", 'Success');
				this.checkTransactions();
			});
		}

		this.submitting = false;
	}

	loadMethodList()
	{
	  	this.method_loading = true;
	  	this.http.post(this.rest.domain + "/api/cashin/get_method_list", 
	  		{}, 
	  		{  headers: this.headers }
	  		).subscribe(response=>
		{
			this.method_table = response;

			if(this.method_table)
			{
				this.chooseMethod(this.method_table[0].cash_in_method_id, this.method_table[0].cash_in_method_category);
			}

			

			this.method_loading = false;
		});
	}

	changeMethod(method)
	{
		this.active_method = method;
	}

	computeTotalDue(fix = 0, percent = 0, service = 0)
	{
		this.total_due = 0;

		if(fix > 0)
		{
			this.total_due = this.cash_in_amount + fix;
		}

		if(service > 0)
		{
			this.total_due = this.total_due + service;
		}

		if(percent > 0)
		{
			this.total_due = (this.total_due) + ((percent/100)*this.cash_in_amount);
		}


		this.total_charge = this.total_due - this.cash_in_amount;
	}

	chooseMethod(id, category)
	{
		this.active_method = id;
		this.cash_in_amount = 0;
		this.data_focus = this.rest.findObjectByKey(this.method_table, 'cash_in_method_id', id);
		this.category = category;
		this.computeTotalDue();
		this.removeAttachment();
	}

	onFileChange(event)
	{
		this.form_data = new FormData();

		if(event.target.files.length > 0)
		{
			this.form_data.append('upload', event.target.files[0]);
			this.form_data.append('folder', 'cash_in_proof');

			this.uploading 				= true;

			this.rest.uploadImageOnServer(this.form_data, this.layout.headers).subscribe(response =>
			{
				if(response)
				{
					this.attachment_link = response;
					this.uploaded = true;
					this.submitting = false;
				}
				this.uploading 			= false;
			});
		}
	}

	removeAttachment()
	{
		this.form_data = new FormData();
		this.attachment_link = null;
		this.uploaded = false;
	}

	uploadProof()
	{
		if(!this.uploading && !this.uploaded)
		{
			$("#payment_proof").trigger('click');
		}
	}
}

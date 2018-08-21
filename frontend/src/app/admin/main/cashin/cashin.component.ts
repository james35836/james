import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import { ToastrService } from 'ngx-toastr';
import * as $ from 'jquery';

@Component({
  selector: 'app-cashin',
  templateUrl: './cashin.component.html',
  styleUrls: ['./cashin.component.scss']
})
export class CashinComponent implements OnInit {
  
  //cash-in method table
  method_table = null;
  method_loading = false;
  method :any = {};
  category :any = {};
  headers :any = null;
  cash_in_method_loading = true;

  //cash-in method add & edit
  new_method :any = {};
  edit_method :any = {};
  archive_method :any = {};

  //cash-in processing
  cash_in_table = null;
  cash_in :any = {};
  processing = false;
  pending_table = null;

  //events
  other = null;
  saving = false;
  data_focus :any = {};
  process_focus = null;
  charge = null;

  process_interval = 0;
  process_interval2 = 55;

  constructor(private rest: UserService, private http: HttpClient, private layout: MainLayoutComponent, private toastr: ToastrService) {}

  ngOnInit() 
  {


  	this.headers         = this.layout.headers;
  	this.method.category = "all";
  	this.method.currency = "all";

  	this.new_method.cash_in_method_category 					    = "remittance";
  	this.new_method.cash_in_method_currency 					    = "php";
  	this.new_method.cash_in_method_charge_fixed 				  = 0;
  	this.new_method.cash_in_method_charge_percentage 			= 0;

    this.cash_in.cash_in_status           = "all";
    this.cash_in.cash_in_method_id        = "all";
    this.cash_in.cash_in_currency                 = "all";

    this.rest.getServiceCharge('cash_in', this.headers).subscribe(response=>
      {
        this.charge = response;
      });

  	this.loadMethodList();
    this.loadCashInList();
  }

  loadCashInList()
  {
    this.cash_in_table = null;
    this.http.post(this.rest.domain + "/api/cashin/get_transactions", this.cash_in,
    {
      headers : this.headers
    }).subscribe(response=>
    {
      this.cash_in_table = response;
      this.process_focus = this.rest.findObjectByKey(this.cash_in_table, 'cash_in_proof_id', this.cash_in_table[0].cash_in_proof_id);
      this.getAllPending();
    });
  }

  loadMethodList()
  {
  	this.method_loading = true;
  	this.loadMethodCategories();
  	this.http.post(this.rest.domain + "/api/cashin/get_method_list", 
  		this.method, 
  		{  headers: this.headers }
  		).subscribe(response=>
	{
		this.method_table = response;
    this.method_table.page = 1;
	});
  }

  loadMethodCategories()
  {
  	this.http.post(this.rest.domain + "/api/cashin/get_method_category_list", 
  		{}, 
  		{ headers: this.headers }
  		).subscribe(response=>
	{
		this.category = response;
		this.method_loading = false;
		this.cash_in_method_loading = false;
	});
  }

  addMethod()
  {
  	this.saving = true;
	this.http.post(this.rest.domain + "/api/cashin/add_new_method", 
  		this.new_method, 
  		{ headers: this.headers }
  		).subscribe(response=>
	{
		if(response["status"] == "success")
		{
			this.toastr.success(response["status_message"], 'Success');
			this.loadMethodList();
      this.new_method.cash_in_method_name = null;
      this.new_method.cash_in_method_charge_fixed = null;
      this.new_method.cash_in_method_charge_percentage = null;
      this.new_method.cash_in_method_thumbnail = null;
		}
		else
		{
			this.toastr.error(response["status_message"], 'Fail');
		}
		this.saving = false;
	});
  }

  saveMethod()
  {
  	this.saving = true;
	this.http.post(this.rest.domain + "/api/cashin/update_method", 
  		this.edit_method, 
  		{ headers: this.headers }
  		).subscribe(response=>
  	{
  		if(response["status"] == "success")
  		{
  			this.toastr.success(response["status_message"], 'Success');
  			this.loadMethodList();
        
  		}
  		else
  		{
  			this.toastr.error(response["status_message"], 'Fail');
  		}
  		this.saving = false;
  	});
  }

  otherEvent(param = null, id = null)
  {
  	this.other = param;

  	if(param == "edit")
  	{
  		this.edit_method = this.rest.findObjectByKey(this.method_table, 'cash_in_method_id', id)
  	}

    if(param == "archive" || param == "unarchive")
    {
      this.http.post(this.rest.domain + "/api/cashin/archive_method", 
        { 
          cash_in_method_id : id,
          archive : param == 'archive' ? 1 : 0
        }, 
        { headers: this.headers })
        .subscribe(response=>
        {
          if(response["status"] == "success")
          {
            this.toastr.success(response["status_message"], 'Success');
            this.loadMethodList();
          }
          else
          {
            this.toastr.error(response["status_message"], 'Fail');
          }
        }
        )
    }
  }

  onFileChange(event) : void
	{
		var formData = new FormData();

    formData.append('upload', event.target.files[0]);
		formData.append('folder', 'cash_in_thumbnail');

		this.saving 				= true;

		this.rest.uploadImageOnServer(formData, this.layout.headers).subscribe(response =>
		{
			this.new_method.cash_in_method_thumbnail = response;
			this.saving 			= false;
		});
	}

  openTransaction(id, status)
  {
    this.process_focus = this.rest.findObjectByKey(this.cash_in_table, 'cash_in_proof_id', id);
    $("#processTransactionModal").modal('show');
  }

  processTransaction(p, id)
  {
    this.processing = true;
    this.http.post(this.rest.domain + "/api/cashin/process_transaction", 
    {
      proof_id : id,
      process : p
    },
    {
      headers : this.layout.headers
    }).subscribe(response=>
    {
      this.loadCashInList();
      this.processing = false;
      $("#processTransactionModal").trigger('click');
    });
  }

  getAllPending()
  {
    this.pending_table = this.rest.filterArrayByKey(this.cash_in_table, 'cash_in_status', 'pending');
    return this.pending_table;
  }

  processAll(proc)
  {
    this.processing = true;

    var arr = [];
    for(var i = 0; i<=this.pending_table.length-1;i++)
    {
      arr[i] = this.pending_table[i].cash_in_proof_id;
    }

    if(arr.length > 0)
    {
      this.http.post(this.rest.domain + "/api/cashin/process_all_transaction",
      {
        proof_id : arr,
        process : proc
      },
      {
        headers: this.headers
      }).subscribe(response=>
      {
        if(response["status"] == "success")
        {
          this.toastr.success(response["status_message"], "Success");
        }
        else
        {
          this.toastr.error(response["status_message"], "Fail");
        }
        this.processing = false;
        this.loadCashInList();
      });
    }
    else
    {
      this.toastr.error("No pending transaction at this moment.", "Fail");
      this.processing = false;
    }
    
  }

}

import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { AdminLayoutComponent } from '../admin-layout/admin-layout.component';
import { ToastrService } from 'ngx-toastr';
import * as $ from 'jquery';
import * as Datepicker from 'bootstrap4-datetimepicker';
import 'bootstrap';

@Component({
  selector: 'app-admin-job',
  templateUrl: './admin-job.component.html',
  styleUrls: ['./admin-job.component.scss']
})

export class AdminJobComponent implements OnInit 
{
	
	headers  = null;
	job_list    :any = null;
	new_job     :any = {};

  	
	constructor(private rest: UserService, private http: HttpClient, private layout: AdminLayoutComponent, private toastr: ToastrService) 
	{

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.load_carrer();
	}

	load_carrer()
	{
		this.http.post(this.rest.domain + "/api/admin_carrer/get_carrer", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.job_list = response;
			console.log(this.job_list);
		},
		error =>
		{
			console.log(error);
		});
	}
	carrer_details(key)
	{
		$('#viewEventDetails').modal('show');
		console.log(this.job_list[key]);
	}
	carrerCreateSubmit()
	{
		console.log(this.new_job);
		this.http.post(this.rest.domain + "/api/admin_carrer/create_submit", this.new_job, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.new_job = new Array(Number(7)).fill("");
				this.toastr.success(response["status_message"], 'SUCCESS');
				this.load_carrer();
				$(".modal-backdrop").remove();
				$('#createNewCarrer').modal('hide');
			}
			else
			{
				for (let data of response["status_message"]) 
				{
					this.toastr.error(data, 'Error');
				}
			}
		},
		error =>
		{
			console.log(error);
		});
	}
	

	

	
}



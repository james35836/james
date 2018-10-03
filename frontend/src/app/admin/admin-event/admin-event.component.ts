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
  selector: 'app-admin-event',
  templateUrl: './admin-event.component.html',
  styleUrls: ['./admin-event.component.scss']
})
export class AdminEventComponent implements OnInit 
{
	
	headers  = null;
	event_list    :any = null;
	new_event     :any = {};

  	
	constructor(private rest: UserService, private http: HttpClient, private layout: AdminLayoutComponent, private toastr: ToastrService) 
	{

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.load_event();
	}

	load_event()
	{
		this.http.post(this.rest.domain + "/api/admin_event/get_event", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.event_list = response;
			console.log(this.event_list);
		},
		error =>
		{
			console.log(error);
		});
	}
	event_details(key)
	{
		$('#viewEventDetails').modal('show');
		console.log(this.event_list[key]);
	}
	eventCreateSubmit()
	{
		console.log(this.new_event);
		this.http.post(this.rest.domain + "/api/admin_event/create_submit", this.new_event, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.new_event = new Array(Number(7)).fill("");
				this.toastr.success(response["status_message"], 'SUCCESS');
				this.load_event();
				$(".modal-backdrop").remove();
				$('#createNewEvent').modal('hide');
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



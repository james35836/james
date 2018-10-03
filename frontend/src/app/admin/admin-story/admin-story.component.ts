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
  selector: 'app-admin-story',
  templateUrl: './admin-story.component.html',
  styleUrls: ['./admin-story.component.scss']
})
export class AdminStoryComponent implements OnInit 
{
	
	headers  = null;
	story_list    :any = null;
	new_story     :any = {};

  	
	constructor(private rest: UserService, private http: HttpClient, private layout: AdminLayoutComponent, private toastr: ToastrService) 
	{

	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.load_story();
	}

	load_story()
	{
		this.http.post(this.rest.domain + "/api/admin_story/get_story", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.story_list = response;
			console.log(this.story_list);
		},
		error =>
		{
			console.log(error);
		});
	}
	story_details(key)
	{
		$('#viewEventDetails').modal('show');
		console.log(this.story_list[key]);
	}
	storyCreateSubmit()
	{
		this.http.post(this.rest.domain + "/api/admin_story/create_submit", this.new_story, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.new_story = new Array(Number(7)).fill("");
				this.toastr.success(response["status_message"], 'SUCCESS');
				this.load_story();
				$(".modal-backdrop").remove();
				$('#createNewStory').modal('hide');
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




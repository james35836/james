import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-dashboard',
  templateUrl: './member-dashboard.component.html',
  styleUrls: ['./member-dashboard.component.scss']
})
export class MemberDashboardComponent implements OnInit {

	headers                 		 = null;
	form_data                        = null;

	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit() 
	{
		
	}
	fileUpload(event)
	{
		this.form_data = new FormData();

		if(event.target.files.length > 0)
		{
			this.form_data.append('upload', event.target.files[0]);
			this.form_data.append('folder', 'gdgdfgdfs');

			

			this.http.post(this.rest.domain + "/api/sample",this.form_data, 
	  		{  
	  			headers: this.layout.headers 
	  		}).subscribe(response=>
			{
				console.log(response);
			});
		}
	}
	


	
	
	

}

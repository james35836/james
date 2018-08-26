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


	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		
		
	}


	/* SUBMIT AREA */
	
	

}

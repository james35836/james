import { Component, OnInit, NgModule } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { User } from '../../user';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-initialize',
  templateUrl: './member-initialize.component.html',
  styleUrls: ['./member-initialize.component.scss']
})
export class MemberInitializeComponent implements OnInit
{
	constructor(private userService: UserService, private rest: UserService, private router: Router, private http: HttpClient, public layout: MemberLayoutComponent)
	{ 
	}

	ngOnInit()
	{
		if (!this.layout.auth) 
		{
			this.router.navigate(['/admin/login']);
		}

		if (this.layout.type !='member')
		{
			this.router.navigate(['/'+this.layout.type]);
		}

		this.layout.headers = new HttpHeaders({
            "Accept": "application/json",
            "Authorization": "Bearer " + this.layout.auth,
        });

	    this.layout.getUserData(this.layout.auth);
	}
}

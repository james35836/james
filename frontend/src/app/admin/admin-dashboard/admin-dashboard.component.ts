import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { UserService } from '../../user.service';
import { User } from '../../user';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-admin-dashboard',
  templateUrl: './admin-dashboard.component.html',
  styleUrls: ['./admin-dashboard.component.scss']
})
export class AdminDashboardComponent implements OnInit 
{

  ctx :any;

 constructor(private userService: UserService, private router: Router, private http: HttpClient) 
  { }

  ngOnInit() 
  {
  	
  }

}

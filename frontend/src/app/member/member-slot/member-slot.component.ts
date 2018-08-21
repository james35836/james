import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-slot',
  templateUrl: './member-slot.component.html',
  styleUrls: ['./member-slot.component.scss']
})
export class MemberSlotComponent implements OnInit {
	slots = null;
	current_slot_id = null;
  headers = null;
  constructor(private rest: UserService, private http: HttpClient, private layout: MemberLayoutComponent, private toastr: ToastrService) { }

  ngOnInit() 
  {
  	this.headers = this.layout.headers;
  	this.current_slot_id = localStorage.getItem("slot_id");
  	this.get_all_slot();
  }

  get_all_slot()
  {
  	this.http.post(this.rest.domain + "/api/all_slot", {}, 
	{
		headers: this.headers	
	})
	.subscribe(response =>
	{
		this.slots = response;
	},
	error => 
	{
		console.log(error);
	});
  }

  select_slot(slot_id)
  {
  	localStorage.setItem("slot_id", slot_id);
  	this.current_slot_id = slot_id;
  	this.layout.get_current_slot();
  }
}

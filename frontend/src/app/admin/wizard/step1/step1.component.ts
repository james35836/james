import { Component, OnInit } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';


@Component({
  selector: 'app-step1',
  templateUrl: './step1.component.html',
  styleUrls: ['./step1.component.scss']
})
export class Step1Component implements OnInit {
  

  country_codes : any;

  constructor(private http : HttpClient) { }

  ngOnInit() {

     
  	this.http.get("assets/Phone/phone.json").subscribe(response=>
    {
    	this.country_codes = response;
    	console.log(response);
    });
  
  }

}

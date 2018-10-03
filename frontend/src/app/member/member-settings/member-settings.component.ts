import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';
@Component({
  selector: 'app-member-settings',
  templateUrl: './member-settings.component.html',
  styleUrls: ['./member-settings.component.scss']
})
export class MemberSettingsComponent implements OnInit {

	headers    = null;
	enabled    = "user_info";

	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit() 
	{
		
		var readURL = function(input) 
		{
			
        	if (input.files && input.files[0]) 
        	{
            	var reader = new FileReader();

	            reader.onload = function (e) 
	            {
	                // $('.profile-pic').attr('src', e.target.result);
	                $('.profile-pic').attr('src', e.target);
	            }
	    
	            reader.readAsDataURL(input.files[0]);
        	}
    	}
    	$(".file-upload").on('change', function()
  		{
        	readURL(this);
    	});
    
    	$(".upload-button").on('click', function() 
    	{
       		$(".file-upload").click();
    	});
	}

	click_button(enable)
	{
		this.enabled = enable;
	}

	


	
	
	

}

import { Component, OnInit } from '@angular/core';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../../user.service';
import { MemberLayoutComponent } from '../member-layout/member-layout.component';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-member-chat',
  templateUrl: './member-chat.component.html',
  styleUrls: ['./member-chat.component.scss']
})
export class MemberChatComponent implements OnInit {

	headers                 		 = null;
	form_data                        = null;
	connection_list              :any = null;
	reference                    :any = {};
	message_list
	load_message                :any = null;
	message_loaded              :any = null;
	message_name                : any = "GENERAL ROOM";
	message_on                  :any = "GENERAL";
	send_message                :any = {};
	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit() 
	{

		// this.load_connection();
		this.LOAD_MESSAGE('GENERAL','load');
		this.LOAD_MESSAGE('BATCH','load');
		this.LOAD_MESSAGE('CONNECTION','load');

	}
	LOAD_MESSAGE(reference,load)
	{
		this.reference.ref = reference;
		this.http.post(this.rest.domain + "/api/member_messages/load_message", this.reference, 
		{
			headers: this.layout.headers
		})
		.subscribe(response =>
		{
			this.message_list = response;
			console.log('response',response);
			if(load=='load')
			{
				this.choose_message(reference,response);
			}
			else
			{
				this.choose_messages(reference,response);
			}
			
		},
		error =>
		{
			console.log(error);
		});
	}
	choose_message(reference,response)
	{
		this.load_message    = response;
		if(reference=='GENERAL')
		{
			this.message_loaded  = this.load_message['general'];
			this.message_name 	 = "GENERAL ROOM";
			this.message_on 	 = "GENERAL";
		}
		else if(reference=="CONNECTION")
		{
			this.connection_list = response;
		}
	}

	choose_messages(reference,response)
	{
		if(reference=='GENERAL')
		{
			
			this.load_message    = response;
			this.message_loaded  = this.load_message['general'];
			this.message_name 	 = "GENERAL ROOM";
			this.message_on 	 = "GENERAL";
		}
		else if(reference=='BATCH')
		{
			this.load_message    = response;
			this.message_loaded = this.load_message['batch'];
			this.message_name 	= "BATCH ROOM";
			this.message_on 	= "BATCH";
		}
		else
		{
			this.message_loaded = this.connection_list[response];
			this.message_name 	= this.connection_list[response].connection_name;;
			this.message_on 	= "CONNECTION";
		}
		this.scroll_div("msg_history");
		
	}

	send_messages(send_to=1,send_on)
	{
		this.send_message.send_to = send_to;
		this.send_message.send_on = send_on;
		this.http.post(this.rest.domain + "/api/member_messages/send_message", this.send_message, 
		{
			headers: this.layout.headers
		})
		.subscribe(response =>
		{
			this.load_message = response;
			this.scroll_div("msg_history");
		},
		error =>
		{
			console.log(error);
		});
	}
	
	load_connection()
	{
		this.http.post(this.rest.domain + "/api/member_messages/get_connection", {}, 
		{
			headers: this.layout.headers
		})
		.subscribe(response =>
		{
			this.connection_list = response;
			console.log(this.connection_list);
		},
		error =>
		{
			console.log(error);
		});
	}

	scroll_div(div_class)
	{
		$("."+div_class).stop().animate({ scrollTop: $("."+div_class)[0].scrollHeight}, 100);
	}

	
	


	
	
	

}


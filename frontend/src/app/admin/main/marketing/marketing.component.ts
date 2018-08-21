import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import { ToastrService } from 'ngx-toastr';
import * as $ from 'jquery';
import 'bootstrap';

@Component({
  selector: 'app-marketing',
  templateUrl: './marketing.component.html',
  styleUrls: ['./marketing.component.scss']
})
export class MarketingComponent implements OnInit 
{
	membership_list: 	any = null;
	headers:         	any = null;

	plan_settings:   	any = {};
	plan_label:      	any = {};
	plan_status:     	any = {};

	plan_settings_tab:  any = null;

	manage_membership_data:  any = null;
	
	constructor(private rest: UserService, private http: HttpClient, private layout: MainLayoutComponent, private toastr: ToastrService) 
	{

	}

	ngOnInit() 
	{
		this.prevent_negative();
		this.membership_list = null;
		this.plan_settings = {};
		this.plan_label = {};
		this.plan_status = {};
		this.plan_settings_tab = null;

		this.headers = this.layout.headers;
		this.load_membership();
		this.load_plan("DIRECT");
		this.load_plan("INDIRECT");
		this.load_plan("UNILEVEL");
		this.load_plan("STAIRSTEP");
		this.load_plan("BINARY");
	}

	prevent_negative()
	{
		$(".tab-content").on("keydown",".no-negative",function(event)
		{
	        if (event.shiftKey == true) 
	        {
	            event.preventDefault();
	        }

	        if ((event.keyCode >= 48 && event.keyCode <= 57) || 
	            (event.keyCode >= 96 && event.keyCode <= 105) || 
	            event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
	            event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {
	        } 
	    	else 
	    	{
	            event.preventDefault();
	        }

	        if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
	            event.preventDefault(); 
	        //if a decimal has been added, disable the "."-button
		});
	}

	change_level(plan,membership_id,value)
	{
		if(Number(value) > 100)
		{
			value = 100;
		}
		
		if(plan == "INDIRECT")
		{
			var x = 2;
			var value2 = Number(value) + 1;
		}
		else
		{
			var x = 1;
			var value2 = Number(value);
		}

		for(; x <= Number(value2) ; x++)
		{
			if(!this.plan_settings[plan][plan.toLowerCase()+"_settings"][membership_id])
			{
				this.plan_settings[plan][plan.toLowerCase()+"_settings"][membership_id] = {};
			}
			if(!this.plan_settings[plan][plan.toLowerCase()+"_settings"][membership_id][x])
			{
				this.plan_settings[plan][plan.toLowerCase()+"_settings"][membership_id][x] = {};
			}	
		}	
		this.plan_settings[plan]['membership_level'][membership_id] = new Array(Number(value)).fill("");
		// console.log(this.plan_settings[plan][plan.toLowerCase()+"_settings"]);
	}

	on_new_data(plan)
	{
		if(plan == "STAIRSTEP")
		{
			var set_count = this.plan_settings['STAIRSTEP']['stairstep_settings'].length;
		   
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count]                             = {}; 
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count].stairstep_rank_name         = this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_name;
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count].stairstep_rank_override     = this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_override;
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count].stairstep_rank_personal     = this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_personal;
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count].stairstep_rank_personal_all = this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_personal_all;
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count].stairstep_rank_group_all    = this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_group_all;
		    this.plan_settings['STAIRSTEP']['stairstep_settings'][set_count].stairstep_rank_level        = set_count + 1;
			
			this.plan_settings['STAIRSTEP']['count_stairstep_settings'] 						         = this.plan_settings['STAIRSTEP']['count_stairstep_settings'] + 1;
			this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_name 			     = "";
			this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_override 		     = "";
			this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_personal 		     = "";
			this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_personal_all        = "";
			this.plan_settings['STAIRSTEP']['stairstep_settings_end'].stairstep_rank_group_all 		     = "";
		}
		else if(plan == "BINARY")
		{
			var set_count = this.plan_settings['BINARY']['binary_settings_pair'].length;
		   
		    this.plan_settings['BINARY']['binary_settings_pair'][set_count]                              = {}; 
		    this.plan_settings['BINARY']['binary_settings_pair'][set_count].binary_pairing_left          = this.plan_settings['BINARY']['binary_settings_pair_end'].binary_pairing_left;
		    this.plan_settings['BINARY']['binary_settings_pair'][set_count].binary_pairing_right         = this.plan_settings['BINARY']['binary_settings_pair_end'].binary_pairing_right;
		    this.plan_settings['BINARY']['binary_settings_pair'][set_count].binary_pairing_bonus         = this.plan_settings['BINARY']['binary_settings_pair_end'].binary_pairing_bonus;
 
			this.plan_settings['BINARY']['count_binary_settings_pair'] 						             = this.plan_settings['BINARY']['count_binary_settings_pair'] + 1;
			this.plan_settings['BINARY']['binary_settings_pair_end'].binary_pairing_left 			     = "";
			this.plan_settings['BINARY']['binary_settings_pair_end'].binary_pairing_right 		         = "";
			this.plan_settings['BINARY']['binary_settings_pair_end'].binary_pairing_bonus 		         = "";
		}
	}

	on_close_data(plan , set_count)
	{
		if(plan == "STAIRSTEP")
		{
			var condition = false;
			this.plan_settings['STAIRSTEP']['stairstep_settings'].splice(set_count,1);
			for(var x = set_count; condition == false ; x++)
			{
				if(this.plan_settings['STAIRSTEP']['stairstep_settings'][x])
				{
					console.log(this.plan_settings['STAIRSTEP']['stairstep_settings'][x].stairstep_rank_level = this.plan_settings['STAIRSTEP']['stairstep_settings'][x].stairstep_rank_level);
					this.plan_settings['STAIRSTEP']['stairstep_settings'][x].stairstep_rank_level = this.plan_settings['STAIRSTEP']['stairstep_settings'][x].stairstep_rank_level - 1;
				}
				else
				{
					condition = true;
				}
			}

			this.plan_settings['STAIRSTEP']['count_stairstep_settings'] = this.plan_settings['STAIRSTEP']['count_stairstep_settings'] - 1;
		}
		else if(plan == "BINARY")
		{
			var condition = false;
			this.plan_settings['BINARY']['binary_settings_pair'].splice(set_count,1);
			for(var x = set_count; condition == false ; x++)
			{
				if(this.plan_settings['BINARY']['binary_settings_pair'][x])
				{

				}
				else
				{
					condition = true;
				}
			}

			this.plan_settings['BINARY']['count_binary_settings_pair'] = this.plan_settings['BINARY']['count_binary_settings_pair'] - 1;			
		}
	}

	active_tab(plan)
	{
		plan = plan.toLowerCase();
		$("#"+plan+"-tab").click();
	}

	update_status_plan(plan,send)
	{
		this.http.post(this.rest.domain + "/api/plan/update_status", {plan:plan,send:send}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.plan_status[plan] = String(response["update_status"]);
			this.toastr.success(response["status_message"], 'Success');
		},
		error =>
		{
			console.log(error);
		});		
	}

	update_plan(plan)
	{
		this.plan_settings[plan]["membership_settings"] = this.membership_list;
		this.http.post(this.rest.domain + "/api/plan/update", {plan:plan, label:this.plan_label[plan], data:JSON.stringify(this.plan_settings[plan])}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.plan_status[plan] = String(response["update_status"]);
			this.toastr.success("Plan Updated", 'Success');
		},
		error =>
		{
			console.log(error);
		});		
	}

	/* LOAD DATA SECTION */
	load_membership()
	{
		this.http.post(this.rest.domain + "/api/membership/get", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.membership_list = response;
		},
		error =>
		{
			console.log(error);
		});
	}

	load_plan(plan)
	{
		this.http.post(this.rest.domain + "/api/plan/get", {plan:plan}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.plan_settings[plan]  = response["settings"];

			if(plan == "BINARY")
			{
				console.log(this.plan_settings["BINARY"]);
			}

			this.plan_label[plan]     = response["label"];
			this.plan_status[plan]    = String(response["status"]);
		},
		error =>
		{
			console.log(error);
		});
	}

	manage_membership()
	{
		this.manage_membership_data = this.membership_list;

		$("#manageMembershipPopup").modal();
	}

	membership_submit()
	{
		this.http.post(this.rest.domain + "/api/membership/submit", this.manage_membership_data, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.toastr.success(response['status_message'], 'Success');

			this.ngOnInit();
		},
		error =>
		{
			if (typeof error.error.status_message != 'undefined') 
			{
				for (let data of error.error.status_message) 
				{
					this.toastr.error(data, 'Error');
				}
			}
		});
	}

	add_membership(index)
	{
		this.manage_membership_data[index + 1] = {};
		this.manage_membership_data[index + 1].membership_id = null;
		this.manage_membership_data[index + 1].membership_name = "";
		this.manage_membership_data[index + 1].hierarchy = "";
		this.manage_membership_data[index + 1].archive = 0;
	}

	remove_membership(index)
	{
		if (index > -1) 
		{
		   this.manage_membership_data[index].archive = 1;
		}
	}
}



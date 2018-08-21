import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import { ToastrService } from 'ngx-toastr';
import * as $ from 'jquery';
import { globals } from '../../../../environments/environment';

@Component({
  selector: 'app-member',
  templateUrl: './member.component.html',
  styleUrls: ['./member.component.scss']
})
export class MemberComponent implements OnInit
{
	constructor(private rest: UserService, private http: HttpClient, private layout: MainLayoutComponent, private toastr: ToastrService) {}

	add_member              :any = {};
	add_slot                :any = {"slot_owner":null,"code":null,"pin":null,"slot_sponsor":null, "from_admin":1};
	place_slot              :any = {"slot_code":null,"slot_placement":null,"slot_position":"LEFT"};
	unplaced_slot_list	    :any = null;
	member_list             :any = null;
	country_list            :any = null;
	slot_list               :any = null;
	slot_list_filter        :any = null;
	headers                 :any = null;
	slot_info			    :any = null;
	slot_earnings_filter    :any = null;
	slot_distributed_filter :any = null;
	slot_wallet_filter 		:any = null;
	slot_payout_filter 		:any = null;
	slot_points_filter 		:any = null;
	slot_network_filter 	:any = null;
	slot_codevault_filter   :any = null;
	submitted			    :any = false;
	random_code 			:any = {};

	ngOnInit()
	{
		this.headers = this.layout.headers;
		this.load_country();
		this.load_member();
		this.load_slot();
		this.unplaced_load_slot();
	}

	onSubmitPlaceSlot()
	{
		this.http.post(this.rest.domain + "/api/member/place_slot", this.place_slot, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.load_member();
				this.unplaced_load_slot();
				this.load_slot();
				this.toastr.success(response["status_message"], 'Success');
				this.place_slot.slot_code      = this.unplaced_slot_list[0].slot_no;
				this.place_slot.slot_placement = null;
				this.place_slot.slot_position  = "LEFT";
				$(".place_slot_form").find("input, textarea").val("");
				$(".place_slot_form_close").click();
				$(".modal-backdrop").remove();
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

	onSubmitCreateSlot()
	{
		this.http.post(this.rest.domain + "/api/member/add_slot", this.add_slot, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.load_member();
				this.load_slot();
				this.unplaced_load_slot();
				this.toastr.success(response["status_message"], 'Success');
				$(".add_slot_form").find("input, textarea").val("");
				$(".add_slot_form_close").click();
				$(".modal-backdrop").remove();
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

	onSubmitAddMember()
	{
		this.add_member.register_platform = "system";
		this.http.post(this.rest.domain + "/api/member/add_member", this.add_member, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.load_member();
				this.toastr.success(response["status_message"], 'Success');
				$(".add_member_form").find("input, textarea").val("");
				$(".add_member_form_close").click();
				$(".modal-backdrop").remove();
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

	auto_position()
	{
		alert(123);
	}

	/* LOAD DATA SECTION */
	load_member()
	{
		this.http.post(this.rest.domain + "/api/member/get", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.member_list = response;
			if(this.member_list.length != 0)
			{
				this.add_slot.slot_owner = this.member_list[0].id;
			}
		},
		error =>
		{
			console.log(error);
		});
	}

	load_slot()
	{
		this.http.post(this.rest.domain + "/api/slot/get_full", this.slot_list_filter, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.slot_list = response;
		},
		error =>
		{
			console.log(error);
		});
	}

	unplaced_load_slot()
	{
		this.http.post(this.rest.domain + "/api/slot/get_unplaced", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response[0] != null)
			{
				if(this.place_slot.slot_code == null)
				{
					this.place_slot.slot_code = response[0].slot_no;
				}			
			}
			this.unplaced_slot_list = response;
		},
		error =>
		{
			console.log(error);
		});
	}

	load_country()
	{
		this.http.post(this.rest.domain + "/api/country/get", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.country_list = response;
			if(response != null)
			{
				this.add_member.country_id = this.country_list[0].country_id;
			}
		},
		error =>
		{
			console.log(error);
		});
	}

	set_all_filter(id)
	{
		this.earnings_set(id);
		this.distributed_set(id)
		this.wallet_set(id);
		this.payout_set(id);
		this.points_set(id);
		this.network_set(id);
		this.codevault_set(id);
	}

	slot_information_popup(id)
	{
		this.slot_info = null;
		this.set_all_filter(id);
		this.slot_load_tab_info(id);
		$("#slotInfoPopup").modal();
	}

	slot_load_tab_info(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_information", {id:id}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info = {};
			this.slot_info.information = {};
			this.slot_info.information = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	slot_submit_tab_info()
	{
		this.submitted = true;

		this.http.post(this.rest.domain + "/api/member/submit_slot_information", this.slot_info.information, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.toastr.success(response["status_message"], 'Success');

			this.submitted = false;
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

			this.submitted = false;
		});
	}

	slot_load_tab_details(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_details", {id:id}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.details = {};
			this.slot_info.details = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	earnings_set(id)
	{
		this.slot_earnings_filter = {};
		this.slot_earnings_filter.id = id;
		this.slot_earnings_filter.type = "all";
		this.slot_earnings_filter.from = null;
		this.slot_earnings_filter.to = null;
		this.slot_earnings_filter.search = null;
	}

	slot_load_tab_earnings(id = null)
	{	
		this.http.post(this.rest.domain + "/api/member/get_slot_earnings", this.slot_earnings_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.earnings = {};
			this.slot_info.earnings = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	distributed_set(id)
	{
		this.slot_distributed_filter = {};
		this.slot_distributed_filter.id = id;
		this.slot_distributed_filter.type = "all";
		this.slot_distributed_filter.from = null;
		this.slot_distributed_filter.to = null;
		this.slot_distributed_filter.search = null;
	}

	slot_load_tab_distributed(id = null)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_distributed", this.slot_distributed_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.distributed = {};
			this.slot_info.distributed = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	arrayToQueryString(array_in)
	{
	    var out = new Array();

	    for(var key in array_in){
	        out.push(key + '=' + encodeURIComponent(array_in[key]));
	    }

	    return out.join('&');
	}


	wallet_set(id)
	{
		this.slot_wallet_filter = {};
		this.slot_wallet_filter.id = id;
		this.slot_wallet_filter.from = null;
		this.slot_wallet_filter.to = null;
	}

	slot_load_tab_wallet(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_wallet", this.slot_wallet_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.wallet = {};
			this.slot_info.wallet = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	payout_set(id)
	{
		this.slot_payout_filter = {};
		this.slot_payout_filter.id = id;
		this.slot_payout_filter.from = null;
		this.slot_payout_filter.to = null;
	}

	slot_load_tab_payout(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_payout", this.slot_payout_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.payout = {};
			this.slot_info.payout = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	points_set(id)
	{
		this.slot_points_filter = {};
		this.slot_points_filter.id = id;
		this.slot_points_filter.type = "all";
		this.slot_points_filter.from = null;
		this.slot_points_filter.to = null;
	}

	slot_load_tab_points(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_points", this.slot_points_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.points = {};
			this.slot_info.points = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	network_set(id)
	{
		this.slot_network_filter = {};
		this.slot_network_filter.id = id;
		this.slot_network_filter.level = null;
		this.slot_network_filter.search = null;
	}

	slot_load_tab_network(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_network", this.slot_network_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.network = {};
			this.slot_info.network = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	codevault_set(id)
	{
		this.slot_codevault_filter = {};
		this.slot_codevault_filter.id = id;
		this.slot_codevault_filter.status = null;
		this.slot_codevault_filter.search = null;
	}

	slot_load_tab_codevault(id)
	{
		this.http.post(this.rest.domain + "/api/member/get_slot_codevault", this.slot_codevault_filter, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.slot_info.codevault = {};
			this.slot_info.codevault = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	go_to_page(page)
	{
		this.slot_list_filter.page = page;
		this.load_slot();
	}

	go_to_page_earnings(page)
	{
		this.slot_info.earnings = null;
		this.slot_earnings_filter.page = page;
		this.slot_load_tab_earnings(this.slot_info.information.slot_id);
	}

	go_to_page_distributed(page)
	{
		this.slot_info.distributed = null;
		this.slot_distributed_filter.page = page;
		this.slot_load_tab_distributed(this.slot_info.information.slot_id);
	}

	go_to_page_wallet(page)
	{
		this.slot_info.wallet = null;
		this.slot_wallet_filter.page = page;
		this.slot_load_tab_wallet(this.slot_info.information.slot_id);
	}

	go_to_page_payout(page)
	{
		this.slot_info.payout = null;
		this.slot_payout_filter.page = page;
		this.slot_load_tab_payout(this.slot_info.information.slot_id);
	}

	go_to_page_points(page)
	{
		this.slot_info.points = null;
		this.slot_points_filter.page = page;
		this.slot_load_tab_points(this.slot_info.information.slot_id);
	}

	go_to_page_network(page)
	{
		this.slot_info.network = null;
		this.slot_network_filter.page = page;
		this.slot_load_tab_network(this.slot_info.information.slot_id);
	}

	go_to_page_codevault(page)
	{
		this.slot_info.codevault = null;
		this.slot_codevault_filter.page = page;
		this.slot_load_tab_codevault(this.slot_info.information.slot_id);
	}

	get_random_code()
	{
		this.http.post(this.rest.domain + "/api/admin/get_random_code", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
			this.random_code = response;
			this.add_slot.code = this.random_code.code_activation;
			this.add_slot.pin = this.random_code.code_pin;
		},
		error => 
		{
			console.log(error);
		});
	}
}

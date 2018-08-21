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
	wallet_log      		 :any    = null;
	unplaced_slots  		 :any    = null;
	unplaced_downline_slots  :any    = null;
	unplaced_dl_proceed      :any    = 1;
	data_focus :any = {};
	check_wallet :any = {};
	default :any = {};

	add_slot            :any = {"slot_owner":null,"code":null,"pin":null,"slot_sponsor":null};
	place_the_slot      :any = {"slot_no":null,"placement":null,"position":"LEFT"};
	place_the_downline  :any = {"slot_no":null,"placement":null,"position":"LEFT"};

	wallet_log_loading : any = false;

	constructor(private rest: UserService, private http: HttpClient, public layout: MemberLayoutComponent, private toastr: ToastrService) 
	{
	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.get_wallet_log();
		this.get_unplaced_slot();
		this.get_unplaced_downline_slot();
		this.getDefaultWallet();
		console.log(this.layout.current_slot);
	}


	/* SUBMIT AREA */
	
	onSubmitCreateSlot()
	{
		$(".createthisownslot").prop("disabled",true);
		this.http.post(this.rest.domain + "/api/slot/add_slot", this.add_slot, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.layout.get_current_slot();

				this.toastr.success(response["status_message"], 'Success');
				$(".create_slot_form").find("input, textarea").val("");
				$(".create_slot_form_close").click();
				$(".modal-backdrop").remove();
			}
			else
			{
				for (let data of response["status_message"]) 
				{
					this.toastr.error(data, 'Error');
				}
			}

			$(".createthisownslot").prop("disabled",false);
		},
		error =>
		{
			console.log(error);
		});
	}

	onSubmitOwnUnplacedSlot()
	{
		$(".placethisownslot").prop("disabled",true);
	    this.http.post(this.rest.domain + "/api/place_own_slot", this.place_the_slot, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.layout.get_current_slot();

				this.toastr.success(response["status_message"], 'Success');
				$(".unplace_slot_form").find("input, textarea").val("");
				$(".unplace_slot_form_close").click();
				$(".modal-backdrop").remove();
			}
			else
			{
				for (let data of response["status_message"]) 
				{
					this.toastr.error(data, 'Error');
				}
			}

			$(".placethisownslot").prop("disabled",false);
		},
		error =>
		{
			console.log(error);
		});
	}


	onSubmitDownlineUnplacedSlot()
	{
		$(".placethisslot").prop("disabled",true);
	    this.http.post(this.rest.domain + "/api/place_downline_slot", this.place_the_downline, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response["status"] == "success")
			{
				this.get_unplaced_downline_slot();
				this.toastr.success(response["status_message"], 'Success');
				$(".unplace_downline_form").find("input, textarea").val("");
				$(".unplace_downline_form_close").click();
				$(".modal-backdrop").remove();
			}
			else
			{
				for (let data of response["status_message"]) 
				{
					this.toastr.error(data, 'Error');
				}
			}

			$(".placethisslot").prop("disabled",false);
		},
		error =>
		{
			console.log(error);
		});
	}

	/* GET AREA */

	get_wallet_log()
	{
		this.wallet_log_loading = true;
		this.http.post(this.rest.domain + "/api/wallet_log", {slot_id:this.layout.current_slot.slot_id}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{	
		    this.wallet_log = response;
		    this.wallet_log_loading = false;
			console.log(response);
		},
		error => 
		{
			console.log(error);
		});
	}

	get_unplaced_slot()
	{
		this.http.post(this.rest.domain + "/api/check_unplaced_slot", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			/* IF THERE IS AN UNPLACED SLOT */
			if(!$.isEmptyObject(response))
			{
				this.unplaced_slots = response;
				this.place_the_slot.slot_no = response[0].slot_no;
				$("#popupUnplacedSlot").modal("show");
			}
			else
			{
				this.unplaced_dl_proceed = 0;
			}
		},
		error => 
		{
			console.log(error);
		});
	}

	get_unplaced_downline_slot()
	{
		this.http.post(this.rest.domain + "/api/check_unplaced_downline_slot", {slot_id:this.layout.current_slot.slot_id},
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			/* IF THERE IS AN UNPLACED DOWNLINE SLOT */
			if(!$.isEmptyObject(response))
			{
				this.unplaced_downline_slots = response;
				this.place_the_downline.slot_no = response[0].slot_no;
			}
		},
		error => 
		{
			console.log(error);
		});
	}

	test()
	{
		this.http.post(this.rest.domain + "/api/test", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			console.log(response)
		},
		error => 
		{
			console.log(error);
		});
	}

	getWallet(id)
	{
		if(id)
		{
			this.data_focus = this.rest.findObjectByKey(this.layout.wallet, 'wallet_id', id);
		}
	}

	getDefaultWallet()
	{
		this.default = this.rest.findObjectByKey(this.layout.wallet, 'currency_default', 1);
	}


}

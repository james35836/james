import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import * as $ from 'jquery';
import { ToastrService } from 'ngx-toastr';
import { globals } from '../../../../environments/environment';
import {ProductComponent} from '../product/product.component';

@Component({
  selector: 'app-cashier',
  templateUrl: './cashier.component.html',
  styleUrls: ['./cashier.component.scss']
})
export class CashierComponent implements OnInit 
{
	branch:any = {};
	headers = null;
	branchList = null;
	data : any = {};
	branch_filter :any = {};
	cashier_filter :any = {};
	code_filter:any={};
	cashier : any = {};
	cashierList : any = [];
	cashierInfo : any = [];
	codeList : any = [];
	productList : any = [];
	locationList : any = [];
	p : any;
	codeSelect:any = {};
	rowClicked = null;
	stockistLevel :any = {};
	stockistList :any = {};
	codes_loading : boolean = false;

	constructor(private rest: UserService, private http: HttpClient, private layout: MainLayoutComponent, private toastr: ToastrService)
	{
	}

	ngOnInit() 
	{
		this.headers = this.layout.headers;
		this.get_branch();
		this.get_location();
		this.get_stockist_level();

	}

	branch_submit()
	{
		this.http.post(this.rest.domain + "/api/cashier/add_branch", this.branch, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{

			if(response['status_code'] == 400)
			{
				this.toastr.error(response['status_message'], "Error");
			}
			else
			{
				this.toastr.success(response['status_message'], 'Success');

				$('#addBranchPopup').modal('hide');
				$('.modal-backdrop').remove();
			}
			this.get_branch();
			this.branch = {};

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

	check_stockist()
	{
		if(this.branch.branch_type == "Stockist")
		{
			$("#stockist_level").css("display", "block");
		}
		else
		{
			$("#stockist_level").css("display", "none");
		}
	}

	get_branch()
	{

		this.http.post(this.rest.domain + "/api/cashier/get_branch", {}, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.branchList = response;
			console.log(response)
			this.branch_filter.branch_type = 'all';
			this.branch_filter.branch_location = 'all';
			this.cashier_filter.status = 'all';
			this.cashier_filter.position = 'all';
			this.code_filter.status = 'all';
			
		},
		error => 
		{
			console.log(error);
		});
	}

	edit_branch(id)
	{
		globals.loader = true;

		$("#modify-tab").click();
		this.rowClicked = null;

		this.http.post(this.rest.domain + "/api/cashier/data", 
		{
			id : id
		}, 

		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			globals.loader = false;
			this.data = response;	
			this.get_inventory();	
			$('#code_list').css("display", "none");
			$("#editBranchPopup").modal();
		},
		error => 
		{
			this.edit_branch(id);
		});

		this.get_cashier(id);

	}

	
	archive(id)
	{
		this.http.post(this.rest.domain + "/api/cashier/archive", 
		{
			id : id
		}, 

		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.get_branch();
		},
		error => 
		{
			console.log(error);
		});
	}

	update_branch()
	{
		this.http.post(this.rest.domain + "/api/cashier/edit", this.data, 
		{
			headers: this.headers	
		})
		.subscribe(response =>
		{
			this.toastr.success(response['status_message'], 'Success');
			this.get_branch();
			$('#editBranchPopup').modal('hide');
			$('.modal-backdrop').remove();
		},
		error => 
		{
			console.log(error);
		});
	}

	load_branch_list()
	{
		console.log(this.branch_filter)
		this.http.post(this.rest.domain + "/api/cashier/search", this.branch_filter, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.branchList = response;
		},
		error =>
		{
			console.log(error);
		})
	}

	cashier_add()
	{
		if(this.cashier.password == this.cashier.password_confirm)
		{
			this.cashier.branch_id = this.data.branch_id;
			this.http.post(this.rest.domain + "/api/cashier/add_cashier", this.cashier, 
			{
				headers: this.headers
			})
			.subscribe(response =>
			{
				if(response['status_code'] == 400)
				{
					this.toastr.error(response['status_message'], "Error");
				}
				else
				{
					this.toastr.success(response['status_message'], 'Success');
				}
				this.get_cashier(this.cashier.branch_id)
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
			})
		}
		else
		{
			console.log(this.cashier);
		}
	}

	get_cashier(branch_id)
	{
		this.http.post(this.rest.domain + "/api/cashier/get_cashier", 
		{
			id : branch_id,
			filter : this.cashier_filter

		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.cashierList = response;
		},
		error =>
		{
			console.log(error);
		})
	}

	edit_cashier(cashier_id)
	{
		this.http.post(this.rest.domain + "/api/cashier/edit_cashier", 
		{
			id : cashier_id
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.cashierInfo = response;
			
		},
		error =>
		{
			console.log(error);
		})

		
	}

	edit_cashier_submit(cashier_id)
	{
		this.http.post(this.rest.domain + "/api/cashier/edit_cashier_submit", this.cashierInfo,
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response['status_code'] == 400)
			{
				this.toastr.error(response['status_message'], "Error");
			}
			else
			{
				this.toastr.success(response['status_message'], 'Success');
			}
			this.get_cashier(this.data.branch_id);
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
		})
	}
	
	select_item(item_id,item_sku = null,index = null)
	{

		this.codeSelect = {};
		this.codeSelect.branch_id 		= this.data.branch_id
		this.codeSelect.item_id	  		= item_id
		this.codeSelect.item_sku	  	= item_sku;
		this.get_inventory();
		this.p = 1;

		this.rowClicked = index;

		this.codes_loading = true;

		this.http.post(this.rest.domain + "/api/cashier/get_codes", 
		{
			branch_id : this.codeSelect.branch_id,
			filter : this.code_filter,
			item_id : this.codeSelect.item_id,
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			console.log(response)
			$('#code_list').css("display", "block");
			this.codeList = response;
			this.codes_loading = false;
		},
		error =>
		{
			this.select_item(item_id,item_sku,index);
			console.log(error);
		})
		
	}

	archive_code(code_id)
	{
		this.http.post(this.rest.domain + "/api/cashier/delete_code", 
		{
			code_id : code_id
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.select_item(this.codeSelect.item_id);
			if(response['status_code'] == 400)
			{
				this.toastr.error(response['status_message'], "Error");
			}
			else
			{
				this.toastr.success(response['status_message'], 'Success');
			}

		},
		error =>
		{
			console.log(error);
		})
	}

	generate_codes()
	{

		var quantity = prompt("How many codes would you like to generate?");
		this.http.post(this.rest.domain + "/api/cashier/generate_codes", 
		{
			quantity : quantity,
			branch_id : this.codeSelect.branch_id,
			item_id : this.codeSelect.item_id
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.get_branch();
			this.get_inventory();
			this.select_item(this.codeSelect.item_id);
		},
		error =>
		{
			console.log(error);
		})
	}

	restock(id)
	{
		var quantity = prompt('How many items do we restock?');
		
		if(quantity == null|| quantity == "")
		{
			alert("No value entered.");
		}
		else
		{
			this.http.post(this.rest.domain + "/api/product/restock", 
			{
				quantity : quantity,
				item_id : id,
				branch_id : this.data.branch_id
			},
			{
				headers: this.headers
			})
			.subscribe(response =>
			{
				this.get_inventory();
				this.edit_branch(this.data.branch_id);
			},
			error =>
			{
				console.log(error);
			})
		}
	}

	// get_codes()
	// {	
	// 	this.get_inventory();
	// 	this.http.post(this.rest.domain + "/api/cashier/get_codes", 
	// 	{
	// 		inventory_id : this.data.inventory_id,
	// 		filter : this.code_filter
	// 	},
	// 	{
	// 		headers: this.headers
	// 	})
	// 	.subscribe(response =>
	// 	{
	// 		this.codeList = response;
	// 	},
	// 	error =>
	// 	{
	// 		console.log(error);
	// 	})
	// }

	get_inventory()
	{
		this.http.post(this.rest.domain + "/api/product/get_inventory", 
		{
			branch_id : this.data.branch_id
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.productList = response;

		},
		error =>
		{
			console.log(error);
		})
	}

	

	get_location()
	{
		this.http.post(this.rest.domain + "/api/cashier/get_location", 
		{
			
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.locationList = response;

		},
		error =>
		{
			console.log(error);
		})
	}

	get_stockist_level()
	{
		this.http.post(this.rest.domain + "/api/cashier/get_stockist", 
		{
			
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.stockistList = response;
		},
		error =>
		{
			console.log(error);
		})
	}

	add_stockist_level()
	{	
		this.stockistListfix();
		this.http.post(this.rest.domain + "/api/cashier/add_stockist_level", 
		{
			stockist : this.stockistLevel
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			if(response['status_code'] == 400)
			{
				this.toastr.error(response['status_message'], "Error");
			}
			else
			{
				this.toastr.success(response['status_message'], 'Success');
			}
		},
		error =>
		{
			
		})
	}


	stockistListfix()
	{
		var i = 0;
		var _level = {};

		for (let data of this.stockistList) 
		{
			_level[i]                   				= {};
			_level[i].stockist_level_name 				= data.stockist_level_name;
			_level[i].stockist_level_discount           = data.stockist_level_discount;

			i++;
		}

		this.stockistLevel = _level;
	}

	add_level(index)
	{
		this.stockistList[index + 1]                       = [];
		this.stockistList[index + 1].stockist_level_name   = "";
		this.stockistList[index + 1].stockist_level_discount   = "";
	}

	delete_level(index, level_name)
	{
		if (index > -1) 
		{
		   this.stockistList.splice(index, 1);
		}

		this.http.post(this.rest.domain + "/api/cashier/archive_stockist_level", 
		{
			level_name : level_name
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.get_stockist_level();

		},
		error =>
		{
			console.log(error);
		})
	}

	add_location(index)
	{
		this.locationList[index + 1]                       = [];
		this.locationList[index + 1].location   = "";
	}

	delete_location(index, location)
	{
		if (index > -1) 
		{
		   this.locationList.splice(index, 1);
		}

		this.http.post(this.rest.domain + "/api/cashier/archive_location", 
		{
			location : location
		},
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.get_stockist_level();

		},
		error =>
		{
			console.log(error);
		})
	}

	add_location_submit()
	{
		this.locationListfix();
		this.http.post(this.rest.domain + "/api/cashier/add_location", this.locationList,
		
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.get_location();
			if(response['status_code'] == 400)
			{
				this.toastr.error(response['status_message'], "Error");
			}
			else
			{
				this.toastr.success(response['status_message'], 'Success');
			}

		},
		error =>
		{
			console.log(error);
		})
	}

	locationListfix()
	{
		var i = 0;
		var _level = {};

		for (let data of this.locationList) 
		{
			_level[i]                   				= {};
			_level[i].location 				= data.location;

			i++;
		}

		this.locationList = _level;
	}

}


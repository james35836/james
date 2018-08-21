import { Component, OnInit } from '@angular/core';
import { UserService } from '../../../user.service';
import { HttpClientModule, HttpClient, HttpHeaders } from '@angular/common/http';
import { MainLayoutComponent } from '../main-layout/main-layout.component';
import { ToastrService } from 'ngx-toastr';
import * as $ from 'jquery';
import { globals } from '../../../../environments/environment';
import 'bootstrap';

@Component({
  selector: 'app-product',
  templateUrl: './product.component.html',
  styleUrls: ['./product.component.scss']
})
export class ProductComponent implements OnInit 
{
	item                   = new Item('','','','','','','','','','','','','','','','');
	headers                = null;
	membership             = null;
	product                = null;
	selected_membership_id = null;
	action				   = null;
	submit_url			   = null;
	page				   = null;
	column				   = null;
	branch 				   = null;
	item_code			   = null;
	submitted              = false;
	item_list : any        = null;
	item_list_filter : any = {};
	item_code_submit : any = {};
	item_insert : any      = {};
	rowClicked			   = null;
	stockist_list : any    = {};
	items_submitted		   = {};
	used_codes 			   = null;
	code_loading : boolean = false;

	constructor(private rest: UserService, private http: HttpClient, private layout: MainLayoutComponent, private toastr: ToastrService) 
	{ 
	}

	column_save()
	{
		localStorage.setItem("admin_product_column", JSON.stringify(this.column));
		this.toastr.success("Column Updated", 'Success');
		$("#product-manage-column").modal('hide');
	}

	ngOnInit() 
	{
		if (localStorage.getItem('admin_product_column')) 
		{
			this.column = JSON.parse(localStorage.getItem('admin_product_column'));
		}
		else
		{
			this.column                     = {};
			this.column.product_sku         = true;
			this.column.product_description = true;
			this.column.product_type        = true;
			this.column.product_price       = true;
			this.column.product_pv   		 = true;
		}

		this.item_list_filter.item_type = "all";
		this.item_code_submit.status = "all";
		this.item_code_submit.search = null;
		this.headers = this.layout.headers;
		this.load_membership();
		this.load_product();
		this.load_item_list();
	}

	addProduct()
	{
		$("#modify-tab").trigger("click");

		this.selected_membership_id 			= null;
		this.action = "add";

		this.item.item_sku                      = "";
		this.item.item_description              = "";
		this.item.item_barcode                  = "";
		this.item.item_price                    = "";
		this.item.item_pv                 		= 0;
		this.item.item_type                     = "product";

		if (typeof this.membership[0] !== 'undefined') 
		{
			this.item.membership_id             = this.membership[0].membership_id;
		}
		else
		{
			this.item.membership_id             = "";
		}
		
		this.item.slot_qty                      = "";
		this.item.inclusive_gc                  = "";
		this.item.item_kit                      = [];
		this.item.item_kit[0]                   = [];
		this.item.item_kit[0].item_inclusive_id = "";
		this.item.item_kit[0].item_qty          = "";

		
		var i = 0;

		this.item.item_membership_discount      = [];

		for (let data of this.membership) 
		{
			this.item.item_membership_discount[i]                 = [];
			this.item.item_membership_discount[i].membership_id   = data.membership_id;
			this.item.item_membership_discount[i].membership_name = data.membership_name;
			this.item.item_membership_discount[i].discount        = 0;

			i++;
		}
		this.item.item_points                            = [];
		this.item.item_points[0]                         = [];
		this.item.item_points[0].item_points_key         = "unilevel";
		this.item.item_points[0].item_points_personal_pv = 0;
		this.item.item_points[0].item_points_group_pv    = 0;
		this.item.item_points[1]                         = [];
		this.item.item_points[1].item_points_key         = "stair_step";
		this.item.item_points[1].item_points_personal_pv = 0;
		this.item.item_points[1].item_points_group_pv    = 0;

		$("#editProduct").modal();
		
	}

	editProduct(id)
	{
		globals.loader = true;
		$("#modify-tab").trigger("click");
		this.rowClicked = null;
		this.load_item_inventory(id);
		this.item_code = null;

		this.http.post(this.rest.domain + "/api/product/data", 
		{
			id : id
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			globals.loader = false;
			
			this.item.item_id            = response["item_id"];
			this.selected_membership_id  = null;
			this.item_code_submit        = {};
			this.item_code_submit.status = "all";
			this.item_code_submit.search = null;
			this.action                  = "edit";

			this.item.item_sku                      = response["item_sku"];
			this.item.item_description              = response["item_description"];
			this.item.item_barcode                  = response["item_barcode"];
			this.item.item_price                    = response["item_price"];
			this.item.item_pv                 		= response["item_pv"];
			this.item.item_type                     = response["item_type"];
			this.stockist_list 						= response["stockist_list"];

			console.log(this.stockist_list);

			if (typeof this.membership[0] !== 'undefined') 
			{
				this.item.membership_id             = response["membership_id"];
			}
			else
			{
				this.item.membership_id             = "";
			}
			
			this.item.slot_qty                      = response["slot_qty"];
			this.item.inclusive_gc                  = response["inclusive_gc"];

			if (typeof response["item_kit"] != 'undefined') 
			{
				var i = 0;

				this.item.item_kit                      = [];

				for (let data of response["item_kit"]) 
				{
					this.item.item_kit[i]                   = [];
					this.item.item_kit[i].item_inclusive_id = data.item_inclusive_id;
					this.item.item_kit[i].item_qty          = data.item_qty;

					i++;
				}
			}
			else
			{
				this.item.item_kit                      = [];
				this.item.item_kit[0]                   = [];
				this.item.item_kit[0].item_inclusive_id = "";
				this.item.item_kit[0].item_qty          = "";
			}

			if (typeof response["membership_discount"] != 'undefined') 
			{
				var i = 0;

				this.item.item_membership_discount      = [];

				for (let data of this.membership) 
				{
					this.item.item_membership_discount[i]                 = [];
					this.item.item_membership_discount[i].membership_id   = data.membership_id;
					this.item.item_membership_discount[i].membership_name = data.membership_name;
					this.item.item_membership_discount[i].discount        = 0;

					for (let check_data of response["membership_discount"]) 
					{
						if (check_data.membership_id == data.membership_id) 
						{
							this.item.item_membership_discount[i].discount = check_data.discount;
						}
					}

					i++;
				}
			}
			else
			{
				var i = 0;

				this.item.item_membership_discount      = [];

				for (let data of this.membership) 
				{
					this.item.item_membership_discount[i]                 = [];
					this.item.item_membership_discount[i].membership_id   = data.membership_id;
					this.item.item_membership_discount[i].membership_name = data.membership_name;
					this.item.item_membership_discount[i].discount        = 0;

					i++;
				}
			}

			if (typeof response["item_points"] != 'undefined') 
			{
				this.item.item_points                            = [];
				this.item.item_points[0]                         = [];
				this.item.item_points[0].item_points_key         = "unilevel";
				this.item.item_points[0].item_points_personal_pv = 0;
				this.item.item_points[0].item_points_group_pv    = 0;
				this.item.item_points[1]                         = [];
				this.item.item_points[1].item_points_key         = "stair_step";
				this.item.item_points[1].item_points_personal_pv = 0;
				this.item.item_points[1].item_points_group_pv    = 0;

				for (let check_data of response["item_points"]) 
				{
					if (check_data.item_points_key == "unilevel") 
					{
						this.item.item_points[0].item_points_personal_pv = check_data.item_points_personal_pv;
						this.item.item_points[0].item_points_group_pv    = check_data.item_points_group_pv;
					}

					if (check_data.item_points_key == "stair_step") 
					{
						this.item.item_points[1].item_points_personal_pv = check_data.item_points_personal_pv;
						this.item.item_points[1].item_points_group_pv    = check_data.item_points_group_pv;
					}
				}
			}
			else
			{
				this.item.item_points                            = [];
				this.item.item_points[0]                         = [];
				this.item.item_points[0].item_points_key         = "unilevel";
				this.item.item_points[0].item_points_personal_pv = 0;
				this.item.item_points[0].item_points_group_pv    = 0;
				this.item.item_points[1]                         = [];
				this.item.item_points[1].item_points_key         = "stair_step";
				this.item.item_points[1].item_points_personal_pv = 0;
				this.item.item_points[1].item_points_group_pv    = 0;

			}

			$("#editProduct").modal();
		},
		error => 
		{
			console.log(error);
		});
	}

	change_item_kit(index, item_id)
	{
		this.item.item_kit[index].item_inclusive_id = item_id;
	}

	add_item_kit(index)
	{
		this.item.item_kit[index + 1]                   = [];
		this.item.item_kit[index + 1].item_inclusive_id = "";
		this.item.item_kit[index + 1].item_qty          = "";
	}

	delete_item_kit(index)
	{
		if (index > -1) 
		{
		   this.item.item_kit.splice(index, 1);
		}
	}

	load_membership()
	{
		this.http.post(this.rest.domain + "/api/get_membership", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.membership = response;
		},
		error => 
		{
			console.log(error);
		});
	}

	load_product()
	{
		this.http.post(this.rest.domain + "/api/get_product", {}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.product = response;
			console.log(response)
		},
		error => 
		{
			console.log(error);
		});
	}

	load_item_list()
	{
		this.http.post(this.rest.domain + "/api/product/get", this.item_list_filter, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.item_list = response;
		},
		error =>
		{
			console.log(error);
		});
	}

	load_item_inventory(id = null)
	{
		if (!id) 
		{
			var id = this.item.item_id;
		}

		this.http.post(this.rest.domain + "/api/product/get_item_inventory", 
		{
			id : id
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.branch = response;
		},
		error =>
		{
			console.log(error);
		});
	}

	change_item_branch(item_id, inventory_id, branch_name, branch_id, index)
	{	
		this.item_code_submit.branch_name  = branch_name;
		this.item_code_submit.branch_id    = branch_id;
		this.item_code_submit.item_id      = item_id;
		this.item_code_submit.inventory_id = inventory_id;
		this.item_code_submit.page         = 1;
		this.item_code_submit.status       = "all";
		this.item_code_submit.search       = null;
		this.rowClicked 				   = index;
		this.load_item_code();	

	}

	load_item_code()
	{
		this.code_loading = true;
		this.http.post(this.rest.domain + "/api/product/get_item_code", this.item_code_submit, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.item_code = response;
			this.code_loading = false;


		},
		error =>
		{
			console.log(error);
		});
	}

	product_submit()
	{
		this.submitted = true;

		this.item_kit_fix();
		this.membership_discount_fix();
		this.item_points_fix();

		if (this.action == "add") 
		{
			this.submit_url = "/api/product/add";
			this.items_submitted = this.item
		}
		else
		{
			this.submit_url = "/api/product/edit";
			this.items_submitted = 
			{
				item : this.item, 
				stockist :this.stockist_list
			}
		}
		console.log(this.item);
		this.http.post(this.rest.domain + this.submit_url, this.items_submitted,
		
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.toastr.success(response['status_message'], 'Success');
			this.submitted = false;

			this.load_item_list();
			this.load_product();

			if (this.action == "add") 
			{
				this.item.item_id           = response["id"];
				this.action                 = "edit";
			}
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

	item_kit_fix()
	{
		var i = 0;
		var _kit = {};

		for (let data of this.item.item_kit) 
		{
			_kit[i]                   = {};
			_kit[i].item_inclusive_id = data.item_inclusive_id;
			_kit[i].item_qty          = data.item_qty;

			i++;
		}

		this.item.item_kit_fix = _kit;
	}

	membership_discount_fix()
	{
		var i = 0;
		var _membership_discount = {};

		for (let data of this.item.item_membership_discount) 
		{
			_membership_discount[i] = {};
			_membership_discount[i].membership_id   = data.membership_id;
			_membership_discount[i].membership_name = data.membership_name;
			_membership_discount[i].discount        = data.discount;

			i++;
		}

		this.item.item_membership_discount_fix = _membership_discount;
	}

	item_points_fix()
	{
		var i = 0;
		var _points = {};

		for (let data of this.item.item_points) 
		{
			_points[i]                         = {};
			_points[i].item_points_key         = data.item_points_key;
			_points[i].item_points_personal_pv = data.item_points_personal_pv;
			_points[i].item_points_group_pv    = data.item_points_group_pv;

			i++;
		}

		this.item.item_points_fix = _points;
	}

	go_to_page(page)
	{
		this.item_list_filter.page = page;
		this.load_item_list();
	}

	item_code_go_to_page(page)
	{
		this.item_code_submit.page = page;
		this.load_item_code();
	}

	product_archive(id)
	{
		if (confirm("Are you sure?")) 
		{
			this.http.post(this.rest.domain + "/api/product/archive", 
			{
				id : id
			}, 
			{
				headers: this.headers
			})
			.subscribe(response =>
			{
				this.toastr.success(response['status_message'], 'Success');

				this.load_item_list();
				this.load_product();
			},
			error =>
			{
				console.log(error);
			});
	    } 
	}

	product_manage_column()
	{
		$("#product-manage-column").modal();
	}

	generate_code()
	{
		var count = prompt('How many codes would you like to generate?');

		if (count) 
		{
			if (this.item_code_submit.branch_id) 
			{
				this.http.post(this.rest.domain + "/api/cashier/generate_codes", 
				{
					branch_id : this.item_code_submit.branch_id,
					item_id : this.item.item_id,
					quantity : count,
				}, 
				{
					headers: this.headers
				})
				.subscribe(response =>
				{
					this.toastr.success(response['status_message'], 'Success');

					this.load_item_code();
					this.load_item_inventory();
				},
				error =>
				{
					console.log(error);
				});
			}
			else
			{
				this.toastr.error("Please select branch first.", 'Error');
			}
		}
	}

	delete_code(code_id)
	{
		this.http.post(this.rest.domain + "/api/cashier/delete_code", 
		{
			code_id : code_id,
		}, 
		{
			headers: this.headers
		})
		.subscribe(response =>
		{
			this.toastr.success(response['status_message'], 'Success');

			this.load_item_code();
			this.load_item_inventory();
		},
		error =>
		{
			console.log(error);
		});
	}
}

export class Item 
{

  constructor(
    public item_sku: any,
    public item_description: any,
    public item_barcode: any,
    public item_price: any,
    public item_pv: any,
    public item_type: any,
    public membership_id: any,
    public slot_qty: any,
    public inclusive_gc: any,
    public item_kit: any,
    public item_kit_fix: any,
    public item_membership_discount: any,
    public item_membership_discount_fix: any,
    public item_points: any,
    public item_points_fix: any,
    public item_id: any
  ) {  }

}
<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;

use App\Models\Tbl_branch;
use App\Models\Tbl_cashier;
use App\Models\Tbl_inventory;
use App\Models\Tbl_item;
use App\Models\Tbl_codes;
use Validator;

class Branch
{
	public static function add($data)
	{
		$rules["branch_name"] = "required";
		$rules["branch_location"] = "required";
		$rules["branch_type"] = "required";

		$validator = Validator::make($data, $rules);

        if ($validator->fails()) 
        {
            $return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = $validator->messages()->all();
        }
        else
        {
			$insert['branch_name']			=	$data['branch_name'];
			$insert['branch_location']		=	$data['branch_location'];
			$insert['branch_type']			=	$data['branch_type'];
			$insert['branch_date_created']	=	Carbon::now();
			if($data['branch_type'] == 'Stockist')
			{
				$insert['stockist_level'] 	= $data['stockist_level'];
			}

			$branch_id = Tbl_branch::insertGetId($insert);

			$item_list = Tbl_item::count();
			
			if($item_list == 0)
			{
				Tbl_inventory::insert(['inventory_branch_id' => $branch_id]);
			}
			else
			{
				$item_list = Tbl_item::get();
				foreach($item_list as $key=>$value)
				{
					$insert_inventory['inventory_branch_id'] = $branch_id;
					$insert_inventory['inventory_item_id'] = $value->item_id;
					Tbl_inventory::insert($insert_inventory);
				}
			}


			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Branch Created";

		}

		return $return;
	}

	public static function get()
	{
		$return = [];

		$branch = Tbl_branch::where('archived', 0)->leftJoin('tbl_stockist_level', 'tbl_branch.stockist_level', '=', 'tbl_stockist_level.stockist_level_id')->get();
		foreach($branch as $key => $value)
		{
			$product_quantity[$key] = Tbl_inventory::JoinItem()->where('tbl_inventory.inventory_branch_id', $value->branch_id)->ItemTypeProduct()->sum('inventory_quantity');

			$membership_quantity[$key] = Tbl_inventory::JoinItem()->where('tbl_inventory.inventory_branch_id', $value->branch_id)->ItemTypeMembershipkit()->sum('inventory_quantity');

			$cashier_count[$key] = Tbl_cashier::where('cashier_branch_id', $value->branch_id)->count();

			$sold_membership_quantity[$key] = Tbl_codes::Inventory()->InventoryItem()->CheckIfSold(1)->CheckIfKit()->where('tbl_inventory.inventory_branch_id', $value->branch_id)->get();

			$sold_product_quantity[$key] = Tbl_codes::Inventory()->InventoryItem()->CheckIfSold(1)->CheckIfProduct()->where('tbl_inventory.inventory_branch_id', $value->branch_id)->get();

			$return[$key] = $value->toArray();

			$return[$key] += ['membership_codes_count' => $membership_quantity[$key]];
			$return[$key] += ['product_codes_count' => $product_quantity[$key]];
			$return[$key] += ['cashier_count' => $cashier_count[$key]];
			$return[$key] += ['sold_membership_quantity' => count($sold_membership_quantity[$key])];
			$return[$key] += ['sold_product_quantity' => count($sold_product_quantity[$key])];
			
		}
		return $return;
	}

	public static function get_data($id)
	{
		$return = Tbl_branch::join('tbl_inventory', 'tbl_branch.branch_id', '=', 'tbl_inventory.inventory_branch_id')->where('tbl_branch.branch_id', $id)->first();


		return $return;
	}

	public static function archive($id)
	{
		$return = Tbl_branch::where('branch_id', $id)->update(['archived' => 1]);

		return $return;
	}

	public static function edit($data)
	{
		$update['branch_name']			=	$data['branch_name'];
		$update['branch_location']		=	$data['branch_location'];
		$update['branch_type']			=	$data['branch_type'];

		Tbl_branch::where('branch_id', $data['branch_id'])->update($update);

		$return["status"]         = "success"; 
		$return["status_code"]    = 200; 
		$return["status_message"] = "Branch Successfully Updated!";
		return $return;
	}

	public static function search($filters = null)
	{
		$data = Tbl_branch::where("tbl_branch.archived", 0);
		if(isset($filters["branch_type"]) && $filters["branch_type"] != "all")
		{
			$data = $data->where("branch_type", $filters["branch_type"]);
		}
		if(isset($filters["branch_location"]) && $filters["branch_location"] != "all")
		{
			$data = $data->where("branch_location", $filters["branch_location"]);
		}
		if(isset($filters["search_key"]))
		{
			$data = $data->where("branch_name", "like", "%". $filters["search_key"] . "%");
		}
		$data = $data->get();
		return $data;
	}

	public static function get_stockist()
	{
		$return = DB::table("tbl_stockist_level")->where('archive', 0)->get();
		
		return $return;
	}

	public static function add_stockist($data)
	{
		foreach($data as $key => $value)
		{
			if($value["stockist_level_name"] == null || $value["stockist_level_name"] == "")
			{
				continue;
			}

			$rules["stockist_level_name"] = "required";

			$validator = Validator::make($value, $rules);

	        if ($validator->fails()) 
	        {
	            $return["status"]         = "error"; 
				$return["status_code"]    = 400; 
				$return["status_message"] = $validator->messages()->all();
	        }
	        else
	        {
	        	$check_exist = DB::table('tbl_stockist_level')->where('archive', 0)->where('stockist_level_name', $value['stockist_level_name'])->first();
	        	$success_add = 0;
	        	if(count($check_exist) == 0)
	        	{
	        		$insert['stockist_level_name']			= 	$value['stockist_level_name'];
		        	$insert['stockist_level_date_created'] 	=	Carbon::now();

		        	$stockist_level_id = DB::table('tbl_stockist_level')->insertGetId($insert);

		        	$get_item 		   = Tbl_item::get();
		        	foreach($get_item as $key => $value)
		        	{
		        		$insert_stockist_discount['stockist_level_id'] 		= $stockist_level_id;
		        		$insert_stockist_discount['item_id']				= $value->item_id;
		        		DB::table('tbl_item_stockist_discount')->insert($insert_stockist_discount);
		        	}

		        	$success_add = $success_add + 1;
		        	$return["status"]         = "success"; 
					$return["status_code"]    = 200; 
					$return["status_message"] = "Stockist level Successfully created.";
	        	}
			}
        }

        if($success_add == 0)
    	{
    		$return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = "Stockist level already exists.";
    	}

        return $return;
	}

	public static function archive_stockist($data)
	{
		DB::table("tbl_stockist_level")->where("stockist_level_name", $data)->update(['archive' => 1]);
	}
}
<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;

use App\Models\Tbl_branch;
use App\Models\Tbl_cashier;
use App\Models\Tbl_codes;
use App\Models\Tbl_inventory;
use App\Models\Tbl_item;
use Validator;

class Code
{
	public static function generate($branch_id, $item_id,$quantity)
	{
		$count = Tbl_codes::orderBy('code_id', 'desc')->first();
		if($count == null)
		{
			$start = 1;
		}
		else
		{
			$start = $count->code_id+1;
		}

		

		for($ctr = 0; $ctr < $quantity; $ctr ++)
		{
			$code[$ctr]['activation'] = 'DGM'. str_pad($start,8,'0',STR_PAD_LEFT);
			$code[$ctr]['pin'] = str_pad(rand(0, pow(10, 8)-1), 8, '0', STR_PAD_LEFT);
			
			$check_activation = Tbl_codes::where('code_pin', $code[$ctr]['pin'])->first();

			if($check_activation === null)
			{
				$inventory				= Tbl_inventory::where([['inventory_branch_id', $branch_id],['inventory_item_id' , $item_id]])->first();
				$insert['code_inventory_id'] = $inventory->inventory_id;
				$insert['code_activation'] = $code[$ctr]['activation'];
				$insert['code_pin'] = $code[$ctr]['pin'];



				Tbl_codes::insert($insert);
			}
			else
			{
				$ctr--;
			}

			$start++;
		}

		return $code;
	}

	public static function get($branch_id, $filter = null, $item_id = null, $paginate = null)
	{
		$return = [];

		$inventory = Tbl_inventory::where([['inventory_branch_id', $branch_id], ['inventory_item_id', $item_id]])->first();
		
		if (!$inventory) 
		{
			$inventory_default["inventory_branch_id"] = $branch_id;
			$inventory_default["inventory_status"] = null;
			$inventory_default["inventory_item_id"] = $item_id;
			$inventory_default["inventory_quantity"] = 0;
			
			Tbl_inventory::insert($inventory_default);
		}

		$data = Tbl_codes::where('code_inventory_id', $inventory->inventory_id);
		$data = $data->join("tbl_inventory", "tbl_codes.code_inventory_id", "=", "tbl_inventory.inventory_id");
		$data = $data->join("tbl_branch", "tbl_inventory.inventory_branch_id", "=", "tbl_branch.branch_id");

		if ($item_id) 
		{
			$data = $data->where("tbl_inventory.inventory_item_id", $item_id);
		}
		if(isset($filter["status"]) && $filter["status"] != "all")
		{
			// $data = $data->where("branch_type", $filter["branch_type"]);
			switch($filter["status"])
			{
				case "Used": 
					$data = $data->where([['code_used','=',1],['code_sold','=', 0]])->orWhere([['code_used','=',1],['code_sold','=', 1]]);

				break;

				case "Unused": 
					$data = $data->where([['code_used','=',0],['code_sold','=', 0]])->orWhere([['code_used','=',0],['code_sold','=', 1]]);
				break;

				case "Sold": 
					$data = $data->where([['code_used','=',1],['code_sold','=', 0]])->orWhere([['code_used','=',1],['code_sold','=', 1]]);
				break;

				case "Unsold": 
					$data = $data->where([['code_used','=',1],['code_sold','=', 0]])->orWhere([['code_used','=',0],['code_sold','=', 0]]);
				break;

				default:
					$data = $data;
			}
		}

		if(isset($filter["search"]))
		{
			$data = $data->where("code_activation", "like", "%". $filter["search"] . "%")->orWhere("code_pin", "like", "%". $filter["search"] . "%");
		}

		if ($paginate) 
		{
			$data = $data->paginate($paginate);
		}
		else
		{
			$data = $data->get();
		}

		foreach($data as $key => $value)
		{
			$data[$key]->code_user = Tbl_codes::UsedBy($value->code_used_by)->select('name')->first();
			$data[$key]->code_buyer = Tbl_codes::UsedBy($value->code_sold_to)->select('name')->first();
		}

		return $data;
	}

	public static function delete($code_id)
	{
		$code 	= Tbl_codes::where('code_id', $code_id)->first();

		$inventory_id = $code->code_inventory_id;

		Tbl_codes::where('code_id', $code_id)->delete();
		
		$count = Tbl_codes::where('code_inventory_id', $inventory_id)->count();

		$update['inventory_quantity']	= $count;

		Tbl_inventory::where('inventory_id', $inventory_id)->update($update);
		
		$return["status"]         = "success"; 
		$return["status_code"]    = 200; 
		$return["status_message"] = "Deleted Successfully!";

		return $return;
	}

	public static function check_membership_code_unused($code,$pin)
	{
		$code = Tbl_codes::where("code_activation",$code)->where("code_pin",$pin)->inventory()->inventoryitem()->where("item_type","membership_kit")->first();
		if($code)
		{
			if($code->code_used == 0)
			{
	            $return  = "unused"; 
			}
			else
			{
	            $return  = "used"; 
			}
		}
		else
		{
            $return  = "not_exist"; 
		}

		return $return;
	}

	public static function use_membership_code($code,$pin,$user_id,$from_admin = null)
	{
		$code = Tbl_codes::where("code_activation",$code)->where("code_pin",$pin)->inventory()->inventoryitem()->where("item_type","membership_kit")->first();
		if($code)
		{
			if($code->code_used == 0)
			{
	            $return["status"]  = "unused"; 
			}
			else
			{
	            $return["status"]  = "used"; 
			}
		}
		else
		{
            $return["status"]  = "not_exist"; 
		}

		if($return["status"] == "unused")
		{
			if($from_admin == 1)
			{
				$update['code_sold_to'] = $user_id;
			}

			$return["code_id"]   = $code->code_id;
			$update["code_used"] = 1;
			$update["code_sold"] = 1;
			$update["code_used_by"] = $user_id;
			$update["code_date_used"] = Carbon::now();
			$update["code_date_sold"] = Carbon::now();
			Tbl_codes::where("code_id",$code->code_id)->update($update);
		}

		return $return;
	}

	public static function get_membership($code,$pin)
	{
		$code = Tbl_codes::where("code_activation",$code)->where("code_pin",$pin)->inventory()->inventoryitem()->first();	

		return $code->membership_id;
	}

	public static function get_member_codes($user_id)
	{
		$code_list = Tbl_codes::where('code_sold_to', $user_id)->get();
		foreach($code_list as $key => $value)
		{

			$return[$key]['code_activation']	= $value->code_activation;
			$return[$key]['code_pin']			= $value->code_pin;
			$return[$key]['membership_name']	= $value->membership_name;
			$return[$key]['code_used']			= $value->code_used;
			$return[$key]['first_name']			= $value->first_name;
			$return[$key]['last_name']			= $value->last_name;
			$return[$key]['code_date_used']		= $value->code_date_used;
		}
		return $return;
	}

	public static function get_claim_codes($slot_id)
	{
		$claim_codes = DB::table('tbl_receipt')->where('buyer_slot_id', $slot_id)->get();
		foreach ($claim_codes as $key => $value) 
		{
			$return[$key]['claim_code'] 		= $value->claim_code;
			$return[$key]['claimed']			= $value->claimed;
		}
		return $return;
	}

	public static function get_random()
	{
		$return 	= Tbl_codes::Inventory()->InventoryItem()->CheckIfKit()->FromMainBranch()->CheckIfUsed()->CheckIfSold()->first();
		return $return;
	}
}
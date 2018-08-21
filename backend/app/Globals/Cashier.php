<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Request;
use App\Models\Tbl_cashier;
use App\Models\Tbl_inventory;
use App\Models\Users;
use App\Models\Tbl_item;
use Validator;
use Hash;
use App\Models\Tbl_wallet;
class Cashier
{
	public static function add($data)
	{

		$rules["full_name"] 		= "required";
		$rules["email"] 			= "required|email";
		$rules["password"] 			= "required";
		$rules["address"] 			= "required";
		$rules["contact_number"]	= "required";

		$validator = Validator::make($data, $rules);

        if ($validator->fails()) 
        {
            $return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = $validator->messages()->all();
        }
        else
        {
			$insert_user['name']								=	$data['full_name'];
			$insert_user['password']							=	Hash::make($data['password']);
			$insert_user['email']								=	$data['email'];
			$insert_user['created_at']							=	Carbon::now();
			$insert_user['updated_at']							=	Carbon::now();
			$insert_user['type']								=	'cashier';


			$insert_cashier['cashier_branch_id']				=	$data['branch_id'];
			$insert_cashier['cashier_address']					=	$data['address'];
			$insert_cashier['cashier_contact_number']			=	$data['contact_number'];
			$insert_cashier['cashier_status']					=	$data['status'];
			$insert_cashier['cashier_position']					=	$data['position'];
			$insert_cashier['cashier_date_created']				=	Carbon::now();
			
			$cashier_user_id 									=	DB::table('users')->insertGetId($insert_user);
			$insert_cashier['cashier_user_id'] 					=	$cashier_user_id;

			Tbl_cashier::insert($insert_cashier);
			

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Cashier Created";

		}
		return $return;
	}

	public static function getList($branch_id, $filter = null)
	{
		$data = Tbl_cashier::where('cashier_branch_id', $branch_id)->join('users', 'tbl_cashier.cashier_user_id', '=', 'users.id');
		if(isset($filter["status"]) && $filter["status"] != "all")
		{
			$data = $data->where("cashier_status", $filter["status"]);
		}
		if(isset($filter["position"]) && $filter["position"] != "all")
		{
			$data = $data->where("cashier_position", $filter["position"]);
		}
		
		$data = $data->get();
		return $data;
	}

	public static function get_data($id)
	{
		$return = Tbl_cashier::where('cashier_id', $id)->join('users', 'tbl_cashier.cashier_user_id', '=', 'users.id')->select('users.name','users.email', 'tbl_cashier.cashier_position', 'tbl_cashier.cashier_status','tbl_cashier.cashier_id', 'users.id')->first();

		return $return;
	}

	public static function cashier_update($data)
	{

		$update_user['name']						=	$data['name'];
		$update_user['email']						=	$data['email'];
		$update_cashier['cashier_position']			=	$data['cashier_position'];
		$update_cashier['cashier_status']			=	$data['cashier_status'];

		Tbl_cashier::where('cashier_id', $data['cashier_id'])->update($update_cashier);
		Users::where('id', $data['id'])->update($update_user);
		
		$return["status"]         = "success"; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Cashier Updated";
	}

	public static function search($filters = null)
	{
		$data = Tbl_cashier::where("tbl_cashier.archived", 0);
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

	public static function add_location($data)
	{
		foreach($data as $key => $value)
		{
			if($value["location"] == null || $value["location"] == "")
			{
				continue;
			}

			$rules["location"] = "required";

			$validator = Validator::make($value, $rules);

	        if ($validator->fails()) 
	        {
	            $return["status"]         = "error"; 
				$return["status_code"]    = 400; 
				$return["status_message"] = $validator->messages()->all();
	        }
	        else
	        {
	        	$check_exist = DB::table('tbl_location')->where('archive', 0)->where('location', $value['location'])->first();
	        	$success_add = 0;
	        	if(count($check_exist) == 0)
	        	{
	        		$insert['location']			= 	$value['location'];

		        	DB::table('tbl_location')->insert($insert);
		        	$success_add = $success_add + 1;
		        	$return["status"]         = "success"; 
					$return["status_code"]    = 200; 
					$return["status_message"] = "Location created Successfully.";
	        	}
	        	
			}
        	
        }

        if($success_add == 0)
        {
        	$return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = "Location already exists.";
        }
		return $return;

	}

	public static function get_location()
	{
		$return = DB::table('tbl_location')->where('archive', 0)->get();
		return $return;
	}

	public static function archive_location($data)
	{
		DB::table("tbl_location")->where("location", $data)->update(['archive' => 1]);
	}

	public static function ecom_checkout($data)
	{
		$get_user = Request::user();
		$subtotal	= 0;
		//direct = pickup, indirect = delivery
		if($data['method'] == 'direct')
		{
			$rules["branch_id"] = "required";

			$validator = Validator::make($data, $rules);

	        if ($validator->fails()) 
	        {
	            $return["status"]         = "error"; 
				$return["status_code"]    = 400; 
				$return["status_message"] = "Pick up location is required.";

				return $return;
	        }
	        else
	        {
	        	
				$delivery_charge = 0;
				$delivery_method = 'pickup';
				
				$order_status = 'processed';
				$retailer  	  = $data['branch_id'];
				//if true, slot ng bumibili yung nakalogin
	        }
		}
		else
		{
			$delivery_charge = 120;
			$delivery_method = 'delivery';
			$order_status = 'pending';
			//retailer is yung main branch
			$retailer 	  = 1;
		}
		if($data['slot']['slot_owner'] == $get_user->id)
		{

			foreach($data['items'] as $key => $value)
			{
				$items[$key]['quantity'] = $value['item_qty'];
				$items[$key]['item'] = $value['item_id'];
				$get_item[$key] = Tbl_item::where('item_id', $value['item_id'])->first();
				$check_item_kit[$key]['type'] 	  = $get_item[$key]->item_type;
				$check_item_kit[$key]['item'] 	  = $get_item[$key]->item_id;
				$check_item_kit[$key]['quantity'] =	$value['item_qty'];
				$subtotal = $subtotal + ($get_item[$key]->item_price * $value['item_qty']);
			}

			$user = DB::table('tbl_slot')->where('tbl_slot.slot_id', $data['slot']['slot_id'])->join('users', 'tbl_slot.slot_owner', '=', 'users.id')->join('tbl_wallet', 'tbl_slot.slot_id', '=', 'tbl_wallet.slot_id')->first();

			$grand_total = $subtotal + $delivery_charge;
			//check wallet if kasya
			if($user->wallet_amount < $grand_total)
			{
				dd('not enough wallet');
			}
			else
			{
				$insert_order['items'] 						= json_encode($items);
				$insert_order['delivery_method'] 			= $delivery_method;
				$insert_order['delivery_charge'] 			= $delivery_charge;
				$insert_order['order_status'] 				= $order_status;
				$insert_order['subtotal'] 					= $subtotal;
				$insert_order['buyer_name'] 				= $user->name;
				$insert_order['buyer_slot_code'] 			= $user->slot_no;
				$insert_order['buyer_slot_id'] 				= $user->slot_id;
				$insert_order['order_date_created'] 		= Carbon::now();
				$insert_order['discount_type'] 				= 'none';
				$insert_order['discount'] 					= 0;
				$insert_order['grand_total'] 				= $grand_total;
				$insert_order['retailer']					= $retailer;
				

				$order_id = DB::table('tbl_orders')->insertGetId($insert_order);
				$user_wallet = DB::table('tbl_wallet')->where('slot_id', $user->slot_id)->first();
				//update wallet
				$update['wallet_amount'] = $user_wallet->wallet_amount - $grand_total;

				Tbl_wallet::Currency()->Peso()->where('tbl_wallet.slot_id', $user->slot_id)->update($update);
				foreach($check_item_kit as $key => $value)
				{
					$item[$key] = $value;
						//code usage
						$code = DB::table('tbl_inventory')->join('tbl_codes', 'tbl_inventory.inventory_id', '=', 'tbl_codes.code_inventory_id')->where('tbl_inventory.inventory_branch_id', $retailer)->where('tbl_inventory.inventory_item_id', $item[$key]['item'])->where('code_used', 0)->where('code_sold', 0);
						
						if($item[$key]['quantity'] == 1)
						{
							$code = $code->first();	
							if(count($code) < $item[$key]['quantity'])
							{
								dd('Kulang ang inventory');
							}
							else
							{
								$update_codes['code_sold']		= 1;
								$update_codes['code_sold_to']	= $user->slot_owner;
								$update_codes['code_date_sold']	= Carbon::now();
								$update_inventory['inventory_quantity']		= $code->inventory_quantity - 1;
								DB::table('tbl_codes')->where('code_id', $code->code_id)->update($update_codes);
								Tbl_inventory::where('inventory_item_id', $item[$key]['item'])->where('inventory_id', $code->inventory_id)->update($update_inventory);
							}
						}
						else
						{
							$code = $code->limit($item[$key]['quantity'])->get();
							if(count($code) < $item[$key]['quantity'])
							{
								dd('Kulang ang inventory');
							}
							else
							{
								foreach($code as $key2 => $value2)
								{
									$update_codes['code_sold']		= 1;
									$update_codes['code_sold_to']	= $user->slot_owner;
									$update_codes['code_date_sold']	= Carbon::now();
									$update_inventory['inventory_quantity']		= $value2->inventory_quantity - 1;
									DB::table('tbl_codes')->where('code_id', $value2->code_id)->update($update_codes);
									Tbl_inventory::where('inventory_item_id', $item[$key]['item'])->where('inventory_id', $value2->inventory_id)->update($update_inventory);
									
								}
							}
						}
					
				}
				//Receipt making here(auto receipt kasi di naman makakapag checkout kung kulang ang wallet)
				
				do
				{
					$claim_code = implode("-", str_split(strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8)), 4));
					$check_claim_code = DB::table('tbl_receipt')->where('claim_code', $claim_code)->first();
				}
				while (count($check_claim_code) != 0); 

				$insert_receipt['items'] 						= json_encode($items);
				$insert_receipt['delivery_method'] 				= $delivery_method;
				$insert_receipt['delivery_charge'] 				= $delivery_charge;
				$insert_receipt['subtotal'] 					= $subtotal;
				$insert_receipt['buyer_name'] 					= $user->name;
				$insert_receipt['buyer_slot_code'] 				= $user->slot_no;
				$insert_receipt['buyer_slot_id'] 				= $user->slot_id;
				$insert_receipt['receipt_date_created'] 		= Carbon::now();
				$insert_receipt['discount_type'] 				= 'none';
				$insert_receipt['discount'] 					= 0;
				$insert_receipt['grand_total'] 					= $grand_total;
				$insert_receipt['claim_code'] 					= $claim_code;
				$insert_receipt['claimed'] 						= 0;
				$insert_receipt['retailer']						= $retailer;
				$insert_receipt['receipt_order_id'] 			= $order_id;

				DB::table('tbl_receipt')->insert($insert_receipt);

				$return["status"]         = "Success"; 
				$return["status_code"]    = 200; 
				$return["status_message"] = "Ordered Successfully.";

			}
		}
		else
		{
			$return["status"]         = "Error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = "This ain't yours, is it?";
		}	
		return $return;
	}
}


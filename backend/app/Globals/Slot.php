<?php
namespace App\Globals;
use App\Models\Tbl_slot;
use App\Models\Tbl_earning_log;
use App\Models\Tbl_mlm_settings;
use App\Models\Tbl_wallet_log;
use App\Models\Tbl_points_log;
use App\Models\Tbl_codes;
use App\Models\Users;
use App\Globals\Code;
use App\Globals\Tree;
use App\Globals\MLM;
use App\Globals\Wallet;
use DB;
use Carbon\Carbon;
use Validator;
use Crypt;
use Hash;
class Slot
{	
	public static function get()
	{
		return Tbl_slot::where("archive",0)->get();
	}
	public static function get_unplaced_slot($owner_id)
	{
		return Tbl_slot::where("archive",0)->where("slot_sponsor","!=",0)->where("slot_owner",$owner_id)->where("slot_placement",0)->where("membership_inactive",0)->get();
	}
	public static function get_unplaced_downline_slot($owner_id,$sponsor_id)
	{
		$sponsor = Tbl_slot::where("slot_id",$sponsor_id)->where("slot_owner",$owner_id)->first();
		if($sponsor)
		{
			return Tbl_slot::where("archive",0)->where("slot_sponsor",$sponsor_id)->where("slot_placement",0)->where("membership_inactive",0)->get();
		}
		else
		{
			return null;
		}
	}
	public static function get_full($filter, $limit = null)
	{
		$query = Tbl_slot::where("tbl_slot.archive",0)
					   ->leftJoin("users","users.id","=","tbl_slot.slot_owner")
					   ->leftJoin("tbl_slot as sponsor","sponsor.slot_id","=","tbl_slot.slot_sponsor")
					   ->leftJoin("tbl_slot as placement","placement.slot_id","=","tbl_slot.slot_placement")
					   ->leftJoin("tbl_membership","tbl_membership.membership_id","=","tbl_slot.slot_membership")
					   ->select("tbl_slot.*","users.*","tbl_membership.*","placement.slot_no as slot_placement_no","tbl_membership.*","sponsor.slot_no as slot_sponsor_no", 
					   			 DB::raw("DATE_FORMAT(tbl_slot.slot_date_created, '%m/%d/%Y (%h:%i %p)') as slot_date_created"), 
					   	         DB::raw("DATE_FORMAT(tbl_slot.slot_date_placed, '%m/%d/%Y (%h:%i %p)') as slot_date_placed_new"));
		
		if ($limit) 
		{
			return $query->paginate($limit);
		}
		else
		{
			return $query->get();
		}
	}
	public static function get_unplaced()
	{
		return Tbl_slot::where("tbl_slot.archive",0)
					   ->leftJoin("users","users.id","=","tbl_slot.slot_owner")
					   ->leftJoin("tbl_slot as sponsor","sponsor.slot_id","=","tbl_slot.slot_sponsor")
					   ->leftJoin("tbl_membership","tbl_membership.membership_id","=","tbl_slot.slot_membership")
					   ->where("tbl_slot.slot_placement","0")
					   ->where("tbl_slot.slot_sponsor","!=","0")
					   ->select("tbl_slot.*","users.*","tbl_membership.*","sponsor.slot_no as slot_sponsor_no", 
					   			 DB::raw("DATE_FORMAT(tbl_slot.slot_date_created, '%m/%d/%Y (%h:%i %p)') as slot_date_created"), 
					   	         DB::raw("DATE_FORMAT(tbl_slot.slot_date_placed, '%m/%d/%Y (%h:%i %p)') as slot_date_placed_new"))
					   ->get();
	}

	public static function create_slot($data)
	{
		$i = 0;
		$return["status_message"] = [];	

		$check_code = Code::check_membership_code_unused($data["code"],$data["pin"]);
		if($check_code == "used")
		{
			$return["status_message"][$i] = "Code already used.";
		    $i++;
		}
		else if($check_code == "not_exist")
		{
			$return["status_message"][$i] = "Code does not exists.";
			$i++;
		}



		$rules["slot_owner"]    = "required|exists:users,id";
		$rules["slot_sponsor"]  = "required|exists:tbl_slot,slot_no";
		$validator = Validator::make($data, $rules);
        if ($validator->fails()) 
        {
			$len = count($validator->errors()->getMessages());

			foreach ($validator->errors()->getMessages() as $key => $value) 
			{
				foreach($value as $val)
				{
					$return["status_message"][$i] = $val;

				    $i++;		
				}
			}
        }	

		$proceed_to_inactive               = 0;
		$check_inactive                    = Tbl_slot::where("slot_owner",$data["slot_owner"])->first();
		if($check_inactive)
		{
			if($check_inactive->membership_inactive == 1)
			{
				$proceed_to_inactive = 1;
			}
		}

		$slot_sponsor 				       = Tbl_slot::where("slot_no",$data["slot_sponsor"])->first();
		if($slot_sponsor)
		{
			if($slot_sponsor->membership_inactive == 1)
			{
				$return["status_message"][$i] = "Sponsor is inactive...";

		   		 $i++;	
			}
		}

        if($proceed_to_inactive == 1)
        {
    		if($slot_sponsor->slot_id == $check_inactive->slot_id)
    		{
				$return["status_message"][$i] = "Cannot sponsor yourself...";

		   		$i++;	
    		}
        }

        if($i == 0)
        {
        	if(array_key_exists("from_admin",$data))
        	{
        		$from_admin = 1;
        	}
        	else
        	{
        		$from_admin = 0;
        	}
        	$check_code = Code::use_membership_code($data["code"],$data["pin"],$data["slot_owner"],$from_admin);
        	if($check_code["status"] == "unused")
        	{
        		if($proceed_to_inactive == 0)
        		{
	        		$slot_sponsor 				       = Tbl_slot::where("slot_no",$data["slot_sponsor"])->first();
	        		$membership_id                     = Code::get_membership($data["code"],$data["pin"]);
	        		$user                              = Users::where("id",$data["slot_owner"])->first();
					$insert["slot_owner"]              = $user->id;
					$insert["slot_sponsor"]            = $slot_sponsor->slot_id;
					$insert["slot_membership"]         = $membership_id;
					$insert["slot_no"]                 = Slot::name_based_on_settings($user->first_name);
					$insert["slot_position"]           = "";
					$insert["slot_type"]           	   = "PS";
					$insert["slot_used_code"]          = $check_code["code_id"];
					$insert["slot_date_created"]       = Carbon::now();

					$new_id   = Tbl_slot::insertGetId($insert);
					$new_slot = Tbl_slot::where("slot_id",$new_id)->first();
					Tree::insert_tree_sponsor($new_slot, $new_slot, 1);
					MLM::create_entry($new_id);

					$return["status"]         = "success"; 
					$return["status_code"]    = 201; 
					$return["status_message"] = "Slot Created";
        			Wallet::generateSlotWalletAddress($new_id);

        		}
        		else if($proceed_to_inactive == 1)
        		{
	        		$slot_sponsor 				       = Tbl_slot::where("slot_no",$data["slot_sponsor"])->first();
	        		$membership_id                     = Code::get_membership($data["code"],$data["pin"]);
	        		$user                              = Users::where("id",$data["slot_owner"])->first();

					$update["slot_sponsor"]            = $slot_sponsor->slot_id;
					$update["slot_membership"]         = $membership_id;
					// $update["slot_no"]                 = Slot::name_based_on_settings($user->first_name);
					$update["slot_position"]           = "";
					$update["slot_type"]           	   = "PS";
					$update["slot_used_code"]          = $check_code["code_id"];
					$update["slot_date_created"]       = Carbon::now();
					$update["membership_inactive"]     = 0;

					Tbl_slot::where("slot_id",$check_inactive->slot_id)->update($update);

					$new_slot = Tbl_slot::where("slot_id",$check_inactive->slot_id)->first();
					Tree::insert_tree_sponsor($new_slot, $new_slot, 1);
					MLM::create_entry($check_inactive->slot_id);

					$return["status"]         = "success"; 
					$return["status_code"]    = 201; 
					$return["status_message"] = "Slot Activated";	
        		}	
        	}
        	else
        	{
				$return["status"]             = "error"; 
				$return["status_code"]        = 400; 
				$return["status_message"][$i] = "Please try again.";
				$i++;
        	}
        }
        else
        {
			$return["status"]         = "error"; 
			$return["status_code"]    = 400; 
        }
		
        return $return;
	}

	public static function create_blank_slot($owner)
	{
		$user                              = Users::where("id",$owner)->first();
		$insert["slot_owner"]              = $user->id;
		$insert["slot_sponsor"]            = 0;
		$insert["slot_membership"]         = 0;
		$insert["slot_no"]                 = Slot::name_based_on_settings($user->first_name);
		$insert["slot_position"]           = "";
		$insert["slot_type"]           	   = "PS";
		$insert["slot_used_code"]          = 0;
		$insert["slot_date_created"]       = Carbon::now();
		$insert["membership_inactive"]     = 1;

		$new_id   = Tbl_slot::insertGetId($insert);

        Wallet::generateSlotWalletAddress($new_id);

	}

	public static function place_slot($data,$type = "admin_area",$owner_id = 0)
	{
		$i = 0;
		$return["status_message"] = [];	

		$placement = $data["slot_placement"];
		$position  = $data["slot_position"];
		$slot_no   = $data["slot_code"];

		$rules["slot_placement"]  = "required|exists:tbl_slot,slot_no";
		$rules["slot_code"]       = "required|exists:tbl_slot,slot_no";

		$validator = Validator::make($data, $rules);
        if ($validator->fails()) 
        {
			foreach ($validator->errors()->getMessages() as $key => $value) 
			{
				foreach($value as $val)
				{
					$return["status_message"][$i] = $val;
				    $i++;		
				}
			}
        }	
        else
        {
         	if($position != "LEFT" && $position != "RIGHT")
         	{
				$return["status_message"][$i] = "Position error...";
				$i++;					
         	}

         	$slot_id         = Tbl_slot::where("slot_no",$slot_no)->first()->slot_id;
         	$placement       = Tbl_slot::where("slot_no",$placement)->first()->slot_id;
         	$check_placement = Tbl_slot::where("slot_placement",$placement)->where("slot_position",$position)->first();
         	if($check_placement)
         	{
				$return["status_message"][$i] = "Placement already taken...";
				$i++;	
         	}
         	else
         	{
         		$check_placement = Tbl_slot::where("slot_id",$placement)->first();
         		if( ($check_placement->slot_placement == 0 && $check_placement->slot_sponsor != 0) || $check_placement->membership_inactive == 1)
         		{
					$return["status_message"][$i] = "Placement is not allowed on unplaced slot";
					$i++;	
         		}
         	}



         	if($type == "member_owned")
         	{
         		$slot_owned  = Tbl_slot::where("slot_no",$slot_no)->where("slot_owner",$owner_id)->first();
         		if(!$slot_owned)
         		{
					$return["status_message"][$i] = "Error 501...";
					$i++;		
         		}

         	}

         	if($type == "member_downline")
         	{
         		$slot_owned  = Tbl_slot::where("slot_no",$slot_no)->first();
         		if(!$slot_owned)
         		{
					$return["status_message"][$i] = "Error 501...";
					$i++;		
         		}
         		else
         		{
         			$check_sponsor = Tbl_slot::where("slot_id",$slot_owned->slot_sponsor)->where("slot_owner",$owner_id)->first();
         			if(!$check_sponsor)
         			{
						$return["status_message"][$i] = "Error 503...";
						$i++;		
         			}
         		}
         	}
        }

        if($i == 0)
        {
        	$update["slot_placement"]    = $placement;
        	$update["slot_position"]     = $position;
        	$update["slot_date_placed"]  = Carbon::now();
        	Tbl_slot::where("slot_id",$slot_id)->update($update);

			$new_slot = Tbl_slot::where("slot_id",$slot_id)->first();
			Tree::insert_tree_placement($new_slot, $new_slot, 1);
			MLM::placement_entry($slot_id);
			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Slot placed";	      	
        }
        else
        {
			$return["status"]         = "error"; 
			$return["status_code"]    = 400; 
        }

        return $return;
	}

	public static function name_based_on_settings($name)
	{
		/*SLOT CREATION FORMAT*/
		// mlm_slot_no_format_type
		// 1 = Name Abbreviation + Auto
		// 2 = Number
		// 3 = Auto Number
		// 4 = Random Letters
		// 5 = Random Numbers
		$setting = Tbl_mlm_settings::first();
	    $return  = "";
	    $ctr     = 1;

	    $condition = false;

	    while($condition == false)
	    { 	
		    // mlm_slot_no_format
			if($setting->mlm_slot_no_format_type == 1)
			{
				$return = strtoUpper(substr($name, 0, 3)).Slot::generateRandomString(6);
			}
			else if($setting->mlm_slot_no_format_type == 2)
			{
				$return = Slot::generateRandomString(6,"number");
			}
			else if($setting->mlm_slot_no_format_type == 3)
			{
				$return = str_pad( (Tbl_slot::count() + $ctr) , 6, "0", STR_PAD_LEFT); 
			}
			else if($setting->mlm_slot_no_format_type == 4)
			{
				$return = Slot::generateRandomString(6,"alpha");
			}
			else if($setting->mlm_slot_no_format_type == 5)
			{
				$return = Slot::generateRandomString(6,"number");
			}

			$ctr++;


			$check = Tbl_slot::where("slot_no",$return)->first();
			if(!$check)
			{
				$condition = true;
			}
	    }

		return $return;
	}

	public static function generateRandomString($length = 5,$type = "all") 
	{
		if($type == "all")
		{
	    	$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		else if($type == "number")
		{
			$characters = '0123456789';
		}
		else if ($type == "alpha")
		{
			$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}

	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) 
	    {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}

	// public static function check_placement($slot_placement,$slot_position)
	// {
	// 	$slot    = Tbl_slot::where("slot_id",$slot_id)->first();
	// 	if($slot)
	// 	{
	// 		if($slot_position == "LEFT" || $slot_position == "RIGHT")
	// 		{
	// 			$exist = Tbl_slot::where("slot_id",$slot_placement)->where("slot_position",$slot_position)->first();
	// 			if($exist)
	// 			{

	// 			}
	// 			else
	// 			{
					
	// 			}
	// 		}
	// 		else
	// 		{
	// 			$message = "Only LEFT or RIGHT for position.";
	// 			$status  = "no_position";				
	// 		}
	// 	}
	// 	else
	// 	{
	// 		$message = "Placement slot not found.";
	// 		$status  = "no_placement";
	// 	}

	// 	$data["message"] = $message;
	// 	$data["status"]  = $status;
	// }

	public static function get_slot_information($id)
	{
		$query = Tbl_slot::where("tbl_slot.slot_id", $id);
		$query = $query->leftJoin("users", "tbl_slot.slot_owner", "=", "users.id");

		return $query->first();
	}

	public static function submit_slot_information($request)
	{
		/* Slot Information */
		$rules["slot_no"] = "required";
		$rules["slot_owner"] = "required";
		$rules["slot_status"] = "required";
		$rules["slot_sponsor"] = "required";
		$rules["slot_placement"] = "required";
		$rules["slot_position"] = "required";

		/* Member Information */
		$rules["email"] = "required";
		$rules["first_name"] = "required";
		$rules["last_name"] = "required";
		$rules["contact"] = "required";
		$rules["country_id"] = "required";
		$rules["password"] = "required";
		
		$validator = Validator::make($request, $rules);

        if ($validator->fails()) 
        {
            $return["status"] = "error"; 
			$return["status_code"] = 400; 
			$return["status_message"] = $validator->messages()->all();
        }
        else
        {
        	$slot_id = $request['slot_id'];
        	$update_slot["slot_no"] = $request["slot_no"];
        	$update_slot["slot_owner"] = $request["slot_owner"];
        	$update_slot["slot_status"] = $request["slot_status"];
        	$update_slot["slot_sponsor"] = $request["slot_sponsor"];
        	$update_slot["slot_placement"] = $request["slot_placement"];
        	$update_slot["slot_position"] = $request["slot_position"];

			Tbl_slot::where("slot_id", $slot_id)->update($update_slot);

			$user_id = $request['id'];
        	$update_user["email"] = $request["email"];
        	$update_user["name"] = $request["first_name"] . " " . $request["last_name"]; 
        	$update_user["first_name"] = $request["first_name"];
        	$update_user["last_name"] = $request["last_name"];
        	$update_user["contact"] = $request["contact"];
        	$update_user["country_id"] = $request["country_id"];
        	$update_user["crypt"] = Crypt::encrypt($request["show_password"]);
        	$update_user["password"] = Hash::make($request["show_password"]);

			Users::where("id", $user_id)->update($update_user);

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Slot Updated";
        }

        return $return;
	}

	public static function get_slot_details($id)
	{
		$query = Tbl_slot::where("tbl_slot.slot_id", $id);
		$query = $query->leftJoin("users", "tbl_slot.slot_owner", "=", "users.id");
		$query = $query->leftJoin("tbl_membership", "tbl_slot.slot_membership", "=", "tbl_membership.membership_id");
		$query = $query->leftJoin("tbl_country", "users.country_id", "=", "tbl_country.country_id");
		$query = $query->first();

		if ($query) 
		{
			if ($query->slot_sponsor) 
			{
				$sponsor = Tbl_slot::where("tbl_slot.slot_id", $query->slot_sponsor)->first();

				if ($sponsor) 
				{
					$query->sponsor = $sponsor;
				}
				else
				{
					$query->sponsor = null;
				}
			}

			$slot_count = Tbl_slot::where("tbl_slot.slot_owner", $query->slot_owner)->where("tbl_slot.archive", 0)->count();

			if ($slot_count) 
			{
				$query->slot_count = $slot_count;
			}
		}

		return $query;
	}

	public static function get_slot_earnings($data, $limit = null)
	{
		$query = Tbl_earning_log::where("tbl_earning_log.earning_log_slot_id", $data["id"]);
		$query = $query->leftJoin("tbl_slot", "tbl_slot.slot_id", "=", "tbl_earning_log.earning_log_slot_id");

		if ($data["search"])
		{
			$query = $query->where("tbl_slot.slot_no", 'LIKE', '%' . $data["search"] . '%');
		}

		if ($data["from"] && $data["to"]) 
		{
			$query = $query->whereBetween("tbl_earning_log.earning_log_date_created", [$data["from"], $data["to"]]);
		}

		if ($data["type"] && $data["type"] != "all") 
		{
			$query = $query->where("tbl_earning_log.earning_log_plan_type", $data["type"]);
		}

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		return $query;
	}

	public static function get_slot_total_earnings($id)
	{
		$query = Tbl_earning_log::where("tbl_earning_log.earning_log_slot_id", $id);
		$query = $query->sum("earning_log_amount");	

		return $query;
	}

	public static function get_slot_distributed($data, $limit = null)
	{
		$query = Tbl_earning_log::where("tbl_earning_log.earning_log_slot_id", $data["id"]);
		$query = $query->leftJoin("tbl_slot", "tbl_slot.slot_id", "=", "tbl_earning_log.earning_log_slot_id");

		if ($data["search"])
		{
			$query = $query->where("tbl_slot.slot_no", 'LIKE', '%' . $data["search"] . '%');
		}

		if ($data["from"] && $data["to"]) 
		{
			$query = $query->whereBetween("tbl_earning_log.earning_log_date_created", [$data["from"], $data["to"]]);
		}

		if ($data["type"] && $data["type"] != "all") 
		{
			$query = $query->where("tbl_earning_log.earning_log_plan_type", $data["earning_log_plan_type"]);
		}

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		return $query;
	}

	public static function get_slot_total_distributed($id)
	{
		$query = Tbl_earning_log::where("tbl_earning_log.earning_log_slot_id", $id);
		$query = $query->sum("earning_log_amount");	

		return $query;
	}

	public static function get_slot_wallet($data, $limit = null)
	{
		$query = Tbl_wallet_log::where("tbl_wallet_log.wallet_log_slot_id", $data["id"]);
		$query = $query->leftJoin("tbl_slot", "tbl_slot.slot_id", "=", "tbl_wallet_log.wallet_log_slot_id");

		if ($data["from"] && $data["from"] != "null" && $data["to"] && $data["to"] != "null") 
		{
			$query = $query->whereBetween("tbl_wallet_log.wallet_log_date_created", [$data["from"], $data["to"]]);
		}

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		return $query;
	}

	public static function get_slot_total_wallet($id)
	{
		$query = Tbl_wallet_log::where("tbl_wallet_log.wallet_log_slot_id", $id);
		$query = $query->sum("wallet_log_amount");	

		return $query;
	}

	public static function get_slot_payout($data, $limit = null)
	{
		$query = Tbl_wallet_log::where("tbl_wallet_log.wallet_log_slot_id", $data["id"]);
		$query = $query->leftJoin("tbl_slot", "tbl_slot.slot_id", "=", "tbl_wallet_log.wallet_log_slot_id");

		if ($data["from"] && $data["from"] != "null" && $data["to"] && $data["to"] != "null") 
		{
			$query = $query->whereBetween("tbl_wallet_log.wallet_log_date_created", [$data["from"], $data["to"]]);
		}

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		return $query;
	}

	public static function get_slot_total_payout($id)
	{
		$query = Tbl_wallet_log::where("tbl_wallet_log.wallet_log_slot_id", $id);
		$query = $query->sum("wallet_log_amount");	

		return $query;
	}

	public static function get_slot_points($data, $limit = null)
	{
		$query = Tbl_points_log::where("tbl_points_log.points_log_slot_id", $data["id"]);
		$query = $query->leftJoin("tbl_slot", "tbl_slot.slot_id", "=", "tbl_points_log.points_log_slot_id");

		if ($data["from"] && $data["from"] != "null" && $data["to"] && $data["to"] != "null") 
		{
			$query = $query->whereBetween("tbl_points_log.points_log_date_created", [$data["from"], $data["to"]]);
		}

		if ($data["type"] && $data["type"] != "all") 
		{
			$query = $query->where("tbl_points_log.points_log_type", $data["type"]);
		}

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		foreach ($query as $key => $value) 
		{
			$slot_trigger = Tbl_slot::where("tbl_slot.slot_id", $value->points_log_cause_id)->first();

			if ($slot_trigger) 
			{
				$query[$key]->slot_trigger = $slot_trigger;
			}
			else
			{
				$query[$key]->slot_trigger = null;
			}
		}

		return $query;
	}

	public static function get_slot_total_points($id)
	{
		$query = Tbl_points_log::where("tbl_points_log.points_log_slot_id", $id);
		$query = $query->sum("points_log_amount");	

		return $query;
	}

	public static function get_slot_network($data, $limit = null)
	{
		$query = Tbl_slot::where("tbl_slot.slot_sponsor", $data["id"]);
		$query = $query->leftJoin("users", "tbl_slot.slot_owner", "=", "users.id");

		if($data["search"])
		{
			$query = $query->where('tbl_slot.slot_no', 'LIKE', '%' . $data["search"] . '%')
                		   ->orWhere('users.name','LIKE','%' . $data["search"] . '%');
		}

		if($data["level"] && $data["level"] != "all")
		{

		}

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		return $query;
	}

	public static function get_slot_codevault($data, $limit = null)
	{
		$query = Tbl_codes::soldTo($data["id"])->inventory()->inventoryItem()->inventoryItemMembership();

		if ($limit) 
		{
			$query = $query->paginate($limit);
		}
		else
		{
			$query = $query->get();	
		}

		return $query;
	}
}
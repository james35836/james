<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use App\Models\Tbl_membership;
use App\Models\Tbl_membership_income;
use App\Models\Tbl_mlm_plan;
use App\Models\Tbl_membership_indirect_level;
use App\Models\Tbl_membership_unilevel_level;
use App\Models\Tbl_mlm_unilevel_settings;
use App\Models\Tbl_stairstep_settings;
use App\Models\Tbl_stairstep_rank;
use App\Models\Tbl_binary_pairing;
use App\Models\Tbl_binary_settings;
use App\Models\Tbl_binary_points_settings;
use App\Models\Tbl_label;

class Update_plan
{
	public static function DIRECT($plan,$label,$data)
	{
		$data = json_decode($data,true);
		if($data != null)
		{
			foreach($data["direct_settings"] as $key => $value)
			{
				foreach($value as $key2 => $value2)
				{
					$check = $check = Tbl_membership_income::where("membership_id",$key)->where("membership_entry_id",$key2)->first();
					if($check)
					{
						$update["membership_direct_income"] = $value2;
						Tbl_membership_income::where("membership_id",$key)->where("membership_entry_id",$key2)->update($update);
					}
					else
					{
						$insert["membership_id"]			= $key;
						$insert["membership_entry_id"]      = $key2;
						$insert["membership_direct_income"] = $value2;
						Tbl_membership_income::insert($insert);
					}
				}
			}
		}

		Plan::update_label($plan,$label);
		$update_plan["mlm_plan_enable"] = 1;
		Tbl_mlm_plan::where("mlm_plan_code",$plan)->update($update_plan);


		$return["status"]         = "success"; 
		$return["update_status"]  = $update_plan["mlm_plan_enable"]; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Settings updated...";

		return $return;
	}

	public static function INDIRECT($plan,$label,$data)
	{

		$data = json_decode($data,true);
		if($data != null)
		{
			foreach($data["membership_settings"] as $key => $value)
			{			
				$value["membership_indirect_level"] = $value["membership_indirect_level"] + 1;
				if(isset($data["indirect_settings"][$value["membership_id"]]))
				{
					$level = 2;	
					/* GET THE DATA SETTINGS PER MEMBERSHIP */
					foreach($data["indirect_settings"][$value["membership_id"]] as $level_target => $per_membership)
					{
						/* GET THE DATA SETTINGS PER LEVEL OF TARGET MEMBERSHIP */
						foreach($per_membership as $membership_entry_id => $membership_indirect_income)
						{
							/* membership_entry_id  = membership_entry_id */
							/* membership_indirect_income = membership_indirect_income*/
							$check = Tbl_membership_indirect_level::where("membership_level",$level)->where("membership_id",$value["membership_id"])->where("membership_entry_id",$membership_entry_id)->first();
							if($check)
							{
								$update_level["membership_level"]		    = $level;
								$update_level["membership_id"]		        = $value["membership_id"];
								$update_level["membership_entry_id"]	    = $membership_entry_id;
								$update_level["membership_indirect_income"] = $membership_indirect_income;
								Tbl_membership_indirect_level::where("membership_level",$level)->where("membership_id",$value["membership_id"])->where("membership_entry_id",$membership_entry_id)->update($update_level);
							}
							else
							{
								$insert["membership_level"]		      = $level;
								$insert["membership_id"]		      = $value["membership_id"];
								$insert["membership_entry_id"]	      = $membership_entry_id;
								$insert["membership_indirect_income"] = $membership_indirect_income;
								Tbl_membership_indirect_level::insert($insert);
							}
						}


						$level++;
						if($level > $value["membership_indirect_level"])
						{
							Tbl_membership_indirect_level::where("membership_level",">=",$level)->where("membership_id",$value["membership_id"])->delete();
							break;
						}
					}
				}

				$update["membership_indirect_level"] = count(Tbl_membership_indirect_level::select("membership_level")->where("membership_id",$value["membership_id"])->groupBy("membership_level")->get());
				Tbl_membership::where("membership_id",$value["membership_id"])->update($update);
			}
		}

		Plan::update_label($plan,$label);
		$update_plan["mlm_plan_enable"] = 1;
		Tbl_mlm_plan::where("mlm_plan_code",$plan)->update($update_plan);


		$return["status"]         = "success"; 
		$return["update_status"]  = $update_plan["mlm_plan_enable"]; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Settings updated...";			

		return $return;
	}

	public static function UNILEVEL($plan,$label,$data)
	{
		$data = json_decode($data,true);
		if($data != null)
		{
			foreach($data["membership_settings"] as $key => $value)
			{			
				if(isset($data["unilevel_settings"][$value["membership_id"]]))
				{
					$level = 1;	
					/* GET THE DATA SETTINGS PER MEMBERSHIP */
					foreach($data["unilevel_settings"][$value["membership_id"]] as $membership_id => $per_membership)
					{
						/* GET THE DATA SETTINGS PER LEVEL OF TARGET MEMBERSHIP */
						foreach($per_membership as $membership_entry_id => $membership_percentage)
						{
							/* membership_entry_id  = membership_entry_id */
							/* membership_percentage = membership_percentage*/
							$check = Tbl_membership_unilevel_level::where("membership_level",$level)->where("membership_id",$value["membership_id"])->where("membership_entry_id",$membership_entry_id)->first();
							if($check)
							{
								$update_level["membership_level"]		    = $level;
								$update_level["membership_id"]		        = $value["membership_id"];
								$update_level["membership_entry_id"]	    = $membership_entry_id;
								$update_level["membership_percentage"] = $membership_percentage;
								Tbl_membership_unilevel_level::where("membership_level",$level)->where("membership_id",$value["membership_id"])->where("membership_entry_id",$membership_entry_id)->update($update_level);
							}
							else
							{
								$insert["membership_level"]		      = $level;
								$insert["membership_id"]		      = $value["membership_id"];
								$insert["membership_entry_id"]	      = $membership_entry_id;
								$insert["membership_percentage"] = $membership_percentage;
								Tbl_membership_unilevel_level::insert($insert);
							}
						}


						$level++;

						if($level > $value["membership_indirect_level"])
						{
							Tbl_membership_unilevel_level::where("membership_level",">=",$level)->where("membership_id",$value["membership_id"])->delete();
							break;
						}
					}
				}

				$update["membership_unilevel_level"] = count(Tbl_membership_unilevel_level::select("membership_level")->where("membership_id",$value["membership_id"])->groupBy("membership_level")->get());
				$update["membership_required_pv"]    = $value["membership_required_pv"];
				Tbl_membership::where("membership_id",$value["membership_id"])->update($update);
			}
		}


		Plan::update_label($plan,$label);
		$update_plan["mlm_plan_enable"] = 1;
		Tbl_mlm_plan::where("mlm_plan_code",$plan)->update($update_plan);

		$update_unilevel_settings["personal_as_group"]  	   = $data["setup"]["personal_as_group"];
		$update_unilevel_settings["gpv_to_wallet_conversion"]  = $data["setup"]["gpv_to_wallet_conversion"];

		Plan::update_label("PERSONAL_PV",$data["setup"]["personal_pv"]);
		Plan::update_label("GROUP_PV",$data["setup"]["group_pv"]);
		
		Tbl_mlm_unilevel_settings::where("mlm_unilevel_settings_id",1)->update($update_unilevel_settings);


		$return["status"]         = "success"; 
		$return["update_status"]  = $update_plan["mlm_plan_enable"]; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Settings updated...";	

		return $return;
	}
	
	public static function STAIRSTEP($plan,$label,$data)
	{
		$data = json_decode($data,true);
		if($data != null)
		{
			$ctr = 1;
			foreach($data["stairstep_settings"] as $index => $value)
			{
				if($value["stairstep_rank_name"] != "" && strlen(trim($value["stairstep_rank_name"])) != 0)
				{
					$check = Tbl_stairstep_rank::where("stairstep_rank_level",$ctr)->first();
					if(!$check)
					{
						$insert["stairstep_rank_name"]			= $value["stairstep_rank_name"];
						$insert["stairstep_rank_override"]		= $value["stairstep_rank_override"] ? $value["stairstep_rank_override"] : 0;
						$insert["stairstep_rank_personal"]		= $value["stairstep_rank_personal"] ? $value["stairstep_rank_personal"] : 0;
						$insert["stairstep_rank_personal_all"]	= $value["stairstep_rank_personal_all"] ? $value["stairstep_rank_personal_all"] : 0;
						$insert["stairstep_rank_group_all"]		= $value["stairstep_rank_group_all"] ? $value["stairstep_rank_group_all"] : 0;
						$insert["stairstep_rank_level"]			= $ctr;
						$insert["stairstep_rank_date_created"]	= Carbon::now();

						Tbl_stairstep_rank::insert($insert);
					}
					else
					{
						$update["stairstep_rank_name"]			= $value["stairstep_rank_name"];
						$update["stairstep_rank_override"]		= $value["stairstep_rank_override"] ? $value["stairstep_rank_override"] : 0;
						$update["stairstep_rank_personal"]		= $value["stairstep_rank_personal"] ? $value["stairstep_rank_personal"] : 0;
						$update["stairstep_rank_personal_all"]	= $value["stairstep_rank_personal_all"] ? $value["stairstep_rank_personal_all"] : 0;
						$update["stairstep_rank_group_all"]		= $value["stairstep_rank_group_all"] ? $value["stairstep_rank_group_all"] : 0;
						$update["archive"]						= 0;

						Tbl_stairstep_rank::where("stairstep_rank_level",$ctr)->update($update);	
					}
					$ctr++;
				}
			}

			if($data["stairstep_settings_end"]["stairstep_rank_name"] != "" && strlen(trim($data["stairstep_settings_end"]["stairstep_rank_name"])) != 0 )
			{
				$check = Tbl_stairstep_rank::where("stairstep_rank_level",$ctr)->first();
				if(!$check)
				{
					$insert["stairstep_rank_name"]			= $data["stairstep_settings_end"]["stairstep_rank_name"];
					$insert["stairstep_rank_override"]		= $data["stairstep_settings_end"]["stairstep_rank_override"] ? $data["stairstep_settings_end"]["stairstep_rank_override"] : 0;
					$insert["stairstep_rank_personal"]		= $data["stairstep_settings_end"]["stairstep_rank_personal"] ? $data["stairstep_settings_end"]["stairstep_rank_personal"] : 0;
					$insert["stairstep_rank_personal_all"]	= $data["stairstep_settings_end"]["stairstep_rank_personal_all"] ? $data["stairstep_settings_end"]["stairstep_rank_personal_all"] : 0;
					$insert["stairstep_rank_group_all"]		= $data["stairstep_settings_end"]["stairstep_rank_group_all"] ? $data["stairstep_settings_end"]["stairstep_rank_group_all"] : 0;
					$insert["stairstep_rank_date_created"]	= Carbon::now();
					$insert["stairstep_rank_level"]			= $ctr;
					Tbl_stairstep_rank::insert($insert);
					$ctr++;
				}
				else
				{
					$update["stairstep_rank_name"]			= $data["stairstep_settings_end"]["stairstep_rank_name"];
					$update["stairstep_rank_override"]		= $data["stairstep_settings_end"]["stairstep_rank_override"] ? $data["stairstep_settings_end"]["stairstep_rank_override"] : 0;
					$update["stairstep_rank_personal"]		= $data["stairstep_settings_end"]["stairstep_rank_personal"] ? $data["stairstep_settings_end"]["stairstep_rank_personal"] : 0;
					$update["stairstep_rank_personal_all"]	= $data["stairstep_settings_end"]["stairstep_rank_personal_all"] ? $data["stairstep_settings_end"]["stairstep_rank_personal_all"] : 0;
					$update["stairstep_rank_group_all"]		= $data["stairstep_settings_end"]["stairstep_rank_group_all"] ? $data["stairstep_settings_end"]["stairstep_rank_group_all"] : 0;
					$update["archive"]						= 0;

					Tbl_stairstep_rank::where("stairstep_rank_level",$ctr)->update($update);
					$ctr++;	
				}
			}




			$update_archive["archive"] = 1;
			Tbl_stairstep_rank::where("stairstep_rank_level",">=",$ctr)->update($update_archive);
		}


		Plan::update_label($plan,$label);
		$update_plan["mlm_plan_enable"] = 1;
		Tbl_mlm_plan::where("mlm_plan_code",$plan)->update($update_plan);

		$update_stairstep_settings["personal_as_group"]  		  = $data["setup"]["personal_as_group"];
		$update_stairstep_settings["sgpv_to_wallet_conversion"]   = $data["setup"]["sgpv_to_wallet_conversion"];
		$update_stairstep_settings["live_update"]  		          = $data["setup"]["live_update"];
		Tbl_stairstep_settings::where("stairstep_settings_id",1)->update($update_stairstep_settings);

		Plan::update_label("PERSONAL_STAIRSTEP_PV_LABEL",$data["setup"]["personal_stairstep_pv_label"]);
		Plan::update_label("GROUP_STAIRSTEP_PV_LABEL",$data["setup"]["group_stairstep_pv_label"]);
		Plan::update_label("STAIRSTEP_EARNING_POINTS_LABEL",$data["setup"]["earning_label_points"]);


		$return["status"]         = "success"; 
		$return["update_status"]  = $update_plan["mlm_plan_enable"]; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Settings updated...";			

		return $return;
	}

	public static function BINARY($plan,$label,$data)
	{
		$data = json_decode($data,true);
		if($data != null)
		{
			$combined_id = array();
			foreach($data["binary_settings_pair"] as $index => $value)
			{
				if($value["binary_pairing_bonus"] != "" && $value["binary_pairing_bonus"] != 0)
				{
					$left  = $value["binary_pairing_left"]  ? $value["binary_pairing_left"]  : 0;
					$right = $value["binary_pairing_right"] ? $value["binary_pairing_right"] : 0;

					$check = Tbl_binary_pairing::where("binary_pairing_left",$left)->where("binary_pairing_right",$right)->first();
					if(!$check)
					{
						$insert["binary_pairing_left"]	    = $left;
						$insert["binary_pairing_right"]		= $right;
						$insert["binary_pairing_bonus"]		= $value["binary_pairing_bonus"] ? $value["binary_pairing_bonus"] : 0;
						$id 								= Tbl_binary_pairing::insertGetId($insert);
						array_push($combined_id,$id);
					}
					else
					{
						$update["binary_pairing_bonus"]  = $value["binary_pairing_bonus"] ? $value["binary_pairing_bonus"] : 0;
						$update["archive"]				 = 0;

						Tbl_binary_pairing::where("binary_pairing_left",$left)->where("binary_pairing_right",$right)->update($update);
						array_push($combined_id,$check->binary_pairing_id);
					}
				}
			}

			if($data["binary_settings_pair_end"]["binary_pairing_bonus"] != "" && $data["binary_settings_pair_end"]["binary_pairing_bonus"] != 0)
			{
				$left  = $data["binary_settings_pair_end"]["binary_pairing_left"]  ? $data["binary_settings_pair_end"]["binary_pairing_left"]  : 0;
				$right = $data["binary_settings_pair_end"]["binary_pairing_right"] ? $data["binary_settings_pair_end"]["binary_pairing_right"] : 0;

				$check = Tbl_binary_pairing::where("binary_pairing_left",$left)->where("binary_pairing_right",$right)->first();
				if(!$check)
				{
					$insert["binary_pairing_left"]	    = $left;
					$insert["binary_pairing_right"]		= $right;
					$insert["binary_pairing_bonus"]		= $data["binary_settings_pair_end"]["binary_pairing_bonus"] ? $data["binary_settings_pair_end"]["binary_pairing_bonus"] : 0;
					$id 								= Tbl_binary_pairing::insertGetId($insert);
					array_push($combined_id,$id);
				}
				else
				{
					$update["binary_pairing_bonus"]  = $data["binary_settings_pair_end"]["binary_pairing_bonus"] ? $data["binary_settings_pair_end"]["binary_pairing_bonus"] : 0;
					$update["archive"]				 = 0;

					Tbl_binary_pairing::where("binary_pairing_left",$left)->where("binary_pairing_right",$right)->update($update);
					array_push($combined_id,$check->binary_pairing_id);
				}
			}

			$update_archive["archive"] = 1;
			Tbl_binary_pairing::whereNotIn("binary_pairing_id",$combined_id)->update($update_archive);


			foreach($data["binary_settings"] as $key => $value)
			{
				foreach($value as $key2 => $value2)
				{
					$check = $check = Tbl_binary_points_settings::where("membership_id",$key)->where("membership_entry_id",$key2)->first();
					if($check)
					{
						$update_pts["membership_binary_points"] = $value2;
						Tbl_binary_points_settings::where("membership_id",$key)->where("membership_entry_id",$key2)->update($update_pts);
					}
					else
					{
						$insert_pts["membership_id"]			= $key;
						$insert_pts["membership_entry_id"]      = $key2;
						$insert_pts["membership_binary_points"] = $value2;
						Tbl_binary_points_settings::insert($insert_pts);
					}
				}
			}

			foreach($data["membership_settings"] as $key => $value)
			{
				$update_set["membership_pairings_per_day"] = $value["membership_pairings_per_day"];
				Tbl_membership::where("membership_id",$value["membership_id"])->update($update_set);					
			}
		}


		$update_plan["mlm_plan_enable"] = 1;
		Tbl_mlm_plan::where("mlm_plan_code",$plan)->update($update_plan);

		Plan::update_label($plan,$label);
		Plan::update_label("BINARY_POINTS_LEFT",$data["setup"]["binary_points_left"]);
		Plan::update_label("BINARY_POINTS_RIGHT",$data["setup"]["binary_points_right"]);

		$update_binary_settings["strong_leg_retention"]  	 = $data["setup"]["strong_leg_retention"];
		$update_binary_settings["gc_pairing_count"]          = $data["setup"]["gc_pairing_count"];
		$update_binary_settings["cycle_per_day"]  			 = $data["setup"]["cycle_per_day"];

		Tbl_binary_settings::where("binary_settings_id",1)->update($update_binary_settings);


		$return["status"]         = "success"; 
		$return["update_status"]  = $update_plan["mlm_plan_enable"]; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Settings updated...";	

		return $return;
	}
}
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

class Get_plan
{
	public static function DIRECT()
	{
		$plan 				   = Tbl_mlm_plan::where("mlm_plan_code","DIRECT")->first();
		$data["label"]         = Plan::get_label($plan->mlm_plan_code);
		$data["status"]        = $plan->mlm_plan_enable;

		$data["settings"]      = [];


		$data["settings"]["direct_settings"] = [];

		$get                   = Tbl_membership::where("archive",0)->get();
		foreach($get as $g)
		{
			foreach($get as $g2)
			{
				$check = Tbl_membership_income::where("membership_id",$g->membership_id)->where("membership_entry_id",$g2->membership_id)->first();
				if($check)
				{
					$data["settings"]["direct_settings"][$g->membership_id][$g2->membership_id] = $check->membership_direct_income; 			
				}
				else
				{
					$data["settings"]["direct_settings"][$g->membership_id][$g2->membership_id] = 0; 			
				}
			}
		}

		return $data;
	}

	public static function INDIRECT()
	{
		$plan 				   = Tbl_mlm_plan::where("mlm_plan_code","INDIRECT")->first();
		$data["label"]         = Plan::get_label($plan->mlm_plan_code);
		$data["status"]        = $plan->mlm_plan_enable;
		
		$data["settings"]      = [];
		$get                   = Tbl_membership_indirect_level::get();
		$membership            = Tbl_membership::where("archive",0)->get();

		$data["settings"]["indirect_settings"] = [];
		$data["settings"]["membership_level"]  = [];
		foreach($membership as $memb)
		{
			$data["settings"]["membership_level"][$memb->membership_id] = array_fill(0, $memb->membership_indirect_level, "");
		}

		foreach($get as $g)
		{
			$data["settings"]["indirect_settings"][$g->membership_id][$g->membership_level][$g->membership_entry_id] = $g->membership_indirect_income;
		}

		return $data;
	}

	public static function UNILEVEL()
	{
		$plan 				   = Tbl_mlm_plan::where("mlm_plan_code","UNILEVEL")->first();
		$data["label"]         = Plan::get_label($plan->mlm_plan_code);
		$data["status"]        = $plan->mlm_plan_enable;
		
		$data["settings"]      = [];
		$get                   = Tbl_membership_unilevel_level::get();
		$membership            = Tbl_membership::where("archive",0)->get();

		$check_exist = Tbl_mlm_unilevel_settings::first();
		if(!$check_exist)
		{
			$settings["personal_as_group"]		  = 0;
			$settings["gpv_to_wallet_conversion"] = 0;
			Tbl_mlm_unilevel_settings::insert($settings);
		}

		$data["settings"]["setup"]             		  = Tbl_mlm_unilevel_settings::first();
		$data["settings"]["setup"]->personal_pv       = Plan::get_label("PERSONAL_PV");
		$data["settings"]["setup"]->group_pv          = Plan::get_label("GROUP_PV");
		$data["settings"]["unilevel_settings"]        = [];
		$data["settings"]["membership_level"]         = [];

		foreach($membership as $memb)
		{
			$data["settings"]["membership_level"][$memb->membership_id] = array_fill(0, $memb->membership_unilevel_level, "");
		}

		foreach($get as $g)
		{
			$data["settings"]["unilevel_settings"][$g->membership_id][$g->membership_level][$g->membership_entry_id] = $g->membership_percentage;
		}

		return $data;
	}
	
	public static function STAIRSTEP()
	{
		$plan 				   = Tbl_mlm_plan::where("mlm_plan_code","STAIRSTEP")->first();
		$data["label"]         = Plan::get_label($plan->mlm_plan_code);
		$data["status"]        = $plan->mlm_plan_enable;
		
		$data["settings"]      = [];

		$check_exist = Tbl_stairstep_settings::first();
		if(!$check_exist)
		{
			$settings["personal_as_group"]	= 0;
			$settings["live_update"]		= 0;
			Tbl_stairstep_settings::insert($settings);
		}

		$data["settings"]["setup"]              	            = Tbl_stairstep_settings::first();
		$data["settings"]["setup"]->personal_stairstep_pv_label = Plan::get_label("PERSONAL_STAIRSTEP_PV_LABEL");
		$data["settings"]["setup"]->group_stairstep_pv_label    = Plan::get_label("GROUP_STAIRSTEP_PV_LABEL");
		$data["settings"]["setup"]->earning_label_points        = Plan::get_label("STAIRSTEP_EARNING_POINTS_LABEL");
		$data["settings"]["stairstep_settings"] 	            = [];
		$data["settings"]["stairstep_settings_end"]             = (object)array("stairstep_rank_name"=>"","stairstep_rank_override"=>"","stairstep_rank_personal"=>"","stairstep_rank_personal_all"=>"","stairstep_rank_group_all"=>"");
		$data["settings"]["membership_level"]  		            = [];

		$stairstep_rank    = Tbl_stairstep_rank::where("archive",0)
											   ->select("stairstep_rank_level","stairstep_rank_id","stairstep_rank_name","stairstep_rank_override","stairstep_rank_personal","stairstep_rank_personal_all","stairstep_rank_group_all")
											   ->get();

		$array = array();
		foreach($stairstep_rank as $srank)
		{
			array_push($array,$srank);
		}
		$data["settings"]["stairstep_settings"]      = $array;
		$data["settings"]["count_stairstep_settings"] = count($array);


		return $data;
	}

	public static function BINARY()
	{
		$plan 				   = Tbl_mlm_plan::where("mlm_plan_code","BINARY")->first();
		$data["label"]         = Plan::get_label($plan->mlm_plan_code);
		$data["status"]        = $plan->mlm_plan_enable;
		
		$data["settings"]      = [];

		$check_exist = Tbl_binary_settings::first();
		if(!$check_exist)
		{
			$setting["auto_placement"]               = 0;
			$setting["auto_placement_type"]          = 0;
			$setting["member_disable_auto_position"] = 0;
			$setting["member_default_position"]      = 0;
			$setting["strong_leg_retention"]         = 0;
			$setting["gc_pairing_count"]             = 0;
			$setting["cycle_per_day"]                = 0;
			Tbl_binary_settings::insert($setting);
		}

		$data["settings"]["setup"]                               = Tbl_binary_settings::first();
		$data["settings"]["setup"]->binary_points_left           = Plan::get_label("BINARY_POINTS_LEFT");
		$data["settings"]["setup"]->binary_points_right          = Plan::get_label("BINARY_POINTS_RIGHT");

		$data["settings"]["binary_settings_pair"] 	  			 = [];
		$data["settings"]["binary_settings_pair_end"] 			 = (object)array("binary_pairing_id"=>"","binary_pairing_left"=>"","binary_pairing_right"=>"","binary_pairing_bonus"=>"");
		
		$data["settings"]["label_log"]["binary_points_left"]  = "Left Points";
		$data["settings"]["label_log"]["binary_points_right"] = "Right Points";

		$binary_pairing    = Tbl_binary_pairing::where("archive",0)
											   ->get();
		$array = array();
		foreach($binary_pairing as $bpair)
		{
			array_push($array,$bpair);
		}

		$data["settings"]["binary_settings_pair"]        = $array;
		$data["settings"]["count_binary_settings_pair"]  = count($array);


		$data["settings"]["binary_settings"] 	      = [];
		$get                                          = Tbl_membership::where("archive",0)->get();
		foreach($get as $g)
		{
			foreach($get as $g2)
			{
				$check = Tbl_binary_points_settings::where("membership_id",$g->membership_id)->where("membership_entry_id",$g2->membership_id)->first();
				if($check)
				{
					$data["settings"]["binary_settings"][$g->membership_id][$g2->membership_id] = $check->membership_binary_points; 			
				}
				else
				{
					$data["settings"]["binary_settings"][$g->membership_id][$g2->membership_id] = 0; 			
				}
			}
		}

		return $data;
	}
}
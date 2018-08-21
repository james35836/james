<?php
namespace App\Globals;

use DB;
use App\Models\Tbl_points_log;
use App\Models\Tbl_wallet_log;
use App\Models\Tbl_earning_log;
use App\Models\Tbl_currency;
use App\Models\Tbl_slot;
use App\Models\Tbl_binary_points;
use App\Models\Tbl_stairstep_points;
use App\Models\Tbl_unilevel_points;
use Carbon\Carbon;
class Log
{
	public static function insert_wallet($slot_id,$amount,$plan,$entry,$currency_id = 0)
	{
		if($currency_id == 0)
		{
			$currency_default = Tbl_currency::where("currency_default",1)->first();
			if($currency_default)
			{
				$currency_id = $currency_default->currency_id;
			}
			else
			{
				$currency_id = null;
			}
		}

		$running_balance = Tbl_wallet_log::where("wallet_log_slot_id",$slot_id)->sum("wallet_log_amount");

		$insert["wallet_log_slot_id"]             = $slot_id;
		$insert["wallet_log_amount"]              = $amount;
		$insert["wallet_log_details"]             = $plan;
		$insert["wallet_log_type"]                = $entry;
		$insert["wallet_log_running_balance"]     = $running_balance + $amount;
		$insert["wallet_log_date_created"]        = Carbon::now();
		$insert["currency_id"]                    = $currency_id;

		Tbl_wallet_log::insert($insert);
	}

	public static function insert_earnings($slot_id,$amount,$plan,$entry,$cause_id,$details, $level = 0)
	{
		$cause_info									       = Tbl_slot::where("slot_id",$cause_id)->first();

		$insert_earning["earning_log_slot_id"]             = $slot_id;
		$insert_earning["earning_log_amount"]              = $amount;
		$insert_earning["earning_log_plan_type"]           = $plan;
		$insert_earning["earning_log_entry_type"]          = $entry;
		$insert_earning["earning_log_cause_id"]            = $cause_id;
		$insert_earning["earning_log_cause_membership_id"] = $cause_info->slot_membership;
		$insert_earning["earning_log_cause_level"] 		   = $level;
		$insert_earning["earning_log_date_created"]        = Carbon::now();

		Tbl_earning_log::insert($insert_earning);
	}

	public static function insert_points($slot_id,$amount,$type,$cause_id, $level = 0)
	{
		$cause_info = Tbl_slot::where("slot_id",$cause_id)->first();

		$insert["points_log_slot_id"]				= $slot_id;				
		$insert["points_log_amount"]				= $amount;				
		$insert["points_log_type"]					= $type;
		$insert["points_log_cause_id"]				= $cause_id;
		$insert["points_log_cause_membership_id"]	= $cause_info->slot_membership;
		$insert["points_log_cause_level"]			= $level;
		$insert["points_log_date_created"]			= Carbon::now();

		Tbl_points_log::insert($insert);
	}

	public static function insert_stairstep_points($slot_id,$amount,$type,$cause_id, $level = 0,$override = 0)
	{
		$cause_info = Tbl_slot::where("slot_id",$cause_id)->first();

		$insert["stairstep_points_slot_id"]				= $slot_id;				
		$insert["stairstep_points_amount"]				= $amount;				
		$insert["stairstep_points_type"]				= $type;
		$insert["stairstep_points_cause_id"]			= $cause_id;
		$insert["stairstep_points_cause_membership_id"]	= $cause_info->slot_membership;
		$insert["stairstep_points_cause_level"]			= $level;
		$insert["stairstep_points_date_created"]		= Carbon::now();
		$insert["stairstep_override_points"]		    = $override;

		Tbl_stairstep_points::insert($insert);
	}

	public static function insert_unilevel_points($slot_id,$amount,$type,$cause_id, $level = 0)
	{
		$cause_info = Tbl_slot::where("slot_id",$cause_id)->first();

		$insert["unilevel_points_slot_id"]				= $slot_id;				
		$insert["unilevel_points_amount"]				= $amount;				
		$insert["unilevel_points_type"]				    = $type;
		$insert["unilevel_points_cause_id"]			    = $cause_id;
		$insert["unilevel_points_cause_membership_id"]	= $cause_info->slot_membership;
		$insert["unilevel_points_cause_level"]			= $level;
		$insert["unilevel_points_date_created"]		    = Carbon::now();

		Tbl_unilevel_points::insert($insert);
	}

	public static function insert_binary_points($slot_id,$receive,$old,$new,$cause_id,$log_earnings,$log_flushout,$level,$trigger)
	{
		$cause_info = Tbl_slot::where("slot_id",$cause_id)->first();

		$insert["binary_points_slot_id"]		= $slot_id;					
		$insert["binary_receive_left"]			= $receive["left"];
		$insert["binary_receive_right"]			= $receive["right"];
		$insert["binary_old_left"]				= $old["left"];
		$insert["binary_old_right"]				= $old["right"];
		$insert["binary_new_left"]				= $new["left"];
		$insert["binary_new_right"]				= $new["right"];
		$insert["binary_points_income"]			= $log_flushout;
		$insert["binary_points_flushout"]		= $log_earnings;
		$insert["binary_points_trigger"]		= $trigger;
		$insert["binary_cause_slot_id"]			= $cause_info->slot_id;
		$insert["binary_cause_membership_id"]	= $cause_info->slot_membership;
		$insert["binary_cause_level"]			= $level;
		$insert["binary_points_date_received"]	= Carbon::now();

		Tbl_binary_points::insert($insert);
	}
}
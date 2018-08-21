<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Validator;

use App\Models\Tbl_slot;
use App\Models\Tbl_mlm_plan;

use App\Globals\Mlm_complan_manager;


class MLM
{
	public static function entry($slot_info)
	{

	}

	public static function placement_entry($slot_id)
	{
        $slot_info = Tbl_slot::where('slot_id', $slot_id)->where("slot_sponsor","!=","0")->first();
        if($slot_info)
        {
            // Mlm Computation Plan
            $plan_settings = Tbl_mlm_plan::where('mlm_plan_enable', 1)
                                         ->where('mlm_plan_trigger', 'Slot Placement')
                                         ->get();
            
            if($slot_info->slot_type == 'PS')
            {
                foreach($plan_settings as $key => $value)
                {
                    $plan = strtolower($value->mlm_plan_code);
                    $a = Mlm_complan_manager::$plan($slot_info);
                }
            }
            // End Computation Plan
            
        }
	}

	public static function create_entry($slot_id)
	{
        $slot_info = Tbl_slot::where('slot_id', $slot_id)->first();

        // Mlm Computation Plan
        $plan_settings = Tbl_mlm_plan::where('mlm_plan_enable', 1)
                                     ->where('mlm_plan_trigger', 'Slot Creation')
                                     ->get();

        if($slot_info->slot_type == 'PS')
        {
            foreach($plan_settings as $key => $value)
            {
                $plan = strtolower($value->mlm_plan_code);
                $a = Mlm_complan_manager::$plan($slot_info);
            }
        }
        // End Computation Plan
	}
}
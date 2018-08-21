<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Validator;

use App\Globals\Wallet;
use App\Globals\Log;

use App\Models\Tbl_slot;
use App\Models\Tbl_binary_points;
use App\Models\Tbl_mlm_plan;
use App\Models\Tbl_membership_income;
use App\Models\Tbl_binary_points_settings;
use App\Models\Tbl_tree_placement;
use App\Models\Tbl_tree_sponsor;
use App\Models\Tbl_binary_pairing;
use App\Models\Tbl_membership_indirect_level;


class Mlm_complan_manager
{
	public static function binary($slot_info)
	{
		$tree_placement = Tbl_tree_placement::where("placement_child_id",$slot_info->slot_id)->orderBy("placement_level","ASC")->get();
		foreach($tree_placement as $tree)
		{
			$slot_placement  = Tbl_slot::where("slot_id",$tree->placement_parent_id)->first();
			$points_settings = Tbl_binary_points_settings::where("membership_id",$slot_placement->slot_membership)->where("membership_entry_id",$slot_info->slot_membership)->first();
			if($points_settings)
			{
				$points = $points_settings->membership_binary_points;
			}
			else
			{
				$points = 0;
			}
                    
            $receive["left"]   = 0;
            $receive["right"]  = 0;
            $old["left"]       = $slot_placement->slot_left_points;
            $old["right"]      = $slot_placement->slot_right_points;
            $new["left"]       = $slot_placement->slot_left_points;
            $new["right"]      = $slot_placement->slot_right_points;
            $log_earnings      = 0;
            $log_flushout      = 0;

			if($points != 0)
			{
				$position = strtolower($tree->placement_position);
				if($position == "left" || $position == "right")
				{
                    $receive[$position] = $points;
                    $new[$position]     = $new[$position] + $points;

					$update_string = "slot_".$position."_points";
					$update[$update_string] = Tbl_slot::where("slot_id",$slot_placement->slot_id)->first()->$update_string + $points;
					Tbl_slot::where("slot_id",$slot_placement->slot_id)->update($update);

                    $plan_type = "BINARY_".strtoupper($tree->placement_position);
                    Log::insert_points($slot_placement->slot_id,$points,$plan_type,$slot_info->slot_id, $tree->placement_level);

					$binary["left"]  = Tbl_slot::where("slot_id",$slot_placement->slot_id)->first()->slot_left_points;
					$binary["right"] = Tbl_slot::where("slot_id",$slot_placement->slot_id)->first()->slot_right_points;

					$pairing_settings = Tbl_binary_pairing::where("archive",0)->orderBy("binary_pairing_right","DESC")->orderBy("binary_pairing_left","DESC")->where("binary_pairing_bonus","!=",0)->where("binary_pairing_left","!=",0)->where("binary_pairing_right","!=",0)->get();

					foreach($pairing_settings as $pairing)
					{
		                while($binary["left"] >= $pairing->binary_pairing_left && $binary["right"] >= $pairing->binary_pairing_right)
		                {
		                	/* PAIR THE POINTS */
                            $binary["left"]  = $binary["left"] - $pairing->binary_pairing_left;
                            $binary["right"] = $binary["right"] - $pairing->binary_pairing_right;

                            /* FOR LOGS BINARY PTS RECORD */
                            $new["left"]     = $new["left"] - $pairing->binary_pairing_left; 
                            $new["right"]    = $new["right"] - $pairing->binary_pairing_right;
                            $log_earnings    = $log_earnings + $pairing->binary_pairing_bonus;

                            /* ANOTHER RECORD FOR POINTS LOG */
                            $plan_type = "BINARY_LEFT";
                            Log::insert_points($slot_placement->slot_id,(-1 * $pairing->binary_pairing_left),$plan_type,$slot_info->slot_id, $tree->placement_level);
                           
                            $plan_type = "BINARY_RIGHT";
                            Log::insert_points($slot_placement->slot_id,(-1 * $pairing->binary_pairing_right),$plan_type,$slot_info->slot_id, $tree->placement_level);
                           

                            /* UPDATE POINTS AND WALLET*/
                            $update_slot["slot_left_points"]	= $binary["left"];
                            $update_slot["slot_right_points"]	= $binary["right"];
                            Wallet::update_wallet($slot_placement->slot_id,$pairing->binary_pairing_bonus);

                            Tbl_slot::where("slot_id",$slot_placement->slot_id)->update($update_slot);

                            /*LOGS*/
                            $details = "";
                            Log::insert_wallet($slot_placement->slot_id,$pairing->binary_pairing_bonus,"BINARY","DEBIT");
                            Log::insert_earnings($slot_placement->slot_id,$pairing->binary_pairing_bonus,"BINARY","SLOT PLACEMENT",$slot_info->slot_id,$details,$tree->placement_level);
	
							/* REFRESH GET DATA ON POINTS */  
							$binary["left"]  = Tbl_slot::where("slot_id",$slot_placement->slot_id)->first()->slot_left_points;
							$binary["right"] = Tbl_slot::where("slot_id",$slot_placement->slot_id)->first()->slot_right_points;
		                }
					}




                    Log::insert_binary_points($slot_placement->slot_id,$receive,$old,$new,$slot_info->slot_id,$log_earnings,$log_flushout,$tree->placement_level,"Slot Placement");
				}
			}
		}
	}

	public static function direct($slot_info)
	{
		/* CHECK SPONSOR SLOT*/
        $slot_sponsor = Tbl_slot::where('slot_id', $slot_info->slot_sponsor)->first();
        if($slot_sponsor)
        {
        	/* CHECK INCOME SETTINGS */
        	$membership_income = Tbl_membership_income::where("membership_id",$slot_sponsor->slot_membership)->where("membership_entry_id",$slot_info->slot_membership)->first();
        	if($membership_income)
        	{
        		$direct_income = $membership_income->membership_direct_income;
        	}
        	else
        	{
        		$direct_income = 0;
        	}

        	/* IF DIRECT INCOME IS NOT 0 */
        	if($direct_income != 0)
        	{
                Wallet::update_wallet($slot_sponsor->slot_id,$direct_income);

                /*LOGS*/
                $details = "";
                Log::insert_wallet($slot_sponsor->slot_id,$direct_income,"DIRECT","DEBIT");
                Log::insert_earnings($slot_sponsor->slot_id,$direct_income,"DIRECT","SLOT CREATION",$slot_info->slot_id,$details,1);
        	}
        }
	}

    public static function indirect($slot_info)
    {
        $slot_tree         = Tbl_tree_sponsor::where("sponsor_child_id",$slot_info->slot_id)->where("sponsor_level","!=",1)->orderby("sponsor_level", "asc")->get();
        /* RECORD ALL INTO A SINGLE VARIABLE */
        /* CHECK IF LEVEL EXISTS */
        foreach($slot_tree as $key => $tree)
        {
            /* GET SPONSOR AND GET INDIRECT BONUS INCOME */
            $slot_sponsor   = Tbl_slot::where("slot_id",$tree->sponsor_parent_id)->first();
            $indirect_bonus = Tbl_membership_indirect_level::where("membership_id",$slot_sponsor->slot_membership)->where("membership_entry_id",$slot_info->slot_membership)->where("membership_level",$tree->sponsor_level)->first();
            if($indirect_bonus)
            {
                $indirect_bonus = $indirect_bonus->membership_indirect_income;
            }
            else
            {
                $indirect_bonus = 0;
            }

            /* CHECK IF BONUS IS ZERO */
            if($indirect_bonus != 0)
            {
                /* ADD WALLET ON SLOT */
                Wallet::update_wallet($slot_sponsor->slot_id,$indirect_bonus);

                /*LOGS*/
                $details = "";
                Log::insert_wallet($slot_sponsor->slot_id,$indirect_bonus,"DIRECT","DEBIT");
                Log::insert_earnings($slot_sponsor->slot_id,$indirect_bonus,"INDIRECT","SLOT CREATION",$slot_info->slot_id,$details,$tree->sponsor_level);
            }
        } 
    }

}
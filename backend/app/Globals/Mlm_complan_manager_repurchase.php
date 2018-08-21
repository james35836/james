<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Validator;

use App\Globals\Log;

use App\Models\Tbl_slot;
use App\Models\Tbl_mlm_plan;
use App\Models\Tbl_membership_income;
use App\Models\Tbl_binary_points_settings;
use App\Models\Tbl_tree_placement;
use App\Models\Tbl_tree_sponsor;
use App\Models\Tbl_binary_pairing;
use App\Models\Tbl_membership_unilevel_level;
use App\Models\Tbl_stairstep_settings;
use App\Models\Tbl_stairstep_rank;
use App\Models\Tbl_unileveL_points;
use App\Models\Tbl_stairstep_points;


class Mlm_complan_manager_repurchase
{
	public static function unilevel($slot_info, $points)
	{
        if($points != 0)
        {
            /* ADD POINTS ON SLOT */
            $update_slot_child["slot_personal_pv"] = Tbl_slot::where("slot_id",$slot_info->slot_id)->first()->slot_personal_pv + $points;
            Tbl_slot::where("slot_id",$slot_info->slot_id)->update($update_slot_child);

            Log::insert_points($slot_info->slot_id,$points,"UNILEVEL_PPV",$slot_info->slot_id, 0);
            Log::insert_unilevel_points($slot_info->slot_id,$points,"UNILEVEL_PPV",$slot_info->slot_id, 0);

            $slot_tree         = Tbl_tree_sponsor::where("sponsor_child_id",$slot_info->slot_id)->orderby("sponsor_level", "asc")->get();
            foreach($slot_tree as $key => $tree)
            {
                /* GET SPONSOR AND GET UNILEVEL BONUS INCOME PERCENTAGE  */
                $slot_sponsor   = Tbl_slot::where("slot_id",$tree->sponsor_parent_id)->first();
                $unilevel_percentage = Tbl_membership_unilevel_level::where("membership_id",$slot_sponsor->slot_membership)->where("membership_entry_id",$slot_info->slot_membership)->where("membership_level",$tree->sponsor_level)->first();
                if($unilevel_percentage)
                {
                    $unilevel_pts = $unilevel_percentage->membership_percentage;
                }
                else
                {
                    $unilevel_pts = 0;
                }

                /* CHECK IF BONUS IS ZERO */
                if($unilevel_pts != 0)
                {
                    /* ADD POINTS ON SLOT */
                    $update_slot_parent["slot_group_pv"] = Tbl_slot::where("slot_id",$slot_sponsor->slot_id)->first()->slot_group_pv + $unilevel_pts;
                    Tbl_slot::where("slot_id",$slot_sponsor->slot_id)->update($update_slot_parent);

                    Log::insert_points($slot_sponsor->slot_id,$points,"UNILEVEL_GPV",$slot_info->slot_id, $tree->sponsor_level);
                    Log::insert_unilevel_points($slot_sponsor->slot_id,$points,"UNILEVEL_GPV",$slot_info->slot_id, $tree->sponsor_level);
                }
            } 
        }
	}

    public static function stairstep($slot_info,$points)
    {
        $settings                    = Tbl_stairstep_settings::first();
        $override_percentage         = 0;
        $compare_override_percentage = 0;

        if($settings)
        {
                $cause_slot                       = Tbl_slot::where("slot_id",$slot_info->slot_id)->first();
                $update_self["slot_personal_spv"] = $cause_slot->slot_personal_spv + $points;
                Tbl_slot::where("slot_id",$cause_slot->slot_id)->update($update_self);
                Log::insert_points($cause_slot->slot_id,$points,"STAIRSTEP_PPV",$cause_slot->slot_id, 0);
                Log::insert_stairstep_points($cause_slot->slot_id,$points,"STAIRSTEP_PPV",$cause_slot->slot_id, 0);

                $cause_current_rank_info = Tbl_stairstep_rank::where("stairstep_rank_id",$cause_slot->slot_stairstep_rank)->first();
                if($cause_current_rank_info)
                {
                    $override_percentage = $cause_current_rank_info->stairstep_rank_override;
                }
                else
                {
                    $override_percentage = 0;
                }

                /* CHECK UPPER SPONSOR IF THEY WILL RANK UP OR NOT */
                $slot_tree = Tbl_tree_sponsor::where("sponsor_child_id",$slot_info->slot_id)->orderby("sponsor_level", "asc")->get();
                foreach($slot_tree as $key => $tree)
                {
                    $parent_slot                     = Tbl_slot::where("slot_id",$tree->sponsor_parent_id)->first();
                    $update_parent["slot_group_spv"] = $parent_slot->slot_group_spv + $points;
                    Tbl_slot::where("slot_id",$parent_slot->slot_id)->update($update_parent);
                    Log::insert_points($parent_slot->slot_id,$points,"STAIRSTEP_GPV",$cause_slot->slot_id, $tree->sponsor_level);

                    
                    /* GET OVERRIDE PERCENTAGE BY RANK*/
                    $current_rank_info = Tbl_stairstep_rank::where("stairstep_rank_id",$parent_slot->slot_stairstep_rank)->first();
                    if($current_rank_info)
                    {
                        $compare_override_percentage = $current_rank_info->stairstep_rank_override;
                    }

                    $override_given = 0;

                    /* COMPUTE OVERRIDE POINTS */
                    if($compare_override_percentage > $override_percentage)
                    {
                        $compute_override    = (($compare_override_percentage - $override_percentage)/100) * ($points);
                        $update_override["slot_override_points"] = Tbl_slot::where("slot_id",$tree->sponsor_parent_id)->first()->slot_override_points + $compute_override;
                        Tbl_slot::where("slot_id",$parent_slot->slot_id)->update($update_override);
                        Log::insert_points($parent_slot->slot_id,$compute_override,"OVERRIDE_POINTS",$cause_slot->slot_id, $tree->sponsor_level);
                        // Log::insert_stairstep_points($parent_slot->slot_id,$compute_override,"OVERRIDE_POINTS",$cause_slot->slot_id, $tree->sponsor_level);
                       

                        $override_given      = $compute_override;
                        $override_percentage = $compare_override_percentage;
                    }

                    Log::insert_stairstep_points($parent_slot->slot_id,$points,"STAIRSTEP_GPV",$cause_slot->slot_id, $tree->sponsor_level, $override_given);

                    /* PROCEED TO HERE IF LIVE UPDATE IS ON*/
                    if($settings->live_update == 1)
                    {
                        $slot_sponsor       = Tbl_slot::where("slot_id",$tree->sponsor_parent_id)->first();
                        $current_rank_level = Tbl_stairstep_rank::where("stairstep_rank_id",$slot_sponsor->slot_stairstep_rank)->first() ? Tbl_stairstep_rank::where("stairstep_rank_id",$slot_sponsor->slot_stairstep_rank)->first()->stairstep_rank_level : 0;
                        $get_rank           = Tbl_stairstep_rank::where("archive",0)->where("stairstep_rank_level",">",$current_rank_level)->orderBy("stairstep_rank_level","ASC")->get();

                        foreach($get_rank as $srank)
                        {
                            $rank_personal     = $srank->stairstep_rank_personal;
                            $rank_group        = $srank->stairstep_rank_group;
                            $rank_personal_all = $srank->stairstep_rank_personal_all;
                            $rank_group_all    = $srank->stairstep_rank_group_all;

                            $all_personal          = $slot_sponsor->slot_personal_spv;
                            $all_group             = $slot_sponsor->slot_group_spv;

                            /* DATE RANGE NALANG ANG KULANG*/
                            $monthly_personal      = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot_sponsor->slot_id)->where("stairstep_points_type","STAIRSTEP_PPV")->sum("stairstep_points_amount");
                            $monthly_group         = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot_sponsor->slot_id)->where("stairstep_points_type","STAIRSTEP_GPV")->sum("stairstep_points_amount");
                            
                            
                        } 
                    }
                }     
        }
    }

}
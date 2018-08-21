<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use DB;

use App\Models\Tbl_slot;
use App\Models\Tbl_earning_log;
use App\Models\Tbl_wallet_log;
use App\Models\Tbl_points_log;
use App\Models\Tbl_mlm_unilevel_settings;
use App\Models\Tbl_membership;
use App\Models\Tbl_binary_points;
use App\Models\Tbl_unilevel_points;
use App\Models\Tbl_stairstep_points;
use App\Models\Tbl_stairstep_rank;
use Carbon\Carbon;

class MemberEarningController extends Controller
{
    function __construct()
    {

    }

    public function user_data()
    {
    	return Request::user();
    }

    public function direct_earning()
    {
        $slot                    = Tbl_slot::where("slot_owner",Request::user()->id)->first();
        $data                    = null;
        $running_balance         = 0;

        if($slot)
        {
            $log = Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)
                                    ->leftJoin("tbl_slot","tbl_slot.slot_id","=","tbl_earning_log.earning_log_slot_id")
                                    ->leftJoin("users","users.id","=","tbl_slot.slot_owner")
                                    ->leftJoin("tbl_membership","tbl_membership.membership_id","=","tbl_slot.slot_membership")
                                    ->where("earning_log_plan_type","DIRECT")
                                    ->leftJoin("tbl_label","tbl_label.plan_code","=","tbl_earning_log.earning_log_plan_type")
                                    ->select("*",DB::raw("DATE_FORMAT(tbl_earning_log.earning_log_date_created, '%m/%d/%Y') as earning_log_date_created"),
                                                 DB::raw("DATE_FORMAT(tbl_earning_log.earning_log_date_created, '%h:%i %p') as earning_log_time_created"))
                                    ->get();

            $data["log"] = $log;     
            $running_balance = Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)->where("earning_log_plan_type","DIRECT")->sum("earning_log_amount");                 
        }

        $data["total"]  = number_format($running_balance,2); 
            
        return json_encode($data);
    }

    public function indirect_earning()
    {
        $slot                    = Tbl_slot::where("slot_owner",Request::user()->id)->first();
        $data                    = null;
        $running_balance         = 0;
        $log                     = null;
        $ctr                     = 0;
        $points_left             = 0;
        $points_right            = 0;
        if($slot)
        {
            $membership                            = Tbl_membership::where("membership_id",$slot->slot_membership)->first();
            if($membership)
            {
                $membership->membership_indirect_level = $membership->membership_indirect_level + 1;
                $level                                 = 2;

                while($membership->membership_indirect_level >= $level)
                {
                    $log[$ctr]["level_name"]         = $this->ordinal($level);
                    $log[$ctr]["number_of_slots"]    = Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)->where("earning_log_plan_type","INDIRECT")->where("earning_log_cause_level",$level)->count() ? Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)->where("earning_log_cause_level",$level)->where("earning_log_plan_type","INDIRECT")->count()." Slot(s)" : "No Slots";
                    $log[$ctr]["last_slot_creation"] = Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)->where("earning_log_plan_type","INDIRECT")->first() ? Carbon::parse(Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)->orderBy("earning_log_date_created","DESC")->where("earning_log_plan_type","INDIRECT")->first()->earning_log_date_created)->format("m/d/Y") : "---";
                    $log[$ctr]["earnings"]           = number_format(Tbl_earning_log::where("earning_log_slot_id",$slot->slot_id)->where("earning_log_plan_type","INDIRECT")->where("earning_log_cause_level",$level)->sum("earning_log_amount"),2);

                    $running_balance = $running_balance + $log[$ctr]["earnings"]; 
                    $ctr++;
                    $level++;
                }    
            }
        }

        $data["log"]    = $log; 
        $data["total"]  = number_format($running_balance,2); 
            
        return json_encode($data);
    }

    public function binary_earning()
    {
        $slot                    = Tbl_slot::where("slot_owner",Request::user()->id)->first();
        $log                     = null;
        $running_balance         = 0;
        $points_left             = 0;
        $points_right            = 0;
        if($slot)
        {
            $log = Tbl_binary_points::where("binary_points_slot_id",$slot->slot_id)
                                    ->leftJoin("tbl_slot as cause_slot","cause_slot.slot_id","=","tbl_binary_points.binary_cause_slot_id")
                                    ->select("tbl_binary_points.*","cause_slot.slot_no as cause_no",
                                                 DB::raw("DATE_FORMAT(tbl_binary_points.binary_points_date_received, '%m/%d/%Y') as binary_log_date_created"),
                                                 DB::raw("DATE_FORMAT(tbl_binary_points.binary_points_date_received, '%h:%i %p') as binary_log_time_created"))
                                    ->get(); 


            $running_balance = Tbl_binary_points::where("binary_points_slot_id",$slot->slot_id)->sum("binary_points_income");                                             
        }

        $data["log"]           = $log;
        $data["total_running"] = $running_balance;    
        return json_encode($data);
    }

    public function unilevel()
    {
        $slot                    = Tbl_slot::where("slot_owner",Request::user()->id)->first();
        $data                    = null;
        $total_ppv               = 0;
        $total_gpv               = 0;
        $required_ppv            = 0;
        $log                     = null;
        $ctr                     = 0;

        if($slot)
        {
            $membership                            = Tbl_membership::where("membership_id",$slot->slot_membership)->first();
            if($membership)
            {
                $membership->membership_unilevel_level = $membership->membership_unilevel_level;
                $level                                 = 1;


                $required_ppv                    = $membership->membership_required_pv;
                $first_date                      = Carbon::now()->startOfMonth();
                $end_date                        = Carbon::now()->endOfMonth();

                $log[$ctr]["level_name"]         = "Personal Purchase";
                $log[$ctr]["number_of_slots"]    = Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_date_created",">=",$first_date)->where("unilevel_points_date_created","<=",$end_date)->where("unilevel_points_type","UNILEVEL_PPV")->count() ? Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_date_created",">=",$first_date)->where("unilevel_points_date_created","<=",$end_date)->where("unilevel_points_type","UNILEVEL_PPV")->count()." Purchase(s)" : "No Purchase";
                $log[$ctr]["last_slot_creation"] = Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_type","UNILEVEL_PPV")->first() ? Carbon::parse(Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_type","UNILEVEL_PPV")->orderBy("unilevel_points_date_created","DESC")->first()->unilevel_points_date_created)->format("m/d/Y") : "---";
                $log[$ctr]["earnings"]           = Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_type","UNILEVEL_PPV")->where("unilevel_points_date_created",">=",$first_date)->where("unilevel_points_date_created","<=",$end_date)->sum("unilevel_points_amount");

                $total_ppv = $total_ppv + $log[$ctr]["earnings"];

                $log[$ctr]["earnings"] = number_format($log[$ctr]["earnings"],2);

                $ctr++;

                while($membership->membership_unilevel_level >= $level)
                {
                    $log[$ctr]["level_name"]         = $this->ordinal($level);
                    $log[$ctr]["number_of_slots"]    = Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_date_created",">=",$first_date)->where("unilevel_points_date_created","<=",$end_date)->where("unilevel_points_cause_level",$level)->where("unilevel_points_type","UNILEVEL_GPV")->count() ? Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_cause_level",$level)->where("unilevel_points_date_created",">=",$first_date)->where("unilevel_points_date_created","<=",$end_date)->where("unilevel_points_type","UNILEVEL_GPV")->count()." Purchase(s)" : "No Purchase";
                    $log[$ctr]["last_slot_creation"] = Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_cause_level",$level)->where("unilevel_points_type","UNILEVEL_GPV")->first() ? Carbon::parse(Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_cause_level",$level)->where("unilevel_points_type","UNILEVEL_GPV")->orderBy("unilevel_points_date_created","DESC")->first()->unilevel_points_date_created)->format("m/d/Y") : "---";
                    $log[$ctr]["earnings"]           = Tbl_unilevel_points::where("unilevel_points_slot_id",$slot->slot_id)->where("unilevel_points_cause_level",$level)->where("unilevel_points_date_created",">=",$first_date)->where("unilevel_points_date_created","<=",$end_date)->where("unilevel_points_type","UNILEVEL_GPV")->sum("unilevel_points_amount");
                    
                    $total_gpv = $total_gpv + $log[$ctr]["earnings"]; 

                    $log[$ctr]["earnings"] = number_format($log[$ctr]["earnings"],2);

                    $ctr++;
                    $level++;
                }    
            }
        }

        $data["log"]           = $log; 
        $data["total_ppv"]     = number_format($total_ppv,2); 
        $data["total_gpv"]     = number_format($total_gpv,2); 
        $data["required_ppv"]  = number_format($required_ppv,2); 
        $data["passed"]        = $total_ppv >= $required_ppv ? 1 : 0; 
    
        return json_encode($data);
    }

    public function stairstep()
    {
        $slot                    = Tbl_slot::where("slot_owner",Request::user()->id)->first();
        $data                    = null;
        $log                     = null;
        $required_ppv            = 0;
        $total_override_points   = 0;
        $total_all_personal_gpv  = 0;
        $total_all_personal_ppv  = 0;
        $rank_level              = 0;
        if($slot)
        {
            $first_date          = Carbon::now()->startOfMonth();
            $end_date            = Carbon::now()->endOfMonth();

            $log = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot->slot_id)
                                    ->leftJoin("tbl_slot","tbl_slot.slot_id","=","tbl_stairstep_points.stairstep_points_cause_id")
                                    ->leftJoin("users","users.id","=","tbl_slot.slot_owner")
                                    ->where("stairstep_points_type","STAIRSTEP_GPV")
                                    ->where("stairstep_points_date_created",">=",$first_date)
                                    ->where("stairstep_points_date_created","<=",$end_date)
                                    ->select("*",DB::raw("DATE_FORMAT(tbl_stairstep_points.stairstep_points_date_created, '%m/%d/%Y') as stairstep_points_date_created"))
                                    ->get();
   
            $total_override_points     = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot->slot_id)->where("stairstep_points_type","STAIRSTEP_GPV")->where("stairstep_points_date_created",">=",$first_date)->where("stairstep_points_date_created","<=",$end_date)->sum("stairstep_override_points");
            $total_personal_pv         = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot->slot_id)->where("stairstep_points_type","STAIRSTEP_PPV")->where("stairstep_points_date_created",">=",$first_date)->where("stairstep_points_date_created","<=",$end_date)->sum("stairstep_points_amount");
            
            $total_all_personal_gpv    = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot->slot_id)->sum("stairstep_points_amount");
            $total_all_personal_ppv    = Tbl_stairstep_points::where("stairstep_points_slot_id",$slot->slot_id)->sum("stairstep_points_amount");

            $get_rank = Tbl_stairstep_rank::where("stairstep_rank_id",$slot->slot_stairstep_rank)->first();
            if($get_rank)
            {
                $required_ppv = $get_rank->stairstep_rank_personal;
                $rank_level   = $get_rank->stairstep_rank_level;
            }
        }

        $data["log"]                     = $log;  
        $data["total_override_points"]   = number_format($total_override_points,2); 
        $data["total_personal_pv"]       = number_format($total_personal_pv,2); 
        $data["required_ppv"]            = number_format($required_ppv,2); 

        $data["total_all_personal_gpv"]  = number_format($total_all_personal_gpv); 
        $data["total_all_personal_ppv"]  = number_format($total_all_personal_ppv); 
        $data["passed"]                  = $total_personal_pv >= $required_ppv ? 1 : 0;

        $all_rank = Tbl_stairstep_rank::where("archive",0)->get();

        foreach($all_rank as $key => $rnk)
        {
            $all_rank[$key]->all_ppv_percentage = ($total_all_personal_ppv >= $rnk->stairstep_rank_personal_all) ? "Qualified (100%)" : $total_all_personal_ppv." of ".$rnk->stairstep_rank_personal_all." (".(($total_all_personal_ppv/$rnk->stairstep_rank_personal_all) * 100)."%)"; 
            $all_rank[$key]->all_gpv_percentage = ($total_all_personal_gpv >= $rnk->stairstep_rank_group_all) ? "Qualified (100%)" : $total_all_personal_gpv." of ".$rnk->stairstep_rank_group_all." (".(($total_all_personal_gpv/$rnk->stairstep_rank_group_all) * 100)."%)";
            $all_rank[$key]->qualified          = $rank_level > $rnk->stairstep_rank_level ? 1 : 0;

            if($all_rank[$key]->qualified == 0)
            {
                if($all_rank[$key]->all_ppv_percentage == "Qualified (100%)" && $all_rank[$key]->all_gpv_percentage == "Qualified (100%)")
                {
                    $all_rank[$key]->qualified = 1;
                }
            }
        }   

        $data["all_rank"]     = $all_rank;
        $data["current_rank"] = isset($get_rank) ? $get_rank->stairstep_rank_name : 0;

        return json_encode($data);

    }

    function ordinal($number) 
    {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }

}
<?php
namespace App\Http\Controllers\Member;

use App\Globals\Code;
use App\Globals\Item;
use App\Globals\Slot;

use App\Models\Tbl_slot;

use Request;

class MemberSlotController extends MemberController
{
    public function get_unplaced_slot()
    {
        $slots = Slot::get_unplaced_slot(Request::user()->id);

        return json_encode($slots);
    }

    public function get_unplaced_downline_slot()
    {
    	  $slots = Slot::get_unplaced_downline_slot(Request::user()->id,Request::input("slot_id"));

        return json_encode($slots);
    }


    public function place_own_slot()
    {

       $data["slot_placement"]  = Request::input("placement");
       $data["slot_position"]   = Request::input("position");
       $data["slot_code"]       = Request::input("slot_no");


        $response              = Slot::place_slot($data,"member_owned",Request::user()->id);
        return $response;
    }

    public function place_downline_slot()
    {

       $data["slot_placement"]  = Request::input("placement");
       $data["slot_position"]   = Request::input("position");
       $data["slot_code"]       = Request::input("slot_no");


        $response              = Slot::place_slot($data,"member_downline",Request::user()->id);
        return $response;
    }

}
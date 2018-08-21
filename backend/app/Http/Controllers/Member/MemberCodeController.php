<?php
namespace App\Http\Controllers\Member;

use App\Globals\Code;
use App\Globals\Item;

use Request;

class MemberCodeController extends MemberController
{
    public function get_member_codes()
    {
    	$user_id 	= Request::input('user_id');
    	$response 	= Code::get_member_codes($user_id);

    	return response()->json($response);
    }

    public function get_claim_codes()
    {
    	$slot_id 	= Request::input('slot_id');
    	$response 	= Code::get_claim_codes($slot_id);

    	return response()->json($response);
    }
}
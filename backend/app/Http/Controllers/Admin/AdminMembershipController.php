<?php
namespace App\Http\Controllers\Admin;
use App\Globals\Membership;

use Request;
class AdminMembershipController extends AdminController
{
    public function get() 
	{
		$response = Membership::get();
	    return response()->json($response, 200);
	}

	public function submit()
	{
		$response = Membership::submit(Request::input());
		return response()->json($response, $response["status_code"]);
	}
}

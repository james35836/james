<?php
namespace App\Http\Controllers\Admin;

use App\Globals\Payout;

use Request;

class AdminPayoutController extends AdminController
{
    public function charge_settings()
    {
    	$response = Payout::add_settings(Request::input());

    	return response()->json($response);
    }

    public function get_charge_settings()
    {
    	$response = Payout::get_settings();

    	return response()->json($response);
    }

    public function payout_configuration()
    {
    	$response = Payout::payout_configuration(Request::input());

    	return response()->json($response);
    }
}

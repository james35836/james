<?php

namespace App\Http\Controllers;

use App\Globals\Country;
use App\Globals\Member;
use Carbon\Carbon;
use Request;
class RegisterController extends Controller
{
    public function get_country()
    {
        $response = Country::get();
        return response()->json($response, 200);
    }

    public function new_register()
    {
        $register_area = Request::input("register_platform") == "system" ? "register_area" : "social";
		$response = Member::add_member(request()->all(),$register_area);
		return $response;
    }

    public function check_credentials()
    {
        $response = Member::check_credentials(Request::input('member'));
        return json_encode($response);
    }
}
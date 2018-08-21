<?php
namespace App\Http\Controllers\Admin;
use App\Globals\Plan;

use Request;
class AdminPlanController extends AdminController
{
    public function get() 
	{
		$plan     = Request::input("plan");
		$response = Plan::get($plan);
	    return response()->json($response, 200);
	}

    public function update() 
	{
		$plan     = Request::input("plan");
		$label    = Request::input("label");
		$data     = Request::input("data");
		$response = Plan::update($plan,$label,$data);
	    return response()->json($response, 200);
	}

    public function update_status() 
	{
		$plan     = Request::input("plan");
		$send     = Request::input("send");
		$response = Plan::update_status($plan,$send);
	    return response()->json($response, 200);
	}
}

<?php
namespace App\Http\Controllers\Admin;
use App\Globals\Slot;
use App\Globals\Member;
use Crypt;

use Request;
class AdminMemberController extends AdminController
{
    public function get() 
	{
		$response = Member::get("member");
	    return response()->json($response, 200);
	}

	public function add()
	{
		$response = Member::add_member(request()->all());
		return $response;
	}

	public function add_slot()
	{
		$response = Slot::create_slot(request()->all());
		return $response;
	}

	public function place_slot()
	{
		$response = Slot::place_slot(request()->all());
		return $response;	
	}

	public function get_slot_information()
	{
		$response = Slot::get_slot_information(Request::input('id'));

		if ($response->crypt) 
		{
			try 
			{
				$response->show_password = Crypt::decrypt($response->crypt);
			} 
			catch (\Exception $e) 
			{
				$response->show_password = "";
			}
		}
		else
		{
			$response->show_password = "";
		}

		return response()->json($response, 200);	
	}

	public function submit_slot_information()
	{
		$response = Slot::submit_slot_information(Request::input());
		return response()->json($response, $response["status_code"]);	
	}

	public function get_slot_details()
	{
		$response = Slot::get_slot_details(Request::input('id'));
		return response()->json($response, 200);	
	}

	public function get_slot_earnings()
	{
		$response = Slot::get_slot_earnings(Request::input(), 10)->toArray();
		$response["total_earning"] = Slot::get_slot_total_earnings(Request::input("id"));
		return response()->json($response, 200);	
	}

	public function get_slot_distributed()
	{
		$response = Slot::get_slot_distributed(Request::input(), 10)->toArray();
		$response["total_distributed"] = Slot::get_slot_total_distributed(Request::input("id"));
		return response()->json($response, 200);	
	}

	public function get_slot_wallet()
	{
		$response = Slot::get_slot_wallet(Request::input(), 10)->toArray();
		$response["total_wallet"] = Slot::get_slot_total_wallet(Request::input("id"));
		return response()->json($response, 200);	
	}

	public function get_slot_payout()
	{
		$response = Slot::get_slot_payout(Request::input(), 10)->toArray();
		$response["total_payout"] = Slot::get_slot_total_payout(Request::input("id"));
		return response()->json($response, 200);	
	}

	public function get_slot_points()
	{
		$response = Slot::get_slot_points(Request::input(), 10)->toArray();
		$response["total_points"] = Slot::get_slot_total_points(Request::input("id"));
		return response()->json($response, 200);	
	}

	public function get_slot_network()
	{
		$response = Slot::get_slot_network(Request::input(), 10)->toArray();
		return response()->json($response, 200);	
	}

	public function get_slot_codevault()
	{
		$response = Slot::get_slot_codevault(Request::input(), 10)->toArray();
		return response()->json($response, 200);	
	}
}

<?php
namespace App\Http\Controllers\Admin;

use App\Globals\Slot;

use Request;

class AdminSlotController extends AdminController
{
	public function get()
	{	
		$response = Slot::get();
		return response()->json($response, 200);
	}

	public function get_full()
	{	
		$response = Slot::get_full(Request::input(), 10);
		return response()->json($response, 200);
	}

	public function get_unplaced()
	{	
		$response = Slot::get_unplaced();
		return response()->json($response, 200);
	}
}

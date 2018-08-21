<?php
namespace App\Http\Controllers\Admin;

use App\Globals\Code;
use App\Globals\Item;

use Request;

class AdminCodeController extends AdminController
{
    public function generate_codes() 
	{
		$branch_id			= Request::input('branch_id');
		$item_id			= Request::input('item_id');
		$quantity			= Request::input('quantity');
	    $response 			= Code::generate($branch_id,$item_id,$quantity);

	    $update_inventory 	= Item::update_inventory($branch_id,$item_id,$quantity);

	    return response()->json($response);
	}

	public function get_codes() 
	{
		$branch_id 			= Request::input('branch_id');
		$filter 			= Request::input('filter');
		$item_id			= Request::input('item_id');
	    $response = Code::get($branch_id,$filter,$item_id);

	    return response()->json($response);
	}

	public function delete_code() 
	{
		$code_id 			= Request::input('code_id');
	    $response 			= Code::delete($code_id);

	    return response()->json($response);
	}

	public function get_random_code()
	{
		$response 		= Code::get_random();

		return response()->json($response);
	}
	
}

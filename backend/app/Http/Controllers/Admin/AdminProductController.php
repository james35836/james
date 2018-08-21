<?php
namespace App\Http\Controllers\Admin;

use App\Globals\Item;
use App\Globals\Code;

use Request;

class AdminProductController extends AdminController
{
    public function add() 
	{
	    $response = Item::add(Request::input());
	    return response()->json($response, $response["status_code"]);
	}

	public function edit()
	{

		$response = Item::edit(Request::input());
		return response()->json($response, $response["status_code"]);
	}

	public function get()
	{	
		$response = Item::get_item(Request::input(), 15);
		return response()->json($response, 200);
	}

	public function data()
	{
		$response = Item::get_data(Request::input("id"));
		return response()->json($response, 200);
	}

	public function archive()
	{
		$response = Item::archive(Request::input("id"));
		return response()->json($response, 200);
	}

	public function restock() 
	{
	    $response = Item::restock(Request::input());
	    return response()->json($response, $response["status_code"]);
	}

	public function get_inventory() 
	{
	    $response = Item::get_inventory(Request::input());
	    return response()->json($response, 200);
	}

	public function get_item_inventory()
	{
		$response = Item::get_item_inventory(Request::input("id"));
		return response()->json($response, 200);
	}

	public function get_item_code()
	{
		$response = Code::get(Request::input("branch_id"), Request::input(), Request::input("item_id"), 5);
		return response()->json($response);
	}
}

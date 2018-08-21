<?php
namespace App\Http\Controllers\Admin;

use App\Globals\Cashier;

use Request;
use Hash;
class AdminCashierController extends AdminController
{
    public function add_cashier() 
	{
	    $response = Cashier::add(Request::input());

	    return response()->json($response);
	}

	public function get_cashierList()
	{
		$branch_id = Request::input('id');
		$filter = Request::input('filter');

		$response = Cashier::getList($branch_id, $filter);

		return response()->json($response, 200);
	}

	public function edit_cashier()
	{
		$response = Cashier::get_data(Request::input("id"));
		return response()->json($response, 200);
	}
	public function edit_cashier_submit()
	{
		$response = Cashier::cashier_update(Request::input());
		return response()->json($response);
	}

	public function archive()
	{
		$response = Cashier::archive(Request::input("id"));
		return response()->json($response, 200);
	}

	public function edit()
	{
		$response = Cashier::edit(Request::input());
		return response()->json($response,200);
	}

	public function search()
	{	
		$response = Cashier::search(Request::input());
		return response()->json($response, 200);
	}

	public function add_location()
	{
		$response = Cashier::add_location(Request::input());

		return response()->json($response);
	}

	public function get_location()
	{
		$response = Cashier::get_location();
		return response()->json($response, 200);
	}

	public function archive_location()
	{
		$data = Request::input('location');
		$response = Cashier::archive_location($data);

		return response()->json($response);
	}

	
}

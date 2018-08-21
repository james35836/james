<?php
namespace App\Http\Controllers\Admin;

use App\Globals\Branch;

use Request;

class AdminBranchController extends AdminController
{
    public function add_branch() 
	{
	    $response = Branch::add(Request::input());

	    return response()->json($response, $response["status_code"]);
	}

	public function get_branch()
	{
		$response = Branch::get();

		return response()->json($response, 200);
	}

	public function data()
	{
		$response = Branch::get_data(Request::input("id"));
		return response()->json($response, 200);
	}

	public function archive()
	{
		$response = Branch::archive(Request::input("id"));
		return response()->json($response, 200);
	}

	public function edit()
	{
		$response = Branch::edit(Request::input());
		return response()->json($response);
	}

	public function search()
	{	
		$response = Branch::search(Request::input());
		return response()->json($response, 200);
	}

	public function get_stockist()
	{	
		$response = Branch::get_stockist();
		return response()->json($response, 200);
	}

	public function add_stockist_level()
	{	
		$data 		= Request::input('stockist');
		$response = Branch::add_stockist($data);
		return response()->json($response);
	}

	public function archive_stockist_level()
	{
		
		$data 		= Request::input('level_name');
		$response 	= Branch::archive_stockist($data);

		return response()->json($response);
	}
}

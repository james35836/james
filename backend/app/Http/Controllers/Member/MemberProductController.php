<?php
namespace App\Http\Controllers\Member;

use App\Globals\Code;
use App\Globals\Item;
use App\Globals\Cashier;
use App\Globals\Branch;

use Request;

class MemberProductController extends MemberController
{
    public function get_all_products()
    {
    	$response = Item::get_all_products();
    	return response()->json($response);
    }

    public function get_product()
    {
    	$response = Item::get_data(Request::input('item_id'));
    	return response()->json($response);
    }

    public function get_cart_items()
    {
    	$response = Item::get_cart(Request::input());

        return response()->json($response);
    }

    public function checkout()
    {
        $response = Cashier::ecom_checkout(Request::input());

        return response()->json($response);
    }

    public function get_branch()
    {
        $response = Branch::get();

        return response()->json($response);
    }
}
<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use File;
use Storage;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

use App\Globals\Membership;
use App\Globals\Item;
use App\Globals\Slot;
use App\Globals\Wallet;
use App\Models\Tbl_slot;

use Illuminate\Http\Request as Request2;

class AdminController extends Controller
{
    function __construct()
    {

    }

    public function user_data()
    {
        if(isset(Request::user()->id))
        {
        	if(Request::user()->type == "member")
    		{	
              $check_has_slot = Tbl_slot::where("slot_owner",Request::user()->id)->first();
              if(!$check_has_slot)
              {
                  Slot::create_blank_slot(Request::user()->id);
              }
    		}	
        }
    	return Request::user();
    }

    public function get_membership()
	{
		$membership = Membership::get();

		return response()->json($membership, 200);
	}

	public function get_product()
	{
		$item = Item::get_product();

		return response()->json($item, 200);
	}

  
}
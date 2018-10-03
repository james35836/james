<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use DB;
use App\Models\Users;
use App\Models\Tbl_connection;
use Carbon\Carbon;
class MemberDirectoryController extends Controller
{
    

    public function get_member()
    {
        $response = Users::UserInfo()->get();

    	return response()->json($response);
    }

    public function check_status()
    {
    	$connection_status = Tbl_connection::where('connection_of',Request::input('id'))->where('connection_by',Request::user()->id)->value('connection_status');
    	
    	$return = $connection_status == null ? 0 : $connection_status;

    	return response()->json($return);
    }

    public function connect_submit()
    {

    	if(Request::input('status')=="connect")
    	{
    		$insert['connection_of'] 		= Request::input('user_id');
	    	$insert['connection_by'] 		= Request::user()->id;
	    	$insert['connection_date'] 		= Carbon::now();
	    	$insert['connection_status'] 	= 1;

            $connection = Tbl_connection::where('connection_of',Request::input('user_id'))->where('connection_by',Request::user()->id)->first();
            if(count($connection)==0)
            {
                Tbl_connection::insert($insert);
            }
            
    	}
    	else
    	{
    		Tbl_connection::where('connection_of',Request::input('user_id'))->where('connection_by',Request::user()->id)->delete();
    	}
    	

    	$return["status"]         = "success"; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Confirm";

		return response()->json($return);

    }
}
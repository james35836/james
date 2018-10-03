<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Request;
use App\Models\Users;
use App\Models\Tbl_message;
use App\Models\Tbl_connection;
use Carbon\Carbon;

use App\Globals\Messages;

class MemberMessageController extends Controller
{
    public function load_message()
    {
        $reference = Request::input('ref');

        if($reference=="BATCH")
        {
            $response = Messages::BATCH();
        }
        else if($reference=="GENERAL")
        {
            $response = Messages::GENERAL();
        }
        else if($reference=="CONNECTION")
        {
            $response = Messages::CONNECTION();
        }
        

        return response()->json($response, 200);
        
    }

    public function send_message()
    {
        $response = Messages::submit(Request::all());
        return response()->json($response, 200);
    }
	public function get_all_messages()
    {
        $group = Messages::get_group_messages();

        return response()->json($group, 200);
    }
    public function get_messages()
    {
    	return Tbl_message::orderBy('message_date','ASC')->get();
    }
    public function create_submit()
    {
    	$insert['message'] 		= $data['message'];
		$insert['message_date'] = Carbon::now();
		$insert['sender_id'] 	= Request::user()->id;
		$insert['receiver_id'] 	= 1;
		$insert['group_id'] 	= 1;
	   



		Tbl_message::insert($insert);
		return "success";
    }

    public function get_connections()
    {
        $connection_list  = Messages::CONNECTION();
    	return response()->json($connection_list, 200);
    }




    

	

  
}
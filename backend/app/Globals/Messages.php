<?php
namespace App\Globals;
use App\Models\Users;
use DB;
use Carbon\Carbon;
use Validator;
use Crypt;
use Hash;
use Request;

use App\Models\Tbl_message;

class Messages
{	
	public static function get()
	{
		return Tbl_message::orderBy('message_date','ASC')->get();
	} 
	public static function submit($data)
	{
		$insert['message'] 		= $data['message'];
		$insert['message_date'] = Carbon::now();
		$insert['sender_id'] 	= Request::user()->id;
		$insert['receiver_id'] 	= 1;
		$insert['group_id'] 	= 1;
	
		Tbl_message::insert($insert);


		return "success";

	}
}
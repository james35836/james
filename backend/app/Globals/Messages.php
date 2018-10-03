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
use App\Models\Tbl_connection;


class Messages
{	
	public static function GENERAL()
	{
		$messages['general']['messages'] = Tbl_message::where('group_id',1)->orderBy('message_date','ASC')->get();
		$messages['general']['group_name'] = "GENERAL ROOM";
		
		foreach($messages['general']['messages'] as $key=>$message)
		{
			if($message->sender_id==Request::user()->id)
			{
				$messages['general']['messages'][$key]['who'] = "sender";
			}
			else
			{
				$messages['general']['messages'][$key]['who'] = "receiver";
			}
		}
		return $messages;
	}

	public static function BATCH()
	{
		$messages['batch']['messages']   = Tbl_message::where('group_id',2)->orderBy('message_date','ASC')->get();
		$messages['batch']['group_name']   = "BATCH ROOM";

		foreach($messages['batch']['messages'] as $key=>$message)
		{
			if($message->sender_id==Request::user()->id)
			{
				$messages['batch']['messages'][$key]['who'] = "sender";
			}
			else
			{
				$messages['batch']['messages'][$key]['who'] = "receiver";
			}
		}
		return $messages;
	}

	public static function CONNECTION()
	{
		$connection_list = Tbl_connection::where('connection_of',Request::user()->id)->orWhere('connection_by',Request::user()->id)->where('connection_status',2)->get();
    	foreach($connection_list as $key => $value) 
    	{
    		$id = $value->connection_by != Request::user()->id ? $value->connection_by : $value->connection_of;
            $connection_list[$key]['connection_name']        = Users::where('id',$id)->value('name');
            $connection_list[$key]['connection_id']          = $value->connection_id;
			$connection_list[$key]['connection_profile']     = Users::where('id',$id)->value('profile');
			$connection_list[$key]['messages']               = Tbl_message::where('connection_id',$value->connection_id)->get();
			foreach($connection_list[$key]['messages']  as $keys=>$message)
			{
				if($message->sender_id==Request::user()->id)
				{
					$connection_list[$key]['messages'][$keys]['who'] = "sender";
				}
				else
				{
					$connection_list[$key]['messages'][$keys]['who'] = "receiver";
				}
			}
    	}


    	return $connection_list;
	}



	public static function get()
	{
		return Tbl_message::orderBy('message_date','ASC')->get();
	} 
	public static function submit($data)
	{
		$insert['message'] 			= $data['message'];
		$insert['message_date'] 	= Carbon::now();
		$insert['sender_id'] 		= Request::user()->id;

		if($data['send_on'] == "connection")
		{
			
			$insert['connection_id'] 	= $data['send_to'];
			$insert['group_id'] 		= 0;
		}
		else
		{
			$insert['connection_id'] 	= 0;
			$insert['group_id'] 		= $data['send_to'];
		}
		
	
		Tbl_message::insert($insert);

		$return["status"]         = "success"; 
		$return["status_code"]    = 201; 
		$return["status_message"] = "Message send Successfully";

		return $return;

	}

	public static function get_group_messages()
	{
		$messages['general'] = Tbl_message::where('group_id',1)->orderBy('message_date','ASC')->get();
		$messages['batch']   = Tbl_message::where('group_id',2)->orderBy('message_date','ASC')->get();
		
		$messages['general']['group_name'] = "GENERAL ROOM";
		$messages['batch']['group_name']   = "BATCH ROOM";
		return $messages;
	}

	
}
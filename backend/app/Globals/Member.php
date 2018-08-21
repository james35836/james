<?php
namespace App\Globals;
use App\Models\Users;
use DB;
use Carbon\Carbon;
use Validator;
use Crypt;
use Hash;
class Member
{	
	public static function get($type = "member")
	{
		return Users::where("type",$type)->get();
	}

	public static function add_member($data,$area = "admin", $platform = null)
	{
		if($area == "register_area")
		{
			$rules["password"]    		       = "required";
			$rules["password_confirmation"]    = "required|same:password";
		}

		if($data["register_platform"] == "system")
		{
			$rules["email"]    		= "unique:users,email";
			$rules["first_name"]    = "required";
			$rules["last_name"]    	= "required";
			$rules["password"] 		= "required";
		}
		else
		{
			$rules["social_id"] = "unique:users,social_id,required";
		}
		
		$validator = Validator::make($data, $rules);

        if ($validator->fails()) 
        {
            $return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = [];

			$i = 0;
			$len = count($validator->errors()->getMessages());

			foreach ($validator->errors()->getMessages() as $key => $value) 
			{
				foreach($value as $val)
				{
					$return["status_message"][$i] = $val;

				    $i++;		
				}
			}
        }
        else
        {
        	if($data["register_platform"] == "system")
        	{
        		$insert["email"]			= $data["email"];
				$insert["password"]			= Hash::make($data["password"]);
				$insert["crypt"]			= Crypt::encryptString($data["password"]);
				$insert["created_at"]		= Carbon::now();
				$insert["type"]				= "member";
				$insert["first_name"]		= $data["first_name"];
				$insert["last_name"]		= $data["last_name"];
				$insert["contact"]			= $data["contact"];
				$insert["country_id"]	    = $data["country_id"];
				$insert["name"]	            = $data["first_name"]." ".$data["last_name"];
        	}
        	else
        	{
				$insert["created_at"]				= Carbon::now();
				$insert["type"]						= "member";
				$insert["crypt"]					= Crypt::encryptString($data["social_id"]);
        		// $insert["email"]					= isset($data["email"]) ? $data["email"] : null;
				$insert["first_name"]				= isset($data["first_name"]) ? $data["first_name"] : null;
				$insert["last_name"]				= isset($data["last_name"]) ? $data["last_name"] : null;
				$insert["name"]	            		= isset($data["first_name"]) && isset($data["last_name"]) ? $data["first_name"]." ".$data["last_name"] : null;
        		$insert["registration_platform"]    = $data["register_platform"];
        		$insert["social_id"]				= $data["social_id"];
				$insert["password"]					= Hash::make($data["social_id"]);
        	}

			Users::insert($insert);

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Member Created";
        }

        return $return;
	}

	public static function check_credentials($member)
	{
		$password = Users::where("social_id", $member)->first();
		if($password)
		{
			return Crypt::decryptString($password->crypt);
		}
		else
		{
			return 0;
		}
	}
}
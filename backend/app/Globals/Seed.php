<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Hash;
use Crypt;
class Seed
{
	public static function initial_seed()
	{
	
		Seed::admin_seed();
		


		$return["status"]         = "success"; 
		$return["status_code"]    = 1; 
		$return["status_message"] = "Successfully seeded";

		return $return;
	}
	

	
	

	public static function admin_seed()
	{
		$count       = DB::table("users")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE users AUTO_INCREMENT =  1");
			
			$insert_admin["name"]			= "Administrator";	
			$insert_admin["email"]			= "james@james.com";	
			$insert_admin["password"]		= Hash::make("habagat");		
			$insert_admin["remember_token"]	= null;			
			$insert_admin["created_at"]		= Carbon::now();		
			$insert_admin["updated_at"]		= Carbon::now();
			$insert_admin["type"]			= "admin";	
			$insert_admin["crypt"]			= Crypt::encryptString("habagat");	
			$insert_admin["first_name"]		= "";		
			$insert_admin["last_name"]		= "";		
			$insert_admin["school_year_id"]		= 0;

			DB::table("users")->insert($insert_admin);	

			$inserrt_info['user_phone_1'] 		= "09##########";
			$inserrt_info['user_id'] 			= 1;
			$inserrt_info['user_bio']           = '"Homecoming means more than winning a competition or a sporting event. It provides an opportunity for every component of the university to come together to celebrate as a whole."';
			$inserrt_info["user_job"]		    = "PROFESSIONAL";
			DB::table("tbl_user_info")->insert($inserrt_info);	
				
		}
	}

	

	

	

	
}
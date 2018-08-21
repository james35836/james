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
		Seed::country_seed();
		Seed::mlm_plan_seed();
		Seed::membership_seed();
		Seed::slot_seed();
		Seed::currency_seed();
		Seed::mlm_settings_seed();
		Seed::cash_in_method_category_seed();
		Seed::location_seed();
		Seed::stockist_level_seed();
		Seed::item_seed();
		Seed::branch_seed();
		Seed::inventory_seed();
		Seed::stockist_level_discount_seed();
		Seed::service_charge_seed();
		Seed::cash_in_method_seed();


		$return["status"]         = "success"; 
		$return["status_code"]    = 1; 
		$return["status_message"] = "Successfully seeded";

		return $return;
	}

	public static function reset_seed()
	{

		$return["status"]         = "success"; 
		$return["status_code"]    = 1; 
		$return["status_message"] = "Successfully reset";

		return $return;
	}

	public static function hard_reset_seed()
	{
		DB::table("tbl_country")->truncate();

		$return["status"]         = "success"; 
		$return["status_code"]    = 1; 
		$return["status_message"] = "Successfully hard reset";

		return $return;
	}

	public static function currency_seed()
	{
		$name     = ['Philippine Peso','US Dollar', 'Ultra Pro Token', 'Gift Card', 'Bitcoin'];
		$abv      = ['PHP','USD','UPT','GC','BTC'];
		$set      = [1,0,0,0,0];
		foreach($name as $key => $value)
		{
			$insert["currency_name"]         = $value;
			$insert["currency_abbreviation"] = $abv[$key];
			$insert["currency_default"]      = $set[$key];
			$check = DB::table("tbl_currency")->where("currency_abbreviation",$abv[$key])->first();
			
			if(!$check)
			{
				DB::table("tbl_currency")->insert($insert);
			}	
		}
	}

	public static function country_seed()
	{
		$country  = ['Philippines','Japan','USA'];
		$currency = ['PHP'        ,'JPY'  ,'USD'];
		foreach($country as $key => $value)
		{
			$insert["country_name"]  = $value;
			$insert["currency_code"] = $currency[$key];
			$check = DB::table("tbl_country")->where("country_name",$value)->first();
			
			if(!$check)
			{
				DB::table("tbl_country")->insert($insert);
			}	
		}
	}

	public static function mlm_plan_seed()
	{
		$code    = ['BINARY'       ,'DIRECT'       ,'UNILEVEL'       ,'STAIRSTEP'       ,'INDIRECT'];
		// $label   = ['Binary'       ,'Direct'       ,'Unilevel Bonus' ,'Override Bonus'  ,'Indirect Referral Bonus'];
		$trigger = ['Slot Placement','Slot Creation','Slot Repurchase','Slot Repurchase' ,'Slot Creation'];
		
		foreach($code as $key => $value)
		{
			$insert["mlm_plan_code"]    = $value;
			$insert["mlm_plan_label"]   = "";
			$insert["mlm_plan_trigger"] = $trigger[$key];
			$insert["mlm_plan_enable"]  = 0;

			$check = DB::table("tbl_mlm_plan")->where("mlm_plan_code",$value)->first();
			
			if(!$check)
			{
				DB::table("tbl_mlm_plan")->insert($insert);
			}	
		}
	}

	public static function membership_seed()
	{
		$membership  = ['Bronze'];
		$price       = [5000];
		$count       = DB::table("tbl_membership")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_membership AUTO_INCREMENT =  1");
			foreach($membership as $key => $value)
			{
				$insert["membership_name"]         = $value;
				$insert["membership_price"]        = $price[$key];
				$insert["membership_date_created"] = Carbon::now();

			DB::table("tbl_membership")->insert($insert);
			}
		}
	}

	public static function slot_seed()
	{
		$count       = DB::table("tbl_slot")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_slot AUTO_INCREMENT =  1");
			
			$admin_id = null;
			if(!$admin_id)
			{
				$insert_admin["name"]			= "Administrator";	
				$insert_admin["email"]			= "admin@test.com";	
				$insert_admin["password"]		= Hash::make("toptal");		
				$insert_admin["remember_token"]	= null;			
				$insert_admin["created_at"]		= Carbon::now();		
				$insert_admin["updated_at"]		= Carbon::now();
				$insert_admin["type"]			= "admin";	
				$insert_admin["crypt"]			= Crypt::encryptString("toptal");	
				$insert_admin["first_name"]		= "";		
				$insert_admin["last_name"]		= "";		
				$insert_admin["contact"]		= "";		
				$insert_admin["country_id"]		= 0;	

				DB::table("users")->insert($insert_admin);	

				$admin_id = DB::table("users")->where("type","admin")->first();
			}

			$admin_id = $admin_id->id;

			$insert["slot_no"]                 = "COMPANYHEAD";
			$insert["slot_membership"]         = 1;
			$insert["slot_position"]           = "LEFT";
			$insert["slot_sponsor"]            = 0;
			$insert["slot_owner"]              = $admin_id;
			$insert["slot_type"]               = "PS";
			$insert["slot_date_created"]       = Carbon::now();
			

			DB::table("tbl_slot")->insert($insert);
		}
	}

	public static function mlm_settings_seed()
	{
		$count       = DB::table("tbl_mlm_settings")->count();

		if($count == 0)
		{			
			$mlm_setting["mlm_slot_no_format_type"]      = 1;
			$mlm_setting["mlm_slot_no_format"]			 = "";
			$mlm_setting["free_registration"]			 = 0;
			$mlm_setting["multiple_type_membership"]	 = 0;
			$mlm_setting["gc_inclusive_membership"]		 = 0;
			$mlm_setting["product_inclusive_membership"] = 0;
			DB::table("tbl_mlm_settings")->insert($mlm_setting);
		}
	}

	public static function admin_seed()
	{
		$count       = DB::table("tbl_slot")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_slot AUTO_INCREMENT =  1");
			$admin_id = DB::table("users")->where("type","admin")->first();
			$admin_id = $admin_id->id;

			$insert["slot_no"]                 = "COMPANYHEAD";
			$insert["slot_membership"]         = 1;
			$insert["slot_position"]           = "LEFT";
			$insert["slot_sponsor"]            = 0;
			$insert["slot_owner"]              = $admin_id;
			$insert["slot_date_created"]       = Carbon::now();

			DB::table("tbl_slot")->insert($insert);
		}
	}

	public static function cash_in_method_category_seed()
	{
		$category_name     = ['remittance', 'bank', 'paymaya', 'crypto'];
		foreach($category_name as $key => $value)
		{
			$insert["cash_in_method_category"]         = $value;
			$check = DB::table("tbl_cash_in_method_category")->where("cash_in_method_category",$value)->first();
			
			if(!$check)
			{
				DB::table("tbl_cash_in_method_category")->insert($insert);
			}
		}
	}	

	public static function location_seed()
	{
		$count       = DB::table("tbl_location")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_location AUTO_INCREMENT =  1");

			$insert["location"]                 = "Manila";

			DB::table("tbl_location")->insert($insert);
		}
	}

	public static function stockist_level_discount_seed()
	{
		$count       = DB::table("tbl_item_stockist_discount")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_item_stockist_discount AUTO_INCREMENT =  1");

			$insert["stockist_level_id"]                 = 1;
			$insert["item_id"]          			     = 1;

			DB::table("tbl_item_stockist_discount")->insert($insert);
		}
	}

	public static function stockist_level_seed()
	{
		$count       = DB::table("tbl_stockist_level")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_stockist_level AUTO_INCREMENT =  1");

			$insert["stockist_level_name"]                 = "Mobile";
			$insert["stockist_level_date_created"]          = Carbon::now();

			DB::table("tbl_stockist_level")->insert($insert);
		}
	}

	public static function item_seed()
	{
		$count       = DB::table("tbl_item")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_item AUTO_INCREMENT =  1");

			$insert["item_sku"]              			= "Sample Item";
			$insert["item_description"]              	= "Sample Item";
			$insert["item_barcode"]              		= "11101";
			$insert["membership_id"]              		=  1;
			$insert["item_date_created"]              	= Carbon::now();

			DB::table("tbl_item")->insert($insert);
		}
	}
	
	public static function branch_seed()
	{
		$count       = DB::table("tbl_branch")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_branch AUTO_INCREMENT =  1");

			$insert["branch_name"]                 = "Main";
			$insert["branch_type"]                 = "Branch";
			$insert["branch_location"]             = "Manila";
			$insert["branch_date_created"]          = Carbon::now();
			$insert['stockist_level']				= 1;
			$branch_id = DB::table("tbl_branch")->insert($insert);

			
			
		}
	}

	public static function inventory_seed()
	{
		$count       = DB::table("tbl_inventory")->count();

		if($count == 0)
		{			
			DB::statement("ALTER TABLE tbl_inventory AUTO_INCREMENT =  1");

			$insert['inventory_branch_id'] 	= 1;
			$insert['inventory_item_id'] 		= 1;

			DB::table("tbl_inventory")->insert($insert);
		}
	}

	public static function service_charge_seed()
	{
		$service     = ['cash_in', 'cash_out'];
		$charge      = [10, 10];
		foreach($service as $key => $value)
		{
			$insert["service_name"]           = $value;
			$insert["service_charge"]         = $charge[$key];
			$check = DB::table("tbl_service_charge")->where("service_name",$value)->first();
			
			if(!$check)
			{
				DB::table("tbl_service_charge")->insert($insert);
			}
		}
	}

	public static function cash_in_method_seed()
	{
		$method            = ['Banco De Oro', 'Cebuana Lhuillier', 'GCash', 'Bitcoin'];
		$category          = ['bank', 'remittance', 'remittance', 'crypto'];
		$thumbnail         = [
			'https://digima.sgp1.digitaloceanspaces.com/mlm/cash_in_proof/gnK9uoJWsiTfolD5gCWjQqYQhUXneFREi1kBWZqk.png',
			'https://digima.sgp1.digitaloceanspaces.com/mlm/cash_in_proof/b78Pn3YOGIPWqTKvIwXreeDLeAPPb1US60U52n4w.jpeg',
			'https://digima.sgp1.digitaloceanspaces.com/mlm/cash_in_proof/siHdjzgD3HFr99UaPo6FPWKDaqXXRWw8zREzncUm.png',
			'https://digima.sgp1.digitaloceanspaces.com/mlm/cash_in_proof/4ezQwFWDEvWrH6zENdyudmvi3IijZu8NJykuOJRx.jpeg'];
		$currency 	       = ['php', 'php', 'php', 'btc'];
		$fix_charge    	   = [10, 20, 30, 0.0001];
		$percent_charge    = [2, 4, 6, 8];
		$primary 		   = ['Ultraproactive, Inc.', 'John Doeterte', 'Jane Doeterte', null];
		$secondary 		   = ['000910028667', 'Eiffel Tower', 'Statue of Liberty', null];
		$optional 		   = [null, null, '09112234456', null];

		foreach($method as $key => $value)
		{
			$insert["cash_in_method_name"]           		= $value;
			$insert["cash_in_method_category"]         		= $category[$key];
			$insert["cash_in_method_thumbnail"]         	= $thumbnail[$key];
			$insert["cash_in_method_currency"]         		= $currency[$key];
			$insert["cash_in_method_charge_fixed"]      	= $fix_charge[$key];
			$insert["cash_in_method_charge_percentage"]     = $percent_charge[$key];
			$insert["primary_info"]         				= $primary[$key];
			$insert["secondary_info"]         				= $secondary[$key];
			$insert["optional_info"]         				= $optional[$key];

			$check = DB::table("tbl_cash_in_method")->where("cash_in_method_name",$value)->first();
			
			if(!$check)
			{
				DB::table("tbl_cash_in_method")->insert($insert);
			}
		}
	}
}
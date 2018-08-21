<?php
namespace App\Globals;

use App\Models\Tbl_admin;
use App\Models\Tbl_country;
use App\Models\Tbl_company;
use App\Models\Tbl_company_settings;
use App\Models\Tbl_customer;
use App\Models\Tbl_mlm_settings;
use App\Models\Tbl_binary_settings;
use App\Models\Tbl_mlm_plan;
use App\Models\Tbl_membership;
use App\Models\Tbl_membership_indirect_level;
use DB;
use Carbon\Carbon;
use Validator;
class Membership
{
	public static function add($data)
	{
		$insert["membership_name"]			 = $data["membership_name"];
		$insert["membership_price"]			 = $data["membership_price"];
		$insert["membership_gc"]			 = $data["membership_gc"];
		$insert["membership_indirect_level"] = 0;
		$insert["membership_unilevel_level"] = 0;
		$insert["membership_date_created"]	 = Carbon::now();
		$insert["membership_required_pv"]	 = 0;

		Tbl_membership::insert($insert);


		$return["status"]         = "success"; 
		$return["status_code"]    = 1; 
		$return["status_message"] = "Membership Created";

		return $return;
	}

	public static function get()
	{
		$data = Tbl_membership::where("archive",0)->get();

		foreach($data as $key => $d)
		{
			$data[$key]->membership_indirect_level = count(Tbl_membership_indirect_level::select("membership_level")->where("membership_id",$d->membership_id)->groupBy("membership_level")->get());
		}

		return $data;
	}

	public static function submit($data)
	{
		foreach ($data as $key => $value) 
		{
			$rules["hierarchy"] = "required|numeric|min:1|max:100";

			$validator = Validator::make($value, $rules);

	        if ($validator->fails()) 
	        {
	            $return["status"]         = "error"; 
				$return["status_code"]    = 400; 
				$return["status_message"] = $validator->messages()->all();

				return $return;
	        }
	        else
	        {
	        	$param["membership_name"] = $value["membership_name"];
				$param["hierarchy"] = $value["hierarchy"];

				if ($value["membership_id"]) 
				{
					$param["archive"] = $value["archive"];

					Tbl_membership::where("membership_id", $value["membership_id"])->update($param);
				}
				else
				{
					$param["membership_date_created"] = Carbon::now();
					$param["membership_price"] = 0;

					Tbl_membership::insert($param);
				}
			}
		}

		$return["status"]         = "success"; 
		$return["status_code"]    = 200; 
		$return["status_message"] = "Membership Updated";

		return $return;
	}
}
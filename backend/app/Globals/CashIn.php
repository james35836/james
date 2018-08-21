<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Request;
use App\Models\Tbl_cash_in_proofs;
use App\Models\Tbl_cash_in_method;
use App\Models\Tbl_cash_in_method_category;
use App\Models\Tbl_slot;
use App\Models\Users;
use Validator;
use Hash;
class CashIn
{
	public static function get_transactions($params = null, $slot_owner = null)
	{
		$data  = Tbl_cash_in_proofs::method();

		//single member
		if($slot_owner)
		{
			$slot_info = Tbl_slot::where("slot_id", $slot_owner)->first();
			if($slot_info)
			{
				$data = $data->where("cash_in_slot_code", $slot_info->slot_no);
			}
		}
		
		//filter by cash in status
		if(isset($params["cash_in_status"]) && $params["cash_in_status"] != "all")
		{
			$data = $data->where("cash_in_status", $params["cash_in_status"]);
		}

		//filter by slot code or slot owner
		if(isset($params["cash_in_owner"]) && $params["cash_in_owner"] != null)
		{
			$owner = $params["cash_in_owner"];
			$data = $data->where(function($query) use ($owner)
				{
					$query->where("cash_in_slot_code", "like", "%".$owner."%")
						  ->orWhere("cash_in_member_name", "like", "%".$owner."%");
				});
		}

		//filter by method
		if(isset($params["cash_in_method_id"]) && $params["cash_in_method_id"] != "all")
		{
			$data = $data->where("tbl_cash_in_proofs.cash_in_method_id", $params["cash_in_method_id"]);
		}

		//filter by currency
		if(isset($params["cash_in_currency"]) && $params["cash_in_currency"] != "all")
		{
			$data = $data->where("cash_in_currency", $params["cash_in_currency"]);
		}

		//filter by cash in date from
		if(isset($params["cash_in_date_from"]) && $params["cash_in_date_from"] != "all")
		{
			$data = $data->whereDate("cash_in_date", ">=", $params["cash_in_date_from"]);
		}

		//filter by cash in date to
		if(isset($params["cash_in_date_to"]) && $params["cash_in_date_to"] != "all")
		{
			$data = $data->whereDate("cash_in_date", "<=", $params["cash_in_date_to"]);
		}

		$data = $data->get();
		return $data;
	}

	public static function get_method_list($category = null, $currency = null, $except_archive = null)
	{
		$data = Tbl_cash_in_method::where("cash_in_method_id", "!=", 0);

		if($except_archive)
		{
			$data = $data->where("is_archived", 0);
		}	

		if($category && $category != "all")
		{
			$data = $data->where("cash_in_method_category", $category);
		}

		if($currency && $currency != "all")
		{
			$data = $data->where("cash_in_method_currency", $currency);
		}
		return $data->get();
	}

	public static function get_method_category_list()
	{
		$data = Tbl_cash_in_method_category::where("cash_in_method_category_id", "!=", 0);
		
		return $data->get();
	}

	public static function add_new_method($params = null)
	{
		if($params)
		{
			$rules["cash_in_method_thumbnail"] = "required";
			$rules["cash_in_method_name"] = "unique:tbl_cash_in_method|required";

			$validator = Validator::make($params, $rules);

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
	        	Tbl_cash_in_method::insert($params);
				$return["status_message"] = "Method Succesfully Added!";
				$return["status_code"]    = 200; 
				$return["status"] = "success";
	        }
			
		}
		else
		{
			$return["status_message"] = "Oops! Something went wrong!";
			$return["status_code"]    = 500; 
			$return["status"] = "error";
		}

		return $return;
	}

	public static function update_method($params = null)
	{
		if($params)
		{
			Tbl_cash_in_method::where("cash_in_method_id", $params["cash_in_method_id"])->update($params);
			$return["status_message"] = "Method Succesfully Updated!";
			$return["status"] = "success";
		}
		else
		{
			$return["status_message"] = "Oops! Something went wrong!";
			$return["status"] = "error";
		}

		return $return;
	}

	public static function archive_method($id = null, $archive = null)
	{
		if($id)
		{
			Tbl_cash_in_method::where("cash_in_method_id", $id)->update(["is_archived"=>$archive]);
			$return["status_message"] = $archive == 1 ? "Method Succesfully Archived!" : "Method Succesfully Unarchived!";
			$return["status"] = "success";
		}
		else
		{
			$return["status_message"] = "Oops! Something went wrong!";
			$return["status"] = "error";
		}

		return $return;
	}

	public static function record_cash_in($params)
	{
		if(isset($params["slot_id"]))
		{
			$slot_info = Tbl_slot::owner()->where("tbl_slot.slot_id", $params["slot_id"])->first();

			if($slot_info)
			{
				$insert["cash_in_slot_code"] 	= $slot_info->slot_no;
				$insert["cash_in_member_name"] 	= $slot_info->name;
				$insert["cash_in_method_id"] 	= $params["cash_in_method_id"];
				$insert["cash_in_currency"] 	= $params["cash_in_method_currency"];
				$insert["cash_in_charge"] 		= $params["total_due"] - $params["cash_in_amount"];
				$insert["cash_in_receivable"] 	= $params["cash_in_amount"];;
				$insert["cash_in_payable"] 		= $params["total_due"];
				$insert["cash_in_proof"] 		= $params["cash_in_proof"];
				$insert["cash_in_date"] 		= Carbon::now('Asia/Manila');
				Tbl_cash_in_proofs::insert($insert);

				$return["status"] = "success";
				$return["status_message"] = "Successfully placed your Cash-In request.";
			}
		}
		else
		{
			$return["status"] = "error";
			$return["status_message"] = "Oops! Something went wrong.";
		}

		return $return;
	}

	public static function process_transaction($params = null)
	{
		if($params)
		{
			$transaction = Tbl_cash_in_proofs::slot()->where("cash_in_proof_id", $params["proof_id"])->first();
			if($transaction)
			{
				Wallet::update_wallet($transaction->slot_id, $transaction->cash_in_receivable);
				$data = Tbl_cash_in_proofs::where("cash_in_proof_id", $transaction->cash_in_proof_id)->update(["cash_in_status" => $params['process']]);
				$return["status"] 		  = "success";
				$return["status_message"] = "Successfully ".$params["process"]." transaction!";
			}
		}
		else
		{
			$return["status"] 		  = "error";
			$return["status_message"] = "Parameters cannot be blank";
		}

		return $return;
	}

	public static function process_all_transaction($params = null)
	{
		if($params)
		{
			foreach ($params["proof_id"] as $key => $value) {
				$transaction = Tbl_cash_in_proofs::slot()->where("cash_in_proof_id", $value)->first();
				if($transaction)
				{
					Wallet::update_wallet($transaction->slot_id, $transaction->cash_in_receivable);
					$data = Tbl_cash_in_proofs::where("cash_in_proof_id", $value)->update(["cash_in_status" => $params['process']]);
					$return["status"] 		  = "success";
					$return["status_message"] = "Successfully ".$params["process"]." all pending transactions!";
				}
			}
		}
		else
		{
			$return["status"] 		  = "error";
			$return["status_message"] = "Parameters cannot be blank";
		}

		return $return;
	}
}
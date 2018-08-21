<?php
namespace App\Globals;

use DB;
use Carbon\Carbon;
use Validator;

use App\Models\Tbl_item;
use App\Models\Tbl_item_membership_discount;
use App\Models\Tbl_item_stockist_discount;
use App\Models\Tbl_item_points;
use App\Models\Rel_item_kit;
use App\Models\Tbl_membership;
use App\Models\Tbl_inventory;

class Item
{
	public static function add($data)
	{
		$rules["item_sku"]         = "required|unique:tbl_item";
		$rules["item_description"] = "required";
		$rules["item_barcode"]     = "";
		$rules["item_price"]       = "required|numeric|min:1";
		$rules["item_pv"]    	   = "required|numeric";
		$rules["item_type"]        = "required";

		if ($data["item_type"] == "membership_kit") 
		{
			$rules["membership_id"] = "required";
			$rules["slot_qty"]      = "required|numeric|min:1";
			$rules["inclusive_gc"]  = "required|numeric|min:0";
		}
		
		$validator = Validator::make($data, $rules);

        if ($validator->fails()) 
        {
            $return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = $validator->messages()->all();
        }
        else
        {
        	$insert["item_sku"]          = $data["item_sku"];
			$insert["item_description"]  = $data["item_description"];
			$insert["item_barcode"]      = isset($data["item_barcode"]) ? $data["item_barcode"] : "";
			$insert["item_price"]        = $data["item_price"];
			$insert["item_pv"]     		 = $data["item_pv"];
			$insert["item_type"]         = $data["item_type"];
			$insert["membership_id"]     = $data["item_type"] == "membership_kit" ? $data["membership_id"] : 0;
			$insert["slot_qty"]          = $data["item_type"] == "membership_kit" ? $data["slot_qty"] : 0;
			$insert["inclusive_gc"]      = $data["item_type"] == "membership_kit" ? $data["inclusive_gc"] : 0;
			$insert["item_date_created"] = Carbon::now();

			$id = Tbl_item::insertGetId($insert);

			if ($data["item_type"] == "membership_kit") 
			{
				$item_kit = $data["item_kit_fix"];

				if (count($item_kit) > 0) 
				{
					foreach ($item_kit as $key => $value) 
					{
						if ($value["item_inclusive_id"] != null && $value["item_qty"] != null) 
						{
							$insert_kit["item_id"]           = $id;
							$insert_kit["item_inclusive_id"] = $value["item_inclusive_id"];
							$insert_kit["item_qty"]          = $value["item_qty"];

							Rel_item_kit::insert($insert_kit);
						}
					}
				}
			}
			elseif ($data["item_type"] == "product")
			{
				$item_membership_discount = $data["item_membership_discount_fix"];

				if (count($item_membership_discount) > 0) 
				{
					foreach ($item_membership_discount as $key => $value) 
					{
						$insert_discount["membership_id"] = $value["membership_id"];
						$insert_discount["item_id"]       = $id;
						$insert_discount["discount"]      = $value["discount"] < 0 ? 0 : ($value["discount"] > 100 ? 100 : $value["discount"]);

						Tbl_item_membership_discount::insert($insert_discount);
					}
				}

			}
		




			//tbl_inventory_item_id
			$check_null_items = Tbl_inventory::where('inventory_item_id', null)->get();
			
			if(count($check_null_items) == 0)
			{
				
				$table_inventory = Tbl_inventory::where('inventory_item_id', '!=', $id)->select('inventory_branch_id')->distinct()->get();

				foreach ($table_inventory as $key => $value) 
				{

					$insert_inventory['inventory_branch_id'] = $value->inventory_branch_id;
					$insert_inventory['inventory_item_id']   = $id;

					Tbl_inventory::insert($insert_inventory);
				}
			}
			else
			{
				foreach($check_null_items as $key => $value)
				{
					Tbl_inventory::where('inventory_id', $value->inventory_id)->update(['inventory_item_id' => $id]);
				}
			}

			$table_stockist_discount = Tbl_item_stockist_discount::where('item_id', '!=', $id)->select('stockist_level_id')->distinct()->get();

			foreach ($table_stockist_discount as $key => $value) 
			{

				$insert_stockist_discount['stockist_level_id'] = $value->stockist_level_id;
				$insert_stockist_discount['item_id']   = $id;

				Tbl_item_stockist_discount::insert($insert_stockist_discount);
			}
			

			
			
			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Item Created";
			$return["id"]			  = $id;
        }

        return $return;
	}

	public static function edit($data)
	{
		$rules["item_sku"]         = "required";
		$rules["item_description"] = "required";
		$rules["item_barcode"]     = "";
		$rules["item_price"]       = "required|numeric|min:1";
		$rules["item_pv"]          = "required|numeric";
		$rules["item_type"]        = "required";
		


		if ($data['item']["item_type"] == "membership_kit") 
		{
			$rules["membership_id"] = "required";
			$rules["slot_qty"]      = "required|numeric|min:1";
			$rules["inclusive_gc"]  = "required|numeric";
		}
		$validator = Validator::make($data['item'], $rules);
        if ($validator->fails()) 
        {
            $return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = $validator->messages()->all();
        }
        else
        {	
        	
        	$id 						 = $data['item']['item_id'];
        	$update["item_sku"]          = $data['item']["item_sku"];
			$update["item_description"]  = $data['item']["item_description"];
			$update["item_barcode"]      = isset($data['item']["item_barcode"]) ? $data['item']["item_barcode"] : "";
			$update["item_price"]        = $data['item']["item_price"];
			$update["item_pv"]     		 = $data['item']["item_pv"];
			$update["item_type"]         = $data['item']["item_type"];
			$update["membership_id"]     = $data['item']["item_type"] == "membership_kit" ? $data['item']["membership_id"] : 0;
			$update["slot_qty"]          = $data['item']["item_type"] == "membership_kit" ? $data['item']["slot_qty"] : 0;
			$update["inclusive_gc"]      = $data['item']["item_type"] == "membership_kit" ? $data['item']["inclusive_gc"] : 0;
			$update["item_date_created"] = Carbon::now();

			Tbl_item::where("tbl_item.item_id", $id)->update($update);

			if ($data['item']["item_type"] == "membership_kit") 
			{
				Rel_item_kit::where("rel_item_kit.item_id", $id)->delete();

				$item_kit = $data['item']["item_kit_fix"];

				if (count($item_kit) > 0) 
				{
					foreach ($item_kit as $key => $value) 
					{
						$item_kit_rules["item_qty"] = "required|numeric|min:1";

						$validator = Validator::make($value, $item_kit_rules);

				        if ($validator->fails()) 
				        {
				            $return["status"]         = "error"; 
							$return["status_code"]    = 400; 
							$return["status_message"] = $validator->messages()->all();

							return $return;
				        }
				        else
				        {
							if ($value["item_inclusive_id"] != null && $value["item_qty"] != null) 
							{
								$insert_kit["item_id"]           = $id;
								$insert_kit["item_inclusive_id"] = $value["item_inclusive_id"];
								$insert_kit["item_qty"]          = $value["item_qty"];

								Rel_item_kit::insert($insert_kit);
							}
						}
					}
				}
				//stockist discount
				Tbl_item_stockist_discount::where("tbl_item_stockist_discount.item_id", $id)->delete();

				$item_stockist_discount = $data["stockist"];

				if (count($item_stockist_discount) > 0) 
				{
					foreach ($item_stockist_discount as $key => $value) 
					{
						if(isset($value["discount"]) && $value["discount"] == null)
						{
							$value["discount"] = 0;
						}
						$insert_stockist_discount["stockist_level_id"] = $value["stockist_level_id"];
						$insert_stockist_discount["item_id"]       	   = $id;
						$insert_stockist_discount["discount"]          = $value["discount"] < 0 ? 0 : ($value["discount"] > 100 ? 100 : $value["discount"]);

						Tbl_item_stockist_discount::insert($insert_stockist_discount);
					}
				}
				//membership discounts
				Tbl_item_membership_discount::where("tbl_item_membership_discount.item_id", $id)->delete();

				$item_membership_discount = $data['item']["item_membership_discount_fix"];

				if (count($item_membership_discount) > 0) 
				{
					foreach ($item_membership_discount as $key => $value) 
					{
						$insert_discount["membership_id"] = $value["membership_id"];
						$insert_discount["item_id"]       = $id;
						$insert_discount["discount"]      = $value["discount"] < 0 ? 0 : ($value["discount"] > 100 ? 100 : $value["discount"]);

						Tbl_item_membership_discount::insert($insert_discount);
					}
				}
			}
			elseif ($data['item']["item_type"] == "product")
			{
				Tbl_item_membership_discount::where("tbl_item_membership_discount.item_id", $id)->delete();

				$item_membership_discount = $data['item']["item_membership_discount_fix"];

				if (count($item_membership_discount) > 0) 
				{
					foreach ($item_membership_discount as $key => $value) 
					{
						$insert_discount["membership_id"] = $value["membership_id"];
						$insert_discount["item_id"]       = $id;
						$insert_discount["discount"]      = $value["discount"] < 0 ? 0 : ($value["discount"] > 100 ? 100 : $value["discount"]);

						Tbl_item_membership_discount::insert($insert_discount);
					}
				}

				Tbl_item_stockist_discount::where("tbl_item_stockist_discount.item_id", $id)->delete();

				$item_stockist_discount = $data["stockist"];

				if (count($item_stockist_discount) > 0) 
				{
					foreach ($item_stockist_discount as $key => $value) 
					{
						$insert_stockist_discount["stockist_level_id"] = $value["stockist_level_id"];
						$insert_stockist_discount["item_id"]       	   = $id;
						$insert_stockist_discount["discount"]          = $value["discount"] < 0 ? 0 : ($value["discount"] > 100 ? 100 : $value["discount"]);

						Tbl_item_stockist_discount::insert($insert_stockist_discount);
					}
				}

				Tbl_item_points::where("tbl_item_points.item_id", $id)->delete();

				$item_points = $data['item']["item_points_fix"];

				if (count($item_points) > 0) 
				{
					foreach ($item_points as $key => $value) 
					{
						$insert_points["item_points_key"]         = $value["item_points_key"];
						$insert_points["item_points_personal_pv"] = $value["item_points_personal_pv"];
						$insert_points["item_points_group_pv"]    = $value["item_points_group_pv"];
						$insert_points["item_id"]                 = $id;

						Tbl_item_points::insert($insert_points);
					}
				}
			}

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Item Updated";
        }

        return $return;
	}

	public static function get_product()
	{
		return Tbl_item::where("tbl_item.archived", 0)->where("tbl_item.item_type", "product")->get();
	}

	public static function get_item($filters = null, $limit = null, $branch_id = null)
	{
		$data = Tbl_item::where("tbl_item.archived", 0);

		if(isset($filters["item_type"]) && $filters["item_type"] != "all")
		{
			$data = $data->where("item_type", $filters["item_type"]);
		}
		if(isset($filters["search_key"]))
		{
			$data = $data->where("item_description", "like", "%". $filters["search_key"] . "%");
		}

		if ($limit) 
		{
			$data = $data->paginate($limit);
		}
		else
		{
			$data = $data->get();
		}


		return $data;
	}

	public static function get_inventory($data)
	{
		$items 					 	= Tbl_item::Unarchived()->JoinInventory()->where('tbl_inventory.inventory_branch_id', $data['branch_id'])->get();
		foreach($items as $key => $value)
		{
			$items[$key]->used_codes = Tbl_item::Unarchived()->JoinInventory()->JoinCodesInventory()->where('tbl_inventory.inventory_id', $value->inventory_id)->Used()->count();
			$items[$key]->sold_codes = Tbl_item::Unarchived()->JoinInventory()->JoinCodesInventory()->where('tbl_inventory.inventory_id', $value->inventory_id)->Sold()->count();
			
		}
		return $items;
	}

	public static function get_item_inventory($item_id)
	{
		$data = Tbl_item::Unarchived()->JoinInventory()->JoinBranch()
						->where('tbl_inventory.inventory_item_id', $item_id)
						->get();
		foreach($data as $key => $value)
		{
			$data[$key]->used_codes = Tbl_item::Unarchived()->JoinInventory()->JoinCodesInventory()->where('tbl_inventory.inventory_id', $value->inventory_id)->Used()->count();
			$data[$key]->sold_codes = Tbl_item::Unarchived()->JoinInventory()->JoinCodesInventory()->where('tbl_inventory.inventory_id', $value->inventory_id)->Sold()->count();
		}
		return $data;
	}

	public static function get_data($id)
	{
		$data = Tbl_item::where("tbl_item.item_id", $id)->first();

		if ($data) 
		{
			$rel_item_kit = Rel_item_kit::select("rel_item_kit.item_id", "tbl_item.item_id", "rel_item_kit.item_inclusive_id", "rel_item_kit.item_qty")->where("rel_item_kit.item_id", $data->item_id)->leftJoin("tbl_item", "tbl_item.item_id", "=", "rel_item_kit.item_id")->get();
			
			if ($rel_item_kit && count($rel_item_kit) > 0) 
			{
				$data->item_kit = $rel_item_kit;
			}
			
			$membership_discount = Tbl_item_membership_discount::where("tbl_item_membership_discount.item_id", $data->item_id)->get();

			if ($membership_discount && count($membership_discount) > 0) 
			{
				$data->membership_discount = $membership_discount;
			}

			$item_points = Tbl_item_points::where("tbl_item_points.item_id", $data->item_id)->get();

			if ($item_points && count($item_points) > 0) 
			{
				$data->item_points = $item_points;
			}
		}

		$check_stockist_list = DB::table('tbl_stockist_level')->where('archive', 0)->join('tbl_item_stockist_discount', 'tbl_stockist_level.stockist_level_id', '=', 'tbl_item_stockist_discount.stockist_level_id')->where('tbl_item_stockist_discount.item_id', $id)->get();

		if(count($check_stockist_list) == 0)
		{
			$data->stockist_list = DB::table('tbl_stockist_level')->where('archive', 0)->get();
		}
		else
		{
			$data->stockist_list = $check_stockist_list;
		}
		return $data;
	}

	public static function archive($id)
	{
		Tbl_item::where("tbl_item.item_id", $id)->update(["archived" => 1]);
        
		$return["status"]         = "success"; 
		$return["status_code"]    = 200; 
		$return["status_message"] = "Item Archived";

        return $return;
	}

	public static function restock($data)
	{
		$update['inventory_quantity']			= $data['quantity'];

		$query = Tbl_inventory::where([['inventory_branch_id','=',$data['branch_id']],['inventory_item_id','=', $data['item_id']]])->first();
		
		if($query->inventory_quantity == null)
		{
			Tbl_inventory::where([['inventory_branch_id','=',$data['branch_id']],['inventory_item_id','=', $data['item_id']]])->update($update);

			$return["status"]         = "success"; 
			$return["status_code"]    = 200; 
			$return["status_message"] = "Item Archived";

        	return $return;

		}
		else
		{
			$update['inventory_quantity'] = $query->inventory_quantity + $data['quantity'];

			Tbl_inventory::where([['inventory_branch_id','=',$data['branch_id']],['inventory_item_id','=', $data['item_id']]])->update($update);

			$return["branch_id"]	  = $data['branch_id'];
			$return["status"]         = "success"; 
			$return["status_code"]    = 200; 
			$return["status_message"] = "Item Archived";

        	return $return;
		}
	}

	public static function update_inventory($branch_id, $item_id,$quantity)
	{
		$current_quantity			= Tbl_inventory::where([['inventory_branch_id', $branch_id],['inventory_item_id' , $item_id]])->sum('inventory_quantity');

		

		$update_quantity			= $current_quantity + $quantity;

		Tbl_inventory::where([['inventory_branch_id', $branch_id],['inventory_item_id' , $item_id]])->update(['inventory_quantity' => $update_quantity]);

		$return["status"]         = "success"; 
		$return["status_code"]    = 200; 
		$return["status_message"] = "Updated Successfully!";	
		
		return $return;	
	}

	public static function get_all_products()
	{
		$return['membership_kit'] = Tbl_item::where('archived', 0)->where('item_type', 'membership_kit')->get();
		$return['product'] = Tbl_item::where('archived', 0)->where('item_type', 'product')->get();
		
		return $return;
	}

	public static function get_cart($data)
	{
		$test = collect($data['items']);
		$unique = $test->unique()->values()->all();

		foreach($unique as $key => $value)
		{
			$return[$key] = Tbl_item::where('archived', 0)->where('item_id', $value)->first();
			$return[$key]->item_qty = 1;
		}

		return $return;
	}
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_codes extends Model
{
	protected $table = 'tbl_codes';
	protected $primaryKey = "code_id";
    public $timestamps = false;


    public function scopeInventory($query)
    {
    	 return $query->join('tbl_inventory', 'tbl_inventory.inventory_id', '=', 'tbl_codes.code_inventory_id');
    }
    public function scopeInventoryItem($query)
    {
    	 return $query->join('tbl_item', 'tbl_item.item_id', '=', 'tbl_inventory.inventory_item_id');
    }
    public function scopeInventoryItemMembership($query)
    {
    	 return $query->join('tbl_membership', 'tbl_membership.membership_id', '=', 'tbl_item.membership_id');
    }
    
    public function scopeCheckIfKit($query)
    {
        return $query->where('tbl_item.item_type', 'membership_kit');
    }

    public function scopeCheckIfProduct($query)
    {
        return $query->where('tbl_item.item_type', 'product');
    }

    public function scopeCheckIfUsed($query, $param = 0)
    {
        return $query->where('tbl_codes.code_used', $param);
    }

    public function scopeCheckIfSold($query, $param = 0)
    {
        return $query->where('tbl_codes.code_sold', $param);
    }

    public function scopeFromMainBranch($query)
    {
        return $query->where('tbl_inventory.inventory_branch_id', 1);
    }

    public function scopeSoldTo($query, $id)
    {
        return $query->where("tbl_codes.code_sold_to", $id)->join("users", "tbl_codes.code_sold_to", "=", "users.id");
    }

    public function scopeUsedBy($query, $id)
    {
        return $query->where("tbl_codes.code_used_by", $id)->join("users", "tbl_codes.code_used_by", "=", "users.id");
    }

}

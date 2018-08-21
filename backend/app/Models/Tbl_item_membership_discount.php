<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_item_membership_discount extends Model
{
	protected $table = 'tbl_item_membership_discount';
	protected $primaryKey = "item_membership_discount_id";
    public $timestamps = false;
}

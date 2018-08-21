<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_item_stockist_discount extends Model
{
	protected $table = 'tbl_item_stockist_discount';
	protected $primaryKey = "item_stockist_discount_id";
    public $timestamps = false;
}

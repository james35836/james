<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_item_points extends Model
{
	protected $table = 'tbl_item_points';
	protected $primaryKey = "item_points_id";
    public $timestamps = false;
}

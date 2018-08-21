<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_points_log extends Model
{
	protected $table = 'tbl_points_log';
	protected $primaryKey = "points_log_id";
    public $timestamps = false;
}

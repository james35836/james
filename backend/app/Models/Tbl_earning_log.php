<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_earning_log extends Model
{
	protected $table = 'tbl_earning_log';
	protected $primaryKey = "earning_log_id";
    public $timestamps = false;
}

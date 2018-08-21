<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_service_charge extends Model
{
	protected $table = 'tbl_service_charge';
	protected $primaryKey = "service_id";
    public $timestamps = false;
}

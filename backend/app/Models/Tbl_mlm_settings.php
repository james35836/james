<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_mlm_settings extends Model
{
	protected $table = 'tbl_mlm_settings';
	protected $primaryKey = "mlm_settings_id";
    public $timestamps = false;
}

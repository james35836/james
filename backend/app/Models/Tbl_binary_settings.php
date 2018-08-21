<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_binary_settings extends Model
{
	protected $table = 'tbl_binary_settings';
	protected $primaryKey = "binary_settings_id";
    public $timestamps = false;
}

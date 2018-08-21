<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_country extends Model
{
	protected $table = 'tbl_country';
	protected $primaryKey = "country_id";
    public $timestamps = false;
}

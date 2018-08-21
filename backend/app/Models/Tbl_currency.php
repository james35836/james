<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_currency extends Model
{
	protected $table = 'tbl_currency';
	protected $primaryKey = "currency_id";
    public $timestamps = false;
}


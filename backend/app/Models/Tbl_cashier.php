<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_cashier extends Model
{
	protected $table = 'tbl_cashier';
	protected $primaryKey = "cashier_id";
    public $timestamps = false;
}


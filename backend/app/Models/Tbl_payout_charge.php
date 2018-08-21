<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_payout_charge extends Model
{
	protected $table = 'tbl_payout_charge';
	protected $primaryKey = "payout_charge_id";
    public $timestamps = false;
}

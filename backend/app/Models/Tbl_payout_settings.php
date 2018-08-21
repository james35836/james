<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_payout_settings extends Model
{
	protected $table = 'tbl_payout_settings';
	protected $primaryKey = "payout_settings_id";
    public $timestamps = false;
}

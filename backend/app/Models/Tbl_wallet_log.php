<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_wallet_log extends Model
{
	protected $table = 'tbl_wallet_log';
	protected $primaryKey = "wallet_log_id";
    public $timestamps = false;
}

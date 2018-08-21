<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_payout_method extends Model
{
	protected $table = 'tbl_payout_method';
	protected $primaryKey = "payout_method_id";
    public $timestamps = false;
}

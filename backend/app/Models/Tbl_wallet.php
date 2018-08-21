<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_wallet extends Model
{
	protected $table = 'tbl_wallet';
	protected $primaryKey = "wallet_id";
    public $timestamps = false;

    public function scopeCurrency($query)
    {
    	 return $query->join('tbl_currency', 'tbl_currency.currency_id', '=', 'tbl_wallet.currency_id');
    }

    public function scopePeso($query)
    {
    	return $query->where('tbl_currency.currency_id', 1);
    }
}

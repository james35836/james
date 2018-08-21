<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_cash_in_proofs extends Model
{
	protected $table = 'tbl_cash_in_proofs';
	protected $primaryKey = "cash_in_proof_id";
    public $timestamps = false;

    public function scopeMethod($query)
    {
		return $query->join('tbl_cash_in_method', 'tbl_cash_in_method.cash_in_method_id', '=', 'tbl_cash_in_proofs.cash_in_method_id');
    }

    public function scopeSlot($query)
    {
    	return $query->join('tbl_slot', 'tbl_slot.slot_no', '=', 'tbl_cash_in_proofs.cash_in_slot_code');
    }
}

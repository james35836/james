<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_slot extends Model
{
	protected $table = 'tbl_slot';
	protected $primaryKey = "slot_id";
    public $timestamps = false;

    public function scopeOwner($query)
    {
    	 return $query->join('users', 'users.id', '=', 'tbl_slot.slot_owner');
    }
}

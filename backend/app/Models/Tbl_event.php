<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_event extends Model
{
	protected $table = 'tbl_event';
	protected $primaryKey = "event_id";
    public $timestamps = false;

    public function scopeUserInfo($query)
    {
    	$query->join('users','users.id','=','tbl_event.event_posted_by');
    	return $query;
    }
}

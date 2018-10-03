<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_connection extends Model
{
	protected $table = 'tbl_connection';
	protected $primaryKey = "connection_id";
    public $timestamps = false;

    public function scopeUserInfo($query)
    {
    	$query->join('users','users.id','=','tbl_connection.story_posted_by');
    	return $query;
    }
}

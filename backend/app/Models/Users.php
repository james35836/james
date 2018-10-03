<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
	protected $table = 'users';
	protected $primaryKey = "id";
    public $timestamps = false;


    public function scopeUserInfo($query)
    {
    	$query->join('tbl_user_info','tbl_user_info.user_id','=','users.id');
    	return $query;
    }
}

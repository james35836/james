<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_job extends Model
{
	protected $table = 'tbl_job';
	protected $primaryKey = "job_id";
    public $timestamps = false;

    public function scopeUserInfo($query)
    {
    	$query->join('users','users.id','=','tbl_job.job_posted_by');
    	return $query;
    }
}

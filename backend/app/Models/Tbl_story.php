<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_story extends Model
{
	protected $table = 'tbl_story';
	protected $primaryKey = "story_id";
    public $timestamps = false;

    public function scopeUserInfo($query)
    {
    	$query->join('users','users.id','=','tbl_story.story_posted_by');
    	return $query;
    }
}

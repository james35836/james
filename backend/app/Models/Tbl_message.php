<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_message extends Model
{
	protected $table = 'tbl_message';
	protected $primaryKey = "message_id";
    public $timestamps = false;
}

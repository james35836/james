<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_branch extends Model
{
	protected $table = 'tbl_branch';
	protected $primaryKey = "branch_id";
    public $timestamps = false;
}

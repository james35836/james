<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_school_year extends Model
{
	protected $table = 'tbl_school_year';
	protected $primaryKey = "school_year_id";
    public $timestamps = false;
}

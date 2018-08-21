<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_company extends Model
{
	protected $table = 'tbl_company';
	protected $primaryKey = "company_id";
    public $timestamps = false;
}

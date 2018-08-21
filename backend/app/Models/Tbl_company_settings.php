<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_company_settings extends Model
{
	protected $table = 'tbl_company_settings';
	protected $primaryKey = "company_settings_id";
    public $timestamps = false;
}

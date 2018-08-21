<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_remittance extends Model
{
	protected $table = 'tbl_remittance';
	protected $primaryKey = "remittance_id";
    public $timestamps = false;
}

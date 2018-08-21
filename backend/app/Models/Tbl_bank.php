<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tbl_bank extends Model
{
	protected $table = 'tbl_bank';
	protected $primaryKey = "bank_id";
    public $timestamps = false;
}

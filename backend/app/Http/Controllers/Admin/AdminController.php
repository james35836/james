<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use File;
use Storage;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

use App\Globals\Membership;
use App\Globals\Item;
use App\Globals\Slot;
use App\Globals\Wallet;
use App\Models\Tbl_slot;

use Illuminate\Http\Request as Request2;

class AdminController extends Controller
{
    function __construct()
    {

    }

    public function user_data()
    {
        
    	return Request::user();
    }

    

	

  
}
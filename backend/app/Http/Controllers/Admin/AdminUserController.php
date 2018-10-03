<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use App\Models\Users;

class AdminUserController extends Controller
{
    public function get_users()
    {
    	$response = Users::UserInfo()->get();
        return response()->json($response);
    }



    

	

  
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class SecretController extends Controller
{
    public function get()
    {
    	return response()->json(DB::table('oauth_clients')->where('id', 2)->first());
    }

    public function logout() 
    {
	    $accessToken = Auth::user()->token();
	    DB::table('oauth_refresh_tokens')
	        ->where('access_token_id', $accessToken->id)
	        ->update([
	            'revoked' => true
	        ]);

	    $accessToken->revoke();
	    return response()->json(null, 204);
	}
}

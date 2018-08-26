<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use DB;
use Storage;
use App\Globals\Messages;

use Illuminate\Http\Request as Request2;

class MemberController extends Controller
{
    function __construct()
    {

    }

    public function user_data()
    {
        if(isset(Request::user()->id))
        {
            if(Request::user()->type == "member")
            {
                /*SOME HERE*/
            }   
        }

    	return Request::user();
    }

    public function upload_image(Request2 $request)
    {
        $file = $request->file('upload');

        $path_prefix = 'https://digima.sgp1.digitaloceanspaces.com/';
        $path = "mlm/".Request::input('folder');
        $storage_path = storage_path();

        if ($file->isValid())
        {
            $full_path = Storage::disk('s3')->putFile($path, $file, "public");
            $url = Storage::disk('s3')->url($full_path);
            return json_encode($url);
        }
    }
    public function messages()
    {
        $response   = Messages::get();
        return response()->json($response, 200);
    }
    public function message_submit()
    {
        $response   = Messages::submit(Request::input());
        return response()->json($response, 200);
    }


    
}
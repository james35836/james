<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Request;
use App\Models\Tbl_story;
use Carbon\Carbon;

class AdminStoryController extends Controller
{
    public function get_story()
    {
    	$response = Tbl_story::UserInfo()->get();
    	foreach($response as $key=>$res)
    	{
    		$response[$key]['story_created_format'] = date("F j, Y",strtotime($res->story_created));
    	}
        return response()->json($response);
    }

    public function create_submit()
    {
    	$data                   = Request::all();
    	$rules["story_by"]    	= "required";
		$rules["story_title"]   = "required";
		$rules["story_body"]   	= "required";
		$rules["story_qoute"] 	= "required";

		$validator = Validator::make($data, $rules);
		if($validator->fails()) 
        {
            $return["status"]         = "error"; 
			$return["status_code"]    = 400; 
			$return["status_message"] = [];

			$i = 0;
			$len = count($validator->errors()->getMessages());

			foreach ($validator->errors()->getMessages() as $key => $value) 
			{
				foreach($value as $val)
				{
					$return["status_message"][$i] = $val;

				    $i++;		
				}
			}
        }
        else
        {
        	$insert["story_by"]				= $data["story_by"];
			$insert["story_title"]			= $data["story_title"];
			$insert["story_body"]			= $data["story_body"];
			$insert["story_qoute"]			= $data["story_qoute"];
			$insert["story_created"]		= Carbon::now();
			$insert["story_posted_by"]		= Request::user()->id;

			Tbl_story::insert($insert);

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Story Added Successfully";
	 	}

	 	return $return;
    	
    }




    

	

  
}
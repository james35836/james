<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Request;
use App\Models\Tbl_event;
use Carbon\Carbon;

class AdminEventController extends Controller
{
    public function get_event()
    {
    	$response = Tbl_event::UserInfo()->get();
    	foreach($response as $key=>$res)
    	{
    		$response[$key]['event_date_format'] 	= date("F j, Y",strtotime($res->event_date));
    		$response[$key]['event_created_format'] = date("F j, Y",strtotime($res->event_created));
    	}
        return response()->json($response);
    }

    public function create_submit()
    {
    	$data                           = Request::all();
    	$rules["event_name"]    		= "required";
		$rules["event_description"]    	= "required";
		$rules["event_date"]    		= "required";
		$rules["event_time"] 			= "required";
		$rules["event_venue"]    		= "required";
		


		

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
        	$insert["event_name"]			= $data["event_name"];
			$insert["event_description"]	= $data["event_description"];
			$insert["event_date"]			= $data["event_date"];
			$insert["event_time"]			= $data["event_time"];
			$insert["event_venue"]			= $data["event_venue"];
			// $insert["event_photo"]			= $data["event_photo"];
			$insert["event_created"]		= Carbon::now();

			if(isset($data["event_facebook"]))
			{
				$insert["event_facebook"]		= $data["event_facebook"];
			}
			if(isset($data["event_twitter"]))
			{
				$insert["event_twitter"]		= $data["event_twitter"];
			}
			if(isset($data["event_linkedin"]))
			{
				$insert["event_linkedin"]		= $data["event_linkedin"];
			}
			
			$insert["event_posted_by"]		= Request::user()->id;

			Tbl_event::insert($insert);

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Event Added Successfully";
	 	}


	 	return $return;
    	
    }




    

	

  
}
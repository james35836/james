<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Auth;
use Validator;
use Request;
use App\Models\Tbl_job;
use Carbon\Carbon;

class AdminCarrerController extends Controller
{
    public function get_carrer()
    {
    	$response = Tbl_job::UserInfo()->get();
    	foreach($response as $key=>$res)
    	{
    		$response[$key]['job_created_format'] = date("F j, Y",strtotime($res->job_created));
    	}
        return response()->json($response);
    }

    public function create_submit()
    {
    	$data                   = Request::all();

    	$rules["job_company_name"]    	= "required";
		$rules["job_title"]   			= "required";
		$rules["job_description"]   	= "required";
		$rules["job_contact_person"] 	= "required";
		$rules["job_contact_email"]   	= "required";
		$rules["job_date"] 				= "required";
		$rules["job_site"]   			= "required";
		

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
        	$insert["job_company_name"]		= $data["job_company_name"];
			$insert["job_title"]			= $data["job_title"];
			$insert["job_description"]		= $data["job_description"];
			$insert["job_contact_person"]	= $data["job_contact_person"];
			$insert["job_contact_email"]	= $data["job_contact_email"];
			$insert["job_date"]				= $data["job_date"];
			$insert["job_site"]				= $data["job_site"];

			$insert["job_created"]			= Carbon::now();
			$insert["job_posted_by"]		= Request::user()->id;

			Tbl_job::insert($insert);

			$return["status"]         = "success"; 
			$return["status_code"]    = 201; 
			$return["status_message"] = "Carrer Added Successfully";
	 	}

	 	return $return;
    	
    }




    

	

  
}
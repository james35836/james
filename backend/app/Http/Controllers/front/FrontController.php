<?php

namespace App\Http\Controllers\Front;

use Illuminate\Routing\Controller;


use DB;
use Request;
use Validator;

use App\Models\Tbl_story;
use App\Models\Tbl_event;
use App\Models\Tbl_job;
use Mail;

class FrontController extends Controller
{
    function get_event()
    {
    	$res = Tbl_event::UserInfo()->get();
    	foreach($res as $key=>$r)
    	{
    		$res[$key]['event_day'] 	= date('j',strtotime($r->event_date));
    		$res[$key]['event_month'] = date('M',strtotime($r->event_date));
    		$res[$key]['event_created_format'] = date("F j, Y",strtotime($r->event_created));
    	}
        return response()->json($res,200);
    }

    function get_story()
    {
        $response = Tbl_story::UserInfo()->get();
        foreach($response as $key=>$res)
        {
            $response[$key]['story_created_format'] = date("F j, Y",strtotime($res->story_created));
        }
        return response()->json($response);
    }

    function get_carrer()
    {
        $response = Tbl_job::UserInfo()->get();
        foreach($response as $key=>$res)
        {
            $response[$key]['job_created_format'] = date("F j, Y",strtotime($res->job_created));
        }
        return response()->json($response);
    }

    function submit_email()
    {
        
        $data                   = Request::all();
        
        $rules["guest_name"]      = "required";
        $rules["guest_email"]   = "required";
        $rules["guest_phone"]    = "required";
        $rules["guest_message"]   = "required";
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
            $new   = array('name'=>"Virat Gandhi",'message'=>"Virat Gandhi",'phone'=>"Virat Gandhi",'email'=>"Virat Gandhi");

            Mail::send('email.customer_email_front',$data, function($sending)
            {
                $sending->to('jamesomosora@gmail.com','Support')->subject('Support');
                $sending->from('carewelladmin@admin.com','Alumni Website(Front)');
            });

            if(Mail::failures())
            {
                $return["status"]         = "error"; 
                $return["status_code"]    = 400; 
                $return["status_message"] = "Failed to send message!";
            }
            else
            {
                $return["status"]         = "success"; 
                $return["status_code"]    = 201; 
                $return["status_message"] = "Message Send";
            }
        }

        return $return;
    }
}

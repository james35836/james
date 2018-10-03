<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;


use App\Models\Tbl_school_year;

class SchoolYearController extends Controller
{
      function get_school_year(Request $request)
      {
              
            $res = Tbl_school_year::get();
            return response()->json($res,200);

      }
}

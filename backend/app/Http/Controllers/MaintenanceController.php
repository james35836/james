<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

class MaintenanceController extends Controller
{
    function maintenance_insert_data(Request $request)
    {
        if (DB::table('tbl_school_year')->count() <= 0) 
        {
            $year[0]["school_year_id"]     = 1;
            $year[0]["school_year"]        = "1973-1974";

            $year[1]["school_year_id"]     = 2;
            $year[1]["school_year"]        = "1974-1975";

            $year[2]["school_year_id"]     = 3;
            $year[2]["school_year"]        = "1975-1976";

            $year[3]["school_year_id"]     = 4;
            $year[3]["school_year"]        = "1976-1977";

            $year[4]["school_year_id"]     = 5;
            $year[4]["school_year"]        = "1977-1978";

            $year[5]["school_year_id"]     = 6;
            $year[5]["school_year"]        = "1978-1979";

            $year[6]["school_year_id"]     = 7;
            $year[6]["school_year"]        = "1979-1980";

            $year[7]["school_year_id"]     = 8;
            $year[7]["school_year"]        = "1980-1981";

            $year[8]["school_year_id"]     = 9;
            $year[8]["school_year"]        = "1981-1982";

            $year[9]["school_year_id"]     = 10;
            $year[9]["school_year"]        = "1982-1983";

            $year[10]["school_year_id"]     = 11;
            $year[10]["school_year"]        = "1983-1984";

            $year[11]["school_year_id"]     = 12;
            $year[11]["school_year"]        = "1984-1985";

            $year[12]["school_year_id"]     = 13;
            $year[12]["school_year"]        = "1985-1986";

            $year[13]["school_year_id"]     = 14;
            $year[13]["school_year"]        = "1986-1987";

            $year[14]["school_year_id"]     = 15;
            $year[14]["school_year"]        = "1987-1988";

            $year[15]["school_year_id"]     = 16;
            $year[15]["school_year"]        = "1988-1989";

            $year[16]["school_year_id"]     = 17;
            $year[16]["school_year"]        = "1989-1990";

            $year[17]["school_year_id"]     = 18;
            $year[17]["school_year"]        = "1990-1991";

            $year[18]["school_year_id"]     = 19;
            $year[18]["school_year"]        = "1991-1992";

            $year[19]["school_year_id"]     = 20;
            $year[19]["school_year"]        = "1992-1993";

            $year[20]["school_year_id"]     = 21;
            $year[20]["school_year"]        = "1993-1994";

            $year[21]["school_year_id"]     = 22;
            $year[21]["school_year"]        = "1994-1995";

            $year[22]["school_year_id"]     = 23;
            $year[22]["school_year"]        = "1995-1996";

            $year[23]["school_year_id"]     = 24;
            $year[23]["school_year"]        = "1996-1997";

            $year[24]["school_year_id"]     = 25;
            $year[24]["school_year"]        = "1997-1998";

            $year[25]["school_year_id"]     = 26;
            $year[25]["school_year"]        = "1998-1999";

            $year[26]["school_year_id"]     = 27;
            $year[26]["school_year"]        = "1999-2000";

            $year[27]["school_year_id"]     = 28;
            $year[27]["school_year"]        = "2000-2001";

            $year[28]["school_year_id"]     = 29;
            $year[28]["school_year"]        = "2001-2002";

            $year[29]["school_year_id"]     = 30;
            $year[29]["school_year"]        = "2002-2003";

            $year[30]["school_year_id"]     = 31;
            $year[30]["school_year"]        = "2003-2004";

            $year[31]["school_year_id"]     = 32;
            $year[31]["school_year"]        = "2004-2005";

            $year[32]["school_year_id"]     = 33;
            $year[32]["school_year"]        = "2005-2006";

            $year[33]["school_year_id"]     = 34;
            $year[33]["school_year"]        = "2006-2007";

            $year[34]["school_year_id"]     = 35;
            $year[34]["school_year"]        = "2007-2008";

            $year[35]["school_year_id"]     = 36;
            $year[35]["school_year"]        = "2008-2009";

            $year[36]["school_year_id"]     = 37;
            $year[36]["school_year"]        = "2009-2010";

            $year[37]["school_year_id"]     = 38;
            $year[37]["school_year"]        = "2010-2011";

            $year[38]["school_year_id"]     = 39;
            $year[38]["school_year"]        = "2011-2012";

            $year[39]["school_year_id"]     = 40;
            $year[39]["school_year"]        = "2012-2013";

            $year[40]["school_year_id"]     = 41;
            $year[40]["school_year"]        = "2013-2014";

            $year[41]["school_year_id"]     = 42;
            $year[41]["school_year"]        = "2014-2015";

            $year[42]["school_year_id"]     = 43;
            $year[42]["school_year"]        = "2015-2016";

            $year[43]["school_year_id"]     = 44;
            $year[43]["school_year"]        = "2016-2017";

            $year[44]["school_year_id"]     = 45;
            $year[44]["school_year"]        = "2017-2018";

            $year[45]["school_year_id"]     = 46;
            $year[45]["school_year"]        = "2018-2019";

            $year[46]["school_year_id"]     = 47;
            $year[46]["school_year"]        = "2019-2020";

            $year[47]["school_year_id"]     = 48;
            $year[47]["school_year"]        = "2020-2021";

            $year[48]["school_year_id"]     = 49;
            $year[48]["school_year"]        = "2021-2022";

            $year[49]["school_year_id"]     = 50;
            $year[49]["school_year"]        = "2022-2023";

            $year[50]["school_year_id"]     = 51;
            $year[50]["school_year"]        = "2023-2024";

            $year[51]["school_year_id"]     = 52;
            $year[51]["school_year"]        = "2024-2025";

            $year[52]["school_year_id"]     = 53;
            $year[52]["school_year"]        = "2025-2026";

            $year[53]["school_year_id"]     = 54;
            $year[53]["school_year"]        = "2026-2027";

            $year[54]["school_year_id"]     = 55;
            $year[54]["school_year"]        = "2027-2028";

            $year[55]["school_year_id"]     = 56;
            $year[55]["school_year"]        = "2028-2029";

            $year[56]["school_year_id"]     = 57;
            $year[56]["school_year"]        = "2029-2030";

            $year[57]["school_year_id"]     = 58;
            $year[57]["school_year"]        = "2030-2031";

            DB::table('tbl_school_year')->insert($year);
        }
        
    }
}

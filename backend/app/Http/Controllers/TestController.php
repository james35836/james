<?php

namespace App\Http\Controllers;

use App\Globals\Seed;
use App\Globals\Wizard;
use App\Globals\MlmSettings;
use App\Globals\Code;
use App\Globals\Item;
use App\Globals\MLM;
use App\Globals\Mlm_complan_manager_repurchase;

use App\Models\Tbl_slot;

use Carbon\Carbon;
use Crypt;
class TestController extends Controller
{
    public function test_generate()
    {
        dd(123);
        $slot_info = Tbl_slot::where("slot_id",17)->first();
        Mlm_complan_manager_repurchase::stairstep($slot_info,100);
    }

    public function add_item()
    {
        $data["item_sku"]          = "sample sku";
        $data["item_description"]  = "sample desc";
        $data["item_barcode"]      = "32131";
        $data["item_price"]        = 1000;
        $data["item_gc_price"]     = 2000;
        $data["item_type"]         = "membership_kit";
        $data["membership_id"]     = 1;
        $data["slot_qty"]          = 1;
        $data["inclusive_gc"]      = 100;
        $data["quantity"]          = "";
        $data["item_kit_fix"]      = null;

        dd(Item::add($data));
    }

    public function seed()
    {
        Seed::initial_seed();
    }

    public function wizard_one()
    {
        $insert["admin_username"]               = "Sample";
        $insert["admin_password"]               = "123123";
        $insert["admin_rpassword"]              = "123123";
        $insert["admin_first_name"]             = "Erwin";
        $insert["admin_last_name"]              = "Guevarra";
        $insert["admin_email"]                  = "guevarra129@gmail.com";
        $insert["admin_contact"]                = "09354666344";
        $insert["admin_date_created"]           = Carbon::now();

        $insert_company["company_name"]         = "Sample Company";
        $insert_company["company_contact"]      = "8785641113";
        $insert_company["company_address"]      = "Sample Address";
        $insert_company["company_office_hours"] = "8 hours";

        dd(Wizard::step_one($insert,$insert_company));
    }

    public function wizard_two()
    {
        $insert["country_id"]               = 1;
        $insert["base_currency"]            = 1;
        $insert["allow_multiple_currency"]  = 1;

        // 1 / 2 is ID of a country samples only
        $insert_country[1] = 1;
        $insert_country[2] = 50;

        dd(Wizard::step_two($insert,$insert_country));
    }

    public function wizard_three()
    {
        $insert["binary_enabled"]               = 1;
        $insert["auto_placement"]               = 1;
        $insert["auto_placement_type"]          = "left_to_right"; 
        $insert["member_disable_auto_position"] = 1; 
        $insert["member_default_position"]      = 1; 
        $insert["mlm_slot_no_format_type"]      = 1; 

        dd(Wizard::step_three($insert));
    }

    public function wizard_four()
    {
        $insert["free_registration"]            = 1;
        $insert["multiple_type_membership"]     = 1;
        $insert["gc_inclusive_membership"]      = 1;
        $insert["product_inclusive_membership"] = 1;


        dd(Wizard::step_four($insert));
    }

    public function wizard_five()
    {
        $insert[1] = "BINARY";
        $insert[2] = "DIRECT";


        dd(Wizard::step_five($insert));
    }

    public function wizard_five_one()
    {
        $insert[0]["membership_id"]              = 1;
        $insert[0]["membership_entry_id"]        = 1;
        $insert[0]["membership_direct_income"]   = 500;

        $insert[1]["membership_id"]              = 1;
        $insert[1]["membership_entry_id"]        = 2;
        $insert[1]["membership_direct_income"]   = 500;

        $insert[2]["membership_id"]              = 1;
        $insert[2]["membership_entry_id"]        = 3;
        $insert[2]["membership_direct_income"]   = 1000;

        $insert[2]["membership_id"]              = 2;
        $insert[2]["membership_entry_id"]        = 1;
        $insert[2]["membership_direct_income"]   = 1000;



        dd(Wizard::step_five_one($insert));
    }

    public function wizard_five_two()
    {
        $insert[0]["membership_level"]           = 1;
        $insert[0]["membership_id"]              = 1;
        $insert[0]["membership_entry_id"]        = 1;
        $insert[0]["membership_indirect_income"] = 500;

        $insert[1]["membership_level"]           = 1;
        $insert[1]["membership_id"]              = 1;
        $insert[1]["membership_entry_id"]        = 2;
        $insert[1]["membership_indirect_income"] = 600;

        $insert[2]["membership_level"]           = 1;
        $insert[2]["membership_id"]              = 1;
        $insert[2]["membership_entry_id"]        = 3;
        $insert[2]["membership_indirect_income"] = 700;



        dd(Wizard::step_five_two($insert));
    }

    public function wizard_five_three()
    {
        $insert_plan_setting["strong_leg_retention"]    = 0;
        $insert_plan_setting["gc_pairing_count"]        = 5;
        $insert_plan_setting["cycle_per_day"]           = 1;
 
        $insert[0]["membership_id"]                     = 1;
        $insert[0]["membership_entry_id"]               = 1;
        $insert[0]["membership_binary_points"]          = 500;
 
        $insert[1]["membership_id"]                     = 1;
        $insert[1]["membership_entry_id"]               = 2;
        $insert[1]["membership_binary_points"]          = 500;
 
        $insert[2]["membership_id"]                     = 1;
        $insert[2]["membership_entry_id"]               = 3;
        $insert[2]["membership_binary_points"]          = 1000;
 
        $insert[2]["membership_id"]                     = 2;
        $insert[2]["membership_entry_id"]               = 1;
        $insert[2]["membership_binary_points"]          = 1000;

        $insert_combination[0]["binary_pairing_left"]   = 1;
        $insert_combination[0]["binary_pairing_right"]  = 1;
        $insert_combination[0]["binary_pairing_bonus"]  = 500;

        $insert_combination[1]["binary_pairing_left"]   = 2;
        $insert_combination[1]["binary_pairing_right"]  = 2;
        $insert_combination[1]["binary_pairing_bonus"]  = 1000;

        dd(Wizard::step_five_three($insert_plan_setting,$insert,$insert_combination));
    }

    public function wizard_five_four()
    {
        $data["personal_as_group"]         = 0;
        $data["gpv_to_wallet_conversion"]  = 5;
 
        $data_membership[0]["membership_id"]           = 1;
        $data_membership[0]["membership_required_pv"]  = 1;


        dd(Wizard::step_five_four($data,$data_membership));
    }

    public function wizard_five_five()
    {
        $data["personal_as_group"] = 1;
        $data["live_update"]       = 1;
        $data["allow_downgrade"]   = 1;
        $data["rank_first"]        = 1;


        dd(Wizard::step_five_five($data));
    }

    public function unilevel_save_settings()
    {
        $data[0]["membership_level"]      = 1;   
        $data[0]["membership_id"]         = 1;       
        $data[0]["membership_entry_id"]   = 1;   
        $data[0]["membership_percentage"] = 39;  

        $data[1]["membership_level"]      = 2;   
        $data[1]["membership_id"]         = 1;       
        $data[1]["membership_entry_id"]   = 1;   
        $data[1]["membership_percentage"] = 20;    

        dd(MlmSettings::unilevel_save_settings($data));
    }

}
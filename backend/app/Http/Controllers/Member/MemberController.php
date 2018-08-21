<?php
namespace App\Http\Controllers\Member;
use App\Http\Controllers\Controller;
use Auth;
use Request;
use DB;
use Storage;

use App\Models\Tbl_slot;
use App\Models\Tbl_wallet_log;
use App\Models\Tbl_wallet;
use App\Models\Tbl_currency;
use App\Models\Tbl_service_charge;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

use App\Globals\Slot;
use App\Globals\Wallet;
use App\Globals\CashIn;

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
                $check_has_slot = Tbl_slot::where("slot_owner",Request::user()->id)->first();
                if(!$check_has_slot)
                {
                    Slot::create_blank_slot(Request::user()->id);
                }
            }   
        }

    	return Request::user();
    }

    public function wallet_log()
    {
        $slot                    = Tbl_slot::where("slot_owner",Request::user()->id)->where("slot_id",Request::input("slot_id"))->first();
        $data                    = null;
        $running_balance         = 0;

        if($slot)
        {
            $data = Tbl_wallet_log::where("wallet_log_slot_id",$slot->slot_id)
                                    ->select("*",DB::raw("DATE_FORMAT(tbl_wallet_log.wallet_log_date_created, '%m/%d/%Y') as wallet_log_date_created"))
                                    ->get();                
        }

        $wallet["total_running"] = number_format(Tbl_wallet_log::where("wallet_log_slot_id",$slot->slot_id)->first() ? Tbl_wallet_log::where("wallet_log_slot_id",$slot->slot_id)->orderBy("wallet_log_id","DESC")->first()->wallet_log_running_balance : 0,2); 
        $wallet["wallet"]        = $data;
        
        return json_encode($wallet);
    }

    public function current_slot()
    {
        if (Request::input('slot_id')) 
        {
            $slot = Tbl_slot::where("slot_owner", Request::user()->id)->where("slot_id", Request::input('slot_id'))->first();
            if(!$slot)
            {
                $slot = Tbl_slot::where("slot_owner", Request::user()->id)->first();
            }
        }
        else
        {
            $slot = Tbl_slot::where("slot_owner", Request::user()->id)->first();
        }

        
        $wallet = 0;
        if($slot)
        {

            $wallet_info = Tbl_wallet::currency()->where("slot_id",$slot->slot_id)->get();
            if($wallet_info)
            {
                Wallet::generateSlotWalletAddress($slot->slot_id);

                $wallet = [];
                
                foreach ($wallet_info as $key => $value) 
                {
                    $wallet_info[$key]["wallet"] = Tbl_wallet::currency()->where("currency_abbreviation", $value->currency_abbreviation)->first();
                }
            }

            $slot->get_wallets = $wallet_info;
        }

        return json_encode($slot);
    }

    public function add_slot()
    {
        $data["pin"]           = Request::input("pin");
        $data["code"]          = Request::input("code");
        $data["slot_sponsor"]  = Request::input("slot_sponsor");
        $data["slot_owner"]    = Request::user()->id;

        $response              = Slot::create_slot($data);
        return $response;
    }

    public function all_slot()
    {
        $slot = Tbl_slot::where("slot_owner", Request::user()->id)->get();

        foreach ($slot as $key => $value) 
        {
            $sponsor = Tbl_slot::where("slot_owner", $value->slot_sponsor)->first();

            $slot[$key]->sponsor = $sponsor;
        }

        return json_encode($slot);
    }

    public function test()
    {
    	dd(Request::user());
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

    public function get_service_charge()
    {
        $data = Tbl_service_charge::where("service_name", Request::input('service'))->first();
        return json_encode($data);
    }

}
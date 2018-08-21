<?php
namespace App\Http\Controllers\Admin;

use Request;
use PDF;
use Excel;
use App\Globals\Slot;

class AdminExportController extends AdminController
{
	public function slot_wallet_history_pdf()
	{	
		$data = $this->slot_wallet_history();

		$pdf = PDF::loadView('export.pdf.slot_wallet_history', $data);
		return $pdf->download('slot_wallet_history.pdf');
	}

	public function slot_wallet_history_csv()
	{
		$data = $this->slot_wallet_history();

        Excel::create("slot_wallet_history" . "_" . (isset($data["_wallet"][0]->slot_no) ? ($data["_wallet"][0]->slot_no) : "slot_wallet_history"), function($excel) use ($data)
        {
            $excel->sheet("slot_wallet_history", function($sheet) use ($data)
            {
                $sheet->setOrientation('landscape');
                $sheet->loadView('export.csv.slot_wallet_history', $data);
            });
            
        })->export('csv');
	}

	public function slot_wallet_history()
	{
		$data["_wallet"] = Slot::get_slot_wallet(Request::input());
		$data["total_wallet"] = 0;

		foreach ($data["_wallet"] as $key => $value) 
		{
			$data["total_wallet"] += $value->wallet_log_amount;
		}

		return $data;
	}

	public function slot_payout_history_pdf()
	{	
		$data = $this->slot_payout_history();

		$pdf = PDF::loadView('export.pdf.slot_payout_history', $data);
		return $pdf->download('slot_payout_history.pdf');
	}

	public function slot_payout_history_csv()
	{
		$data = $this->slot_payout_history();

        Excel::create("slot_payout_history" . "_" . (isset($data["_payout"][0]->slot_no) ? ($data["_payout"][0]->slot_no) : "slot_payout_history"), function($excel) use ($data)
        {
            $excel->sheet("slot_payout_history", function($sheet) use ($data)
            {
                $sheet->setOrientation('landscape');
                $sheet->loadView('export.csv.slot_payout_history', $data);
            });
            
        })->export('csv');
	}

	public function slot_payout_history()
	{
		$data["_payout"] = Slot::get_slot_payout(Request::input());
		$data["total_payout"] = 0;

		foreach ($data["_payout"] as $key => $value) 
		{
			$data["total_payout"] += $value->wallet_log_amount;
		}

		return $data;
	}
}

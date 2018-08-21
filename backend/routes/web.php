<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test_seed',"TestController@seed"); 
Route::get('/test_wizard',"TestController@wizard_five_five"); 
Route::get('/test_generate',"TestController@test_generate"); 
Route::get('/add_item',"TestController@add_item"); 

/* Export */
Route::get('/export/slot_wallet_history/pdf', "Admin\AdminExportController@slot_wallet_history_pdf");
Route::get('/export/slot_wallet_history/csv', "Admin\AdminExportController@slot_wallet_history_csv");
Route::get('/export/slot_payout_history/pdf', "Admin\AdminExportController@slot_payout_history_pdf");
Route::get('/export/slot_payout_history/csv', "Admin\AdminExportController@slot_payout_history_csv");
Route::get('/export/slot_network_list/pdf', "Admin\AdminExportController@slot_network_list_pdf");
Route::get('/export/slot_network_list/csv', "Admin\AdminExportController@slot_network_list_csv");
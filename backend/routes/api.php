<?php
 
Route::group(['middleware' => ['auth:api']], function() 
{
    // ONLY MEMBER AND ADMIN ACCOUNT ALLOWED

	Route::get('/user_data', 'Admin\AdminController@user_data');
    Route::post('/image/upload', 'Member\MemberController@upload_image');
    Route::post('/service/charge', 'Member\MemberController@get_service_charge');
    Route::post('/logout', 'SecretController@logout');

    //CASH IN
    Route::post('/cashin/get_transactions', 'Member\MemberCashInController@get_transactions');
    Route::post('/cashin/get_method_list', 'Member\MemberCashInController@get_method_list');

});

Route::group(['middleware' => ['auth:api', 'admin']], function() 
{
    // ONLY ADMIN ACCOUNT ALLOWED
    Route::post('/get_membership', 'Admin\AdminController@get_membership');
    Route::post('/get_product', 'Admin\AdminController@get_product');
    Route::post('/admin/get_random_code', 'Admin\AdminCodeController@get_random_code');
    
    /* PRODUCT */
    Route::post('/product/add', 'Admin\AdminProductController@add');
    Route::post('/product/edit', 'Admin\AdminProductController@edit');
    Route::post('/product/get', 'Admin\AdminProductController@get');
    Route::post('/product/restock', 'Admin\AdminProductController@restock');
    Route::post('/product/get_inventory', 'Admin\AdminProductController@get_inventory');
    Route::post('/product/get_item_inventory', 'Admin\AdminProductController@get_item_inventory');
    Route::post('/product/get_item_code', 'Admin\AdminProductController@get_item_code');
    Route::post('/product/archive', 'Admin\AdminProductController@archive');
    Route::post('/product/add_item', 'Admin\AdminProductController@add_item');
    Route::post('/product/archive', 'Admin\AdminProductController@archive');
    Route::post('/product/data', 'Admin\AdminProductController@data');

    //AdminBranch Routes
    Route::post('/cashier/add_branch', 'Admin\AdminBranchController@add_branch');
    Route::post('/cashier/get_branch', 'Admin\AdminBranchController@get_branch');
    Route::post('/cashier/data', 'Admin\AdminBranchController@data');
    Route::post('/cashier/archive', 'Admin\AdminBranchController@archive');
    Route::post('/cashier/edit', 'Admin\AdminBranchController@edit');
    Route::post('/cashier/search', 'Admin\AdminBranchController@search');
    Route::post('/cashier/get_stockist', 'Admin\AdminBranchController@get_stockist');
    Route::post('/cashier/add_stockist_level', 'Admin\AdminBranchController@add_stockist_level');
    Route::post('/cashier/archive_stockist_level', 'Admin\AdminBranchController@archive_stockist_level');

    //AdminCashier
	Route::post('/cashier/add_cashier', 'Admin\AdminCashierController@add_cashier');
	Route::post('/cashier/get_cashier', 'Admin\AdminCashierController@get_cashierList');
	Route::post('/cashier/edit_cashier', 'Admin\AdminCashierController@edit_cashier');
	Route::post('/cashier/edit_cashier_submit', 'Admin\AdminCashierController@edit_cashier_submit');
    Route::post('/cashier/add_location', 'Admin\AdminCashierController@add_location');
    Route::post('/cashier/get_location', 'Admin\AdminCashierController@get_location');
    Route::post('/cashier/archive_location', 'Admin\AdminCashierController@archive_location');

	//AdminCode
	Route::post('/cashier/generate_codes', 'Admin\AdminCodeController@generate_codes');
    Route::post('/cashier/get_codes', 'Admin\AdminCodeController@get_codes');
    Route::post('/cashier/delete_code', 'Admin\AdminCodeController@delete_code');

    //AdminPayout
    Route::post('/payout/charge_settings', 'Admin\AdminPayoutController@charge_settings');
    Route::post('/payout/get_charge_settings', 'Admin\AdminPayoutController@get_charge_settings');
    Route::post('/payout/payout_configuration', 'Admin\AdminPayoutController@payout_configuration');

    /* COUNTRY */
    Route::post('/country/get', 'Admin\AdminCountryController@get');

    
    /* MEMBER */
    Route::post('/member/get', 'Admin\AdminMemberController@get');
    Route::post('/member/add_member', 'Admin\AdminMemberController@add');
    Route::post('/member/add_slot', 'Admin\AdminMemberController@add_slot');
    Route::post('/member/place_slot', 'Admin\AdminMemberController@place_slot');
    Route::post('/member/get_slot_information', 'Admin\AdminMemberController@get_slot_information');
    Route::post('/member/submit_slot_information', 'Admin\AdminMemberController@submit_slot_information');
    Route::post('/member/get_slot_details', 'Admin\AdminMemberController@get_slot_details');
    Route::post('/member/get_slot_earnings', 'Admin\AdminMemberController@get_slot_earnings');
    Route::post('/member/get_slot_distributed', 'Admin\AdminMemberController@get_slot_distributed');
    Route::post('/member/get_slot_wallet', 'Admin\AdminMemberController@get_slot_wallet');
    Route::post('/member/get_slot_payout', 'Admin\AdminMemberController@get_slot_payout');
    Route::post('/member/get_slot_points', 'Admin\AdminMemberController@get_slot_points');
    Route::post('/member/get_slot_network', 'Admin\AdminMemberController@get_slot_network');
    Route::post('/member/get_slot_codevault', 'Admin\AdminMemberController@get_slot_codevault');

    /* SLOT */
    Route::post('/slot/get', 'Admin\AdminSlotController@get');
    Route::post('/slot/get_full', 'Admin\AdminSlotController@get_full');
    Route::post('/slot/get_unplaced', 'Admin\AdminSlotController@get_unplaced');
    Route::post('/slot/get_unplaced', 'Admin\AdminSlotController@get_unplaced');

    /* MEMBERSHIP */
    Route::post('/membership/get', 'Admin\AdminMembershipController@get');
    Route::post('/membership/submit', 'Admin\AdminMembershipController@submit');

    /* PLAN */
    Route::post('/plan/get', 'Admin\AdminPlanController@get');
    Route::post('/plan/update', 'Admin\AdminPlanController@update');
    Route::post('/plan/update_status', 'Admin\AdminPlanController@update_status');
    Route::post('/plan/update_status', 'Admin\AdminPlanController@update_status');

    /* CASH IN */
    Route::post('/cashin/get_method_category_list', 'Admin\AdminCashInController@get_method_category_list');
    Route::post('/cashin/add_new_method', 'Admin\AdminCashInController@add_new_method');
    Route::post('/cashin/update_method', 'Admin\AdminCashInController@update_method');
    Route::post('/cashin/archive_method', 'Admin\AdminCashInController@archive_method');
    Route::post('/cashin/process_transaction', 'Admin\AdminCashInController@process_transaction');
    Route::post('/cashin/process_all_transaction', 'Admin\AdminCashInController@process_all_transaction');


    /*UPLOAD*/

});

Route::group(['middleware' => ['auth:api', 'member']], function() 
{
    
    Route::post('/messages', 'Member\MemberController@messages');
    Route::post('/messages_submit', 'Member\MemberController@message_submit'); 

});

Route::get('/client_secret', 'SecretController@get');
Route::post('/get_country',"RegisterController@get_country"); 
Route::post('/new_register',"RegisterController@new_register"); 
Route::post('/member/check_credentials',"RegisterController@check_credentials"); 

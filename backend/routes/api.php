<?php
 
Route::group(['middleware' => ['auth:api']], function() 
{
    // ONLY MEMBER AND ADMIN ACCOUNT ALLOWED

	Route::get('/user_data', 'Admin\AdminController@user_data');
     Route::post('/logout', 'SecretController@logout');

});

Route::group(['middleware' => ['auth:api', 'admin']], function() 
{
	/*USER*/
    Route::post('/admin_user/get_users', 'Admin\AdminUserController@get_users');


    /*EVENT*/
    Route::post('/admin_event/get_event', 'Admin\AdminEventController@get_event');
    Route::post('/admin_event/create_submit', 'Admin\AdminEventController@create_submit');
    /*STORY*/
    Route::post('/admin_story/get_story', 'Admin\AdminStoryController@get_story');
    Route::post('/admin_story/create_submit', 'Admin\AdminStoryController@create_submit');

    /*CARRER*/
    Route::post('/admin_carrer/get_carrer', 'Admin\AdminCarrerController@get_carrer');
    Route::post('/admin_carrer/create_submit', 'Admin\AdminCarrerController@create_submit');
});

Route::group(['middleware' => ['auth:api', 'member']], function() 
{
    /*DIRECTORY*/
    Route::post('/member_directory/get_member', 'Member\MemberDirectoryController@get_member');
    Route::post('/member_directory/connect_submit', 'Member\MemberDirectoryController@connect_submit');
    Route::post('/member_directory/check_status', 'Member\MemberDirectoryController@check_status');
    
    Route::post('/messages', 'Member\MemberController@messages');
    Route::post('/messages_submit', 'Member\MemberController@message_submit'); 

    /*MESSAGES*/
    Route::post('/member_messages/load_message', 'Member\MemberMessageController@load_message'); 

    Route::post('/member_messages/send_message', 'Member\MemberMessageController@send_message'); 
    



    Route::post('/member_messages/get_all_messages', 'Member\MemberMessageController@get_all_messages'); 
    Route::post('/member_messages/get_messages', 'Member\MemberMessageController@get_messages'); 
    Route::post('/member_messages/get_connection', 'Member\MemberMessageController@get_connections'); 



    Route::post('/sample', 'Member\MemberController@saample');

});
Route::post('/get_school_year', 'SchoolYearController@get_school_year');
Route::get('/client_secret', 'SecretController@get');
Route::post('/new_register',"RegisterController@new_register"); 
Route::post('/member/check_credentials',"RegisterController@check_credentials"); 
/*FRONT*/
Route::post('/get_event', 'Front\FrontController@get_event');
Route::post('/get_story', 'Front\FrontController@get_story');
Route::post('/get_carrer', 'Front\FrontController@get_carrer');
Route::post('/submit_email', 'Front\FrontController@submit_email');

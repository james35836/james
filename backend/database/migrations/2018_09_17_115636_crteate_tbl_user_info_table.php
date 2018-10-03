<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrteateTblUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_user_info', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->string('user_gender')->nullable();
            $table->string('user_birthdate')->nullable();
            $table->string('user_phone_1')->nullable();
            $table->string('user_phone_2')->nullable();
            $table->text('user_bio')->nullable();
            $table->string('user_job')->nullable();
            $table->string('user_course')->default("course");
            $table->string('user_facebook_link')->default('https://www.facebook.com/');
            $table->string('user_twitter_link')->default('https://www.twitter.com/');
            $table->string('user_linkedin_link')->default('https://www.linkedIn.com/');
            $table->integer('user_id');
        });
    }















    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblEventTable611pm9192018 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_event', function (Blueprint $table) {
            $table->increments('event_id');
            $table->string('event_name');
            $table->text('event_description');
            $table->string('event_date');
            $table->string('event_time');
            $table->string('event_venue');
            $table->string('event_photo')->default('../../../assets/backend/event/event_photo.jpg');
            $table->string('event_created');
            $table->string('event_facebook')->default('https://www.facebook.com/');
            $table->string('event_twitter')->default('https://www.twitter.com/');
            $table->string('event_linkedin')->default('https://www.linkedIn.com/');
            $table->integer('event_posted_by');
            $table->tinyInteger('archived')->default(0);
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

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStoryTable611pm9192018 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_story', function (Blueprint $table) {
            $table->increments('story_id');
            $table->string('story_photo')->default('../../../assets/backend/story/story.jpg');
            $table->string('story_by');
            $table->string('story_title');
            $table->text('story_body');
            $table->text('story_qoute');
            $table->string('story_created');
            $table->integer('story_posted_by');
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

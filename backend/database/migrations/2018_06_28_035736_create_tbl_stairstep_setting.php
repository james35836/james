<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStairstepSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stairstep_settings', function (Blueprint $table) 
        {
            $table->increments('stairstep_settings_id');
            $table->tinyInteger('personal_as_group')->default(0);
            $table->tinyInteger('live_update')->default(0);
            $table->tinyInteger('allow_downgrade')->default(0);
            $table->tinyInteger('rank_first')->default(0);
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

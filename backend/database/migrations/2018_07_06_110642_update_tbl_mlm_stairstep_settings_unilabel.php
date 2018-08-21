<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTblMlmStairstepSettingsUnilabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_stairstep_settings', function (Blueprint $table) 
        {
            $table->string('personal_stairstep_pv_label')->default("Accumulated Personal PV");
            $table->string('group_stairstep_pv_label')->default("Accumulated Group PV");
            $table->string('earning_label_points')->default("Override Points");
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

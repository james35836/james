<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStairstepRank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stairstep_rank', function (Blueprint $table) 
        {
            $table->increments('stairstep_rank_id');
            $table->string('stairstep_rank_name');
            $table->double('stairstep_rank_override')->default("0");
            $table->double('stairstep_rank_personal')->default("0");
            $table->double('stairstep_rank_group')->default("0");
            $table->double('stairstep_rank_personal_all')->default("0");
            $table->double('stairstep_rank_group_all')->default("0");
            $table->tinyInteger('archive')->default("0");
            $table->dateTime('stairstep_rank_date_created');
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

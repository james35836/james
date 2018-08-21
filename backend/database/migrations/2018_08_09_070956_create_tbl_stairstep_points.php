<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblStairstepPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_stairstep_points', function (Blueprint $table) 
        {
            $table->increments('stairstep_points_id');
            $table->integer('stairstep_points_slot_id')->unsigned();
            $table->double('stairstep_points_amount');
            $table->string('stairstep_points_type');
            $table->integer('stairstep_points_cause_id')->unsigned()->nullable();
            $table->integer('stairstep_points_cause_membership_id')->unsigned()->nullable();
            $table->integer('stairstep_points_cause_level')->default(0);
            $table->string('stairstep_points_date_created');

            $table->foreign('stairstep_points_slot_id')->references('slot_id')->on('tbl_slot')->onDelete('cascade');
            $table->foreign('stairstep_points_cause_id')->references('slot_id')->on('tbl_slot')->onDelete('cascade');
            // $table->foreign('stairstep_points_cause_membership_id')->references('membership_id')->on('tbl_membership')->onDelete('cascade');
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

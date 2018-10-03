<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblConnectionTable9212018230pm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_connection', function (Blueprint $table) {
            $table->increments('connection_id');
            $table->integer('connection_of');
            $table->integer('connection_by');
            $table->string('connection_date');
            $table->tinyInteger('connection_status')->default(0);
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

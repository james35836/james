<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTblJobTable9212018230pm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_job', function (Blueprint $table) {
            $table->increments('job_id');
            $table->string('job_company_logo')->default('../../../assets/backend/company_logo/company_logo.jpg');
            $table->string('job_company_name');
            $table->string('job_title');
            $table->text('job_description');
            $table->string('job_contact_person');
            $table->string('job_contact_email');
            $table->string('job_date');
            $table->string('job_site')->nullables();
            $table->string('job_created');
            $table->integer('job_posted_by');
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

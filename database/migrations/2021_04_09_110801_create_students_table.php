<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->bigInteger('class_id');
            $table->bigInteger('birth_cert_number');
            $table->string('nemis_number');
            $table->string('adm_number');
            $table->string('gender');
            $table->string('dob');
            $table->string('previous_school');
            $table->string('medical');
            $table->string('passport_photo');
            $table->string('birth_certificate');
            $table->string('location');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}

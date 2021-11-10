<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('student_id');
            $table->string('mother_name')->default('NULL');
            $table->bigInteger('mother_contact')->nullabe();
            $table->string('father_name')->default('NULL');
            $table->bigInteger('father_contact')->nullabe();
            $table->string('guardian_name')->default('NULL');
            $table->bigInteger('guardian_contact')->nullabe();
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
        Schema::dropIfExists('parents');
    }
}

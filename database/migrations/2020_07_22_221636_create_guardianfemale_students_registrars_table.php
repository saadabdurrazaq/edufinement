<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuardianfemaleStudentsRegistrarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guardianfemale_registrars_student_registrars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_registrars_id')->unsigned()->nullable();
            $table->integer('guardianfemale_registrars_id')->unsigned()->nullable();
            $table->timestamps();
 
            $table->foreign('student_registrars_id')->references('id')->on('students_registrars');
            $table->foreign('guardianfemale_registrars_id')->references('id')->on('guardianfemale_registrars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guardianfemale_registrars_student_registrars');
    }
}

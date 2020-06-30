<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentsStudentsRegistrarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('father_registrars_student_registrars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_registrars_id')->unsigned()->nullable();
            $table->integer('father_registrars_id')->unsigned()->nullable();
            $table->integer('mother_registrar_id')->unsigned()->nullable();
            $table->timestamps();
 
            $table->foreign('student_registrars_id')->references('id')->on('students_registrars');
            $table->foreign('father_registrars_id')->references('id')->on('father_registrars');
            $table->foreign('mother_registrars_id')->references('id')->on('mother_registrars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('father_registrars_student_registrars');
    }
}

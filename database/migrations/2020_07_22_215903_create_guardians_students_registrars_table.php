<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuardiansStudentsRegistrarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guardianmale_registrars_student_registrars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_registrars_id')->unsigned()->nullable();
            $table->integer('guardianmale_registrars_id')->unsigned()->nullable();
            $table->timestamps();
 
            $table->foreign('student_registrars_id')->references('id')->on('students_registrars');
            $table->foreign('guardianmale_registrars_id')->references('id')->on('guardianmale_registrars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('guardianmale_registrars_student_registrars');
    }

}

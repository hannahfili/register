<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSclassTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sclass_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreignId('sclass_id')->references('id')->on('sclasses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sclass_teacher');
    }
}

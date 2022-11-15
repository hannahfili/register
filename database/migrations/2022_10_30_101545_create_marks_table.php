<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_student_id')->references('id')->on('register_users')->onDelete('cascade');
            $table->foreignId('subject_id')->nullable()->references('id')->on('subjects')->onDelete('set null');
            $table->foreignId('moderator_id')->nullable()->references('id')->on('register_users')->onDelete('set null');
            $table->foreignId('activity_id')->nullable()->references('id')->on('activities')->onDelete('set null');
            $table->dateTime('mark_datetime');
            $table->double('value', 5, 2);
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
        Schema::dropIfExists('marks');
    }
}

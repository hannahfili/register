<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSclassSubjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sclass_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreignId('sclass_id')->references('id')->on('sclasses')->onDelete('cascade');
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
        Schema::dropIfExists('sclass_subject');
    }
}

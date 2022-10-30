<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarkModificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mark_modifications', function (Blueprint $table) {
            $table->id();
            $table->dateTime('modification_datetime');
            $table->foreignId('moderator_id')->nullable()->references('id')->on('register_users')->onDelete('set null');
            $table->foreignId('mark_id')->references('id')->on('marks')->onDelete('cascade');
            $table->double('mark_before_modification',5,2)->nullable(true);
            $table->double('mark_after_modification',5,2)->nullable(true);
            $table->string('modification_reason');
            $table->timestamps();

            // $table->
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mark_modifications');
    }
}

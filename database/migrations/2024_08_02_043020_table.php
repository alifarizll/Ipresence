<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('activities')){
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('tasks_id');
                $table->string('nama_aktivitas');
                $table->string('uraian');
                $table->date('tanggal');
                $table->string('status');
                $table->unsignedBigInteger('users_id');
                $table->timestamps();

                $table->foreign('tasks_id')->references('id')->on('tasks')->onDelete('cascade');
                $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('tasks')){
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('deskripsi');
                $table->timestamps();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
        Schema::dropIfExists('tasks');
    }
};

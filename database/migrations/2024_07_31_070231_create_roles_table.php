<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();

                
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('nama_lengkap');
                $table->string('asal_sekolah');
                $table->string('email')->unique();
                $table->unsignedBigInteger('role_id');
                $table->integer('nisn')->unique();
                $table->date('tanggal_bergabung');
                $table->string('usertype');
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles');

            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesAndUsersTables extends Migration
{
    public function up()
    {
        // Membuat tabel roles jika belum ada
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();

                
            });
        }

        // Membuat tabel users jika belum ada
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username');
                $table->string('nama_lengkap');
                $table->string('asal_sekolah');
                $table->string('email')->unique();
                $table->string('password');
                $table->unsignedBigInteger('role_id');
                $table->integer('nisn')->unique();
                $table->date('tanggal_bergabung');
                $table->string('roles');
                $table->timestamps();

                // Menambahkan foreign key constraint

            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
}

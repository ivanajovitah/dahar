<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profile', function (Blueprint $table) {
            $table->id();

            $table->integer('idUser');
            $table->integer('tinggi_badan');
            $table->integer('berat_badan');
            $table->string('orientasi_makanan');
            $table->string('tingkat_aktivitas');
            $table->string('kota');
            $table->string('provinsi');

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
        Schema::dropIfExists('userprofile');
    }
}

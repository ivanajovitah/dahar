<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGenerateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generate', function (Blueprint $table) {
            $table->id();
            
            $table->integer('idUser');
            $table->text('forDate'); 
            $table->text('groupMenu'); //Breakfast, Lunch, Dinner
            $table->text('idMenu');
            $table->text('nama_resep');
            $table->text('calories');
            $table->text('carbs');
            $table->text('fat');
            $table->text('protein');
            $table->text('feedback');
            $table->text('id_resultFeedback');

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
        Schema::dropIfExists('generate');
    }
}

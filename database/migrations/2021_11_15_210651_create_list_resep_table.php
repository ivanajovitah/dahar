<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListResepTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_resep', function (Blueprint $table) {
            $table->id();

            $table->text('judul_resep');
            $table->text('url');
            $table->text('yield');
            $table->text('bahan');
            $table->text('bahanLines');
            $table->text('langkah');
            $table->text('likes');
            $table->text('cover');
            $table->text('healthLabels');
            $table->text('calories');
            $table->text('totalWeight');
            $table->text('totalTime');
            $table->text('cuisineType');
            $table->text('mealType');
            $table->text('totalNutrients');
            $table->text('totalDaily');
            $table->text('digest');
            $table->text('score');
            $table->integer('idAuthor');

            $table->softDeletes();
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
        Schema::dropIfExists('list_resep');
    }
}

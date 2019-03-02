<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMovieGenreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('movie_genre', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('movie_translations_id')->unsigned()->nullable();
            $table->foreign('movie_translations_id')->references('id')->on('movie_translations')->onDelete('cascade');
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
        Schema::dropIfExists('movie_genre');
    }
}

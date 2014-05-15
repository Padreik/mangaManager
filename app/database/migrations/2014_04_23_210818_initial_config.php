<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialConfig extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('borrowers', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('loans', function($table) {
            $table->increments('id');
            
            $table->date('loan_date');
            $table->boolean('is_a_return');
            $table->integer('borrower_id')->unsigned();
            
            $table->timestamps();
            
            $table->foreign('borrower_id')->references('id')->on('borrowers');
        });
        
        Schema::create('authors', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('types', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('genres', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('editors', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('countries', function($table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::create('series', function($table) {
            $table->increments('id');
            
            $table->string('name');
            $table->string('original_name');
            $table->integer('author_id');
            $table->integer('artist_id');
            $table->integer('type_id');
            $table->integer('edition_id');
            $table->integer('country_id');
            $table->integer('number_of_volumes');
            $table->integer('number_of_original_volumes');
            $table->integer('recommended_age');
            $table->string('image');
            
            $table->timestamps();
        });
        
        Schema::create('mangas', function($table) {
            $table->increments('id');
            
            $table->integer('series_id')->unsigned();
            $table->integer('number');
            $table->date('parution');
            $table->integer('pages');
            $table->string('ean');
            $table->text('summary');
            $table->string('image');
            
            $table->timestamps();
            
            $table->foreign('series_id')->references('id')->on('series');
        });
        
        Schema::create('genre_series', function($table) {
            $table->increments('id');
            $table->integer('series_id')->unsigned();
            $table->integer('genre_id')->unsigned();
            $table->timestamps();
            $table->foreign('series_id')->references('id')->on('series');
            $table->foreign('genre_id')->references('id')->on('genres');
        });
        
        Schema::create('author_series', function($table) {
            $table->increments('id');
            $table->integer('series_id')->unsigned();
            $table->integer('author_id')->unsigned();
            $table->boolean('author');
            $table->boolean('artist');
            $table->timestamps();
            $table->foreign('series_id')->references('id')->on('series');
            $table->foreign('author_id')->references('id')->on('authors');
        });
        
        Schema::create('series_type', function($table) {
            $table->increments('id');
            $table->integer('series_id')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->timestamps();
            $table->foreign('series_id')->references('id')->on('series');
            $table->foreign('type_id')->references('id')->on('types');
        });
        
        Schema::create('editor_series', function($table) {
            $table->increments('id');
            $table->integer('series_id')->unsigned();
            $table->integer('editor_id')->unsigned();
            $table->timestamps();
            $table->foreign('series_id')->references('id')->on('series');
            $table->foreign('editor_id')->references('id')->on('editors');
        });
        
        Schema::create('country_series', function($table) {
            $table->increments('id');
            $table->integer('series_id')->unsigned();
            $table->integer('country_id')->unsigned();
            $table->timestamps();
            $table->foreign('series_id')->references('id')->on('series');
            $table->foreign('country_id')->references('id')->on('countries');
        });
        
        Schema::create('loan_manga', function($table) {
            $table->increments('id');
            $table->integer('loan_id')->unsigned();
            $table->integer('manga_id')->unsigned();
            $table->timestamps();
            $table->foreign('loan_id')->references('id')->on('loans');
            $table->foreign('manga_id')->references('id')->on('mangas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('borrowers');
        Schema::drop('loans');
        Schema::drop('authors');
        Schema::drop('types');
        Schema::drop('genres');
        Schema::drop('editors');
        Schema::drop('countries');
        Schema::drop('series');
        Schema::drop('mangas');
        Schema::drop('genre_series');
        Schema::drop('author_series');
        Schema::drop('series_type');
        Schema::drop('editor_series');
        Schema::drop('country_series');
        Schema::drop('loan_manga');
    }

}

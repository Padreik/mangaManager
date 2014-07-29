<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNumberOfBooksToMangas extends Migration {

    public function up() {
        Schema::table('mangas', function($table) {
            $table->tinyInteger('number_of_books')->unsigned()->default(1)->after('rating');
        });
    }

    public function down() {
        Schema::table('mangas', function($table) {
            $table->dropColumn('number_of_books');
        });
    }
}

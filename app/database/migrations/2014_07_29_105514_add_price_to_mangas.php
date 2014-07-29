<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPriceToMangas extends Migration {

    public function up() {
        Schema::table('mangas', function($table) {
            $table->float('price')->default(0)->after('number_of_books');
        });
    }

    public function down() {
        Schema::table('mangas', function($table) {
            $table->dropColumn('price');
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToMangasAndSeries extends Migration {

    public function up() {
        Schema::table('mangas', function($table) {
            $table->text('comment')->after('summary');
        });
        Schema::table('series', function($table) {
            $table->text('comment')->after('recommended_age');
        });
    }

    public function down() {
        Schema::table('mangas', function($table) {
            $table->dropColumn('comment');
        });
        Schema::table('series', function($table) {
            $table->dropColumn('comment');
        });
    }

}

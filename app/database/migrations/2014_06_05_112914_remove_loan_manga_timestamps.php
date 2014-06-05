<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLoanMangaTimestamps extends Migration {

    public function up() {
        Schema::table('loan_manga', function($table) {
            $table->dropTimestamps();
        });
    }

    public function down() {
        Schema::table('loan_manga', function($table) {
            $table->timestamps();
        });
    }

}

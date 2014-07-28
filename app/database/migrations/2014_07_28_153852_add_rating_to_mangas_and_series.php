<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRatingToMangasAndSeries extends Migration {

    public function up() {
        Schema::table('mangas', function($table) {
            $table->tinyInteger('rating')->unsigned()->after('comment');
        });
        Schema::table('series', function($table) {
            $table->float('rating')->after('comment');
        });
        DB::connection()->getPdo()->exec(
            "CREATE PROCEDURE sp_update_rating_series (IN _series_id INT)
                BEGIN
                    UPDATE series SET rating =
                    (SELECT AVG(rating) as rating
                        FROM mangas
                        WHERE series_id = _series_id
                        GROUP BY series_id)
                    WHERE id = _series_id;
                END");
        DB::connection()->getPdo()->exec(
            "CREATE TRIGGER mangas_insert AFTER INSERT ON mangas
                FOR EACH ROW
                BEGIN
                    CALL sp_update_rating_series(NEW.series_id);
                END;");
        DB::connection()->getPdo()->exec(
            "CREATE TRIGGER mangas_update AFTER UPDATE ON mangas
                FOR EACH ROW
                BEGIN
                    CALL sp_update_rating_series(NEW.series_id);
                END;");
    }

    public function down() {
        DB::connection()->getPdo()->exec('DROP PROCEDURE sp_update_rating_series');
        DB::connection()->getPdo()->exec('DROP TRIGGER mangas_insert');
        DB::connection()->getPdo()->exec('DROP TRIGGER mangas_update');
        Schema::table('mangas', function($table) {
            $table->dropColumn('rating');
        });
        Schema::table('series', function($table) {
            $table->dropColumn('rating');
        });
    }
}

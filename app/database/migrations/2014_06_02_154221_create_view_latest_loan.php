<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewLatestLoan extends Migration {

    public function up() {
        Schema::table('mangas', function($table) {
            $table->integer('borrower_id')->after('series_id')->unsigned()->nullable();
            $table->foreign('borrower_id')->references('id')->on('borrowers');
        });
        DB::connection()->getPdo()->exec(
            "CREATE PROCEDURE sp_set_last_borrower (IN _manga_id INT)
                BEGIN
                    UPDATE mangas SET borrower_id =
                    (SELECT IF(is_a_return, NULL, borrower_id) as borrower_id
                        FROM loans JOIN loan_manga ON loans.id = loan_manga.loan_id
                        WHERE loan_manga.manga_id = _manga_id
                        ORDER BY loans.loan_date DESC, loans.created_at DESC
                        LIMIT 1)
                    WHERE id = _manga_id;
                END");
        DB::connection()->getPdo()->exec(
            "CREATE TRIGGER loan_manga_insert AFTER INSERT ON loan_manga
                FOR EACH ROW
                BEGIN
                    CALL sp_set_last_borrower(NEW.manga_id);
                END;");
        
        // Execute procedure on all loans
        $loans = \Loan::all();
        $mangasId = array();
        foreach ($loans as $loan) {
            $mangas = $loan->mangas;
            foreach ($mangas as $manga) {
                if (!isset($mangasId[$manga->id])) {
                    DB::statement("CALL sp_set_last_borrower($manga->id)");
                    $mangasId[$manga->id] = $manga->id;
                }
            }
        }
    }

    public function down() {
        DB::connection()->getPdo()->exec('DROP PROCEDURE sp_set_last_borrower');
        DB::connection()->getPdo()->exec('DROP TRIGGER loan_manga_insert');
        Schema::table('mangas', function($table) {
            $table->dropForeign('mangas_borrower_id_foreign');
            $table->dropColumn('borrower_id');
        });
    }

}

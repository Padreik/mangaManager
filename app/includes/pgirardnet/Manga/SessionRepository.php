<?php
namespace pgirardnet\Manga;

class SessionRepository {
    public static function &getImporterSeries() {
        return $_SESSION['manga']['importer']['series'];
    }
    
    public static function setImporterSeries($value) {
        $_SESSION['manga']['importer']['series'] = $value;
    }
    public static function &getImporterMangas() {
        return $_SESSION['manga']['importer']['mangas'];
    }
    
    public static function setImporterMangas($value) {
        $_SESSION['manga']['importer']['mangas'] = $value;
    }
}

if (!isset($_SESSION)) {
    session_start();
}
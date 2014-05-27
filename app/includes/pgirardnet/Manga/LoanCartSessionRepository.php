<?php

namespace pgirardnet\Manga;

class LoanCartSessionRepository {
    public static function getCount() {
        return \Session::get('loan.count', 0);
    }
    
    public static function updateCount() {
        $count = 0;
        foreach (\Session::get('loan.list') as $series) {
            $count += count ($series);
        }
        \Session::put('loan.count', $count);
    }
    
    public static function seriesInCart($seriesId) {
        return \Session::has("loan.list.$seriesId");
    }
    
    public static function mangaInCart($seriesId, $mangaId) {
        $mangas = \Session::get("loan.list.$seriesId", array());
        return in_array($mangaId, $mangas);
    }
    
    public static function addSeries($seriesId, $mangasIdList) {
        return \Session::put("loan.list.$seriesId", $mangasIdList);
    }
    
    public static function removeSeries($seriesId) {
        \Session::forget("loan.list.$seriesId");
    }
    
    public static function getCart() {
        return \Session::get("loan.list", array());
    }
    
    public static function dropCart() {
        \Session::forget("loan.list");
        self::updateCount();
    }
}
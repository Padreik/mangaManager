<?php

namespace pgirardnet\Manga;

class LoanViewHelper {
    public static function seriesAndMangasInArray($loan) {
        $seriesArray = array();
        
        foreach ($loan->mangas as $manga) {
            $seriesId = $manga->series_id;
            if (!isset($seriesArray[$seriesId])) {
                $seriesArray[$seriesId]['series'] = $manga->series;
                $seriesArray[$seriesId]['mangas'] = array();
            }
            $seriesArray[$seriesId]['mangas'][] = $manga;
        }
        
        return $seriesArray;
    }
}
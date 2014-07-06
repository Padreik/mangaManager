<?php

class ImportController extends BaseController {
    
    protected $COLLECTION_URL = "http://www.manga-news.com/index.php/collection-manga/";
    protected $SERIES_URL = "http://www.manga-news.com/index.php/serie/";
    
    public function collection() {
        return View::make('import.collection')->with('collection_url', $this->COLLECTION_URL);
    }
    
    public function collectionSave() {
        $validationRules = array(
            'username' => 'required'
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('ImportController@collection')->withErrors($validator);
        }
        else {
            $url = $this->COLLECTION_URL.Input::get('username');
            try {
                $parser = new \pgirardnet\Manga\HtmlParser\MangaNewsParser();
                $parser->parseCollection($url);
            }
            catch(Exception $e) {
                return Redirect::action('ImportController@collection')->with('error_parse', '1');
            }
            $_SESSION['manga']['importer']['count'] = array(
                'total' => 0,
                'series' => 0,
                'mangas' => 0
            );
            $seriesInSession = \pgirardnet\Manga\SessionRepository::getImporterSeries();
            
            return View::make('import.collectionSave')->with(
                array(
                    'number_of_series' => $this->countSeriesAndMangas($seriesInSession),
                    'first_series' => $seriesInSession[0]['name']
                )
            );
        }
    }
    
    public function series() {
        return View::make('import.series')->with('series_url', $this->SERIES_URL);
    }
    
    public function seriesSave() {
        $validationRules = array(
            'url' => 'required',
            'volumes' => 'required'
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('ImportController@series')->withErrors($validator);
        }
        else {
            $url = $this->SERIES_URL.Input::get('url');
            // http://stackoverflow.com/questions/7698664/converting-a-range-or-partial-array-in-the-form-3-6-or-3-6-12-into-an-arra#answer-7698869
            $mangasInString = preg_replace_callback('/(\d+)-(\d+)/', function($m) {
                return implode(',', range($m[1], $m[2]));
            }, Input::get('volumes'));
            $mangas = array_unique(explode(',', $mangasInString));
            
            try {
                $parser = new \pgirardnet\Manga\HtmlParser\MangaNewsParser();
                $parser->parseOwnedMangaFromSeries($url, $mangas);
            }
            catch(Exception $e) {
                Input::flash();
                return Redirect::action('ImportController@series')->with('error_parse', '1');
            }
            
            $_SESSION['manga']['importer']['count'] = array(
                'total' => 0,
                'series' => 0,
                'mangas' => 0
            );
            $seriesInSession = \pgirardnet\Manga\SessionRepository::getImporterSeries();
            
            return View::make('import.seriesSave')->with(
                array(
                    'number_of_series' => $this->countSeriesAndMangas($seriesInSession),
                    'first_series' => $seriesInSession[0]['name']
                )
            );
        }
    }
    
    protected function countSeriesAndMangas($array) {
        $total = 0;
        foreach ($array as $series) {
            $total++;
            $total += count($series['mangas']);
        }
        return $total;
    }
    
    public function ajaxNextSeries() {
        $seriesInSession = \pgirardnet\Manga\SessionRepository::getImporterSeries();
        $counting = &$_SESSION['manga']['importer']['count'];
        $parser = new \pgirardnet\Manga\HtmlParser\MangaNewsParser();
        
        if ($counting['total'] == 0 || $counting['mangas'] >= count($seriesInSession[$counting['series']]['mangas'])) {
            // Import series
            if ($counting['total'] > 0) {
                $counting['series']++;
            }
            $counting['mangas'] = 0;
            $seriesInSession[$counting['series']]['object'] = $parser->parseSeries($seriesInSession[$counting['series']]['url']);
            \pgirardnet\Manga\SessionRepository::setImporterSeries($seriesInSession);
            $nextImportName = $seriesInSession[$counting['series']]['mangas'][$counting['mangas']]['name'];
        }
        else {
            // Import Manga
            $parser->importManga(
                $seriesInSession[$counting['series']]['mangas'][$counting['mangas']]['url'],
                $seriesInSession[$counting['series']]['object'],
                $seriesInSession[$counting['series']]['mangas'][$counting['mangas']]['number'],
                $seriesInSession[$counting['series']]['mangas'][$counting['mangas']]['name']
            );
            $counting['mangas']++;
            if ($counting['mangas'] >= count($seriesInSession[$counting['series']]['mangas'])) {
                if ($counting['series'] + 1 >= count($seriesInSession)) {
                    $nextImportName = false;
                }
                else {
                    $nextImportName = $seriesInSession[$counting['series'] + 1]['name'];
                }
            }
            else {
                $nextImportName = $seriesInSession[$counting['series']]['mangas'][$counting['mangas']]['name'];
            }
        }
        $counting['total']++;
        
        
        //sleep(rand(0.1,2));
        sleep(1);
        
        return Response::json(array(
            'current_index' => $counting['total'],
            'next_import' => $nextImportName
        ));
    }
}

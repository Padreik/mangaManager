<?php

class ImportController extends BaseController {
    
    protected $COLLECTION_URL = "http://www.manga-news.com/index.php/collection-manga/";
    
    public function collection() {
        return View::make('import.collection')->with('collection_url', $this->COLLECTION_URL);
    }
    
    public function collectionSave() {
        $validationRules = array(
            'username' => 'required'
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flashAll();
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

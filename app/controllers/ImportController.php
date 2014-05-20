<?php

include_once(app_path().'/includes/HtmlParser/MangaNewsParser.php');
include_once(app_path().'/includes/SessionRepository.php');

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
            $_SESSION['manga']['importer']['count'] = 0;
            $seriesInSession = \pgirardnet\Manga\SessionRepository::getImporterSeries();
            //$this->ajaxNextSeries();
            
            /*$series = Series::find(9);
            $mangas = \pgirardnet\Manga\SessionRepository::getImporterMangas();
            $parser = new \pgirardnet\Manga\HtmlParser\MangaNewsParser();
            $parser->importManga($mangas[0], $series);*/
            
            return View::make('import.collectionSave')->with(
                array(
                    'number_of_series' => count($seriesInSession),
                    'first_series' => $seriesInSession[0]['name']
                )
            );
        }
    }
    
    public function ajaxNextSeries() {
        $seriesInSession = \pgirardnet\Manga\SessionRepository::getImporterSeries();
        $_SESSION['manga']['importer']['count']++;
        $i = $_SESSION['manga']['importer']['count'];
        $nextSeries = $i > count($seriesInSession) ? false : $seriesInSession[$i]['name'];
        
        $parser = new \pgirardnet\Manga\HtmlParser\MangaNewsParser();
        $parser->parseSeries($seriesInSession[$i]['url']);
        
        return Response::json(array(
            'current_series' => $i,
            'next_series' => $nextSeries
        ));
    }
}

<?php

include(app_path().'/includes/HtmlParser/MangaNewsParser.php');

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
            return '';
        }
    }
}

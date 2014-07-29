<?php

class MangaController extends BaseController {
    
    public function index() {
        if (Input::has('read') && intval(Input::get('read') > -1)) {
            $borrower = \Borrower::find(intval(Input::get('read')));
            $subtitle = 'Lu par: '.$borrower->name;
            $series = array();
            foreach ($borrower->loans as $loan) {
                foreach ($loan->mangas as $manga) {
                    if (!isset($series[$manga->series_id])) {
                        $series[$manga->series_id] = array(
                            'series' => $manga->series,
                            'mangas' => array()
                        );
                    }
                    $series[$manga->series_id]['mangas'][$manga->id] = $manga;
                }
            }
        }
        else {
            $series = array();
            $subtitle = 'Erreur';
        }
        return View::make('manga.index')->with('series', $series)->with('subtitle', $subtitle);
    }
    
    public function show($id) {
        $manga = \Manga::find($id);
        return View::make('manga.show')->with('manga', $manga);
    }
    
    public function create($series_id) {
        $series = \Series::find($series_id);
        $next_number = $series->mangas()->max('number');
        $parameters = array(
            'manga_url' => \ImportController::MANGA_URL,
            'series' => $series,
            'next_number' => is_null($next_number) ? 1 : $next_number + 1,
        );
        return View::make('manga.create')->with($parameters);
    }
    
    public function store() {
        $validationRules = array(
            'series_id' => 'required|integer',
            'number' => 'integer',
            'date' => 'date',
            'pages' => 'integer',
            'ean' => 'integer',
            'number_of_books' => 'required|integer',
            'price' => 'required|regex:/^\d+([\.,]\d{0,2})?$/',
            'image_upload' => 'image',
            'image_url' => 'url|link_is_image',
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('MangaController@create', array('series_id' => Input::get('series_id')))->withErrors($validator);
        }
        else {
            $manga = new \Manga();
            $manga->number = intval(Input::get('number'));
            $manga->name = Input::get('name');
            $manga->parution = Input::get('parution');
            $manga->pages = intval(Input::get('pages'));
            $manga->ean = Input::get('ean');
            $manga->number_of_books = intval(Input::get('number_of_books'));
            $manga->summary = Input::get('summary');
            $manga->comment = Input::get('comment');
            $manga->rating = Input::get('rating');
            $manga->price = str_replace(',', '.', Input::get('price'));
            $manga->source = Input::get('source') ? \ImportController::MANGA_URL.Input::get('source') : '';
            
            $imagePath = false;
            if (Input::get('image_url')) {
                $imagePath = Input::get('image_url');
            }
            elseif (Input::file('image_upload')) {
                $imagePath = Input::file('image_upload');
            }
            if ($imagePath) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $manga->image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            
            $series = \Series::find(Input::get('series_id'));
            $series->mangas()->save($manga);
            
            return Redirect::action('MangaController@show', array('id' => $manga->id));
        }
    }
    
    public function edit($id) {
        $manga = \Manga::find($id);
        $parameters = array(
            'manga_url' => \ImportController::MANGA_URL,
            'manga_source' => strpos($manga->source, \ImportController::MANGA_URL) === false ? $manga->source : substr($manga->source, strlen(\ImportController::MANGA_URL)),
            'manga' => $manga,
        );
        return View::make('manga.edit')->with($parameters);
    }
    
    public function update($id) {
        $validationRules = array(
            'number' => 'integer',
            'date' => 'date',
            'pages' => 'integer',
            'ean' => 'integer',
            'number_of_books' => 'required|integer',
            'price' => 'required|regex:/^\d+([\.,]\d{0,2})?$/',
            'image_upload' => 'image',
            'image_url' => 'url|link_is_image',
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('MangaController@edit', array('id' => $id))->withErrors($validator);
        }
        else {
            $manga = \Manga::find($id);
            $manga->number = intval(Input::get('number'));
            $manga->name = Input::get('name');
            $manga->parution = Input::get('parution');
            $manga->pages = intval(Input::get('pages'));
            $manga->ean = Input::get('ean');
            $manga->number_of_books = intval(Input::get('number_of_books'));
            $manga->summary = Input::get('summary');
            $manga->comment = Input::get('comment');
            $manga->rating = Input::get('rating');
            $manga->price = str_replace(',', '.', Input::get('price'));
            $manga->source = Input::get('source') ? \ImportController::MANGA_URL.Input::get('source') : '';
            
            $imagePath = false;
            if (Input::get('image_url')) {
                $imagePath = Input::get('image_url');
            }
            elseif (Input::file('image_upload')) {
                $imagePath = Input::file('image_upload');
            }
            if ($imagePath) {
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);
                $manga->image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            
            $manga->save();
            
            return Redirect::action('MangaController@show', array('id' => $manga->id))->with('edit_success', '1');
        }
    }
    
    public function destroy($id) {
        $manga = \Manga::find($id);
        $series_id = $manga->series_id;
        $manga->delete();
        return Redirect::action('SeriesController@show', array('id' => $series_id));
    }
    
    public function image($id) {
        $manga = \Manga::find($id);
        if (is_object($manga)) {
            preg_match("/^data:(.*);base64,(.*)$/", $manga->image, $matches);
            $response = Response::make(base64_decode($matches[2]), 200);
            $response->header('Content-Type', $matches[1]);
            return $response;
        }
    }
    
    public function search() {
        $includes = array(
            'borrowers' => \Borrower::orderby('name')->lists('name', 'id'),
        );
        return View::make('manga.search')->with($includes);
    }
}

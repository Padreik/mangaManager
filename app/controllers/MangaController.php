<?php

class MangaController extends BaseController {
    
    public function index($series_id) {
        $series = \Series::find($series_id);
        return View::make('manga.index')->with('mangas', $series->mangas);
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
            'image_upload' => 'image',
            'image_url' => 'url|link_is_image',
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('MangaController@create')->withErrors($validator);
        }
        else {
            $manga = new \Manga();
            $manga->number = intval(Input::get('number'));
            $manga->name = Input::get('name');
            $manga->parution = Input::get('parution');
            $manga->pages = intval(Input::get('pages'));
            $manga->ean = Input::get('ean');
            $manga->summary = Input::get('summary');
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
            'image_upload' => 'image',
            'image_url' => 'url|link_is_image',
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('MangaController@edit')->withErrors($validator);
        }
        else {
            $manga = \Manga::find($id);
            $manga->number = intval(Input::get('number'));
            $manga->name = Input::get('name');
            $manga->parution = Input::get('parution');
            $manga->pages = intval(Input::get('pages'));
            $manga->ean = Input::get('ean');
            $manga->summary = Input::get('summary');
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
    
    public function image($id) {
        $manga = \Manga::find($id);
        if (is_object($manga)) {
            preg_match("/^data:(.*);base64,(.*)$/", $manga->image, $matches);
            $response = Response::make(base64_decode($matches[2]), 200);
            $response->header('Content-Type', $matches[1]);
            return $response;
        }
    }
}

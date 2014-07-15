<?php

class SeriesController extends BaseController {
    
    public function index() {
        return View::make('series.index')->with('series', \Series::orderBy('name')->get());
    }
    
    public function show($id) {
        return View::make('series.show')->with('series', \Series::find($id));
    }
    
    public function image($id) {
        $series = \Series::find($id);
        if (is_object($series)) {
            preg_match("/^data:(.*);base64,(.*)$/", $series->image, $matches);
            $response = Response::make(base64_decode($matches[2]), 200);
            $response->header('Content-Type', $matches[1]);
            return $response;
        }
    }
    
    public function create() {
        $includes = array(
            'authors' => \Author::orderby('name')->lists('name', 'id'),
            'artists' => \Author::orderby('name')->lists('name', 'id'),
            'countries' => \Country::orderby('name')->lists('name', 'id'),
            'editors' => \Editor::orderby('name')->lists('name', 'id'),
            'genres' => \Genre::orderby('name')->lists('name', 'id'),
            'types' => \Type::orderby('name')->lists('name', 'id'),
            'status' => \Status::orderby('name')->lists('name', 'id'),
            'series_url' => \ImportController::SERIES_URL,
        );
        return View::make('series.create')->with($includes);
    }
    
    public function store() {
        $validationRules = array(
            'name' => 'required',
            'number_of_volumes' => 'integer',
            'number_of_original_volumes' => 'integer',
            'recommended_age' => 'integer',
            'status' => 'required',
            'image_upload' => 'image',
            'image_url' => 'url|link_is_image',
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('SeriesController@create')->withErrors($validator);
        }
        else {
            $series = new \Series();
            $series->name = Input::get('name');
            $series->original_name = Input::get('original_name');
            $series->number_of_volumes = intval(Input::get('number_of_volumes'));
            $series->number_of_original_volumes = intval(Input::get('number_of_original_volumes'));
            $series->recommended_age = intval(Input::get('recommended_age'));
            $series->source = Input::get('source') ? \ImportController::SERIES_URL.Input::get('source') : '';
            
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
                $series->image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            
            $status = \Status::find(Input::get('status'));
            $series->status()->associate($status);
            
            $series->save();
            
            // Weird twist to keep storeSubElements relativly simple
            $GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add'] = array($this, 'convertArtistsId');
            $this->artistsToAdd = Input::get('artist');
            $this->storeSubElementsAndAttach($series, 'authors', Input::get('author'), '\\Author', array('author' => 1));
            unset($GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add']);
            
            $this->storeSubElementsAndAttach($series, 'artists', $this->artistsToAdd, '\\Author', array('artist' => 1));
            $this->storeSubElementsAndAttach($series, 'countries', Input::get('country'), '\\Country');
            $this->storeSubElementsAndAttach($series, 'editions', Input::get('editor'), '\\Editor');
            $this->storeSubElementsAndAttach($series, 'genres', Input::get('genre'), '\\Genre');
            $this->storeSubElementsAndAttach($series, 'types', Input::get('type'), '\\Type');
            
            return Redirect::action('SeriesController@show', array('id' => $series->id));
        }
    }
        
    protected function storeSubElementsAndAttach($mainObject, $attachFunction, $elements, $className, $pivotProperties = array()) {
        if($elements) {
            foreach($elements as $id) {
                if (strpos($id, 'add') === 0) {
                    $newObject = new $className();
                    $newObject->name = substr($id, 3);
                    $newObject->save();
                    $mainObject->$attachFunction()->attach($newObject, $pivotProperties);
                    if (isset($GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add'])) {
                        call_user_func($GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add'], $newObject, $id);
                    }
                }
                else {
                    $object = $className::find($id);
                    if ($object) {
                        $mainObject->$attachFunction()->attach($object, $pivotProperties);
                    }
                }
            }
        }
    }
    
    protected function convertArtistsId($newAuthor, $addId) {
        if (($arrayId = array_search($addId, $this->artistsToAdd, true)) !== false) {
            $this->artistsToAdd[$arrayId] = $newAuthor->id;
        }
    }
    
    public function edit($id) {
        $series = \Series::find($id);
        $includes = array(
            'series' => $series,
            'series_source' => strpos($series->source, \ImportController::SERIES_URL) === false ? $series->source : substr($series->source, strlen(\ImportController::SERIES_URL)),
            'authors' => \Author::orderby('name')->lists('name', 'id'),
            'artists' => \Author::orderby('name')->lists('name', 'id'),
            'countries' => \Country::orderby('name')->lists('name', 'id'),
            'editors' => \Editor::orderby('name')->lists('name', 'id'),
            'genres' => \Genre::orderby('name')->lists('name', 'id'),
            'types' => \Type::orderby('name')->lists('name', 'id'),
            'status' => \Status::orderby('name')->lists('name', 'id'),
            'series_url' => \ImportController::SERIES_URL,
        );
        return View::make('series.edit')->with($includes);
    }
    
    public function update($id) {
        $validationRules = array(
            'name' => 'required',
            'number_of_volumes' => 'integer',
            'number_of_original_volumes' => 'integer',
            'recommended_age' => 'integer',
            'status' => 'required',
            'image_upload' => 'image',
            'image_url' => 'url|link_is_image',
        );
        
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('SeriesController@edit')->withErrors($validator);
        }
        else {
            $series = \Series::find($id);
            $series->name = Input::get('name');
            $series->original_name = Input::get('original_name');
            $series->number_of_volumes = intval(Input::get('number_of_volumes'));
            $series->number_of_original_volumes = intval(Input::get('number_of_original_volumes'));
            $series->recommended_age = intval(Input::get('recommended_age'));
            $series->source = Input::get('source') ? \ImportController::SERIES_URL.Input::get('source') : '';
            
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
                $series->image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
            
            $status = \Status::find(Input::get('status'));
            $series->status()->associate($status);
            
            $series->save();
            
            $series->authors()->detach();
            $series->artists()->detach();
            // Weird twist to keep storeSubElements relativly simple
            $GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add'] = array($this, 'convertArtistsId');
            $this->artistsToAdd = Input::get('artist');
            $this->storeSubElementsAndAttach($series, 'authors', Input::get('author'), '\\Author', array('author' => 1));
            unset($GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add']);
            
            $this->storeSubElementsAndAttach($series, 'artists', $this->artistsToAdd, '\\Author', array('artist' => 1));
            
            $countryIds = $this->storeSubElements(Input::get('country'), '\\Country');
            $series->countries()->sync($countryIds);
            $editorIds = $this->storeSubElements(Input::get('editor'), '\\Editor');
            $series->editions()->sync($editorIds);
            $genreIds = $this->storeSubElements(Input::get('genre'), '\\Genre');
            $series->genres()->sync($genreIds);
            $typeIds = $this->storeSubElements(Input::get('type'), '\\Type');
            $series->types()->sync($typeIds);
            
            return Redirect::action('SeriesController@show', array('id' => $series->id));
        }
    }
        
    protected function storeSubElements($elements, $className) {
        $ids = array();
        if($elements) {
            foreach($elements as $id) {
                if (strpos($id, 'add') === 0) {
                    $newObject = new $className();
                    $newObject->name = substr($id, 3);
                    $newObject->save();
                    $ids[] = $newObject->id;
                    if (isset($GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add'])) {
                        call_user_func($GLOBALS['pgirard']['manga']['hook']['SeriesController']['storeSubElements']['add'], $newObject, $id);
                    }
                }
                else {
                    $ids[] = $id;
                }
            }
        }
        return $ids;
    }
    
    public function destroy($id) {
        \Series::destroy($id);
        return Redirect::action('SeriesController@index');
    }
}

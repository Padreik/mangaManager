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

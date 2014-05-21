<?php

class SeriesController extends BaseController {
    
    public function index() {
        return View::make('series.index')->with('series', \Series::all());
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
}

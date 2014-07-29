<?php

class StatController extends BaseController {
    public function index() {
        $parameters = array(
            'nbOfSeries' => \Series::count(),
            'nbOfMangas' => \Manga::count(),
            'totalPrice' => \Manga::sum('price')
        );
        return View::make('stat.index')->with($parameters);
    }
}

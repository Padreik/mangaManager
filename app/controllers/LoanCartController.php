<?php

class LoanCartController extends BaseController {
    public function add($id) {
        $series = \Series::find($id);
        if (is_object($series)) {
            $mangasId = array();
            foreach ($series->mangas as $manga) {
                $mangasId[] = $manga->id;
            }
            \pgirardnet\Manga\LoanCartSessionRepository::addSeries($id, $mangasId);
        }
        \pgirardnet\Manga\LoanCartSessionRepository::updateCount();
        return Redirect::action('SeriesController@show', array('id' => $id));
    }
    
    public function update($id) {
        $series = \Series::find($id);
        if (is_object($series)) {
            $mangasId = array();
            $selectedMangas = Input::get('mangasId');
            if (count($selectedMangas) > 0) {
                foreach ($series->mangas as $manga) {
                    if (in_array($manga->id, $selectedMangas)) {
                        $mangasId[] = $manga->id;
                    }
                }
                \pgirardnet\Manga\LoanCartSessionRepository::addSeries($id, $mangasId);
            }
            else {
                \pgirardnet\Manga\LoanCartSessionRepository::removeSeries($id);
            }
        }
        \pgirardnet\Manga\LoanCartSessionRepository::updateCount();
        return Redirect::action('SeriesController@show', array('id' => $id));
    }
    
    public function remove($id) {
        \pgirardnet\Manga\LoanCartSessionRepository::removeSeries($id);
        \pgirardnet\Manga\LoanCartSessionRepository::updateCount();
        return Redirect::action('SeriesController@show', array('id' => $id));
    }
}

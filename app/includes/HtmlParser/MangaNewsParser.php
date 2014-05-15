<?php
namespace pgirardnet\Manga\HtmlParser;

include_once(app_path().'/includes/AbsoluteUrl.php');
include_once(app_path().'/includes/HtmlParser/HtmlParser.php');

class MangaNewsParser implements HtmlParser {
    protected $html; // Due to a memory problem, the html object when parsing is stocked here
    
    public function parseCollection($url) {
        $seriesLinks = $this->parseAllCollectionPages($url);
        foreach ($seriesLinks as $seriesLink) {
            $this->parseSeries($seriesLink);
        }
    }
    
    protected function parseAllCollectionPages($url) {
        $links = array();
        $this->parseCollectionPage($url, $links);
        while ($nextPageLink = $this->getNextPageLink($url)) {
            $this->clearPageInMemory();
            $this->parseCollectionPage($nextPageLink, $links);
        }
        $this->clearPageInMemory();
        return $links;
    }
    
    protected function parseCollectionPage($url, &$links) {
        $this->html = new \Htmldom($url);
        $series = $this->html->find('div[id=collecseries] table tbody tr[class=line]');
        foreach ($series as $serie) {
            $links[] = url_to_absolute($url, $serie->find('td[class=titre] a', 0)->href);
        }
    }
    
    protected function getNextPageLink($currentUrl) {
        $absoluteUrl = false;
        $lastPage = $this->html->find('div[class=pager] a', -1);
        
        if (str_contains($lastPage->plaintext, "next")) {
            $relativeUrl = $lastPage->href;
            $absoluteUrl = url_to_absolute($currentUrl, $relativeUrl);
        }
        
        return $absoluteUrl;
    }
    
    protected function clearPageInMemory() {
        $this->html->clear();
        unset($this->html);
    }
    
    public function parseSeries($url) {
        /*$newSeries = new Series();
        $newSeries->name = $serie->find('td[class=titre] a', 0)->innertext;
        $newSeries->originalName = $serie->find('td[class=titre] a', 0)->innertext;
        $newSeries->numberOfVolumes = $serie->find('td[class=titre] a', 0)->innertext;
        $newSeries->numberOfOriginalVolumes = $serie->find('td[class=titre] a', 0)->innertext;
        $newSeries->recommendedAge = $serie->find('td[class=titre] a', 0)->innertext;
        $newSeries->image = $serie->find('td[class=titre] a', 0)->innertext;
        $newSeries->save();*/
    }

}

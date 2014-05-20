<?php
namespace pgirardnet\Manga\HtmlParser;

include_once(app_path().'/includes/AbsoluteUrl.php');
include_once(app_path().'/includes/HtmlParser/HtmlParser.php');
include_once(app_path().'/includes/SessionRepository.php');

class MangaNewsParser implements HtmlParser {
    protected $html; // Due to a memory problem, the html object when parsing is stocked here
    
    public function parseCollection($url) {
        $links = array();
        do {
            $this->parseCollectionPage($url, $links);
            $url = $this->getNextPageLink($url);
            $this->clearPageInMemory();
        } while ($url);
        
        \pgirardnet\Manga\SessionRepository::setImporterSeries($links);
    }
    
    protected function parseCollectionPage($url, &$links) {
        $this->html = new \Htmldom($url);
        $series = $this->html->find('div[id=collecseries] table tbody tr[class=line]');
        foreach ($series as $serie) {
            $link = $serie->find('td[class=titre] a', 0);
            $links[] = array(
                'url' => url_to_absolute($url, $link->href),
                'name' => $link->plaintext
            );
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
        $this->html = new \Htmldom($url);
        $seriesHtml = $this->html->find('div[id=main]', 0);
        $entryInfos = $seriesHtml->find('ul[class=entryInfos] li');
        $newSeries = new \Series();
        
        $newSeries->source = $url;
        
        $nameHtml = $seriesHtml->find('h2[class=entryTitle]', 0);
        if ($nameHtml) {
            $newSeries->name = trim($nameHtml->innertext);
        }
        
        if (strpos($entryInfos[0]->innertext, "Titre VO") !== false) {
            $newSeries->original_name = $this->removeFieldHeader($entryInfos[0]->innertext);
        }
        
        $ageHtml = $seriesHtml->find('div[id=agenumber]', 0);
        if ($ageHtml) {
            $newSeries->recommended_age = intval($ageHtml->innertext);
        }
        
        $numberOfVolumes = count($seriesHtml->find('div[class=serieVolumesImgBlock]'));
        $newSeries->number_of_volumes = $numberOfVolumes;
        $newSeries->number_of_original_volumes = $numberOfVolumes;
        
        $imageHtml = $seriesHtml->find('img[class=entryPicture]', 0);
        if ($imageHtml) {
            $imageSrc = $imageHtml->src;
            if ($imageSrc) {
                $newSeries->image = base64_encode(file_get_contents($imageSrc));
            }
        }
        $newSeries->save();
        
        foreach ($entryInfos as $lineNumber => $entryInfo) {
            $rawEntryTitle = $entryInfo->find("strong", 0);
            if ($rawEntryTitle) {
                $entryTitle = trim($rawEntryTitle->innertext);
                switch ($entryTitle) {
                    case "Dessin :":
                        foreach ($this->importSeriesSubObject($entryInfos, $lineNumber, '\Author') as $object) {
                            $newSeries->artists()->attach($object, array('artist' => 1));
                        }
                        break;
                    case "Scénario :":
                        foreach ($this->importSeriesSubObject($entryInfos, $lineNumber, '\Author') as $object) {
                            $newSeries->authors()->attach($object, array('author' => 1));
                        }
                        break;
                    case "Editeur VF":
                        foreach ($this->importSeriesSubObject($entryInfos, $lineNumber, '\Editor') as $object) {
                            $newSeries->editions()->attach($object);
                        }
                        break;
                    case "Type":
                        foreach ($this->importSeriesSubObject($entryInfos, $lineNumber, '\Type') as $object) {
                            $newSeries->types()->attach($object);
                        }
                        break;
                    case "Genre":
                        foreach ($this->importSeriesSubObject($entryInfos, $lineNumber, '\Genre') as $object) {
                            $newSeries->genres()->attach($object);
                        }
                        break;
                    case "Origine":
                        $newSeries->countries()->attach(
                                $this->importCountry(
                                        $this->removeFieldHeader($entryInfos[$lineNumber]->innertext)
                                    )
                            );
                        break;
                }
            }
        }
        
        $mangas = $seriesHtml->find('div[id=serieVolumes] span[class=smallpicinfo] a');
        $links = array();
        foreach ($mangas as $manga) {
            $links[] = $manga->href;
        }
        \pgirardnet\Manga\SessionRepository::setImporterMangas($links);
        
        $this->clearPageInMemory();
        
        foreach($links as $link) {
            $this->importManga($link, $newSeries);
        }
    }
    
    protected function removeFieldHeader($textWithHeader) {
        $pos = strpos($textWithHeader, ':');
        $untrimmedText = substr($textWithHeader, $pos + 1);
        return trim($untrimmedText);
    }
    
    protected function getFieldHeader($textWithHeader) {
        $pos = strpos($textWithHeader, ':');
        $untrimmedText = substr($textWithHeader, 0, $pos);
        return trim($untrimmedText);
    }
    
    protected function importSeriesSubObject($html, $lineNumber, $class) {
        $objects = $html[$lineNumber]->find('a');
        $createdObjects = array();
        foreach ($objects as $object) {
            $collection = $class::where('source', 'like', $object->href);
            if ($collection->count() > 0) {
                $createdObjects[] = $collection->first();
            }
            else {
                $newObject = new $class();
                $newObject->name = trim($object->innertext);
                $newObject->source = $object->href;
                $newObject->save();
                $createdObjects[] = $newObject;
            }
        }
        return $createdObjects;
    }
    
    protected function importCountry($name) {
        /* Remove year in certain countries names */
        $delimiter = strpos($name, ' - ');
        if ($delimiter !== false) {
            $name = trim(substr($name, 0, $delimiter));
        }
        $collection = \Country::where('name', 'like', $name);
        if ($collection->count() > 0) {
            return $collection->first();
        }
        else {
            $newObject = new \Country();
            $newObject->name = trim($name);
            $newObject->save();
            return $newObject;
        }
    }
    
    public function importManga($url, $series) {
        $this->html = new \Htmldom($url);
        $mangaHtml = $this->html->find('div[id=main]', 0);
        $newManga = new \Manga();
        $newManga->source = $url;
        
        preg_match('/\d+$/', $url, $match);
        $newManga->number = intval(count($match) > 0 ? $match[0] : 0);
        
        $parutionHtml = $mangaHtml->find('meta[itemprop=datePublished]', 0);
        if ($parutionHtml) {
            $newManga->parution = $parutionHtml->content;
        }
        
        $pagesHtml = $mangaHtml->find('span[itemprop=numberOfPages]', 0);
        if ($pagesHtml) {
            $newManga->pages = trim($pagesHtml->plaintext);
        }
        
        $eanHtml = $mangaHtml->find('span[itemprop=isbn]', 0);
        if ($eanHtml) {
            $newManga->ean = trim($eanHtml->plaintext);
        }
        
        $summaryHtml = $mangaHtml->find('div[itemprop=description]', 0);
        if ($summaryHtml) {
            $newManga->summary = trim($summaryHtml->plaintext);
        }
        
        $imageHtml = $mangaHtml->find('img[class=entryPicture]', 0);
        if ($imageHtml) {
            $imageUrl = $imageHtml->src;
            if ($imageUrl) {
                $newManga->image = base64_encode(file_get_contents($imageUrl));
            }
        }
        $newManga->series()->associate($series);
        $newManga->save();
        
        $this->clearPageInMemory();
    }

}

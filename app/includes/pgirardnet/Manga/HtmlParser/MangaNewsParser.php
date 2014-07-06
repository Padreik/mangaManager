<?php
namespace pgirardnet\Manga\HtmlParser;

class MangaNewsParser implements HtmlParser {
    protected $html; // Due to a memory problem, the html object when parsing is stocked here
    protected $doubledSeries = array(
        3467 => 457, // Demon king
        4478 => 846, // D.Gray-man
        10737 => 846, // D.Gray-man
        9908 => 634, // FullMetal Alchemist
        1609 => 605, // Nana
        9321 => 248, // Naruto
        16927 => 3895, // Pandora Hearts
        4475 => 559, // Parmis eux
        2072 => 776, // Platina
    );
    
    public function parseCollection($url) {
        $links = array();
        $userId = -1;
        do {
            $this->parseCollectionPage($url, $links, $userId);
            $url = $this->getNextPageLink($url);
            $this->clearPageInMemory();
        } while ($url);
        
        \pgirardnet\Manga\SessionRepository::setImporterSeries(array_values($links));
    }
    
    protected function parseCollectionPage($url, &$links, &$userId) {
        $this->html = new \Htmldom($url);
        if ($userId == -1) {
            $userId = $this->getUserId();
        }
        $series = $this->html->find('div[id=collecseries] table tbody tr[class=line]');
        foreach ($series as $serie) {
            $seriesIdHtml = $serie->id;
            $mangasLinks = array();
            if ($seriesIdHtml) {
                // Format id s###, we must remove the s
                $seriesId = substr($seriesIdHtml, 1);
                $mangasLinks = $this->parseOwnedMangaFromCollectionPage($seriesId, $userId);
            }
            
            if (isset($this->doubledSeries[$seriesId])) {
                $otherId = $this->doubledSeries[$seriesId];
                if (isset($links[$otherId])) {
                    $links[$otherId]['mangas'] = array_merge($links[$otherId]['mangas'], $mangasLinks);
                }
                else {
                    $links[$otherId]['mangas'] = $mangasLinks;
                }
            }
            else {
                $link = $serie->find('td[class=titre] a', 0);
                if (isset($links[$seriesId])) {
                    $links[$seriesId]['mangas'] = array_merge($links[$otherId]['mangas'], $mangasLinks);
                    $links[$seriesId]['url'] = \AbsoluteUrl::url_to_absolute($url, $link->href);
                    $links[$seriesId]['name'] = $link->plaintext;
                }
                else {
                    $links[$seriesId] = array(
                        'url' => \AbsoluteUrl::url_to_absolute($url, $link->href),
                        'name' => $link->plaintext,
                        'mangas' => $mangasLinks
                    );
                }
            }
        }
    }
    
    protected function getUserId() {
        $userId = 0;
        $scripts = $this->html->find('script');
        foreach ($scripts as $script) {
            $javascript = $script->innertext;
            if ($javascript) {
                preg_match('/var member = (\d+);/', $javascript, $match);
                if (count($match) > 0) {
                    $userId = intval($match[1]);
                    break;
                }
            }
        }
        return $userId;
    }
    
    protected function parseOwnedMangaFromCollectionPage($seriesId, $userId) {
        $mangasLinks = array();
        $seriesId = intval($seriesId);
        $url = "http://www.manga-news.com/services.php?f=getCollecSerieVols&id=$seriesId&member=$userId";
        $xml = new \SimpleXMLElement($url, 0, true);
        $mangas = $xml->volume;
        foreach ($mangas as $manga) {
            $mangasLinks[] = array(
                'url' => "$manga->url",
                'number' => intval($manga->vol),
                'name' => "$manga->title"
            );
        }
        return $mangasLinks;
    }
    
    public function parseOwnedMangaFromSeries($seriesUrl, $mangasNumber) {
        $mangasLinks = array();
        $this->html = new \Htmldom($seriesUrl);
        
        // Parse Mangas links
        $mangas = $this->html->find('div[id=serieVolumes] a');
        foreach ($mangasNumber as $mangaNewsIndex) {
            $link = $mangas[$mangaNewsIndex - 1];
            $mangasLinks[] = array(
                'url' => \AbsoluteUrl::url_to_absolute($seriesUrl, $link->href),
                'number' => $mangaNewsIndex,
                'name' => "$link->title"
            );
        }
        
        // Parse series name for ajax array
        $nameHtml = $this->html->find('h2[class=entryTitle]', 0);
        if ($nameHtml) {
            $seriesName = trim($nameHtml->innertext);
        }
        $links = array(
            0 => array(
                'url' => $seriesUrl,
                'name' => "$seriesName",
                'mangas' => $mangasLinks
            )
        );
        
        \pgirardnet\Manga\SessionRepository::setImporterSeries(array_values($links));
        $this->clearPageInMemory();
    }
    
    protected function getNextPageLink($currentUrl) {
        $absoluteUrl = false;
        $lastPage = $this->html->find('div[class=pager] a', -1);
        
        if (str_contains($lastPage->plaintext, "next")) {
            $relativeUrl = $lastPage->href;
            $absoluteUrl = \AbsoluteUrl::url_to_absolute($currentUrl, $relativeUrl);
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
        
        $numberOfVolumesHtml = $this->html->find('div[id=numberblock]', 0);
        if ($numberOfVolumesHtml) {
            $numberOfVolumes = $numberOfVolumesHtml->plaintext;
            preg_match('/VF\s*:\s*(\d+)/', $numberOfVolumes, $matchVF);
            $newSeries->number_of_volumes = count($matchVF) > 0 ? $matchVF[1] : 0;
            
            preg_match('/VO\s*:\s*(\d+)/', $numberOfVolumes, $matchVO);
            $newSeries->number_of_original_volumes = count($matchVO) > 0 ? $matchVO[1] : 0;
            
            // Find the series status
            preg_match('/VF\s*:\s*\d+\s*\((.*)\)/', $numberOfVolumes, $matchVF);
            $statusVF = count($matchVF) > 0 ? $matchVF[1] : "";
            preg_match('/VO\s*:\s*\d+\s*\((.*)\)/', $numberOfVolumes, $matchVO);
            $statusVO = count($matchVO) > 0 ? $matchVO[1] : "";
            
            if ($statusVF == "Stoppé" || $statusVO == "Stoppé") {
                $status = \Status::where('name', 'like', \Status::STOPPE)->firstOrFail();
            }
            elseif ($statusVF == "En cours" && $statusVO == "Terminé") {
                $status = \Status::where('name', 'like', \Status::TERMINE_VO)->firstOrFail();
            }
            elseif ($statusVF == "Terminé" || $statusVO == "Terminé") {
                $status = \Status::where('name', 'like', \Status::TERMINE)->firstOrFail();
            }
            else {
                $status = \Status::where('name', 'like', \Status::EN_COURS)->firstOrFail();
            }
            $newSeries->status()->associate($status);
        }
        
        $imageHtml = $seriesHtml->find('img[class=entryPicture]', 0);
        if ($imageHtml) {
            $imageSrc = $imageHtml->src;
            if ($imageSrc) {
                $type = pathinfo($imageSrc, PATHINFO_EXTENSION);
                $data = file_get_contents($imageSrc);
                $newSeries->image = 'data:image/' . $type . ';base64,' . base64_encode($data);
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
        
        $this->clearPageInMemory();
        
        return $newSeries;
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
    
    public function importManga($url, $series, $number = -1, $title = '') {
        $this->html = new \Htmldom($url);
        $mangaHtml = $this->html->find('div[id=main]', 0);
        $newManga = new \Manga();
        $newManga->source = $url;
        
        if ($number > -1) {
            $newManga->number = $number;
        }
        else {
            preg_match('/\d+$/', $url, $match);
            $newManga->number = intval(count($match) > 0 ? $match[0] : 0);
        }
        
        if ($title == '') {
            $titleWithWhitespaces = $mangaHtml->find('h2[class=entryTitle]', 0)->plaintext;
            $title = trim(preg_replace('/\s+/', ' ', $titleWithWhitespaces));
        }
        $newManga->name = $title;
        
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
                $type = pathinfo($imageUrl, PATHINFO_EXTENSION);
                $data = file_get_contents($imageUrl);
                $newManga->image = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }
        $newManga->series()->associate($series);
        $newManga->save();
        
        $this->clearPageInMemory();
    }

}

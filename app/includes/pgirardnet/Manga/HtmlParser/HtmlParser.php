<?php
namespace pgirardnet\Manga\HtmlParser;

interface HtmlParser {
    /**
     * @param string $url
     * @return array An array of links from all series
     */
    function parseCollection($url);
    function parseSeries($url);
}

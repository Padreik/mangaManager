<?php

class DivAsArray {
    public static function format($header, $content) {
        $result = '';
        $result .= '<div class="row list-group-item">';
        $result .=      '<div class="col-md-3">';
        $result .=          $header." :";
        $result .=      '</div>';
        $result .=      '<div class="col-md-9">';
        $result .=          $content;
        $result .=      '</div>';
        $result .= '</div>';
        return $result;
    }
    
    public static function formatSubObject($header, $subObjects, $type) {
        $contentInArray = array();
        
        foreach ($subObjects as $object) {
            //$contentInArray[] = link_to_action($type.'Controller@show', $object->name, array('id' => $object->id));
            $contentInArray[] = $object->name;
        }
        return self::format($header, implode(", ", $contentInArray));
    }
    
    public static function formatBorrowersForSeries($header, $mangas) {
        $borrowers = array();
        
        foreach ($mangas as $manga) {
            if (is_object($manga->borrower)) {
                if (!isset($borrowers[$manga->borrower_id])) {
                    $borrowers[$manga->borrower_id]['object'] = $manga->borrower;
                    $borrowers[$manga->borrower_id]['mangas'] = array();
                }
                $borrowers[$manga->borrower_id]['mangas'][] = $manga->shortNameToDisplay;
            }
        }
        if (count($borrowers) > 0) {
            $flatBorrowers = array();
            foreach ($borrowers as $borrower) {
                $flatBorrowers[] = $borrower['object']->name . " (" . implode(', ', $borrower['mangas']) . ")";
            }
            $content = implode("<br/>", $flatBorrowers);
        }
        else {
            $content = '--';
        }
        return self::format($header, $content);
    }
}
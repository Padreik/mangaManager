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
}
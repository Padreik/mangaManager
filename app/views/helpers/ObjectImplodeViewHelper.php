<?php

class ObjectImplodeViewHelper {
    public static function run($objects, $property, $glue) {
        $objectArray = array();
        
        foreach ($objects as $object) {
            $objectArray[] = $object->$property;
        }
        
        return implode($glue, $objectArray);
    }
}
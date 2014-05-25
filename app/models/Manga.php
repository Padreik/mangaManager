<?php

class Manga extends Eloquent {
    public function series() {
        return $this->belongsTo('Series');
    }
    
    public function getNameToDisplayAttribute() {
        if ($this->number == 0) {
            return $this->name;
        }
        else {
            return "Volume $this->number";
        }
    }
}
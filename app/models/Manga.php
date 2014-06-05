<?php

class Manga extends Eloquent {
    public function series() {
        return $this->belongsTo('Series');
    }
    
    public function borrower() {
        return $this->belongsTo('Borrower');
    }
    
    public function getNameToDisplayAttribute() {
        if ($this->number == 0) {
            return $this->name;
        }
        else {
            return "Volume $this->number";
        }
    }
    
    public function getShortNameToDisplayAttribute() {
        if ($this->number == 0) {
            return $this->name;
        }
        else {
            return $this->number;
        }
    }
}
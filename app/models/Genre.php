<?php

class Genre extends Eloquent {
    
    public function series() {
        return $this->belongsToMany('Series')->withTimestamps();
    }
}
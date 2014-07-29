<?php

class Author extends Eloquent {
    
    public function series() {
        return $this->belongsToMany('Series')->wherePivot('author', '=', 1 )->withTimestamps();
    }
}
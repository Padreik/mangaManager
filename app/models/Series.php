<?php

class Series extends Eloquent {
    
    public function mangas() {
        return $this->hasMany('Manga');
    }
    
    public function author() {
        return $this->belongsToMany('Author')->wherePivot('author', '=', 1 );;
    }
    
    public function artist() {
        return $this->belongsToMany('Author')->wherePivot('artist', '=', 1 );;
    }
    
    public function type() {
        return $this->belongsToMany('Type');
    }
    
    public function edition() {
        return $this->belongsToMany('Edition');
    }
    
    public function country() {
        return $this->belongsToMany('Country');
    }
    
    public function genres() {
        return $this->belongsToMany('Genre');
    }
}
<?php

class Series extends Eloquent {
    
    public function mangas() {
        return $this->hasMany('Manga');
    }
    
    public function authors() {
        return $this->belongsToMany('Author')->wherePivot('author', '=', 1 )->withTimestamps();
    }
    
    public function artists() {
        return $this->belongsToMany('Author')->wherePivot('artist', '=', 1 )->withTimestamps();
    }
    
    public function types() {
        return $this->belongsToMany('Type')->withTimestamps();
    }
    
    public function editions() {
        return $this->belongsToMany('Editor')->withTimestamps();
    }
    
    public function countries() {
        return $this->belongsToMany('Country')->withTimestamps();
    }
    
    public function genres() {
        return $this->belongsToMany('Genre')->withTimestamps();
    }
    
    public function status() {
        return $this->belongsTo('Status');
    }
    
    public function delete() {
        $this->artists()->detach();
        $this->authors()->detach();
        $this->countries()->detach();
        $this->editions()->detach();
        $this->genres()->detach();
        $this->types()->detach();
        foreach ($this->mangas() as $manga) {
            $manga->delete();
        }
        parent::delete();
    }
}
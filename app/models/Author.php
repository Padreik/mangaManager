<?php

class Author extends Eloquent {
    public function series() {
        return $this->belongsToMany('Series');
    }
}
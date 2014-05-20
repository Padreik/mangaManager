<?php

class Manga extends Eloquent {
    public function series() {
        return $this->belongsTo('Series');
    }
}
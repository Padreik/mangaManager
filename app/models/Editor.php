<?php

class Editor extends Eloquent {
    public function series() {
        return $this->belongsToMany('Series');
    }
}
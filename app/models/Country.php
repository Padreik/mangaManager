<?php

class Country extends Eloquent {
    public function series() {
        return $this->belongsToMany('Series');
    }
}
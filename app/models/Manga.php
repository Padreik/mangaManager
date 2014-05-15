<?php

class Series extends Eloquent {
    public function series() {
        return $this->belongsTo('Series');
    }
}
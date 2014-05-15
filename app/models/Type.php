<?php

class Type extends Eloquent {
    public function series() {
        return $this->belongsToMany('Series');
    }
}
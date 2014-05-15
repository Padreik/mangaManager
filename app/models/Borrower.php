<?php

class Borrower extends Eloquent {
    public function series() {
        return $this->belongsToMany('Series');
    }
    
    public function loans() {
        return $this->hasMany('Loan');
    }
}
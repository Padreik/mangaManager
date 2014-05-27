<?php

class Loan extends Eloquent {
    public function borrower() {
        return $this->belongsTo('Borrower');
    }
    
    public function mangas() {
        return $this->belongsToMany('Manga');
    }
}
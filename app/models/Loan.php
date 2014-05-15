<?php

class Loan extends Eloquent {
    public function borrower() {
        return $this->belongsTo('Borrower');
    }
}
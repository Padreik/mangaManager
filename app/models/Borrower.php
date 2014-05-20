<?php

class Borrower extends Eloquent {
    public function loans() {
        return $this->hasMany('Loan');
    }
}
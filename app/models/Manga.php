<?php

class Manga extends Eloquent {
    public function series() {
        return $this->belongsTo('Series');
    }
    
    public function borrower() {
        return $this->belongsTo('Borrower');
    }
    
    public function loans() {
        return $this->belongsToMany('Loan');
    }
    
    public function getReadByAttribute() {
        $reader = array();
        foreach ($this->loans as $loan) {
            if (!isset($reader[$loan->borrower->id])) {
                $reader[$loan->borrower->id] = $loan->borrower->name;
            }
        }
        return $reader;
    }
    
    public function getNameToDisplayAttribute() {
        if ($this->number == 0) {
            return $this->name;
        }
        else {
            return "Volume $this->number";
        }
    }
    
    public function getShortNameToDisplayAttribute() {
        if ($this->number == 0) {
            return $this->name;
        }
        else {
            return $this->number;
        }
    }
}
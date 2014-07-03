<?php

class LoanController extends BaseController {
    public function history() {
        return View::make('loan.history')->with('loans', \Loan::orderBy('loan_date', 'desc')->orderBy('id', 'created_at')->paginate(20));
    }
    
    public function create() {
        $cartIds = \pgirardnet\Manga\LoanCartSessionRepository::getCart();
        $cart = array();
        foreach ($cartIds as $seriesId => $mangasIdList) {
            $seriesInCart = array();
            $seriesInCart['object'] = \Series::find($seriesId);
            foreach ($mangasIdList as $mangaId) {
                $seriesInCart['mangas'][] = \Manga::find($mangaId);
            }
            $cart[] = $seriesInCart;
        }
        $borrowers = \Borrower::all()->lists('name', 'id');
        return View::make('loan.create')->with('cart', $cart)->with('borrowers', $borrowers);
    }
    
    public function store() {
        // If a new_borrower is set we create it
        if (Input::get('new_borrower')) {
            $borrower = new \Borrower();
            $borrower->name = Input::get('new_borrower');
            $borrower->save();
        }
        elseif (Input::get('borrower', false)) {
            // Else we select it
            $borrower = \Borrower::find(Input::get('borrower'));
        }
        if (isset($borrower)) {
            // Get mangas ids
            $cart = \pgirardnet\Manga\LoanCartSessionRepository::getCart();
            $mangas = array_flatten($cart);
            
            // Save the loan
            $loan = new \Loan();
            $loan->borrower()->associate($borrower);
            $loan->loan_date = Input::get('date');
            $loan->is_a_return = 0;
            $loan->save();
            $loan->mangas()->sync($mangas);
            \pgirardnet\Manga\LoanCartSessionRepository::dropCart();
        }
        return View::make('loan.store')->with('success', isset($borrower));
    }
}

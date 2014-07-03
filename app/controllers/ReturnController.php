<?php

class ReturnController extends BaseController {
    public function index() {
        $borrowers = DB::table('borrowers')
                ->join('mangas', 'borrowers.id', '=', 'mangas.borrower_id')
                ->select('borrowers.*')
                ->distinct()
                ->get();
        return View::make('return.index')->with('borrowers', $borrowers);
    }
    
    public function create($borrower_id) {
        $borrower = \Borrower::find($borrower_id);
        $mangas = \Manga::where('borrower_id', '=', $borrower_id)->get();
        return View::make('return.create')->with('borrower', $borrower)->with('mangas', $mangas);
    }
    
    public function store() {
        $validationRules = array(
            'date' => 'required|date_format:Y-m-d',
            'mangas_id' => 'required'
        );
        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            Input::flash();
            return Redirect::action('ReturnController@create', array(Input::get('borrower_id')))->withErrors($validator);
        }
        
        $borrower = \Borrower::find(Input::get('borrower_id'));
        if (isset($borrower)) {
            // Get mangas
            $mangas = \Manga::whereIn('id', Input::get('mangas_id'))->get();
            
            // Save the loan
            $loan = new \Loan();
            $loan->borrower()->associate($borrower);
            $loan->loan_date = Input::get('date');
            $loan->is_a_return = 1;
            $loan->save();
            $loan->mangas()->sync($mangas);
            \pgirardnet\Manga\LoanCartSessionRepository::dropCart();
        }
        return View::make('return.store')->with('success', isset($borrower));
    }
}

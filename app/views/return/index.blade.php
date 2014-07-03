@extends('layout')

@section('title')
    Retour
@stop

@section('content')
    <div class="list-group">
        @foreach ($borrowers as $borrower)
            <a href="{{ URL::action('ReturnController@create', array('borrower_id' => $borrower->id)) }}" class="list-group-item">
                {{ $borrower->name }}
            </a>
        @endforeach
    </div>
@stop
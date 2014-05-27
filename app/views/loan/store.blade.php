@extends('layout')

@section('title')
    Prêt
@stop

@section('content')
    @if ($success)
        <div class="alert alert-success">
            Mangas prêtés!
        </div>
    @else
        <div class="alert alert-danger">
            Le prêt a échoué :(
        </div>
    @endif
@stop
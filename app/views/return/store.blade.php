@extends('layout')

@section('title')
    Retour terminé
@stop

@section('content')
    @if ($success)
        <div class="alert alert-success">
            Mangas retournés!
        </div>
    @else
        <div class="alert alert-danger">
            Le retour a échoué :(
        </div>
    @endif
@stop
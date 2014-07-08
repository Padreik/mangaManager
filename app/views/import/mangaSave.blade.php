@extends('layout')

@section('title')
    Manga importé
@stop

@section('content')
    <p>Importation réussi!</p>
    {{ HTML::linkAction('MangaController@show', 'Voir le manga', array('id' => $manga->id)) }}
@stop
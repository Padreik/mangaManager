@extends('layout')

@section('title')
    Recherche de séries
@stop

@section('content')
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('SeriesController@index'))->get() }}
        {{ BootForm::select('Auteur', 'author', array('-1' => '--Sélectionner--')+$authors) }}
        {{ BootForm::select('Genre', 'genre', array('-1' => '--Sélectionner--')+$genres) }}
        {{ BootForm::submit('Rechercher') }}
    {{ Form::close() }}
@stop
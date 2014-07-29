@extends('layout')

@section('title')
    Recherche de mangas
@stop

@section('content')
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('MangaController@index'))->get() }}
        {{ BootForm::select('Lu par', 'read', array('-1' => '--Sélectionner--')+$borrowers) }}
        {{ BootForm::submit('Rechercher') }}
    {{ Form::close() }}
@stop
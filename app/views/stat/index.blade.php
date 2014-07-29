@extends('layout')

@section('title')
    Statistiques
@stop

@section('content')
    <div class="row">
        <div class="infosArray list-group">
            {{ DivAsArray::format("Nombre de séries", $nbOfSeries) }}
            {{ DivAsArray::format("Nombre de mangas", $nbOfMangas) }}
        </div>
    </div>
@stop
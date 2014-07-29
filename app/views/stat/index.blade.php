@extends('layout')

@section('title')
    Statistiques
@stop

@section('content')
    <div class="row">
        <div class="infosArray list-group">
            {{ DivAsArray::format("Nombre de séries", $nbOfSeries) }}
            {{ DivAsArray::format("Nombre de mangas", $nbOfMangas) }}
            {{ DivAsArray::format("Coût de la collection", number_format($totalPrice, 2, ',', ' ')." $") }}
        </div>
    </div>
@stop
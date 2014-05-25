@extends('layout')

@section('title')
    {{ $manga->series->name }} - {{ $manga->nameToDisplay }}
@stop

@section('content')
    <div class="row">
        <div class="col-md-9 infosArray list-group">
            {{ DivAsArray::format("Numéro", $manga->number) }}
            {{ DivAsArray::format("Parution", strftime('%e %B %Y', strtotime($manga->parution) ) ) }}
            {{ DivAsArray::format("Nombre de pages", $manga->pages) }}
            {{ DivAsArray::format("EAN", $manga->ean) }}
            {{ DivAsArray::format("Résumé", $manga->summary) }}
        </div>
        <div class="col-md-3 text-right">
            <img src="{{ URL::action('MangaController@image', array('id' => $manga->id)) }}" alt="{{ $manga->series->name }} Volume {{ $manga->number }}" class="series-show" />
        </div>
    </div>
@stop
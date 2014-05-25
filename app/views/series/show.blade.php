@extends('layout')

@section('title')
    Série {{ $series->name }}
@stop

@section('content')
    <div class="row">
        <div class="col-md-9 infosArray list-group">
            {{ DivAsArray::format("Nom", $series->name) }}
            {{ DivAsArray::format("Nom original", $series->original_name) }}
            {{ DivAsArray::formatSubObject("Auteur", $series->authors, "Author") }}
            {{ DivAsArray::formatSubObject("Dessinateur", $series->artists, "Author") }}
            {{ DivAsArray::formatSubObject("Pays", $series->countries, "Country") }}
            {{ DivAsArray::formatSubObject("Editeurs", $series->editions, "Editor") }}
            {{ DivAsArray::formatSubObject("Genres", $series->genres, "Genre") }}
            {{ DivAsArray::formatSubObject("Types", $series->types, "Type") }}
            {{ DivAsArray::format("Nombre de volumes VF", $series->number_of_volumes) }}
            {{ DivAsArray::format("Nombre de volumes VO", $series->number_of_original_volumes) }}
            {{ DivAsArray::format("Âge recommendé", $series->recommended_age) }}
        </div>
        <div class="col-md-3 text-right">
            <img src="{{ URL::action('SeriesController@image', array('id' => $series->id)) }}" alt="{{ $series->name }}" class="series-show" />
        </div>
    </div>
    <h2>Liste des volumes</h2>
    <div class="series-list">
        <?php
            $i = 0;
            $nbMangas = count($series->mangas)
        ?>
        @foreach ($series->mangas as $manga)
            @if($i % 6 == 0)
                <div class="row">
            @endif
            <div class="col-md-2">
                <a href="{{ URL::action('MangaController@show', array('id' => $manga->id)) }}" class="thumbnail">
                    <img src="{{ URL::action('MangaController@image', array('id' => $manga->id)) }}" alt="{{ $manga->name }}" />
                    <p>{{ $manga->nameToDisplay }}</p>
                </a>
            </div>
            @if($i % 6 == 5 or $i >= $nbMangas)
                </div>
            @endif
            <?php $i++; ?>
        @endforeach
    </div>
@stop
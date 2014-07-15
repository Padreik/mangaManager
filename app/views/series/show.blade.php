@extends('layout')

@section('title')
    Série {{ $series->name }}
@stop

@section('header')
    {{ HTML::script('js/series.js') }}
@stop

@section('content')
    @if (\pgirardnet\Manga\LoanCartSessionRepository::seriesInCart($series->id))
        {{ Form::open(array('action' => array('LoanCartController@remove', $series->id), 'method' => 'delete')) }}
    @else
        {{ Form::open(array('action' => array('LoanCartController@add', $series->id), 'method' => 'put')) }}
    @endif
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
                {{ DivAsArray::format("Status", $series->status->name) }}
                {{ DivAsArray::formatBorrowersForSeries("Prèté à", $series->mangas) }}
            </div>
            <div class="col-md-3 text-right">
                <img src="{{ URL::action('SeriesController@image', array('id' => $series->id)) }}" alt="{{ $series->name }}" class="series-show" />
                @if (\pgirardnet\Manga\LoanCartSessionRepository::seriesInCart($series->id))
                    <button type="submit" class="btn btn-danger btn-lg btn-block loan-button">Retirer du prêt</button>
                @else
                    <button type="submit" class="btn btn-success btn-lg btn-block loan-button">Prêter la série</button>
                @endif
                <a href="{{ URL::action('SeriesController@edit', array('id' => $series->id)) }}" class="btn btn-default btn-lg btn-block" type="button">Modifier</a>
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
                    {{ Form::checkbox('mangasId[]', $manga->id, \pgirardnet\Manga\LoanCartSessionRepository::mangaInCart($series->id, $manga->id)) }}
                </div>
                @if($i % 6 == 5 or $i >= $nbMangas)
                    </div>
                @endif
                <?php $i++; ?>
            @endforeach
        </div>
    {{ Form::close() }}
@stop
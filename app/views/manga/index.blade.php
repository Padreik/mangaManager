@extends('layout')

@section('title')
    Séries
@stop

@section('content')
    <div class="series-list">
        <?php
            $i = 0;
            $nbMangas = count($mangas)
        ?>
        @foreach ($mangas as $manga)
            @if($i % 6 == 0)
                <div class="row">
            @endif
            <div class="col-md-2">
                <a href="{{ URL::action('MangaController@show', array('series_id' => $manga->serie_id, 'id' => $manga->id)) }}" class="thumbnail">
                    <img src="{{ URL::action('MangaController@image', array('series_id' => $manga->serie_id, 'id' => $manga->id)) }}" alt="{{ $manga->name }}" />
                    <p>{{ $manga->name }}</p>
                </a>
            </div>
            @if($i % 6 == 5 or $i >= $nbMangas)
                </div>
            @endif
            <?php $i++; ?>
        @endforeach
    </div>
@stop
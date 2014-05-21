@extends('layout')

@section('title')
    Séries
@stop

@section('content')
    <div class="series-list">
        <?php
            $i = 0;
            $nbSeries = count($series)
        ?>
        @foreach ($series as $serie)
            @if($i % 6 == 0)
                <div class="row">
            @endif
            <div class="col-md-2">
                <a href="{{ URL::action('SeriesController@show', array('id' => $serie->id)) }}" class="thumbnail">
                    <img src="{{ URL::action('SeriesController@image', array('id' => $serie->id)) }}" alt="{{ $serie->name }}" />
                    <p>{{ $serie->name }}</p>
                </a>
            </div>
            @if($i % 6 == 5 or $i >= $nbSeries)
                </div>
            @endif
            <?php $i++; ?>
        @endforeach
    </div>
@stop
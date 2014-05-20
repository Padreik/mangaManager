@extends('layout')

@section('title')
    Collection importé
@stop

@section('header')
    {{ HTML::script('js/collection.js') }}
    <script>
        $(function() {
            getNextSeries();
        });
    </script>
@stop

@section('content')
    <p>Importation en cours</p>
    <div class="progress progress-striped active">
        <div class="progress-bar"  role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="{{ $number_of_series }}" style="width: 0%">
            <span class="sr-only">0% Complete</span>
        </div>
    </div>
    <span id="importation_text">Importation de <span id="importation_title">{{ $first_series }}</span></span>
@stop
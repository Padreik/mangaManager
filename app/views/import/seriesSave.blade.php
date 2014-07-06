@extends('layout')

@section('title')
    Série importé
@stop

@section('header')
    {{ HTML::script('js/collection.js') }}
    <script>
        $(function() {
            totalImportationToDo = {{ $number_of_series }};
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
    <p id="importation_text">Importation de <span id="importation_title">{{ $first_series }}</span> (<span id="importation_progression">1</span>/{{ $number_of_series }})</p>
    <p id="temps_restant_wrapper">Temps restant : <span id="temps_restants">?</span></p>
@stop
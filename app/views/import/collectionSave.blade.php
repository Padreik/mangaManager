@extends('layout')

@section('title')
    Collection importé
@stop

@section('content')
    <p>Importation en cours</p>
    <div class="progress progress-striped active">
        <div class="progress-bar"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
            <span class="sr-only">45% Complete</span>
        </div>
    </div>
@stop
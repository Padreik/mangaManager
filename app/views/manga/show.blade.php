@extends('layout')

@section('document_title')
    {{ $manga->series->name }} - {{ $manga->nameToDisplay }}
@stop

@section('title')
    {{ HTML::linkAction('SeriesController@show', $manga->series->name, array('id' => $manga->series->id)) }} - {{ $manga->nameToDisplay }}
@stop

@section('content')
    @if(Session::has('edit_success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            Modification réussi!
        </div>
    @endif
    <div class="row">
        <div class="col-md-9 infosArray list-group">
            {{ DivAsArray::format("Prêté à", $manga->borrower ? $manga->borrower->name : "--") }}
            {{ DivAsArray::format("Parution", strftime('%e %B %Y', strtotime($manga->parution) ) ) }}
            {{ DivAsArray::format("Nombre de pages", $manga->pages) }}
            {{ DivAsArray::format("EAN", $manga->ean) }}
            {{ DivAsArray::format("Résumé", $manga->summary) }}
        </div>
        <div class="col-md-3 text-right">
            <img src="{{ URL::action('MangaController@image', array('id' => $manga->id)) }}" alt="{{ $manga->series->name }} Volume {{ $manga->number }}" class="series-show" />
            <div class="btn-group settings-button">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                    <li>
                        <a href="{{ URL::action('MangaController@edit', array('id' => $manga->id)) }}">Modifier</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@stop
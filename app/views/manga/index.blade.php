@extends('layout')

@section('title')
    Mangas
@stop

@section('content')
    @if ($subtitle)
        <h2>{{ $subtitle; }}</h2>
    @endif
    <table class="table table-striped">
        @foreach ($series as $serie)
            <tr>
                <th>
                    {{ HTML::linkAction('SeriesController@show', $serie['series']->name, array('id' => $serie['series']->id)) }}
                </th>
                <td>
                    @foreach ($serie['mangas'] as $manga)
                        {{ HTML::linkAction('MangaController@show', $manga->nameToDisplay, array('id' => $manga->id)) }}
                        <br/>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </table>
@stop
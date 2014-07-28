@extends('layout')

@section('title')
    Série {{ $series->name }}
@stop

@section('header')
    {{ HTML::script('js/series.js') }}
@stop

@section('content')
    <div class="alert alert-danger delete-confirmation alert-dismissible" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        {{ Form::open(array('action' => array('SeriesController@destroy', $series->id), 'method' => 'delete')) }}
            Voulez-vous vraiment supprimer cette série et tous les mangas liés?
            <button type="submit" class="btn btn-danger btn-sm">Oui</button>
        {{ Form::close() }}
    </div>
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
                {{ DivAsArray::format("Commentaire", $series->comment) }}
                <?php
                    $ratingInPourcentage = $series->rating * 10;
                    $ratingOnTen = round($series->rating, 1);
                ?>
                {{ DivAsArray::format("Note", "<span class='rating-wrapper' data-toggle='tooltip' title='$ratingOnTen/10'><span class='rating' style='width:$ratingInPourcentage%'>{$series->rating}</span></span>") }}
                {{ DivAsArray::formatBorrowersForSeries("Prèté à", $series->mangas) }}
            </div>
            <div class="col-md-3 text-right">
                <img src="{{ URL::action('SeriesController@image', array('id' => $series->id)) }}" alt="{{ $series->name }}" class="series-show" />
                @if (\pgirardnet\Manga\LoanCartSessionRepository::seriesInCart($series->id))
                    <button type="submit" class="btn btn-danger btn-lg btn-block loan-button">Retirer du prêt</button>
                @else
                    <button type="submit" class="btn btn-success btn-lg btn-block loan-button">Prêter la série</button>
                @endif
                <div class="btn-group settings-button">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li>
                            <a href="{{ URL::action('SeriesController@edit', array('id' => $series->id)) }}">Modifier</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="" data-show="delete-confirmation">Supprimer</a>
                            <script type="text/javascript">
                                $('a[data-show]').click(function(e) {
                                    e.preventDefault();
                                    var name = $(this).data('show');
                                    $("."+name).show();
                                    window.scrollTo(0, 0);
                                });
                            </script>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <h2>
            Liste des volumes
            <small>
                <a href="{{ URL::action('MangaController@create', array('series_id' => $series->id)) }}" class="green-link">
                    <span class="glyphicon glyphicon-plus-sign"></span><span class="sr-only">Ajouter un manga</span>
                </a>
            </small>
        </h2>
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
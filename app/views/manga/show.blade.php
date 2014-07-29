@extends('layout')

@section('document_title')
    {{ $manga->series->name }} - {{ $manga->nameToDisplay }}
@stop

@section('title')
    {{ HTML::linkAction('SeriesController@show', $manga->series->name, array('id' => $manga->series->id)) }} - {{ $manga->nameToDisplay }}
@stop

@section('header')
    <script type="text/javascript">
        $(function () {
            $('*[data-toggle=tooltip]').tooltip();
        });
    </script>
@stop

@section('content')
    @if(Session::has('edit_success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            Modification réussi!
        </div>
    @endif
    <div class="alert alert-danger delete-confirmation alert-dismissible" role="alert" style="display:none;">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        {{ Form::open(array('action' => array('MangaController@destroy', $manga->id), 'method' => 'delete')) }}
            Voulez-vous vraiment supprimer ce manga?
            <button type="submit" class="btn btn-danger btn-sm">Oui</button>
        {{ Form::close() }}
    </div>
    <div class="row">
        <div class="col-md-9 infosArray list-group">
            {{ DivAsArray::format("Prêté à", $manga->borrower ? $manga->borrower->name : "--") }}
            {{ DivAsArray::format("Parution", strftime('%e %B %Y', strtotime($manga->parution) ) ) }}
            {{ DivAsArray::format("Nombre de pages", $manga->pages) }}
            {{ DivAsArray::format("EAN", $manga->ean) }}
            {{ DivAsArray::format("Résumé", $manga->summary) }}
            {{ DivAsArray::format("Commentaire", $manga->comment) }}
            <?php $ratingInPourcentage = $manga->rating * 10; ?>
            {{ DivAsArray::format("Note", "<span class='rating-wrapper' data-toggle='tooltip' title='{$manga->rating}/10'><span class='rating' style='width:$ratingInPourcentage%'>{$manga->rating}</span></span>") }}
            {{ DivAsArray::format("Lu par", implode($manga->readBy, ', ')) }}
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
@stop
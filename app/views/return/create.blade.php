@extends('layout')

@section('title')
    Retour de {{ $borrower->name }}
@stop

@section('header')
    {{ HTML::script('lib/bootstrap-datepicker/js/bootstrap-datepicker.js') }}
    {{ HTML::script('lib/bootstrap-datepicker/js/locales/bootstrap-datepicker.fr.js') }}
    {{ HTML::style('lib/bootstrap-datepicker/css/datepicker.css') }}
    <script type="text/javascript">
        $(function () {
            $('#datepicker input').datepicker({
                language: "fr",
                todayHighlight: true,
                format: "yyyy-mm-dd"
            });
        });
    </script>
@stop

@section('content')
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('ReturnController@store')) }}
        @if(BootForm::hasError('mangas_id'))
            <div class="alert alert-danger">
                Il faut sélectionner <b>au moins un</b> manga pour faire un retour!
            </div>
        @endif
        {{ BootForm::hidden('borrower_id')->value($borrower->id) }}
        <div id='datepicker'>
            {{ BootForm::text('Date du retour', 'date')->defaultValue(date('Y-m-d')) }}
        </div>
        <div class="form-group">
            <div class="col-lg-{{ Config::get('view.bootformLabelWidth') }} control-label">
                <a href="#" class="toggle-checkbox">Sélectionner tout</a>
            </div>
            @foreach ($mangas as $order => $manga)
                <div class="
                    @if ($order > 0)
                        col-lg-offset-{{ Config::get('view.bootformLabelWidth') }}
                    @endif
                    col-lg-{{ Config::get('view.bootformInputWidth') }}">
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox("mangas_id[]", $manga->id) }} {{$manga->series->name}} {{$manga->nameToDisplay}}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
        {{ BootForm::submit('Enregistrer le retour') }}
    {{ BootForm::close() }}
    
    {{ HTML::script('js/toggleCheckboxes.js') }}
@stop
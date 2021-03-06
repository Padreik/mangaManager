@extends('layout')

@section('title')
    Modifier {{ $manga->series->name }} - {{ $manga->nameToDisplay }}
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
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('MangaController@update', array('id' => $manga->id)))->multipart()->put() }}
        {{ BootForm::bind($manga) }}
        {{ BootForm::text('Numéro', 'number')
                    ->helpBlock(new \AdamWathan\BootForms\Elements\HelpBlock('Laisser 0 s\'il n\'y a pas de numéro de volume'))}}
        {{ BootForm::text('Nom', 'name')
                    ->helpBlock(new \AdamWathan\BootForms\Elements\HelpBlock('Le nom du volume, le numéro doit être 0 afin d\'être pris en compte')) }}
        <div id='datepicker'>
            {{ BootForm::text('Date de parution', 'parution') }}
        </div>
        {{ BootForm::text('Nombre de pages', 'pages') }}
        {{ BootForm::text('EAN', 'ean') }}
        {{ BootForm::textarea('Résumé', 'summary') }}
        {{ BootForm::textarea('Commentaire', 'comment') }}
        {{ BootForm::select('Note', 'rating', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]) }}
        {{ BootForm::text('Nombre de copies', 'number_of_books') }}
        {{ BootForm::text('Coût', 'price', number_format($manga->price, 2, ',', ' ')) }}
        <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
            <label class="col-lg-2 control-label" for="source">Lien manganews</label>
            <div class="col-lg-10">
                <div class="input-group">
                    <span class="input-group-addon">{{ $manga_url }}</span>
                    {{ Form::text('source', $manga_source, array('class' => 'form-control')) }}
                    {{ $errors->first('source', '<p class="help-block">:message</p>') }}
                </div>
            </div>
        </div>
        <div class="form-group {{ $errors->has('image_upload') || $errors->has('image_url') ? 'has-error' : '' }}">
            <label for="image" class="col-lg-2 control-label">Image</label>
            <div class="col-lg-5">
                {{ Form::file('image_upload') }}
                {{ $errors->first('image_upload', '<p class="help-block">:message</p>') }}
            </div>
            <div class="col-lg-5">
                {{ Form::text('image_url', '', array('class' => 'form-control', 'placeholder' => 'Lien vers l\'image')) }}
                {{ $errors->first('image_url', '<p class="help-block">:message</p>') }}
            </div>
        </div>
        {{ BootForm::submit('Modifier') }}
    {{ Form::close() }}
    
    {{ HTML::script('js/selectWithText.js') }}
@stop
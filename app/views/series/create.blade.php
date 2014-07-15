@extends('layout')

@section('title')
    Ajouter une série
@stop

@section('header')
    {{ HTML::script('lib/bootstrap-multiselect/js/bootstrap-multiselect.js') }}
    {{ HTML::style('lib/bootstrap-multiselect/css/bootstrap-multiselect.css') }}
@stop

@section('content')
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('SeriesController@store'))->multipart() }}
        {{ BootForm::text('Nom', 'name') }}
        {{ BootForm::text('Nom original', 'original_name') }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Auteur', 'author', $authors)->selectBoxMultiple()->duplicateNewItemIn('artist')->make() }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Dessinateur', 'artist', $artists)->selectBoxMultiple()->duplicateNewItemIn('author')->make() }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Pays', 'country', $countries)->selectBoxMultiple()->make() }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Éditeur', 'editor', $editors)->selectBoxMultiple()->make() }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Genre', 'genre', $genres)->selectBoxMultiple()->make() }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Type', 'type', $types)->selectBoxMultiple()->make() }}
        {{ BootForm::text('Nombre de volumes VF', 'number_of_volumes') }}
        {{ BootForm::text('Nombre de volumes VO', 'number_of_original_volumes') }}
        {{ BootForm::text('Âge recommendé', 'recommended_age') }}
        {{ \pgirardnet\Manga\Form\SelectWithText::init('Status', 'status', $status)->make() }}
        <div class="form-group {{ $errors->has('source') ? 'has-error' : '' }}">
            <label class="col-lg-2 control-label" for="source">Lien manganews</label>
            <div class="col-lg-10">
                <div class="input-group">
                    <span class="input-group-addon">{{ $series_url }}</span>
                    {{ Form::text('source', '', array('class' => 'form-control')) }}
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
        {{ BootForm::submit('Créer') }}
    {{ Form::close() }}
    
    {{ HTML::script('js/selectWithText.js') }}
@stop
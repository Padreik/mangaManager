@extends('layout')

@section('title')
    Importer une série
@stop

@section('content')
    @if(Session::has('error_parse'))
        <div class="alert alert-danger">
            L'importation a échoué, la base de données peut être corrompue
        </div>
    @endif
    
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('ImportController@seriesSave')) }}
        {{ Form::token() }}
        
        <div class="form-group">
            <label class="col-lg-2 control-label" for="username">Lien vers la série</label>
            <div class="col-lg-10">
                <div class="input-group">
                    <span class="input-group-addon">{{ $series_url }}</span>
                    {{ Form::text('url', '', array('class' => 'form-control', 'required' => 'required', 'placeHolder' => 'Academie-Alice-l')) }}
                </div>
            </div>
        </div>
        {{ BootForm::text('Volumes', 'volumes')
                    ->placeHolder('1-3,5')
                    ->helpBlock(new \AdamWathan\BootForms\Elements\HelpBlock('Entrer les numéros des mangas selon l\'ordre d\'apparition dans manga-news et pas selon leur numéro de série')) }}
        
        {{ BootForm::submit('Importer la série') }}
    {{ BootForm::close() }}
@stop
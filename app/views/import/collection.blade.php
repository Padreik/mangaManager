@extends('layout')

@section('title')
    Importer une collection
@stop

@section('content')
    @if(Session::has('error_parse'))
        <div class="alert alert-danger">
            L'importation a échoué, la base de données peut être corrompue
        </div>
    @endif
    
    <p>Prendre note que l'importation de collection ne doit être utilisé qu'une seule fois. S'il y a déjà des séries et/ou mangas dans la base de données il risque de se créer des doublons.</p>
    
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('ImportController@collectionSave')) }}
        {{ Form::token() }}
        
        <div class="form-group">
            <label class="col-lg-2 control-label" for="username">Nom d'utilisateur</label>
            <div class="col-lg-10">
                <div class="input-group">
                    <span class="input-group-addon">{{ $collection_url }}</span>
                    {{ Form::text('username', '', array('class' => 'form-control', 'required' => 'required')) }}
                </div>
            </div>
        </div>
        
        {{ BootForm::submit('Importer la collection') }}
    {{ BootForm::close() }}
@stop
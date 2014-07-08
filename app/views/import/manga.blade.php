@extends('layout')

@section('title')
    Importer un manga
@stop

@section('content')
    @if(Session::has('error_parse'))
        <div class="alert alert-danger">
            L'importation a échoué, la base de données peut être corrompue
        </div>
    @endif
    @if(Session::has('error_series_not_found'))
        <div class="alert alert-danger">
            La série n'a pas été trouvé, veuillez la sélectionner manuellement dans la liste
        </div>
    @endif
    
    {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('ImportController@mangaSave')) }}
        {{ Form::token() }}
        
        <div class="form-group">
            <label class="col-lg-2 control-label" for="username">Lien vers le manga</label>
            <div class="col-lg-10">
                <div class="input-group">
                    <span class="input-group-addon">{{ $manga_url }}</span>
                    {{ Form::text('url', '', array('class' => 'form-control', 'required' => 'required', 'placeHolder' => 'Academie-Alice-l/vol-1')) }}
                </div>
            </div>
        </div>
        {{ BootForm::select('Série', 'series', $series) }}
        {{ BootForm::text('Numéro', 'number')->placeHolder('Détection automatique') }}
        {{ BootForm::text('Titre', 'title')->placeHolder('Détection automatique') }}
        
        {{ BootForm::submit('Importer le manga') }}
    {{ BootForm::close() }}
@stop
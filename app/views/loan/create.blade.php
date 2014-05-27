@extends('layout')

@section('title')
    Prêt
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
    @if (\pgirardnet\Manga\LoanCartSessionRepository::getCount() > 0)
        {{ BootForm::openHorizontal(Config::get('view.bootformLabelWidth'), Config::get('view.bootformInputWidth'))->action(URL::action('LoanController@store')) }}
            <div id='datepicker'>
                {{ BootForm::text('Date du prêt', 'date')->defaultValue(date('Y-m-d')) }}
            </div>
            <div class="form-group">
                <label for="borrower" class="col-lg-2 control-label">
                    Emprunteur
                </label>
                @if (count($borrowers) > 0)
                    <div class="col-lg-5">
                        {{ Form::select('borrower', $borrowers, null, array('class' => 'form-control')) }}
                    </div>
                    <div class="col-lg-5">
                @else
                    <div class="col-lg-10">
                @endif
                    {{ Form::text('new_borrower', null, array('class' => 'form-control', 'placeholder' => 'Nouveau')) }}
                </div>
            </div>
            {{ BootForm::submit('Prêter') }}
        {{ BootForm::close() }}
        <div class="form-group">
        </div>
        <h2>Mangas à prêter</h2>
        <table class="table table-striped">
            @foreach ($cart as $series)
                <tr>
                    <th>{{ $series['object']->name }}</th>
                    <td>
                        @foreach ($series['mangas'] as $manga)
                            {{ $manga->nameToDisplay }}<br/>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </table>
    @else
        <div class="alert alert-danger">
            Aucun manga de sélectionné
        </div>
    @endif
@stop
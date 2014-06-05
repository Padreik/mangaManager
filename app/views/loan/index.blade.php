@extends('layout')

@section('title')
    Prêts
@stop

@section('content')
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Emprunteur</th>
                <th>Retour?</th>
                <th>Prêt</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                <tr>
                    <td>{{ $loan->loan_date }}</td>
                    <td>{{ $loan->borrower->name }}</td>
                    <td>{{ $loan->is_a_return ? 'X' : '' }}</td>
                    <td>
                        <?php $loansSeries = \pgirardnet\Manga\LoanViewHelper::seriesAndMangasInArray($loan); ?>
                        @foreach ($loansSeries as $loanSeries)
                            {{ $loanSeries['series']->name }}:
                            {{ \ObjectImplodeViewHelper::run($loanSeries['mangas'], 'shortNameToDisplay', ', ') }}
                            <br/>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $loans->links() }}
@stop
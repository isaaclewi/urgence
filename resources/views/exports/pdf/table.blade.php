@extends('exports.pdf.base')

@section('titre', $titre)

@section('corps')
    <h1>{{ $titre }}</h1>
    <div class="sub">
        @if($sousTitre){{ $sousTitre }} — @endif{{ count($rows) }} ligne(s)
    </div>

    <div style="margin-top:12px">
    @if(count($rows) === 0)
        <div class="empty">Aucune donnée à afficher.</div>
    @else
        <table class="data">
            <thead>
                <tr>@foreach($headings as $h)<th>{{ $h }}</th>@endforeach</tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    <tr>@foreach($r as $cell)<td>{{ $cell }}</td>@endforeach</tr>
                @endforeach
            </tbody>
        </table>
    @endif
    </div>
@endsection

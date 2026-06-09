@extends('services.master')

@section('content')

<div class="content-card">
    <h3>Daily Interventions</h3>

    <table class="data-table">
        <thead>
            <tr>
                <th>Alerte</th>
                <th>Équipe</th>
                <th>Statut</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            @foreach($interventions as $i)
                <tr>
                    <td>{{ $i->titre }}</td>
                    <td>{{ $i->equipe_nom }}</td>
                    <td>{{ $i->statut }}</td>
                    <td>{{ $i->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection

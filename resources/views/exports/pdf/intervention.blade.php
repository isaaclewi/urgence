@extends('exports.pdf.base')

@section('titre', 'Rapport d\'intervention n° ' . $iv->id)

@section('corps')
    <h1>Rapport d'intervention n° {{ $iv->id }}</h1>
    <div class="sub">{{ $iv->titre }} — équipe {{ $iv->equipe_nom }}</div>

    <h2>Alerte</h2>
    <table class="data">
        <tbody>
            <tr><td style="width:30%" class="muted">Intitulé</td><td>{{ $iv->titre }}</td></tr>
            <tr><td class="muted">Type</td><td>{{ $iv->type_alerte ? ucfirst($iv->type_alerte) : '—' }}</td></tr>
            <tr><td class="muted">Reçue le</td><td>{{ $alerteLe }}</td></tr>
            <tr><td class="muted">Localisation</td><td>{{ $iv->localisation ?: '—' }}</td></tr>
            <tr><td class="muted">Description</td><td>{{ $iv->description ?: '—' }}</td></tr>
            <tr><td class="muted">Statut de l'alerte</td><td>{{ ucfirst((string) $iv->alerte_statut) }}</td></tr>
        </tbody>
    </table>

    <h2>Personne à l'origine de l'alerte</h2>
    <table class="data">
        <tbody>
            <tr><td style="width:30%" class="muted">Nom</td><td>{{ trim(($iv->c_prenom ?? '') . ' ' . ($iv->c_nom ?? '')) ?: '—' }}</td></tr>
            <tr><td class="muted">Téléphone</td><td>{{ $iv->c_tel ?: '—' }}</td></tr>
        </tbody>
    </table>

    <h2>Intervention</h2>
    <table class="data">
        <tbody>
            <tr><td style="width:30%" class="muted">Équipe</td><td>{{ $iv->equipe_nom }}</td></tr>
            <tr><td class="muted">Prise en charge le</td><td>{{ $priseLe }}</td></tr>
            <tr><td class="muted">Délai alerte → intervention</td><td>{{ $delaiMin }} min</td></tr>
            <tr><td class="muted">Dernière mise à jour</td><td>{{ $majLe }}</td></tr>
            <tr><td class="muted">Statut</td><td>{{ ucfirst((string) $iv->statut) }}</td></tr>
            <tr><td class="muted">Position de l'équipe</td>
                <td>@if($iv->latitude && $iv->longitude){{ $iv->latitude }}, {{ $iv->longitude }}@else — @endif</td></tr>
            <tr><td class="muted">Commentaire</td><td>{{ $iv->commentaire ?: '—' }}</td></tr>
        </tbody>
    </table>

    <table style="width:100%;margin-top:46px;border-collapse:collapse">
        <tr>
            <td style="width:50%;padding-right:20px"><div style="border-top:1px solid #94A3B8;padding-top:4px" class="muted">Responsable d'équipe (nom et signature)</div></td>
            <td style="width:50%;padding-left:20px"><div style="border-top:1px solid #94A3B8;padding-top:4px" class="muted">Responsable du service (nom et signature)</div></td>
        </tr>
    </table>
@endsection

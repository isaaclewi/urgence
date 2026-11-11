@extends('exports.pdf.base')

@section('titre', 'Synthèse des indicateurs')

@section('corps')
    <h1>Synthèse des indicateurs</h1>
    <div class="sub">Période : {{ $jours }} derniers jours (depuis le {{ $debut->format('d/m/Y') }})</div>

    <div style="margin-top:12px">
    <table class="kpis"><tr>
        <td><div class="kpi-l">Alertes reçues</div><div class="kpi-v">{{ $total }}</div>
            <div class="kpi-h">{{ (int) ($parStatut['ouvertes'] ?? 0) }} en attente · {{ (int) ($parStatut['en_cours'] ?? 0) }} en cours</div></td>
        <td><div class="kpi-l">Taux de résolution</div><div class="kpi-v">{{ $tauxResolution !== null ? $tauxResolution . ' %' : '—' }}</div>
            <div class="kpi-h">{{ (int) ($parStatut['terminees'] ?? 0) }} terminée(s)</div></td>
        <td><div class="kpi-l">Délai d'affectation</div><div class="kpi-v">{{ $delaiMoyen }}</div>
            <div class="kpi-h">Médiane : {{ $delaiMedian }}</div></td>
        <td><div class="kpi-l">Durée d'intervention</div><div class="kpi-v">{{ $dureeMoyenne }}</div>
            <div class="kpi-h">{{ $nbInterventions }} intervention(s)</div></td>
    </tr></table>
    </div>

    <h2>Alertes par type</h2>
    @if(count($charts['types']['labels']) === 0)
        <div class="empty">Aucune alerte sur la période.</div>
    @else
        <table class="data">
            <thead><tr><th>Type</th><th class="num">Alertes</th><th class="num">Part</th></tr></thead>
            <tbody>
                @foreach($charts['types']['labels'] as $i => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="num">{{ $charts['types']['values'][$i] }}</td>
                        <td class="num">{{ $total > 0 ? round($charts['types']['values'][$i] * 100 / $total) : 0 }} %</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Charge par équipe</h2>
    @if(count($charts['charge']['labels']) === 0)
        <div class="empty">Aucune équipe.</div>
    @else
        <table class="data">
            <thead><tr><th>Équipe</th><th class="num">Transmises</th><th class="num">En cours</th><th class="num">Terminées</th></tr></thead>
            <tbody>
                @foreach($charts['charge']['labels'] as $i => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td class="num">{{ $charts['charge']['transmise'][$i] }}</td>
                        <td class="num">{{ $charts['charge']['en_cours'][$i] }}</td>
                        <td class="num">{{ $charts['charge']['terminee'][$i] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Zones les plus touchées</h2>
    @if($zones->isEmpty())
        <div class="empty">Aucune localisation renseignée.</div>
    @else
        <table class="data">
            <thead><tr><th>Localisation</th><th class="num">Alertes</th></tr></thead>
            <tbody>
                @foreach($zones as $z)
                    <tr><td>{{ \Illuminate\Support\Str::limit($z['lieu'], 90) }}</td><td class="num">{{ $z['n'] }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($ressources)
        <h2>Ressources</h2>
        <table class="data">
            <tbody>
                <tr><td>Agents actifs</td><td class="num">{{ $ressources['agents_actifs'] }}</td></tr>
                <tr><td>Véhicules disponibles</td><td class="num">{{ $ressources['vehicules_dispo'] }} / {{ $ressources['vehicules'] }}</td></tr>
                <tr><td>Stocks à surveiller (seuil, péremption)</td><td class="num">{{ $ressources['stocks_alerte'] }}</td></tr>
            </tbody>
        </table>
    @endif
@endsection

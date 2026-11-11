@extends('layouts.portal')

@section('title', 'Indicateurs')
@section('page-title', 'Indicateurs')
@section('page-subtitle', 'Activité des ' . $jours . ' derniers jours — alertes, délais et charge des équipes')

@section('page-actions')
    @foreach($periodes as $p)
        <a href="{{ route('services.kpi', ['jours' => $p]) }}"
           class="ds-btn ds-btn-sm {{ $jours === $p ? 'ds-btn-primary' : 'ds-btn-secondary' }}">{{ $p }} j</a>
    @endforeach
@endsection

@section('content')

{{-- ── Indicateurs clés ── --}}
<div class="ds-stats ds-section">
    <x-stat label="Alertes reçues" :value="$total"
            :hint="(int) ($parStatut['ouvertes'] ?? 0) . ' en attente · ' . (int) ($parStatut['en_cours'] ?? 0) . ' en cours'" />
    <x-stat label="Taux de résolution" :value="$tauxResolution !== null ? $tauxResolution . ' %' : '—'"
            :hint="(int) ($parStatut['terminees'] ?? 0) . ' alertes terminées'" type="success" />
    <x-stat label="Délai d'affectation" :value="$delaiMoyen" :hint="'Médiane : ' . $delaiMedian" type="info" />
    <x-stat label="Durée d'intervention" :value="$dureeMoyenne" :hint="$nbInterventions . ' intervention(s) terminée(s)'" type="warning" />
</div>

@if($ressources)
    <div class="ds-stats ds-section">
        <x-stat label="Agents actifs" :value="$ressources['agents_actifs']" />
        <x-stat label="Véhicules disponibles" :value="$ressources['vehicules_dispo'] . ' / ' . $ressources['vehicules']" type="success" />
        <x-stat label="Stocks à surveiller" :value="$ressources['stocks_alerte']" hint="seuil, péremption" type="danger" />
    </div>
@endif

@if($total === 0)
    <x-card class="ds-section">
        <x-empty title="Aucune alerte sur la période">Les graphiques apparaîtront dès que des alertes seront reçues.</x-empty>
    </x-card>
@else

<x-card title="Alertes par jour" class="ds-section">
    <div class="ds-chart"><canvas id="chJours"></canvas></div>
</x-card>

<div class="ds-grid ds-grid-2 ds-section">
    <x-card title="Répartition par type">
        <div class="ds-chart"><canvas id="chTypes"></canvas></div>
    </x-card>
    <x-card title="Alertes par heure de la journée">
        <div class="ds-chart"><canvas id="chHeures"></canvas></div>
    </x-card>
</div>

<div class="ds-grid ds-grid-2">
    <x-card title="Charge par équipe">
        @if(count($charts['charge']['labels']) === 0)
            <x-empty title="Aucune équipe">Créez des équipes pour suivre leur charge.</x-empty>
        @else
            <div class="ds-chart"><canvas id="chCharge"></canvas></div>
        @endif
    </x-card>

    <x-card title="Zones les plus touchées" flush>
        @if($zones->isEmpty())
            <x-empty title="Aucune localisation">Les lieux des alertes apparaîtront ici.</x-empty>
        @else
            <x-table>
                <thead><tr><th>Localisation</th><th style="text-align:right">Alertes</th></tr></thead>
                <tbody>
                    @foreach($zones as $z)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($z['lieu'], 70) }}</td>
                            <td style="text-align:right"><strong>{{ $z['n'] }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </x-table>
        @endif
    </x-card>
</div>

@endif
@endsection

@if($total > 0)
@push('scripts')
<script src="{{ asset('javascript/vendor/chart-4.4.7.umd.js') }}"></script>
<script>
(function () {
    var C = @json($charts);
    var css = getComputedStyle(document.documentElement);
    var accent = (css.getPropertyValue('--ds-accent') || '#1DB87A').trim();
    var palette = [accent, '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#14B8A6', '#64748B'];
    var grid = '#E2E8F0';

    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#475569';

    var common = { responsive: true, maintainAspectRatio: false };
    var intAxis = { beginAtZero: true, ticks: { precision: 0 }, grid: { color: grid } };
    var noGrid = { grid: { display: false } };

    new Chart(document.getElementById('chJours'), {
        type: 'line',
        data: { labels: C.jours.labels, datasets: [{
            label: 'Alertes', data: C.jours.values, borderColor: accent, backgroundColor: accent + '22',
            fill: true, tension: .3, pointRadius: 2
        }]},
        options: Object.assign({}, common, {
            plugins: { legend: { display: false } },
            scales: { y: intAxis, x: noGrid }
        })
    });

    new Chart(document.getElementById('chTypes'), {
        type: 'doughnut',
        data: { labels: C.types.labels, datasets: [{ data: C.types.values, backgroundColor: palette, borderWidth: 0 }] },
        options: Object.assign({}, common, { cutout: '62%', plugins: { legend: { position: 'bottom' } } })
    });

    new Chart(document.getElementById('chHeures'), {
        type: 'bar',
        data: { labels: C.heures.labels, datasets: [{ label: 'Alertes', data: C.heures.values, backgroundColor: accent, borderRadius: 4 }] },
        options: Object.assign({}, common, {
            plugins: { legend: { display: false } },
            scales: { y: intAxis, x: Object.assign({}, noGrid, { ticks: { maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } }) }
        })
    });

    var chargeEl = document.getElementById('chCharge');
    if (chargeEl) {
        new Chart(chargeEl, {
            type: 'bar',
            data: { labels: C.charge.labels, datasets: [
                { label: 'Transmises', data: C.charge.transmise, backgroundColor: '#F59E0B', borderRadius: 4 },
                { label: 'En cours',   data: C.charge.en_cours,  backgroundColor: '#3B82F6', borderRadius: 4 },
                { label: 'Terminées',  data: C.charge.terminee,  backgroundColor: '#10B981', borderRadius: 4 }
            ]},
            options: Object.assign({}, common, {
                plugins: { legend: { position: 'bottom' } },
                scales: { x: Object.assign({}, noGrid, { stacked: true }), y: Object.assign({}, intAxis, { stacked: true }) }
            })
        });
    }
})();
</script>
@endpush
@endif

@extends('citoyen')

@section('title', 'Suivi de mes alertes — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Suivi de mes alertes</h1>
        <p>Historique et état de vos signalements d'urgence.</p>
    </div>
    <a href="{{ route('MesAlertesController') }}" class="btn btn-primary">
        <i data-feather="plus" style="width:14px;height:14px;"></i>
        Nouvelle alerte
    </a>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;margin-bottom:24px;">
    <div class="stat-card anim-fade">
        <div class="stat-icon bg-amber-soft"><i data-feather="clock" style="width:18px;height:18px;"></i></div>
        <div>
            <div class="stat-value">{{ $alertes->where('statut','En attente')->count() }}</div>
            <div class="stat-label">En attente</div>
        </div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.06s">
        <div class="stat-icon bg-blue-soft"><i data-feather="loader" style="width:18px;height:18px;"></i></div>
        <div>
            <div class="stat-value">{{ $alertes->where('statut','En cours')->count() }}</div>
            <div class="stat-label">En cours</div>
        </div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.1s">
        <div class="stat-icon bg-green-soft"><i data-feather="check-circle" style="width:18px;height:18px;"></i></div>
        <div>
            <div class="stat-value">{{ $alertes->where('statut','Résolue')->count() }}</div>
            <div class="stat-label">Résolues</div>
        </div>
    </div>
    <div class="stat-card anim-fade" style="animation-delay:.14s">
        <div class="stat-icon bg-gray-soft"><i data-feather="list" style="width:18px;height:18px;"></i></div>
        <div>
            <div class="stat-value">{{ $alertes->count() }}</div>
            <div class="stat-label">Total</div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card anim-fade" style="animation-delay:.18s;">
    <div class="card-header" style="padding-bottom:14px;border-bottom:1px solid var(--border);">
        <span class="card-title" style="font-size:15px;">Historique complet</span>
        <span style="font-size:12px;color:var(--text-sec);">{{ $alertes->count() }} alerte(s)</span>
    </div>
    @if($alertes->isEmpty())
    <div style="text-align:center;padding:60px 24px;">
        <div style="width:56px;height:56px;background:var(--surface2);border-radius:50%;
                    display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
            <i data-feather="inbox" style="width:24px;height:24px;color:var(--text-muted);"></i>
        </div>
        <div style="font-weight:700;font-size:14px;color:var(--text);margin-bottom:5px;">Aucune alerte enregistrée</div>
        <div style="font-size:13px;color:var(--text-sec);">Vous n'avez pas encore créé d'alerte d'urgence.</div>
    </div>
    @else
    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Localisation</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alertes as $alerte)
                <tr>
                    <td style="font-weight:600;">{{ $alerte->titre }}</td>
                    <td>
                        <span class="pill pill-blue">{{ $alerte->type_alerte }}</span>
                    </td>
                    <td style="color:var(--text-sec);">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <i data-feather="map-pin" style="width:12px;height:12px;color:var(--red);flex-shrink:0;"></i>
                            {{ $alerte->localisation }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $alerte->created_at->format('d/m/Y') }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $alerte->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        @if($alerte->statut == 'En attente')
                            <span class="pill pill-yellow"><i data-feather="clock" style="width:11px;height:11px;"></i> En attente</span>
                        @elseif($alerte->statut == 'En cours')
                            <span class="pill pill-blue"><i data-feather="loader" style="width:11px;height:11px;"></i> En cours</span>
                        @elseif($alerte->statut == 'Résolue')
                            <span class="pill pill-green"><i data-feather="check-circle" style="width:11px;height:11px;"></i> Résolue</span>
                        @else
                            <span class="pill pill-gray">{{ $alerte->statut }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection

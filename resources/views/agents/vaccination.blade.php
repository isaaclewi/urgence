@extends('layouts.portal')

@section('title', 'Mes vaccinations')
@section('page-title', 'Mes vaccinations')
@section('page-subtitle', 'Programmes de vaccination du personnel et suivi de vos doses')

@section('content')

@if(session('success'))<x-card><p>{{ session('success') }}</p></x-card>@endif
@if(session('error'))<x-card><p class="ds-text-danger">{{ session('error') }}</p></x-card>@endif

<x-card>
    <h3 style="margin-bottom:.75rem">Mon suivi</h3>
    @forelse($mes as $i)
        @php $p = $i->programme; $pct = $i->progression(); @endphp
        <div style="padding:.9rem 0;border-bottom:1px solid #E2E8F0">
            <div style="display:flex;justify-content:space-between;gap:.5rem;flex-wrap:wrap;align-items:center">
                <strong>{{ $p->nom_programme }}</strong>
                <x-badge :type="$i->badgeType()">{{ $i->libelleStatut() }}</x-badge>
            </div>
            <div class="ds-muted">{{ $p->service?->nom ?? ($p->organisme_resp ?: 'Programme national') }}</div>
            <div style="margin:.5rem 0 .3rem;font-size:.85rem">{{ $i->libelleDoses() }}@if($p->nb_vaccins) · {{ $pct }}%@endif</div>
            @if($p->nb_vaccins)
            <div style="height:8px;background:#E5E7EB;border-radius:99px;overflow:hidden">
                <div style="height:100%;width:{{ $pct }}%;background:#1DB87A"></div>
            </div>
            @endif
            <div class="ds-muted" style="margin-top:.4rem">
                @if($i->derniere_dose)Dernière dose : {{ $i->derniere_dose->format('d/m/Y') }}. @endif
                @if($i->prochaine_dose && in_array($i->statut, ['inscrit','en_cours']))Prochaine dose : <strong>{{ $i->prochaine_dose->format('d/m/Y') }}</strong>.@endif
            </div>
            @if(! in_array($i->statut, ['termine', 'abandonne']))
            <form method="POST" action="{{ route('agent.vaccination.quitter', $i->id) }}" style="margin-top:.5rem" onsubmit="return confirm('Ne plus suivre ce programme ?')">
                @csrf @method('DELETE')
                <button class="ds-btn ds-btn-secondary ds-btn-sm" type="submit">Ne plus suivre</button>
            </form>
            @endif
        </div>
    @empty
        <p>Vous ne suivez aucun programme pour le moment.</p>
    @endforelse
</x-card>

<x-card>
    <h3 style="margin-bottom:.75rem">Programmes disponibles</h3>
    @forelse($disponibles as $p)
        <div style="padding:.7rem 0;border-bottom:1px solid #E2E8F0;display:flex;justify-content:space-between;gap:.5rem;flex-wrap:wrap;align-items:center">
            <div>
                <strong>{{ $p->nom_programme }}</strong>
                <div class="ds-muted">{{ $p->service?->nom ?? ($p->organisme_resp ?: 'Programme national') }}@if($p->nb_vaccins) · {{ $p->nb_vaccins }} dose(s)@endif · {{ $p->statut() }}</div>
            </div>
            <form method="POST" action="{{ route('agent.vaccination.inscrire', $p->id) }}">
                @csrf
                <button class="ds-btn ds-btn-primary ds-btn-sm" type="submit">Suivre ce programme</button>
            </form>
        </div>
    @empty
        <p>Aucun autre programme ouvert.</p>
    @endforelse
</x-card>
@endsection

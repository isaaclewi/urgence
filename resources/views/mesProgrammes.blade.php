@extends('citoyen')

@section('title', 'Mes programmes — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mes programmes de vaccination</h1>
        <p>Le suivi des programmes que vous avez choisis. Vous êtes notifié à chaque nouvelle dose.</p>
    </div>
    <a href="{{ route('vaccinationMenuController') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Vaccinations
    </a>
</div>

@if(!empty($erreurTechnique))
<div class="alert alert-warning anim-fade" style="margin-bottom:16px;display:block;">
    <strong>Cette page n'a pas pu charger vos programmes.</strong><br>
    <span style="font-size:13px;">Détail technique à transmettre au support : {{ $erreurTechnique }}</span>
</div>
@endif

@if(session('success'))<div class="alert alert-success anim-fade" style="margin-bottom:16px;"><span>{{ session('success') }}</span></div>@endif
@if(session('error'))<div class="alert alert-warning anim-fade" style="margin-bottom:16px;"><span>{{ session('error') }}</span></div>@endif

@if($prochaines->isNotEmpty())
<div class="alert alert-warning anim-fade" style="margin-bottom:20px;">
    <i data-feather="calendar" style="width:16px;height:16px;flex-shrink:0;"></i>
    <span>
        Prochaine dose :
        <strong>{{ $prochaines->first()->prochaine_dose->format('d/m/Y') }}</strong>
        — {{ $prochaines->first()->programme->nom_programme }}
    </span>
</div>
@endif

<h2 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px;">Mon suivi</h2>

@if($mes->isEmpty())
    <div class="card anim-fade" style="margin-bottom:28px;"><div class="card-body" style="text-align:center;color:var(--text-sec);">
        Vous ne suivez aucun programme pour le moment. Choisissez-en un dans Pandémies ou Épidémies.
        <div style="margin-top:14px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('programmeVaccinController') }}" class="btn btn-primary btn-sm">Pandémies</a>
            <a href="{{ route('programmeVaccinEpidemieController') }}" class="btn btn-secondary btn-sm">Épidémies</a>
        </div>
    </div></div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-bottom:32px;">
    @foreach($mes as $i)
    @php $p = $i->programme; $pct = $i->progression(); @endphp
    <div class="card anim-fade">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;gap:8px;align-items:flex-start;">
                <strong style="font-size:15px;">{{ $p->nom_programme }}</strong>
                <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:99px;white-space:nowrap;
                    background:{{ ['termine'=>'#D1FAE5','en_cours'=>'#DBEAFE','inscrit'=>'#FEF3C7','abandonne'=>'#F1F5F9'][$i->statut] ?? '#F1F5F9' }};
                    color:{{ ['termine'=>'#065F46','en_cours'=>'#1E40AF','inscrit'=>'#92400E','abandonne'=>'#64748B'][$i->statut] ?? '#64748B' }};">
                    {{ $i->libelleStatut() }}
                </span>
            </div>
            <div style="font-size:12px;color:var(--text-sec);margin-top:4px;">
                {{ $p->service?->nom ?? ($p->organisme_resp ?: 'Programme national') }}
                @if($p->age_cible) · Âge cible : {{ $p->age_cible }} @endif
            </div>

            <div style="margin-top:14px;">
                <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--text-sec);margin-bottom:5px;">
                    <span>{{ $i->libelleDoses() }}</span>
                    @if($p->nb_vaccins)<span>{{ $pct }}%</span>@endif
                </div>
                @if($p->nb_vaccins)
                <div style="height:8px;background:#E5E7EB;border-radius:99px;overflow:hidden;">
                    <div style="height:100%;width:{{ $pct }}%;background:var(--accent);border-radius:99px;"></div>
                </div>
                @endif
            </div>

            <div style="font-size:12px;color:var(--text-sec);margin-top:12px;line-height:1.6;">
                @if($i->derniere_dose)Dernière dose : {{ $i->derniere_dose->format('d/m/Y') }}<br>@endif
                @if($i->prochaine_dose && in_array($i->statut, ['inscrit','en_cours']))Prochaine dose : <strong>{{ $i->prochaine_dose->format('d/m/Y') }}</strong><br>@endif
                @if($p->date_debut || $p->date_fin)
                    Période : {{ $p->date_debut?->format('d/m/Y') ?? '…' }} → {{ $p->date_fin?->format('d/m/Y') ?? '…' }}
                @endif
            </div>

            @if($i->statut !== 'termine' && $i->statut !== 'abandonne')
            <form method="POST" action="{{ route('mesProgrammes.quitter', $i->id) }}" style="margin-top:14px;"
                  onsubmit="return confirm('Ne plus suivre ce programme ?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-secondary btn-sm">Ne plus suivre</button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection

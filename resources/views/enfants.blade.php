@extends('citoyen')

@section('title', 'Mes enfants — CongoAssist')

@section('content')

<div class="page-header anim-fade">
    <div class="page-header-left">
        <h1>Mes enfants</h1>
        <p>Ajoutez vos enfants pour suivre le calendrier de vaccination de chacun et recevoir des rappels.</p>
    </div>
    <a href="{{ route('vaccinationMenuController') }}" class="btn btn-secondary">
        <i data-feather="arrow-left" style="width:14px;height:14px;"></i> Vaccinations
    </a>
</div>

@if(session('success'))<div class="alert alert-success anim-fade" style="margin-bottom:16px;"><span>{{ session('success') }}</span></div>@endif
@if(session('error'))<div class="alert alert-warning anim-fade" style="margin-bottom:16px;"><span>{{ session('error') }}</span></div>@endif
@if($errors->any())
<div class="alert alert-error anim-fade" style="margin-bottom:16px;">
    <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
</div>
@endif

@if($enfants->isNotEmpty())
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:32px;">
    @foreach($enfants as $e)
    @php $r = $resumes[$e->id]; @endphp
    <div class="card anim-fade">
        <div class="card-body">
            <div style="display:flex;justify-content:space-between;gap:8px;align-items:flex-start;">
                <div>
                    <strong style="font-size:15px;">{{ $e->nomComplet() }}</strong>
                    <div style="font-size:12px;color:var(--text-sec);margin-top:3px;">
                        {{ $e->libelleAge() }} · né(e) le {{ $e->date_naissance->format('d/m/Y') }}
                    </div>
                </div>
                @if($r['retard'] > 0)
                    <span class="pill pill-red">{{ $r['retard'] }} en retard</span>
                @elseif($r['a_faire'] > 0)
                    <span class="pill pill-yellow">{{ $r['a_faire'] }} à faire</span>
                @else
                    <span class="pill pill-green">À jour</span>
                @endif
            </div>

            <div style="font-size:12px;color:var(--text-sec);margin-top:12px;">
                {{ $r['complet'] }} vaccin(s) terminé(s) sur {{ $r['total'] }}
            </div>

            <div style="display:flex;gap:8px;margin-top:14px;align-items:center;">
                <a href="{{ route('enfants.show', $e->id) }}" class="btn btn-primary btn-sm">Ouvrir le calendrier</a>
                <form method="POST" action="{{ route('enfants.destroy', $e->id) }}"
                      onsubmit="return confirm('Retirer {{ e($e->prenom) }} et tout son historique de vaccination ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-secondary btn-sm">Retirer</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<div class="card anim-fade" style="max-width:560px;">
    <div class="card-body">
        <h2 style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px;">Ajouter un enfant</h2>
        <form method="POST" action="{{ route('enfants.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label" for="prenom">Prénom *</label>
                    <input class="form-control" type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" maxlength="80" required>
                </div>
                <div>
                    <label class="form-label" for="nom">Nom</label>
                    <input class="form-control" type="text" id="nom" name="nom" value="{{ old('nom') }}" maxlength="80">
                </div>
                <div>
                    <label class="form-label" for="date_naissance">Date de naissance *</label>
                    <input class="form-control" type="date" id="date_naissance" name="date_naissance"
                           value="{{ old('date_naissance') }}" min="{{ today()->subYears(18)->addDay()->toDateString() }}" max="{{ today()->toDateString() }}" required>
                </div>
                <div>
                    <label class="form-label" for="sexe">Sexe</label>
                    <select class="form-control" id="sexe" name="sexe">
                        <option value="">—</option>
                        <option value="F" @selected(old('sexe') === 'F')>Fille</option>
                        <option value="M" @selected(old('sexe') === 'M')>Garçon</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px;">Ajouter et voir le calendrier</button>
        </form>
    </div>
</div>

@endsection

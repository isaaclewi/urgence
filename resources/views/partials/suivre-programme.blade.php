@php $ins = ($suivis[$programme->id] ?? null); $actif = $ins && $ins->statut !== 'abandonne'; @endphp
<div style="margin-top:12px;">
    @if($actif)
        <a href="{{ route('mesProgrammes') }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
            <i data-feather="check" style="width:13px;height:13px;"></i> Je suis ce programme · voir mon suivi
        </a>
    @elseif($programme->date_fin && $programme->date_fin->lt(today()))
        <span style="font-size:12px;color:var(--text-sec);">Programme terminé : inscriptions closes.</span>
    @else
        <form method="POST" action="{{ route('mesProgrammes.inscrire', $programme->id) }}">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;">Suivre ce programme</button>
        </form>
    @endif
</div>
